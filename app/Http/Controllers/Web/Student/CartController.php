<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\PaymentGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Lấy hoặc tạo mới giỏ hàng của học viên hiện tại.
     *
     * @return Cart
     */
    protected function getCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    /**
     * Hiển thị trang giỏ hàng.
     *
     * @return View
     */
    public function index(): View
    {
        $cart = $this->getCart()->load(['courses.instructor:id,name']);
        $total = $cart->courses->sum(fn ($c) => $c->discount_price ?? $c->sale_price ?? $c->price);

        return view('student.cart.index', compact('cart', 'total'));
    }

    /**
     * Thêm một khóa học vào giỏ hàng.
     *
     * @param Course $course
     * @return RedirectResponse
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
     *
     * @param int $courseId
     * @return RedirectResponse
     */
    public function remove(int $courseId): RedirectResponse
    {
        $cart = $this->getCart();
        $cart->courses()->detach($courseId);

        return back()->with('success', 'Đã xóa khóa học khỏi giỏ hàng.');
    }

    /**
     * Xử lý quy trình checkout và tạo đơn hàng chờ thanh toán.
     *
     * @param Request $request
     * @param PaymentGatewayService $paymentService
     * @return RedirectResponse
     */
    public function checkout(Request $request, PaymentGatewayService $paymentService): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:momo,vnpay,bank_transfer',
            'coupon_code' => 'nullable|string',
        ]);

        $cart = $this->getCart()->load('courses');
        if ($cart->courses->isEmpty()) {
            return back()->with('error', 'Giỏ hàng của bạn hiện đang trống.');
        }

        // Tính toán số tiền
        $subtotal = $cart->courses->sum(fn ($c) => $c->discount_price ?? $c->sale_price ?? $c->price);
        $discount = 0;
        $coupon = null;

        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', $validated['coupon_code'])->first();
            if ($coupon && $coupon->isValid() && $subtotal >= $coupon->min_order_amount) {
                $discount = $coupon->type === 'percent'
                    ? $subtotal * ($coupon->value / 100)
                    : min($coupon->value, $subtotal);
            }
        }

        $total = max(0, $subtotal - $discount);

        // Lưu thông tin dưới dạng JSON snapshot
        $itemsSnapshot = $cart->courses->map(fn ($c) => [
            'course_id' => $c->id,
            'title' => $c->title,
            'price' => (float) ($c->discount_price ?? $c->sale_price ?? $c->price),
        ])->toArray();

        $orderCode = 'ORD-' . strtoupper(Str::random(8));

        // Nếu tổng tiền là 0 (Ví dụ coupon giảm 100%), thực hiện hoàn tất thanh toán ngay lập tức
        if ($total <= 0) {
            DB::transaction(function () use ($cart, $subtotal, $discount, $coupon, $validated, $orderCode, $itemsSnapshot) {
                $order = Order::create([
                    'order_code' => $orderCode,
                    'user_id' => auth()->id(),
                    'coupon_id' => $coupon?->id,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'total_amount' => 0,
                    'status' => 'paid',
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => 'FREE-' . strtoupper(Str::random(10)),
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

                foreach ($cart->courses as $course) {
                    $price = $course->discount_price ?? $course->sale_price ?? $course->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'course_id' => $course->id,
                        'price' => $price,
                    ]);

                    $enrollment = Enrollment::firstOrCreate(
                        ['user_id' => auth()->id(), 'course_id' => $course->id],
                        [
                            'order_id' => $order->id,
                            'status' => 'active',
                            'progress_percent' => 0,
                            'enrolled_at' => now(),
                        ]
                    );

                    if ($enrollment->wasRecentlyCreated) {
                        $course->increment('enrollment_count');
                    }
                }

                if ($coupon) {
                    $coupon->increment('used_count');
                }

                // Xóa các khóa học đã mua khỏi giỏ hàng
                $cart->courses()->detach();
            });

            return redirect()->route('student.checkout.success', $orderCode)
                ->with('success', 'Đơn hàng miễn phí đã được kích hoạt thành công!');
        }

        // Trường hợp đơn hàng cần thanh toán phí (total > 0), tạo đơn hàng chờ thanh toán (pending)
        $order = DB::transaction(function () use ($cart, $subtotal, $discount, $total, $coupon, $validated, $orderCode, $itemsSnapshot) {
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

            foreach ($cart->courses as $course) {
                $price = $course->discount_price ?? $course->sale_price ?? $course->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $course->id,
                    'price' => $price,
                ]);
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order;
        });

        // Lấy URL thanh toán tương ứng và chuyển hướng người dùng
        $paymentUrl = $paymentService->getPaymentUrl($order);

        return redirect($paymentUrl);
    }

    /**
     * Hiển thị giao diện thanh toán chuyển khoản ngân hàng.
     *
     * @param string $orderCode
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
            return redirect()->route('student.dashboard')->with('error', 'Đơn hàng này không ở trạng thái chờ thanh toán.');
        }

        return view('student.cart.bank_transfer', compact('order'));
    }

    /**
     * Hiển thị giao diện giả lập VNPay hoặc MoMo.
     *
     * @param string $orderCode
     * @return View|RedirectResponse
     */
    public function mockGateway(string $orderCode)
    {
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
     *
     * @param Request $request
     * @param string $orderCode
     * @param PaymentGatewayService $paymentService
     * @return RedirectResponse
     */
    public function simulatePayment(Request $request, string $orderCode, PaymentGatewayService $paymentService): RedirectResponse
    {
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

        return redirect()->route('student.dashboard')->with('error', 'Giao dịch thanh toán đã bị hủy hoặc thất bại.');
    }

    /**
     * Hiển thị trang kết quả thanh toán thành công.
     *
     * @param string $orderCode
     * @return View|RedirectResponse
     */
    public function successPage(string $orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status !== 'paid') {
            return redirect()->route('student.dashboard')->with('error', 'Đơn hàng này chưa được thanh toán thành công.');
        }

        // Tải chi tiết các mục đơn hàng dạng Eloquent Model kèm theo thông tin khóa học & giảng viên
        $orderItems = $order->items()->with(['course.instructor'])->get();

        return view('student.cart.success', compact('order', 'orderItems'));
    }
}

