<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class MomoService
{
    public function createPaymentUrl(Order $order): string
    {
        $this->ensureConfigured();
        if ($order->status !== 'pending') throw new RuntimeException('Đơn hàng không ở trạng thái chờ thanh toán.');
        $amount = (int) round((float) $order->total_amount);
        if ($amount <= 0) throw new RuntimeException('Số tiền thanh toán MoMo phải lớn hơn 0.');

        $payment = $order->prepareGatewayPayment('momo', $this->newOrderId($order));
        $order->refresh();
        $amount = (int) round((float) $payment->amount);

        if (is_string(data_get($payment->gateway_response, 'payUrl'))) return $payment->gateway_response['payUrl'];

        $params = [
            'partnerCode' => (string) config('services.momo.partner_code'),
            'partnerName' => (string) config('app.name'), 'storeId' => (string) config('app.name'),
            'requestId' => $payment->gateway_order_code, 'amount' => $amount,
            'orderId' => $payment->gateway_order_code, 'orderInfo' => 'Thanh toán đơn hàng '.$order->order_code,
            'redirectUrl' => route('payments.momo.return', ['local_order' => $order->order_code]),
            'ipnUrl' => route('payments.momo.ipn'),
            'lang' => 'vi', 'extraData' => '', 'requestType' => 'payWithMethod', 'autoCapture' => true,
        ];
        $params['signature'] = $this->signCreate($params);

        try {
            $response = Http::timeout(20)->acceptJson()->post($this->endpoint('/v2/gateway/api/create'), $params);
            $data = $response->json();
        } catch (\Throwable $e) {
            Log::error('MoMo create request failed', ['order_id' => $order->id, 'exception_class' => $e::class]);
            throw new RuntimeException('Không thể kết nối đến MoMo.');
        }
        if (! $response->successful() || ! is_array($data) || (int) ($data['resultCode'] ?? -1) !== 0 || ! str_starts_with((string) ($data['payUrl'] ?? ''), 'https://')) {
            Log::warning('MoMo rejected payment request', ['order_id' => $order->id, 'result_code' => $data['resultCode'] ?? null]);
            throw new RuntimeException('MoMo từ chối yêu cầu thanh toán: '.((string) ($data['message'] ?? 'Lỗi không xác định')));
        }
        $payment->update(['gateway_response' => $this->sanitize($data)]);
        return (string) $data['payUrl'];
    }

    public function verifyResult(array $data): bool
    {
        $received = (string) ($data['signature'] ?? '');
        if ($received === '') return false;
        $fields = ['accessKey' => config('services.momo.access_key')];
        foreach (['amount','extraData','message','orderId','orderInfo','orderType','partnerCode','payType','requestId','responseTime','resultCode','transId'] as $key) {
            $fields[$key] = $data[$key] ?? '';
        }
        return hash_equals(hash_hmac('sha256', $this->raw($fields), (string) config('services.momo.secret_key')), $received);
    }

    public function query(Payment $payment): ?array
    {
        $params = ['partnerCode' => config('services.momo.partner_code'), 'requestId' => (string) Str::uuid(), 'orderId' => $payment->gateway_order_code, 'lang' => 'vi'];
        $params['signature'] = hash_hmac('sha256', $this->raw([
            'accessKey' => config('services.momo.access_key'), 'orderId' => $params['orderId'],
            'partnerCode' => $params['partnerCode'], 'requestId' => $params['requestId'],
        ]), (string) config('services.momo.secret_key'));
        try {
            $response = Http::timeout(30)->acceptJson()->post($this->endpoint('/v2/gateway/api/query'), $params);
            $data = $response->json();
            if (! $response->successful() || ! is_array($data)
                || (string) ($data['partnerCode'] ?? '') !== (string) $params['partnerCode']
                || (string) ($data['requestId'] ?? '') !== (string) $params['requestId']
                || (string) ($data['orderId'] ?? '') !== (string) $params['orderId']) {
                Log::warning('Rejected mismatched MoMo query response', ['payment_id' => $payment->id]);

                return null;
            }

            return $data;
        } catch (\Throwable $e) {
            Log::warning('MoMo query failed', ['payment_id' => $payment->id, 'exception_class' => $e::class]);
            return null;
        }
    }

    public function sanitize(array $data): array { unset($data['signature']); return array_filter($data, fn ($v) => is_scalar($v) || $v === null); }

    private function signCreate(array $p): string
    {
        return hash_hmac('sha256', $this->raw([
            'accessKey' => config('services.momo.access_key'), 'amount' => $p['amount'], 'extraData' => $p['extraData'],
            'ipnUrl' => $p['ipnUrl'], 'orderId' => $p['orderId'], 'orderInfo' => $p['orderInfo'],
            'partnerCode' => $p['partnerCode'], 'redirectUrl' => $p['redirectUrl'], 'requestId' => $p['requestId'], 'requestType' => $p['requestType'],
        ]), (string) config('services.momo.secret_key'));
    }
    private function raw(array $fields): string { return collect($fields)->map(fn ($v, $k) => $k.'='.$v)->implode('&'); }
    private function endpoint(string $path): string
    {
        $base = preg_replace('#/v2/gateway/api/(create|query)/?$#', '', rtrim((string) config('services.momo.endpoint'), '/'));

        return $base.$path;
    }
    private function newOrderId(Order $order): string { return 'MOMO'.$order->id.now()->format('ymdHis').strtoupper(Str::random(4)); }
    private function ensureConfigured(): void
    {
        foreach (['partner_code','access_key','secret_key','endpoint'] as $key) if (blank(config('services.momo.'.$key))) throw new RuntimeException('Chưa cấu hình đầy đủ MoMo.');
    }
}
