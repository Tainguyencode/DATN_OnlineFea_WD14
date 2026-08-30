<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentGatewayService;
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
