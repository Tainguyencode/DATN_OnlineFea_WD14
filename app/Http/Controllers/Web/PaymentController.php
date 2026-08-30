<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentGatewayService;
use App\Services\MomoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentService
    ) {}

    /** Receive PayOS' server-to-server payment webhook. */
    public function payosIpn(Request $request): JsonResponse
    {
        $checksumKey = (string) config('services.payos.checksum_key');
        $data = $request->input('data');
        $signature = (string) $request->input('signature');

        if ($checksumKey === '' || ! is_array($data) || $signature === '') {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 400);
        }

        if (! hash_equals($this->signPayOSData($data, $checksumKey), $signature)) {
            Log::warning('Rejected PayOS webhook with invalid signature');

            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        $gatewayOrderCode = (string) ($data['orderCode'] ?? '');
        $payment = Payment::query()
            ->where('gateway', 'bank_transfer')
            ->where('gateway_order_code', $gatewayOrderCode)
            ->with('order')
            ->first();

        if (! $payment || ! $payment->order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $expectedAmount = (int) round((float) $payment->order->total_amount);
        $receivedAmount = (int) ($data['amount'] ?? -1);
        if ($receivedAmount !== $expectedAmount) {
            Log::warning('Rejected PayOS webhook with mismatched amount', [
                'order_id' => $payment->order_id,
                'expected_amount' => $expectedAmount,
                'received_amount' => $receivedAmount,
            ]);

            return response()->json(['success' => false, 'message' => 'Invalid amount'], 400);
        }

        $code = (string) ($data['code'] ?? $request->input('code', ''));
        if ($code !== '00') {
            return response()->json(['success' => true]);
        }

        $reference = (string) ($data['reference'] ?? 'PAYOS-'.$gatewayOrderCode);
        $this->paymentService->completePayOSPayment($payment->order, $reference, $data);

        return response()->json(['success' => true]);
    }

    public function momoReturn(Request $request, MomoService $momoService)
    {
        $data = $request->query();
        $verifiedQuery = false;
        $localOrderCode = (string) $request->query('local_order', '');

        if (! $momoService->verifyResult($data) && $localOrderCode !== '') {
            $payment = Payment::query()->where('gateway', 'momo')
                ->whereHas('order', fn ($query) => $query->where('order_code', $localOrderCode))
                ->with('order')->first();

            if ($payment && $payment->order && auth()->check() && auth()->id() === $payment->order->user_id) {
                $queried = $momoService->query($payment);
                if (is_array($queried)) {
                    $data = $queried;
                    $verifiedQuery = true;
                }
            }
        }

        return $this->handleMomoResult($data, $momoService, false, $verifiedQuery);
    }

    public function momoIpn(Request $request, MomoService $momoService): JsonResponse
    {
        // MoMo normally sends JSON, while some Sandbox samples use form-urlencoded.
        // Request::all() safely supports both formats.
        $result = $this->handleMomoResult($request->all(), $momoService, true);
        return $result instanceof JsonResponse ? $result : response()->json(['success' => true]);
    }

    private function handleMomoResult(array $data, MomoService $momoService, bool $ipn, bool $verifiedQuery = false)
    {
        $orderId = (string) ($data['orderId'] ?? '');
        $payment = Payment::query()->where('gateway', 'momo')->where('gateway_order_code', $orderId)->with('order')->first();
        if (! $payment || ! $payment->order) return $ipn ? response()->json(['success' => false], 404) : abort(404);
        $order = $payment->order;
        if (! $ipn && (! auth()->check() || auth()->id() !== $order->user_id)) {
            abort(403);
        }
        if ((! $verifiedQuery && ! $momoService->verifyResult($data))
            || (int) ($data['amount'] ?? -1) !== (int) round((float) $order->total_amount)) {
            Log::warning('Rejected MoMo result', ['order_id' => $order->id, 'order_ref' => $orderId]);
            return $ipn ? response()->json(['success' => false], 400) : view('student.cart.momo_result', ['success' => false, 'order' => $order, 'payment' => $payment, 'message' => 'Phản hồi MoMo không hợp lệ.']);
        }
        $success = (int) ($data['resultCode'] ?? -1) === 0 && filled($data['transId'] ?? null)
            && $this->paymentService->completeMomoPayment($order, (string) $data['transId'], $momoService->sanitize($data));
        if (! $success && $payment->status !== 'success') $this->paymentService->failMomoPayment($order, $momoService->sanitize($data));
        if ($ipn) return response()->json(['success' => true]);
        $payment->refresh(); $order->refresh();
        return view('student.cart.momo_result', compact('success', 'order', 'payment') + ['message' => $success ? 'Thanh toán thành công.' : ((string) ($data['message'] ?? 'Thanh toán không thành công.'))]);
    }

    private function signPayOSData(array $data, string $checksumKey): string
    {
        ksort($data);

        $payload = collect($data)->map(function ($value, $key): string {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif ($value === null) {
                $value = '';
            }

            return $key.'='.$value;
        })->implode('&');

        return hash_hmac('sha256', $payload, $checksumKey);
    }
}
