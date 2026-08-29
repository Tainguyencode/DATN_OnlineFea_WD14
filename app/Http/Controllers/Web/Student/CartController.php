<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Wishlist;
use App\Services\NotificationService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Lấy giá hiệu lực (ưu tiên discount_price > sale_price > price).
     */
    private function getEffectivePrice(Course $course): float
    {
        return (float) ($course->discount_price ?? $course->sale_price ?? $course->price);
    }

    /**
     * Tính commission và instructor earning cho một order item.
     */
    private function calculateItemFinancials(Course $course, float $itemNetPrice, ?Coupon $coupon): array
    {
        $price = $this->getEffectivePrice($course);
        $commissionRate = (float) $course->instructor->getCommissionRate();

        if ($coupon && $coupon->isInstructorCoupon()) {
            // Mã GV: Trừ vào thu nhập GV, giữ nguyên hoa hồng Admin
            $baseAdminCommission = ($price * $commissionRate) / 100;
            $commissionAmount = min($itemNetPrice, $baseAdminCommission);
            $instructorEarning = max(0, $itemNetPrice - $commissionAmount);
        } else {
            // Mã Admin hoặc không mã: Trừ vào hoa hồng Admin, giữ nguyên thu nhập GV
            $baseInstructorShare = ($price * (100 - $commissionRate)) / 100;
            $instructorEarning = min($itemNetPrice, $baseInstructorShare);
            $commissionAmount = max(0, $itemNetPrice - $instructorEarning);
        }

        return [
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'instructor_earning' => $instructorEarning,
        ];
    }

    /**
     * Tạo OrderItem + Enrollment cho danh sách courses.
     */
    private function createOrderItemsAndEnrollments(Order $order, $courses, ?Coupon $coupon, float $discount, float $eligibleSubtotal, bool $shouldEnroll = false): void
    {
        if (method_exists($courses, 'loadMissing')) {
            $courses->loadMissing('instructor');
        }

        $existingEnrollments = $shouldEnroll
            ? Enrollment::query()
                ->where('user_id', auth()->id())
                ->whereIn('course_id', $courses->pluck('id'))
                ->get()
                ->keyBy('course_id')
            : collect();

        foreach ($courses as $course) {
            $price = $this->getEffectivePrice($course);
            $isEligible = $coupon ? $coupon->isEligibleForCourse($course) : false;
            $itemDiscount = ($isEligible && $eligibleSubtotal > 0) ? ($price / $eligibleSubtotal) * $discount : 0;
            $itemNetPrice = max(0, $price - $itemDiscount);

            $financials = $this->calculateItemFinancials($course, $itemNetPrice, $coupon);

            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price' => $price,
                'commission_rate' => $financials['commission_rate'],
                'commission_amount' => $financials['commission_amount'],
                'instructor_earning' => $financials['instructor_earning'],
            ]);

            if ($shouldEnroll) {
                $enrollment = $existingEnrollments->get($course->id)
                    ?? new Enrollment(['user_id' => auth()->id(), 'course_id' => $course->id]);
                $shouldActivate = ! $enrollment->exists || $enrollment->status === 'cancelled';

                if ($shouldActivate) {
                    $enrollment->fill([
                        'order_id' => $order->id,
                        'status' => Enrollment::STATUS_ACTIVE,
                        'progress_percent' => 0,
                        'completed_lessons' => 0,
                        'completed_at' => null,
                        'enrolled_at' => now(),
                    ])->save();

                    $course->increment('enrollment_count');

                    if ($course->instructor) {
                        $studentName = auth()->user()?->name ?? 'Một học viên';
                        app(NotificationService::class)->send(
                            $course->instructor,
                            'Học viên mới đăng ký khóa học',
                            "Học viên {$studentName} đã đăng ký khóa học \"{$course->title}\".",
                            'new_enrollment',
                            route('instructor.courses.students', $course)
                        );
                    }
                }
            }
        }
    }

    /**
     * Lấy hoặc tạo mới giỏ hàng của học viên hiện tại.
     */
    protected function getCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    /**
     * Hiển thị trang giỏ hàng.
     */
    public function index(): View
    {
        $cart = $this->getCart()->load(['courses.instructor:id,name']);
        $total = $cart->courses->sum(fn ($c) => $this->getEffectivePrice($c));

        // Lấy đơn hàng pending nếu có để cảnh báo khôi phục
        $pendingOrder = Order::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->first();

        // Lấy danh sách mã giảm giá khả dụng để hiển thị trên UI
        $activeCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
            })
            ->get();

        // Khóa học gợi ý mua kèm (bỏ các khóa đã có trong giỏ hoặc đã đăng ký)
        $cartCourseIds = $cart->courses->pluck('id')->toArray();
        $enrolledCourseIds = Enrollment::where('user_id', auth()->id())->pluck('course_id')->toArray();
        $excludeIds = array_merge($cartCourseIds, $enrolledCourseIds);

        $suggestedCourses = Course::published()
            ->whereNotIn('id', $excludeIds)
            ->with('instructor:id,name,avatar')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('student.cart.index', compact('cart', 'total', 'activeCoupons', 'pendingOrder', 'suggestedCourses'));
    }

    /**
     * Thêm một khóa học vào giỏ hàng.
     */
    public function add(Course $course): RedirectResponse
    {
        if (! $course->isPublished()) {
            return back()->with('error', 'Khóa học chưa được xuất bản hoặc không khả dụng.');
        }

        if (Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->withLearningAccess()
            ->exists()) {
            return back()->with('error', 'Bạn đã sở hữu và đăng ký khóa học này rồi.');
        }

        // 3. Chặn thêm trùng khóa học trong giỏ hàng
        $cart = $this->getCart();
        $isAlreadyInCart = $cart->courses()->where('course_id', $course->id)->exists();

        if ($isAlreadyInCart) {
            return back()->with('error', 'Khóa học đã có sẵn trong giỏ hàng của bạn.');
        }

        // 4. Thêm khóa học vào giỏ hàng
        $cart->courses()->syncWithoutDetaching([$course->id]);

        return back()->with('success', 'Đã thêm khóa học vào giỏ hàng thành công!');
    }

    /**
     * Xóa khóa học khỏi giỏ hàng.
     */
    public function remove(int $courseId): RedirectResponse
    {
        $cart = $this->getCart();
        $cart->courses()->detach($courseId);

        return back()->with('success', 'Đã xóa khóa học khỏi giỏ hàng.');
    }

    /**
     * Xử lý quy trình checkout và tạo đơn hàng chờ thanh toán.
     */
    public function checkout(Request $request, PaymentGatewayService $paymentService): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:payos,bank_transfer',
            'coupon_code' => 'nullable|string',
            'course_ids' => 'required|array',
            'course_ids.*' => 'required|integer|exists:courses,id',
        ]);

        $selectedCourseIds = $validated['course_ids'];
        $cart = $this->getCart()->load(['courses' => function ($query) use ($selectedCourseIds) {
            $query->whereIn('courses.id', $selectedCourseIds)->with('instructor');
        }]);

        if ($cart->courses->isEmpty()) {
            return back()->with('error', 'Vui lòng chọn ít nhất một khóa học để thanh toán.');
        }

        // Tính toán số tiền
        $subtotal = $cart->courses->sum(fn ($c) => $this->getEffectivePrice($c));
        $discount = 0;
        $coupon = null;
        $eligibleSubtotal = $subtotal;

        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', $validated['coupon_code'])->first();
            if (! $coupon || ! $coupon->isValid()) {
                return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
            }
            if ($coupon->isUsedByUser(auth()->id())) {
                return back()->with('error', 'Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó.');
            }

            $eligibleCourses = $cart->courses->filter(fn ($c) => $coupon->isEligibleForCourse($c));
            if ($eligibleCourses->isEmpty()) {
                return back()->with('error', 'Mã giảm giá này không áp dụng cho các khóa học trong giỏ hàng của bạn.');
            }

            $eligibleSubtotal = $eligibleCourses->sum(fn ($c) => $this->getEffectivePrice($c));
            if ($eligibleSubtotal < $coupon->min_order_amount) {
                return back()->with('error', 'Giá trị các khóa học đủ điều kiện chưa đạt tối thiểu để áp dụng mã giảm giá này.');
            }

            $discount = $coupon->calculateDiscount($eligibleSubtotal);
        }

        $total = max(0, $subtotal - $discount);

        // Lưu thông tin dưới dạng JSON snapshot
        $itemsSnapshot = $cart->courses->map(fn ($c) => [
            'course_id' => $c->id,
            'title' => $c->title,
            'price' => $this->getEffectivePrice($c),
        ])->toArray();

        $orderCode = 'ORD-'.strtoupper(Str::random(8));

        // Nếu tổng tiền là 0 (Ví dụ coupon giảm 100%), thực hiện hoàn tất thanh toán ngay lập tức
        if ($total <= 0) {
            $completed = DB::transaction(function () use ($cart, $subtotal, $discount, $coupon, $validated, $orderCode, $itemsSnapshot, $selectedCourseIds, $eligibleSubtotal): bool {
                $lockedCoupon = null;
                if ($coupon) {
                    $lockedCoupon = Coupon::query()->lockForUpdate()->find($coupon->id);

                    if (! $lockedCoupon || ! $lockedCoupon->isValid() || $lockedCoupon->isUsedByUser(auth()->id())) {
                        return false;
                    }
                }

                $order = Order::create([
                    'order_code' => $orderCode,
                    'user_id' => auth()->id(),
                    'coupon_id' => $coupon?->id,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'total_amount' => 0,
                    'status' => 'paid',
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => 'FREE-'.strtoupper(Str::random(10)),
                    'items' => $itemsSnapshot,
                ]);

                Payment::create([
                    'order_id' => $order->id,
                    'gateway' => $validated['payment_method'],
                    'transaction_id' => $order->transaction_id,
                    'amount' => 0,
                    'status' => 'success',
                    'paid_at' => now(),
                    'gateway_response' => ['message' => 'Miễn phí hoặc giảm giá 100%'],
                ]);

                $this->createOrderItemsAndEnrollments($order, $cart->courses, $coupon, $discount, $eligibleSubtotal, true);

                $lockedCoupon?->increment('used_count');

                // Xóa các khóa học đã mua khỏi giỏ hàng
                $cart->courses()->detach($selectedCourseIds);

                return true;
            });

            if (! $completed) {
                return back()->with('error', 'Mã giảm giá không còn lượt sử dụng. Vui lòng kiểm tra lại đơn hàng.');
            }

            return redirect()->route('student.checkout.success', $orderCode)
                ->with('success', 'Đơn hàng miễn phí đã được kích hoạt thành công!');
        }

        // Trường hợp đơn hàng cần thanh toán phí (total > 0), tạo đơn hàng chờ thanh toán (pending)
        $order = DB::transaction(function () use ($cart, $subtotal, $discount, $total, $coupon, $validated, $orderCode, $itemsSnapshot, $eligibleSubtotal) {
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => auth()->id(),
                'coupon_id' => $coupon?->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'items' => $itemsSnapshot,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'gateway' => $validated['payment_method'],
                'amount' => $total,
                'status' => 'pending',
            ]);

            $this->createOrderItemsAndEnrollments($order, $cart->courses, $coupon, $discount, $eligibleSubtotal, false);

            return $order;
        });

        // Lấy URL thanh toán tương ứng và chuyển hướng người dùng tới trang quét mã QR (PayOS / VietQR)
        try {
            $paymentUrl = $paymentService->getPaymentUrl($order);
        } catch (\RuntimeException $exception) {
            Log::warning('Student payment-link creation failed', [
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'exception_class' => $exception::class,
            ]);

            return redirect()->route('student.checkout.pay', $orderCode)
                ->with('error', 'Không thể tạo liên kết thanh toán PayOS. Vui lòng thử lại sau.');
        }

        return redirect($paymentUrl);
    }

    /**
     * Áp dụng mã giảm giá và tính toán số tiền qua AJAX.
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
            'course_ids' => 'required|array',
            'course_ids.*' => 'required|integer|exists:courses,id',
        ]);

        $couponCode = $validated['coupon_code'];
        $courseIds = $validated['course_ids'];

        $coupon = Coupon::where('code', $couponCode)->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại.',
            ]);
        }

        if (! $coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn hoặc hết lượt sử dụng.',
            ]);
        }

        if ($coupon->isUsedByUser(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó.',
            ]);
        }

        $courses = Course::whereIn('id', $courseIds)->get();
        if ($courses->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học nào để áp dụng mã giảm giá.',
            ]);
        }

        $eligibleCourses = $courses->filter(fn ($c) => $coupon->isEligibleForCourse($c));
        if ($eligibleCourses->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá này không áp dụng cho các khóa học đã chọn.',
            ]);
        }

        $subtotal = $courses->sum(fn ($c) => $this->getEffectivePrice($c));
        $eligibleSubtotal = $eligibleCourses->sum(fn ($c) => $this->getEffectivePrice($c));

        if ($eligibleSubtotal < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Các khóa học đủ điều kiện chưa đạt giá trị tối thiểu '.number_format($coupon->min_order_amount, 0, ',', '.').'đ để áp dụng mã này.',
            ]);
        }

        // Tính số tiền giảm giá
        $discount = $coupon->calculateDiscount($eligibleSubtotal);

        $total = max(0, $subtotal - $discount);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => (float) $discount,
            'new_total' => (float) $total,
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
                'min_order_amount' => (float) $coupon->min_order_amount,
            ],
        ]);
    }

    /**
     * Hiển thị trang lựa chọn cổng thanh toán cho đơn hàng chờ thanh toán.
     *
     * @return View|RedirectResponse
     */
    public function showPaymentPage(string $orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Nếu đơn hàng đã trả, chuyển thẳng đến trang thành công
        if ($order->status === 'paid') {
            return redirect()->route('student.checkout.success', $orderCode);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('student.orders')->with('error', 'Đơn hàng này không ở trạng thái chờ thanh toán.');
        }

        $order->load(['items.course', 'coupon']);

        $activeCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
            })
            ->get();

        return view('student.cart.pay', compact('order', 'activeCoupons'));
    }

    /**
     * Áp dụng mã giảm giá trực tiếp vào đơn hàng chờ thanh toán.
     */
    public function applyCouponToOrder(Request $request, string $orderCode, PaymentGatewayService $paymentService): JsonResponse
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $couponCode = $validated['coupon_code'];
        $coupon = Coupon::where('code', $couponCode)->first();

        if (! $coupon || ! $coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.']);
        }

        if ($coupon->isUsedByUser(auth()->id())) {
            return response()->json(['success' => false, 'message' => 'Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó.']);
        }

        $orderItems = $order->items()->with('course')->get();
        $courses = $orderItems->pluck('course');

        $eligibleCourses = $courses->filter(fn ($c) => $coupon->isEligibleForCourse($c));
        if ($eligibleCourses->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá này không áp dụng cho các khóa học trong đơn hàng.']);
        }

        $subtotal = $order->subtotal > 0 ? (float) $order->subtotal : $courses->sum(fn ($c) => $this->getEffectivePrice($c));
        $eligibleSubtotal = $eligibleCourses->sum(fn ($c) => $this->getEffectivePrice($c));

        if ($eligibleSubtotal < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu '.number_format($coupon->min_order_amount, 0, ',', '.').'đ để áp dụng mã này.',
            ]);
        }

        $discount = $coupon->calculateDiscount($eligibleSubtotal);
        $total = max(0, $subtotal - $discount);

        DB::transaction(function () use ($order, $coupon, $discount, $total, $orderItems, $eligibleSubtotal) {
            $order->update([
                'coupon_id' => $coupon->id,
                'discount_amount' => $discount,
                'total_amount' => $total,
            ]);

            if ($order->payment) {
                $order->payment->update(['amount' => $total]);
            }

            foreach ($orderItems as $item) {
                $isEligible = $coupon->isEligibleForCourse($item->course);
                $itemDiscount = ($isEligible && $eligibleSubtotal > 0) ? ($item->price / $eligibleSubtotal) * $discount : 0;
                $itemNetPrice = max(0, $item->price - $itemDiscount);
                $financials = $this->calculateItemFinancials($item->course, $itemNetPrice, $coupon);

                $item->update([
                    'commission_rate' => $financials['commission_rate'],
                    'commission_amount' => $financials['commission_amount'],
                    'instructor_earning' => $financials['instructor_earning'],
                ]);
            }
        });

        if ($total <= 0 && ! $paymentService->completeFreeOrder($order->fresh())) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hoàn tất đơn hàng miễn phí. Vui lòng thử lại.',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => (float) $discount,
            'new_total' => (float) $total,
            'coupon_code' => $coupon->code,
            'order_status' => $total <= 0 ? 'paid' : 'pending',
        ]);
    }

    /**
     * Gỡ mã giảm giá khỏi đơn hàng chờ thanh toán.
     */
    public function removeCouponFromOrder(string $orderCode): JsonResponse
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $subtotal = (float) $order->subtotal;

        DB::transaction(function () use ($order, $subtotal) {
            $order->update([
                'coupon_id' => null,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
            ]);

            if ($order->payment) {
                $order->payment->update(['amount' => $subtotal]);
            }

            foreach ($order->items()->with('course')->get() as $item) {
                $financials = $this->calculateItemFinancials($item->course, $item->price, null);
                $item->update([
                    'commission_rate' => $financials['commission_rate'],
                    'commission_amount' => $financials['commission_amount'],
                    'instructor_earning' => $financials['instructor_earning'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã gỡ mã giảm giá.',
            'new_total' => $subtotal,
        ]);
    }

    /**
     * Xử lý lựa chọn thanh toán PayOS/VietQR.
     */
    public function processPayment(Request $request, string $orderCode, PaymentGatewayService $paymentService): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:payos,bank_transfer',
        ]);

        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status === 'paid') {
            return redirect()->route('student.checkout.success', $orderCode);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('student.orders')->with('error', 'Đơn hàng này không ở trạng thái chờ thanh toán.');
        }

        $paymentMethod = $validated['payment_method'];

        $dbGateway = ($paymentMethod === 'payos') ? 'bank_transfer' : $paymentMethod;

        // Cập nhật phương thức thanh toán cho đơn hàng
        $order->update(['payment_method' => $paymentMethod]);

        if ($order->payment) {
            $order->payment->update(['gateway' => $dbGateway]);
        } else {
            Payment::create([
                'order_id' => $order->id,
                'gateway' => $dbGateway,
                'amount' => $order->total_amount,
                'status' => 'pending',
            ]);
        }

        try {
            $paymentUrl = $paymentService->getPaymentUrl($order);
        } catch (\RuntimeException $exception) {
            Log::warning('Student payment-link creation failed', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'exception_class' => $exception::class,
            ]);

            return redirect()->route('student.checkout.pay', $orderCode)
                ->with('error', 'Không thể tạo liên kết thanh toán PayOS. Vui lòng thử lại sau.');
        }

        return redirect($paymentUrl);
    }

    /**
     * Hiển thị giao diện giả lập PayOS trong local/testing.
     *
     * @return View|RedirectResponse
     */
    public function mockGateway(string $orderCode)
    {
        abort_unless(
            app()->environment(['local', 'testing']) && config('services.payos.mode') === 'mock',
            404
        );

        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status === 'paid') {
            return redirect()->route('student.checkout.success', $orderCode);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('student.dashboard')->with('error', 'Đơn hàng này không ở trạng thái chờ thanh toán.');
        }

        $gateway = $order->payment_method;

        return view('student.cart.mock_gateway', compact('order', 'gateway'));
    }

    /**
     * Xử lý mô phỏng kết quả thanh toán từ phía người dùng.
     */
    public function simulatePayment(Request $request, string $orderCode, PaymentGatewayService $paymentService): RedirectResponse
    {
        abort_unless(
            app()->environment(['local', 'testing']) && config('services.payos.mode') === 'mock',
            404
        );

        $request->validate([
            'status' => 'required|in:success,failed',
        ]);

        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $success = $paymentService->processMockPayment($order, $request->status);

        if ($success) {
            return redirect()->route('student.checkout.success', $orderCode)
                ->with('success', 'Thanh toán thành công! Khóa học đã được đăng ký.');
        }

        return redirect()->route('student.checkout.failed', $orderCode)
            ->with('error', 'Giao dịch thanh toán đã bị hủy hoặc thất bại.');
    }

    /**
     * Hiển thị trang kết quả thanh toán thành công.
     *
     * @return View|RedirectResponse
     */
    public function successPage(string $orderCode, PaymentGatewayService $paymentService)
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Tự động kiểm tra trạng thái thanh toán từ PayOS API nếu đơn hàng chưa được chuyển sang paid (đặc biệt hữu ích khi dev localhost không có Webhook)
        if ($order->status !== 'paid') {
            $paymentService->checkAndUpdatePayOSStatus($order);
            $order->refresh();
        }

        if ($order->status !== 'paid') {
            return redirect()->route('student.dashboard')->with('error', 'Đơn hàng này chưa được thanh toán thành công.');
        }

        // Tải chi tiết các mục đơn hàng dạng Eloquent Model kèm theo thông tin khóa học & giảng viên
        $orderItems = $order->items()->with(['course.instructor'])->get();

        return view('student.cart.success', compact('order', 'orderItems'));
    }

    /**
     * Hiển thị trang kết quả thanh toán thất bại.
     *
     * @return View|RedirectResponse
     */
    public function failedPage(string $orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status !== 'failed') {
            return redirect()->route('student.dashboard')->with('error', 'Đơn hàng này không ở trạng thái thanh toán thất bại.');
        }

        // Tải chi tiết các mục đơn hàng dạng Eloquent Model kèm theo thông tin khóa học & giảng viên
        $orderItems = $order->items()->with(['course.instructor'])->get();

        return view('student.cart.failed', compact('order', 'orderItems'));
    }

    /**
     * Mua ngay một khóa học (bỏ qua giỏ hàng và chuyển hướng đến trang thanh toán).
     */
    public function buyNow(Course $course, PaymentGatewayService $paymentService): RedirectResponse
    {
        if (! $course->isPublished()) {
            return back()->with('error', 'Khóa học chưa được xuất bản hoặc không khả dụng.');
        }

        if (Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->withLearningAccess()
            ->exists()) {
            return back()->with('error', 'Bạn đã sở hữu và đăng ký khóa học này rồi.');
        }

        $price = $this->getEffectivePrice($course);
        $orderCode = 'ORD-'.strtoupper(Str::random(8));

        $order = DB::transaction(function () use ($course, $price, $orderCode) {
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => auth()->id(),
                'coupon_id' => null,
                'subtotal' => $price,
                'discount_amount' => 0,
                'total_amount' => $price,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]);

            Payment::create([
                'order_id' => $order->id,
                'gateway' => 'bank_transfer',
                'amount' => $price,
                'status' => 'pending',
            ]);

            $this->createOrderItemsAndEnrollments($order, collect([$course]), null, 0, $price, false);

            return $order;
        });

        return redirect()->route('student.checkout.pay', $order->order_code);
    }

    /**
     * Xóa khóa học khỏi giỏ hàng qua AJAX.
     */
    public function removeAjax(int $courseId): JsonResponse
    {
        $cart = $this->getCart();
        $cart->courses()->detach($courseId);

        $remainingCourses = $cart->courses()->get();
        $newSubtotal = $remainingCourses->sum(fn ($c) => $this->getEffectivePrice($c));

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khóa học khỏi giỏ hàng.',
            'cart_count' => $remainingCourses->count(),
            'subtotal' => $newSubtotal,
        ]);
    }

    /**
     * Chuyển khóa học từ giỏ hàng sang danh sách yêu thích (Wishlist).
     */
    public function moveToWishlist(Request $request, int $courseId): JsonResponse|RedirectResponse
    {
        $cart = $this->getCart();
        $cart->courses()->detach($courseId);

        Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'course_id' => $courseId,
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã chuyển khóa học sang danh sách yêu thích.',
            ]);
        }

        return back()->with('success', 'Đã chuyển khóa học sang danh sách yêu thích.');
    }

    /**
     * Endpoint Polling kiểm tra trạng thái đơn hàng real-time.
     */
    public function checkStatus(string $orderCode, PaymentGatewayService $paymentService): JsonResponse
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->first();

        if (! $order) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($order->status !== 'paid') {
            $paymentService->checkAndUpdatePayOSStatus($order);
            $order->refresh();
        }

        return response()->json([
            'status' => $order->status,
            'order_code' => $order->order_code,
        ]);
    }
}
