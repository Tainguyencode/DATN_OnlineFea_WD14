<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller Xử lý Phản hồi Thanh toán (VNPay, MoMo, PayOS VietQR)
 * 
 * Chức năng chính:
 * 1. Tiếp nhận Callback từ Cổng thanh toán khi Học viên hoàn tất thanh toán và chuyển hướng về Website.
 * 2. Tiếp nhận IPN (Instant Payment Notification) / Webhook trực tiếp từ Server của VNPay, MoMo, PayOS.
 * 3. Xác thực Chữ ký số (HMAC SHA512 / SHA256) bảo vệ tính toàn vẹn dữ liệu giao dịch.
 * 4. Kích hoạt tự động Đơn hàng, mở khóa học cho học viên và tính hoa hồng cho giảng viên.
 */
class PaymentController extends Controller
{
    /**
     * Service xử lý nghiệp vụ đơn hàng & thanh toán
     */
    protected PaymentGatewayService $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Callback VNPay - Xử lý khi Học viên thực hiện giao dịch xong và được VNPay chuyển hướng quay về Website.
     * 
     * Quy trình:
     * 1. Nhận các thông số `vnp_*` và chữ ký `vnp_SecureHash`.
     * 2. Sắp xếp tham số & Tạo chuỗi băm HMAC-SHA512 với `VNP_HASH_SECRET`.
     * 3. So sánh chữ ký. Nếu hợp lệ và `vnp_ResponseCode == '00'` -> Xác nhận thành công & Mở khóa học.
     * 
     * @param Request $request Tham số do VNPay trả về qua URL query
     * @return RedirectResponse Đơn hàng thành công -> Chuyển đến trang thành công / Thất bại -> Trang thông báo lỗi
     */
    public function vnpayCallback(Request $request): RedirectResponse
    {
        Log::info('VNPay Callback received', $request->all());

        $vnpSecureHash = $request->input('vnp_SecureHash');
        $vnpHashSecret = env('VNP_HASH_SECRET');

        if (empty($vnpSecureHash) || empty($vnpHashSecret)) {
            return redirect()->route('dashboard')->with('error', 'Không tìm thấy thông tin xác thực giao dịch VNPay.');
        }

        // Lọc tất cả tham số bắt đầu bằng vnp_ ngoại trừ vnp_SecureHash & vnp_SecureHashType
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        // Tạo chuỗi mã hóa HMAC SHA512
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);

        // Đối chiếu chữ ký bảo mật
        if ($secureHash !== $vnpSecureHash) {
            return redirect()->route('dashboard')->with('error', 'Chữ ký giao dịch VNPay không hợp lệ.');
        }

        $orderCode = $request->input('vnp_TxnRef');
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // Mã '00' đại diện cho giao dịch thành công trên cổng VNPay
        if ($request->input('vnp_ResponseCode') == '00') {
            $this->paymentService->processMockPayment($order, 'success', $request->input('vnp_TransactionNo'));
            return redirect()->route('student.checkout.success', $orderCode)
                ->with('success', 'Thanh toán VNPay thành công!');
        }

