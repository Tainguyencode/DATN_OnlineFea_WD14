<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $mode = env('PAYMENT_MODE', 'mock');

        if ($mode === 'mock') {
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

        try {
            return match ($order->payment_method) {
                'bank_transfer' => $this->createPayOSUrl($order),
                'vnpay' => $this->createVNPayUrl($order),
                'momo' => $this->createMoMoUrl($order),
                default => throw new \Exception('Cổng thanh toán không được hỗ trợ.'),
            };
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo link thanh toán thật: ' . $e->getMessage());
            // Fallback sang mock để trải nghiệm người dùng không bị gián đoạn khi dev
            if ($mode === 'sandbox') {
                session()->flash('warning', 'Không kết nối được cổng thanh toán thật, chuyển hướng sang cổng giả lập: ' . $e->getMessage());
                if ($order->payment_method === 'bank_transfer') {
                    return route('student.checkout.pay', $order->order_code);
                }
                return route('student.checkout.mock_gateway', [
                    'order_code' => $order->order_code,
                    'gateway' => $order->payment_method,
                ]);
            }
            throw $e;
        }
    }

    /**
     * Sinh link thanh toán PayOS (VietQR)
     */
    protected function createPayOSUrl(Order $order): string
    {
        $clientId = env('PAYOS_CLIENT_ID');
        $apiKey = env('PAYOS_API_KEY');
        $checksumKey = env('PAYOS_CHECKSUM_KEY');

        if (empty($clientId) || empty($apiKey) || empty($checksumKey)) {
            throw new \Exception('Chưa cấu hình tài khoản PayOS trong file .env');
        }

        // Tạo orderCode dạng số nguyên duy nhất cho PayOS
        $orderCode = (int) (time() % 10000000 . $order->id);
        $amount = (int) $order->total_amount;
        $description = 'Nap tien don hang ' . $order->order_code;
        // Loại bỏ ký tự đặc biệt theo yêu cầu mô tả của PayOS (chỉ chữ thường, chữ hoa, số và dấu cách)
        $description = preg_replace('/[^a-zA-Z0-9 ]/', '', $description);
        $description = substr($description, 0, 25); // Giới hạn 25 ký tự

        // Lưu orderCode duy nhất vào bản ghi Payment để tra cứu API khi redirect về
        if ($order->payment) {
            $order->payment->update(['transaction_id' => (string) $orderCode]);
        }

        $cancelUrl = route('student.checkout.failed', $order->order_code);
        $returnUrl = route('student.checkout.success', $order->order_code);

        $params = [
            'orderCode' => $orderCode,
            'amount' => $amount,
            'description' => $description,
            'cancelUrl' => $cancelUrl,
            'returnUrl' => $returnUrl,
        ];

        // Sắp xếp các tham số theo bảng chữ cái để tạo chữ ký
        ksort($params);
        $stringToSign = collect($params)->map(fn($v, $k) => "{$k}={$v}")->implode('&');
        $signature = hash_hmac('sha256', $stringToSign, $checksumKey);
        $params['signature'] = $signature;

        $response = Http::withoutVerifying()->withHeaders([
            'x-client-id' => $clientId,
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api-merchant.payos.vn/v2/payment-requests', $params);

        if ($response->failed()) {
            throw new \Exception('Lỗi từ PayOS API: ' . ($response->json('desc') ?? $response->body()));
        }

        $checkoutUrl = $response->json('data.checkoutUrl');
        if (empty($checkoutUrl)) {
            throw new \Exception('Không nhận được checkoutUrl từ PayOS');
        }

        return $checkoutUrl;
    }

    /**
     * Kiểm tra trạng thái thanh toán trực tiếp từ PayOS API hoặc URL callback nếu đơn hàng chưa xác nhận paid.
     */
    public function checkAndUpdatePayOSStatus(Order $order): bool
    {
        if ($order->status === 'paid') {
            return true;
        }

        if ($order->payment_method !== 'bank_transfer') {
            return false;
        }

        // 1. Nhận diện kết quả thành công trực tiếp từ query params của PayOS redirect URL
        $returnCode = request('code');
        $returnStatus = request('status');
        $returnOrderCode = request('orderCode');

        if (($returnCode === '00' || $returnStatus === 'PAID') && ($returnOrderCode || request('id'))) {
            $ref = request('id') ?? ('PAYOS-' . ($returnOrderCode ?? $order->id));
            $this->processMockPayment($order, 'success', $ref);
            return true;
        }

        // 2. Tra cứu trực tiếp PayOS API
        $clientId = env('PAYOS_CLIENT_ID');
        $apiKey = env('PAYOS_API_KEY');

        if (empty($clientId) || empty($apiKey)) {
            return false;
        }

        $payOSOrderCode = $returnOrderCode 
            ?? $order->payment?->transaction_id 
            ?? $order->transaction_id 
            ?? $order->id;

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'x-client-id' => $clientId,
                'x-api-key' => $apiKey,
            ])->get("https://api-merchant.payos.vn/v2/payment-requests/{$payOSOrderCode}");

            if ($response->successful()) {
                $status = $response->json('data.status');
                if ($status === 'PAID') {
                    $transactions = $response->json('data.transactions', []);
                    $ref = $transactions[0]['reference'] ?? ('PAYOS-' . $payOSOrderCode);
                    $this->processMockPayment($order, 'success', $ref);
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error('Lỗi kiểm tra trạng thái PayOS API: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Sinh link thanh toán VNPay
     */
    protected function createVNPayUrl(Order $order): string
    {
        $vnpTmnCode = env('VNP_TMN_CODE');
        $vnpHashSecret = env('VNP_HASH_SECRET');
        $vnpUrl = env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

        if (empty($vnpTmnCode) || empty($vnpHashSecret)) {
            throw new \Exception('Chưa cấu hình tài khoản VNPay trong file .env');
        }

        $vnp_Params = [
            "vnp_Version" => "2.1.0",
            "vnp_Command" => "pay",
            "vnp_TmnCode" => $vnpTmnCode,
            "vnp_Amount" => $order->total_amount * 100, // VNPay nhân với 100
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip() ?? '127.0.0.1',
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan khoa hoc " . $order->order_code,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => route('payments.vnpay.callback'),
            "vnp_TxnRef" => $order->order_code,
        ];

        ksort($vnp_Params);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($vnp_Params as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnpUrl . "?" . rtrim($query, '&');
        if (isset($vnpHashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnpHashSecret);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Sinh link thanh toán MoMo
     */
    protected function createMoMoUrl(Order $order): string
    {
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');
        $momoUrl = env('MOMO_URL', 'https://test-payment.momo.vn/v2/gateway/api/create');

        if (empty($partnerCode) || empty($accessKey) || empty($secretKey)) {
            throw new \Exception('Chưa cấu hình tài khoản MoMo trong file .env');
        }

        $requestId = (string) Str::uuid();
        $amount = (int) $order->total_amount;
        $orderId = $order->order_code;
        $orderInfo = 'Thanh toan don hang ' . $order->order_code;
        $redirectUrl = route('payments.momo.callback');
        $ipnUrl = route('payments.momo.ipn');
        $extraData = '';
        $requestType = 'captureWallet';

        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . $redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $params = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'OnlineFEA',
            'storeId' => 'OnlineFEA',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
            'lang' => 'vi',
        ];

        $response = Http::withoutVerifying()->timeout(10)->post($momoUrl, $params);

        if ($response->failed()) {
            throw new \Exception('Lỗi từ MoMo API: ' . ($response->json('message') ?? $response->body()));
        }

        $payUrl = $response->json('payUrl');
        if (empty($payUrl)) {
            throw new \Exception('Không nhận được payUrl từ MoMo: ' . ($response->json('localMessage') ?? $response->body()));
        }

        return $payUrl;
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
        // Lấy chi tiết các mục trong đơn hàng từ quan hệ Eloquent kèm thông tin khóa học và giảng viên
        $items = $order->items()->with(['course.instructor'])->get();
        $student = $order->user;

        foreach ($items as $item) {
            $course = $item->course;

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
                $course?->increment('enrollment_count');

                // Gửi thông báo cho giảng viên tạo khóa học
                if ($course && $course->instructor) {
                    $studentName = $student?->name ?? 'Một học viên';
                    app(NotificationService::class)->send(
                        $course->instructor,
                        'Học viên mới mua khóa học',
                        "Học viên {$studentName} đã đăng ký mua khóa học \"{$course->title}\".",
                        'new_enrollment',
                        route('instructor.courses.students', $course)
                    );
                }
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
