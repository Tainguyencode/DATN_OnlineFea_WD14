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

    /**
     * Callback VNPay - Xử lý khi Học viên thanh toán xong quay về Website.
     * 
     * @param Request $request Dữ liệu vnp_* trả về
     * @return RedirectResponse
     */
    public function vnpayReturn(Request $request): RedirectResponse
    {
        Log::info('VNPay Return received', $request->all());

        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret ?? '');
        $orderCode = $request->input('vnp_TxnRef');
        $order = Order::where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Đơn hàng không tồn tại.');
        }

        if (!empty($vnp_HashSecret) && $secureHash === $vnp_SecureHash) {
            if ($request->input('vnp_ResponseCode') == '00') {
                $this->paymentService->processMockPayment($order, 'success', $request->input('vnp_TransactionNo'));
                return redirect()->route('student.checkout.success', $orderCode)
                    ->with('success', 'Thanh toán VNPay thành công!');
            }
        }

        $this->paymentService->processMockPayment($order, 'failed');
        return redirect()->route('student.checkout.failed', $orderCode)
            ->with('error', 'Giao dịch VNPay không thành công hoặc chữ ký không hợp lệ.');
    }

    /**
     * IPN VNPay - Nhận thông báo giao dịch ngầm tự động từ Máy chủ VNPay (Server-to-Server).
     * 
     * @param Request $request
     * @return JsonResponse Phản hồi JSON chuẩn RspCode từ VNPay
     */
    public function vnpayIpn(Request $request): JsonResponse
    {
        Log::info('VNPay IPN received', $request->all());

        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret ?? '');
        $orderCode = $request->input('vnp_TxnRef');
        $order = Order::where('order_code', $orderCode)->first();

        if (empty($vnp_HashSecret) || $secureHash !== $vnp_SecureHash) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid Checksum']);
        }

        if (!$order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order Not Found']);
        }

        $vnp_Amount = $request->input('vnp_Amount') / 100;
        if ((int)$order->total_amount !== (int)$vnp_Amount) {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid Amount']);
        }

        if ($order->status !== 'pending') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if ($request->input('vnp_ResponseCode') == '00') {
            $this->paymentService->processMockPayment($order, 'success', $request->input('vnp_TransactionNo'));
        } else {
            $this->paymentService->processMockPayment($order, 'failed');
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }
}