        $this->paymentService->processMockPayment($order, 'failed');
        return redirect()->route('student.checkout.failed', $orderCode)
            ->with('error', 'Giao dịch VNPay thất bại hoặc đã bị hủy.');
    }

    /**
     * IPN VNPay - Nhận thông báo giao dịch ngầm (Server-to-Server) từ Cổng VNPay.
     * 
     * Đảm bảo cập nhật trạng thái đơn hàng ngay cả khi học viên tắt trình duyệt trước khi chuyển hướng về web.
     * 
     * @param Request $request Dữ liệu HTTP POST gửi trực tiếp từ máy chủ VNPay
     * @return JsonResponse Phản hồi JSON định dạng chuẩn VNPay ({RspCode, Message})
     */
    public function vnpayIpn(Request $request): JsonResponse
    {
        Log::info('VNPay IPN received', $request->all());

        $vnpSecureHash = $request->input('vnp_SecureHash');
        $vnpHashSecret = env('VNP_HASH_SECRET');

        if (empty($vnpSecureHash) || empty($vnpHashSecret)) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);

        if ($secureHash !== $vnpSecureHash) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $orderCode = $request->input('vnp_TxnRef');
        $order = Order::where('order_code', $orderCode)->first();

        if (!$order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        // Kiểm tra đối chiếu số tiền thanh toán (VNPay nhân 100 số tiền gốc)
        $vnpAmount = $request->input('vnp_Amount') / 100;
        if (abs($order->total_amount - $vnpAmount) > 0.01) {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }

        if ($order->status !== 'pending') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if ($request->input('vnp_ResponseCode') == '00') {
            $this->paymentService->processMockPayment($order, 'success', $request->input('vnp_TransactionNo'));
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        $this->paymentService->processMockPayment($order, 'failed');
        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success (Failed payment)']);
    }

    /**
     * Callback MoMo - Xử lý khi Học viên thanh toán bằng Ví MoMo và quay về Website.
     * 
     * @param Request $request Dữ liệu trả về gồm resultCode, orderId, transId
     * @return RedirectResponse Chuyển hướng thành công hoặc thông báo lỗi
     */
    public function momoCallback(Request $request): RedirectResponse
    {
        Log::info('MoMo Callback received', $request->all());

        $resultCode = $request->input('resultCode');
        $orderCode = $request->input('orderId');

        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // resultCode == 0 nghĩa là giao dịch MoMo hoàn thành thành công
        if ($resultCode == 0) {
            $this->paymentService->processMockPayment($order, 'success', $request->input('transId'));
            return redirect()->route('student.checkout.success', $orderCode)
                ->with('success', 'Thanh toán MoMo thành công!');
        }

        $this->paymentService->processMockPayment($order, 'failed');
        return redirect()->route('student.checkout.failed', $orderCode)
            ->with('error', 'Giao dịch MoMo bị hủy hoặc thất bại.');
    }

    /**
     * IPN MoMo - Nhận thông báo giao dịch ngầm tự động từ Ví điện tử MoMo (Server-to-Server).
     * 
     * Xử lý xác thực chữ ký HMAC-SHA256 theo quy chuẩn cổng MoMo API v2.
     * 
     * @param Request $request Payload POST từ máy chủ MoMo
     * @return JsonResponse HTTP 204 No Content nếu xử lý thành công
     */
    public function momoIpn(Request $request): JsonResponse
    {
        Log::info('MoMo IPN received', $request->all());

        $partnerCode = $request->input('partnerCode');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');

        if (empty($partnerCode) || empty($accessKey) || empty($secretKey)) {
            return response()->json(['message' => 'Config keys missing'], 400);
        }

        $amount = $request->input('amount');
        $extraData = $request->input('extraData') ?? '';
        $message = $request->input('message');
        $orderId = $request->input('orderId');
        $orderInfo = $request->input('orderInfo');
        $requestId = $request->input('requestId');
        $responseTime = $request->input('responseTime');
        $resultCode = $request->input('resultCode');
        $transId = $request->input('transId');

        // Tạo chuỗi thô đúng thứ tự bảng chữ cái theo yêu cầu tài liệu MoMo API
        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&message=" . $message .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&requestId=" . $requestId .
            "&responseTime=" . $responseTime .
            "&resultCode=" . $resultCode .
            "&transId=" . $transId;

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        if ($signature !== $request->input('signature')) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $order = Order::where('order_code', $orderId)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order already processed'], 200);
        }

        if ($resultCode == 0) {
            $this->paymentService->processMockPayment($order, 'success', $transId);
        } else {
            $this->paymentService->processMockPayment($order, 'failed');
        }

        return response()->json(null, 204);
    }

    /**
     * Webhook / IPN PayOS - Xử lý thanh toán tự động qua mã QR VietQR (Ngân hàng chuyển khoản).
     * 
     * Khi học viên quét mã VietQR và thực hiện chuyển khoản thành công, PayOS tự động đẩy Webhook về đây.
     * 
     * @param Request $request Chứa `data` đơn hàng và `signature` chữ ký băm
     * @return JsonResponse Phản hồi {success: true/false}
     */
    public function payosIpn(Request $request): JsonResponse
    {
        Log::info('PayOS IPN received', $request->all());

        $checksumKey = env('PAYOS_CHECKSUM_KEY');
        if (empty($checksumKey)) {
            return response()->json(['success' => false, 'message' => 'Checksum key missing'], 400);
        }

        $data = $request->input('data');
        $signature = $request->input('signature');

        if (empty($data) || empty($signature)) {
            return response()->json(['success' => false, 'message' => 'Payload missing'], 400);
        }

        // Sắp xếp các tham số của data theo bảng chữ cái để sinh chữ ký đối chiếu
        ksort($data);
        $stringToSign = collect($data)->map(fn($v, $k) => "{$k}={$v}")->implode('&');
        $secureHash = hash_hmac('sha256', $stringToSign, $checksumKey);

        if ($secureHash !== $signature) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        $orderId = $data['orderCode']; // ID của đơn hàng lưu trên CSDL
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        // Đối chiếu mã trạng thái thanh toán của PayOS ('00' là thành công)
        $code = $request->input('code');
        if ($code === '00') {
            $this->paymentService->processMockPayment($order, 'success', $data['reference']);
        } else {
            $this->paymentService->processMockPayment($order, 'failed');
        }

        return response()->json(['success' => true]);
    }
}
