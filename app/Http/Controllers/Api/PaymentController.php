<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Enrollment;
use App\Models\Order;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use ApiResponse;

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:momo,vnpay,bank_transfer',
            'coupon_code' => 'nullable|string',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)->with('items.course')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return $this->error('Giỏ hàng trống', 422);
        }

        $subtotal = $cart->items->sum(fn ($item) => $item->course->effective_price);
        $discount = 0;
        $coupon = null;

        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', $validated['coupon_code'])->first();

            if (! $coupon || ! $coupon->isValid()) {
                return $this->error('Mã giảm giá không hợp lệ hoặc đã hết hạn', 422);
            }

            if ($coupon->isUsedByUser($request->user()->id)) {
                return $this->error('Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó', 422);
            }

            if ($subtotal < $coupon->min_order_amount) {
                return $this->error('Đơn hàng chưa đạt giá trị tối thiểu', 422);
            }

            $discount = $coupon->type === 'percent'
                ? $subtotal * ($coupon->value / 100)
                : min($coupon->value, $subtotal);
        }

        $total = max(0, $subtotal - $discount);

        $order = DB::transaction(function () use ($request, $cart, $subtotal, $discount, $total, $coupon, $validated) {
            $items = $cart->items->map(function ($item) {
                return [
                    'course_id' => $item->course_id,
                    'price' => $item->course->effective_price,
                    'title' => $item->course->title,
                ];
            })->toArray();

            $order = Order::create([
                'order_code' => 'ORD-'.strtoupper(Str::random(8)),
                'user_id' => $request->user()->id,
                'coupon_id' => $coupon?->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'items' => $items,
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order;
        });

        $paymentUrl = $this->generatePaymentUrl($order, $validated['payment_method']);

        ActivityLogService::log($request->user()->id, 'checkout', Order::class, $order->id, null, $request);

        return $this->success([
            'order' => $order,
            'payment_url' => $paymentUrl,
        ], 'Tạo đơn hàng thành công');
    }

    protected function generatePaymentUrl(Order $order, string $gateway): string
    {
        return match ($gateway) {
            'vnpay' => url("/api/v1/payments/vnpay/redirect/{$order->id}"),
            'momo' => url("/api/v1/payments/momo/redirect/{$order->id}"),
            default => url("/api/v1/payments/bank-transfer/{$order->id}"),
        };
    }

    public function confirmPayment(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return $this->error('Unauthorized', 403);
        }

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'status' => 'paid',
                'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
            ]);

            foreach ($order->items as $item) {
                $enrollment = Enrollment::firstOrCreate(
                    ['user_id' => $order->user_id, 'course_id' => $item['course_id']],
                    [
                        'order_id' => $order->id,
                        'status' => 'active',
                        'progress_percent' => 0,
                        'enrolled_at' => now(),
                    ]
                );

                $course = \App\Models\Course::find($item['course_id']);
                if ($course && $enrollment->wasRecentlyCreated) {
                    $course->increment('enrollment_count');
                }
            }

            Cart::where('user_id', $order->user_id)->first()?->items()->delete();
        });

        $request->user()->notify(new \App\Notifications\OrderPaidNotification($order));

        ActivityLogService::log($request->user()->id, 'payment_success', Order::class, $order->id, null, $request);

        return $this->success($order->fresh(), 'Thanh toán thành công');
    }

    public function history(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->paginated($orders);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $validated['code'])->first();

        if (! $coupon || ! $coupon->isValid()) {
            return $this->error('Mã giảm giá không hợp lệ', 422);
        }

        if ($coupon->isUsedByUser($request->user()->id)) {
            return $this->error('Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó', 422);
        }

        if ($validated['subtotal'] < $coupon->min_order_amount) {
            return $this->error('Chưa đạt giá trị đơn hàng tối thiểu', 422);
        }

        $discount = $coupon->type === 'percent'
            ? $validated['subtotal'] * ($coupon->value / 100)
            : min($coupon->value, $validated['subtotal']);

        return $this->success([
            'coupon' => $coupon,
            'discount_amount' => $discount,
            'final_amount' => max(0, $validated['subtotal'] - $discount),
        ]);
    }
}
