<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Service quản lý các cổng thanh toán (VNPay, MoMo, Chuyển khoản)
 * Được cấu trúc sẵn sàng để thay thế bằng API thật trong tương lai.
 */
class PaymentGatewayService
{
    /**
     * Lấy URL thanh toán tương ứng cho đơn hàng.
     */
    public function getPaymentUrl(Order $order): string
    {
        // Nếu chọn chuyển khoản ngân hàng, dẫn đến trang hiển thị QR Code và thông tin chuyển khoản nội bộ
        if ($order->payment_method === 'bank_transfer') {
            return route('student.checkout.pay', $order->order_code);
        }

        // Đối với VNPay hoặc MoMo, dẫn tới trang cổng thanh toán giả lập (Mock Gateway)
        return route('student.checkout.mock_gateway', [
            'order_code' => $order->order_code,
            'gateway' => $order->payment_method,
        ]);
    }

    /**
     * Xử lý kết quả giao dịch (Thành công / Thất bại).
     * Áp dụng cho cả chuyển khoản giả lập lẫn callback từ các cổng VNPay, MoMo.
     *
     * @param  string  $status  ('success' hoặc 'failed')
     * @param  string|null  $transactionId  Mã giao dịch thực tế hoặc giả lập
     */
    public function processMockPayment(Order $order, string $status, ?string $transactionId = null): bool
    {
        return DB::transaction(function () use ($order, $status, $transactionId): bool {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            // A repeated callback for a completed order must be a no-op.
            if ($lockedOrder->status === 'paid') {
                return true;
            }

            $payment = $lockedOrder->payment()->lockForUpdate()->first();

            if ($status !== 'success') {
                $this->markPaymentFailed($lockedOrder, $payment, 'Giao dịch bị hủy hoặc thất bại.');

                return false;
            }

            $coupon = null;
            if ($lockedOrder->coupon_id) {
                $coupon = $lockedOrder->coupon()->lockForUpdate()->first();

                if (! $coupon || ! $coupon->isValid() || $coupon->isUsedByUser($lockedOrder->user_id)) {
                    $this->markPaymentFailed($lockedOrder, $payment, 'Mã giảm giá đã hết hiệu lực hoặc hết lượt sử dụng.');

                    return false;
                }
            }

            $txn = $transactionId ?? 'TXN-'.strtoupper(Str::random(10));

            $lockedOrder->update([
                'status' => 'paid',
                'transaction_id' => $txn,
            ]);

            $payment?->update([
                'status' => 'success',
                'transaction_id' => $txn,
                'paid_at' => now(),
                'gateway_response' => [
                    'message' => 'Thanh toán giả lập thành công.',
                    'simulated_at' => now()->toDateTimeString(),
                    'gateway' => $lockedOrder->payment_method,
                ],
            ]);

            $this->enrollStudent($lockedOrder);
            $this->clearCart($lockedOrder);
            $coupon?->increment('used_count');

            return true;
        });
    }

    protected function markPaymentFailed(Order $order, ?Payment $payment, string $message): void
    {
        $order->update(['status' => 'failed']);

        $payment?->update([
            'status' => 'failed',
            'gateway_response' => [
                'message' => $message,
                'simulated_at' => now()->toDateTimeString(),
                'gateway' => $order->payment_method,
            ],
        ]);
    }

    /**
     * Đăng ký ghi danh cho học viên khi thanh toán thành công.
     */
    protected function enrollStudent(Order $order): void
    {
        // Lấy chi tiết các mục trong đơn hàng từ quan hệ Eloquent
        $items = $order->items()->with('course')->get();

        foreach ($items as $item) {
            // Tìm hoặc tạo mới bản ghi ghi danh (status: active)
            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $order->user_id,
                    'course_id' => $item->course_id,
                ],
                [
                    'order_id' => $order->id,
                    'status' => 'active',
                    'progress_percent' => 0,
                    'enrolled_at' => now(),
                ]
            );

            // Tăng số lượng học viên đăng ký của khóa học nếu đây là lượt ghi danh mới
            if ($enrollment->wasRecentlyCreated) {
                $item->course?->increment('enrollment_count');
            }
        }
    }

    /**
     * Xóa các khóa học đã mua khỏi giỏ hàng của người dùng.
     */
    protected function clearCart(Order $order): void
    {
        $cart = Cart::where('user_id', $order->user_id)->first();
        if ($cart) {
            // Lấy danh sách ID khóa học trong đơn hàng để loại bỏ khỏi giỏ hàng từ quan hệ Eloquent
            $courseIds = $order->items()->pluck('course_id')->toArray();
            $cart->courses()->detach($courseIds);
        }
    }
}
