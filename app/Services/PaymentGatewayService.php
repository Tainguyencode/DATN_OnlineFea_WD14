<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Cart;
use Illuminate\Support\Str;

/**
 * Service quản lý các cổng thanh toán (VNPay, MoMo, Chuyển khoản)
 * Được cấu trúc sẵn sàng để thay thế bằng API thật trong tương lai.
 */
class PaymentGatewayService
{
    /**
     * Lấy URL thanh toán tương ứng cho đơn hàng.
     * 
     * @param Order $order
     * @return string
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
            'gateway' => $order->payment_method
        ]);
    }

    /**
     * Xử lý kết quả giao dịch (Thành công / Thất bại).
     * Áp dụng cho cả chuyển khoản giả lập lẫn callback từ các cổng VNPay, MoMo.
     * 
     * @param Order $order
     * @param string $status ('success' hoặc 'failed')
     * @param string|null $transactionId Mã giao dịch thực tế hoặc giả lập
     * @return bool
     */
    public function processMockPayment(Order $order, string $status, string $transactionId = null): bool
    {
        // Nếu đơn hàng đã thanh toán trước đó, không xử lý lại để tránh trùng lặp ghi danh
        if ($order->status === 'paid') {
            return true;
        }

        if ($status === 'success') {
            $txn = $transactionId ?? 'TXN-' . strtoupper(Str::random(10));

            // 1. Cập nhật trạng thái đơn hàng sang đã thanh toán (paid)
            $order->update([
                'status' => 'paid',
                'transaction_id' => $txn,
            ]);

            // 2. Cập nhật trạng thái bản ghi Payment tương ứng
            $payment = $order->payment;
            if ($payment) {
                $payment->update([
                    'status' => 'success',
                    'transaction_id' => $txn,
                    'paid_at' => now(),
                    'gateway_response' => [
                        'message' => 'Thanh toán giả lập thành công.',
                        'simulated_at' => now()->toDateTimeString(),
                        'gateway' => $order->payment_method,
                    ]
                ]);
            }

            // 3. Tự động ghi danh học viên vào các khóa học có trong đơn hàng
            $this->enrollStudent($order);

            // 4. Xóa các khóa học đã thanh toán thành công khỏi giỏ hàng
            $this->clearCart($order);

            // 5. Tăng lượt sử dụng mã giảm giá nếu có
            if ($order->coupon) {
                $order->coupon->increment('used_count');
            }

            return true;
        } else {
            // Trường hợp thanh toán thất bại hoặc người dùng hủy giao dịch
            $order->update(['status' => 'failed']);

            $payment = $order->payment;
            if ($payment) {
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => [
                        'message' => 'Giao dịch bị hủy hoặc thất bại.',
                        'simulated_at' => now()->toDateTimeString(),
                        'gateway' => $order->payment_method,
                    ]
                ]);
            }

            return false;
        }
    }

    /**
     * Đăng ký ghi danh cho học viên khi thanh toán thành công.
     * 
     * @param Order $order
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
     * 
     * @param Order $order
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
