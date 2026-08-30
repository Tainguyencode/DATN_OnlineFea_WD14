<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\MomoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MomoPaymentTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'momo-sandbox-secret';

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('services.momo', [
            'partner_code' => 'MOMO_TEST',
            'access_key' => 'ACCESS_TEST',
            'secret_key' => self::SECRET,
            'endpoint' => 'https://test-payment.momo.vn',
        ]);
    }

    public function test_create_payment_url_is_signed_and_reuses_pending_payment(): void
    {
        Http::fake(fn () => Http::response([
            'partnerCode' => 'MOMO_TEST', 'requestId' => 'request', 'orderId' => 'order',
            'resultCode' => 0, 'message' => 'Successful.', 'payUrl' => 'https://test-payment.momo.vn/pay/test',
        ]));

        $order = $this->createOrder();
        $first = app(MomoService::class)->createPaymentUrl($order);
        $second = app(MomoService::class)->createPaymentUrl($order);

        $this->assertSame('https://test-payment.momo.vn/pay/test', $first);
        $this->assertSame($first, $second);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'gateway' => 'momo', 'status' => 'pending']);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->url() === 'https://test-payment.momo.vn/v2/gateway/api/create'
            && $request['amount'] === 125000 && filled($request['signature']));
    }

    public function test_valid_ipn_marks_order_paid_and_duplicate_is_idempotent(): void
    {
        [$order, $payment] = $this->createPayment();
        $payload = $this->payload($payment);

        $this->postJson(route('payments.momo.ipn'), $payload)->assertOk()->assertJson(['success' => true]);
        $this->postJson(route('payments.momo.ipn'), $payload)->assertOk()->assertJson(['success' => true]);

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame('success', $payment->fresh()->status);
        $this->assertSame('987654321', $payment->fresh()->transaction_id);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_invalid_signature_or_amount_cannot_mark_order_paid(): void
    {
        [$order, $payment] = $this->createPayment();
        $payload = $this->payload($payment);
        $payload['signature'] = 'invalid';
        $this->postJson(route('payments.momo.ipn'), $payload)->assertBadRequest();

        $payload = $this->payload($payment);
        $payload['amount'] = 1;
        $payload['signature'] = $this->sign($payload);
        $this->postJson(route('payments.momo.ipn'), $payload)->assertBadRequest();

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('pending', $payment->fresh()->status);
    }

    public function test_local_status_query_can_complete_without_callback_signature(): void
    {
        [$order, $payment] = $this->createPayment();
        Http::fake(function ($request) use ($payment) {
            return Http::response([
                'partnerCode' => 'MOMO_TEST',
                'requestId' => $request['requestId'],
                'orderId' => $payment->gateway_order_code,
                'amount' => 125000,
                'transId' => 987654322,
                'payType' => 'qr',
                'resultCode' => 0,
                'message' => 'Successful.',
                'responseTime' => 1788082200000,
                'extraData' => '',
            ]);
        });

        $this->actingAs($order->user)
            ->getJson(route('student.checkout.status', $order->order_code))
            ->assertOk()->assertJson(['status' => 'paid']);

        $this->assertSame('success', $payment->fresh()->status);
        $this->assertSame('987654322', $payment->fresh()->transaction_id);
    }

    private function createOrder(): Order
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true, 'email_verified_at' => now()]);

        return Order::create([
            'order_code' => 'ORD-MOMO-'.strtoupper(fake()->bothify('####??')),
            'user_id' => $student->id, 'subtotal' => 125000, 'discount_amount' => 0,
            'total_amount' => 125000, 'status' => 'pending', 'payment_method' => 'momo',
        ]);
    }

    private function createPayment(): array
    {
        $order = $this->createOrder();
        $payment = Payment::create([
            'order_id' => $order->id, 'gateway' => 'momo',
            'gateway_order_code' => 'MOMO'.$order->id.'TEST', 'amount' => 125000, 'status' => 'pending',
        ]);

        return [$order, $payment];
    }

    private function payload(Payment $payment): array
    {
        $payload = [
            'amount' => 125000, 'extraData' => '', 'message' => 'Successful.',
            'orderId' => $payment->gateway_order_code, 'orderInfo' => 'Test order',
            'orderType' => 'momo_wallet', 'partnerCode' => 'MOMO_TEST', 'payType' => 'qr',
            'requestId' => $payment->gateway_order_code, 'responseTime' => 1788082200000,
            'resultCode' => 0, 'transId' => 987654321,
        ];
        $payload['signature'] = $this->sign($payload);

        return $payload;
    }

    private function sign(array $payload): string
    {
        $fields = ['accessKey' => 'ACCESS_TEST'];
        foreach (['amount', 'extraData', 'message', 'orderId', 'orderInfo', 'orderType', 'partnerCode', 'payType', 'requestId', 'responseTime', 'resultCode', 'transId'] as $key) {
            $fields[$key] = $payload[$key] ?? '';
        }
        $raw = collect($fields)->map(fn ($value, $key) => $key.'='.$value)->implode('&');

        return hash_hmac('sha256', $raw, self::SECRET);
    }
}
