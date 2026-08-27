<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayOSPaymentSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.payos.checksum_key', 'test-checksum-key');
        Config::set('services.payos.client_id', 'test-client');
        Config::set('services.payos.api_key', 'test-api-key');
    }

    public function test_unsigned_payos_webhook_is_rejected(): void
    {
        [$order] = $this->pendingOrder();

        $this->postJson(route('payments.payos.ipn'), [
            'data' => ['orderCode' => $order->id, 'amount' => 100000, 'code' => '00'],
            'signature' => 'forged',
        ])->assertStatus(400);

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_payos_webhook_requires_exact_amount_and_completes_matching_order(): void
    {
        [$order, $payment] = $this->pendingOrder();
        $invalidData = ['orderCode' => (int) $payment->gateway_order_code, 'amount' => 1, 'code' => '00', 'reference' => 'PAYOS-REF-1'];

        $this->postJson(route('payments.payos.ipn'), [
            'data' => $invalidData,
            'signature' => $this->signature($invalidData),
        ])->assertStatus(400);
        $this->assertSame('pending', $order->fresh()->status);

        $validData = ['orderCode' => (int) $payment->gateway_order_code, 'amount' => 100000, 'code' => '00', 'reference' => 'PAYOS-REF-1'];
        $this->postJson(route('payments.payos.ipn'), [
            'data' => $validData,
            'signature' => $this->signature($validData),
        ])->assertOk();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame('PAYOS-REF-1', $order->fresh()->transaction_id);
    }

    public function test_browser_query_parameters_cannot_mark_order_paid(): void
    {
        [$order] = $this->pendingOrder();
        Http::fake([
            'api-merchant.payos.vn/*' => Http::response(['data' => ['status' => 'PENDING']], 200),
        ]);

        $result = app(PaymentGatewayService::class)->checkAndUpdatePayOSStatus($order);

        $this->assertFalse($result);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_live_mode_rejects_direct_mock_payment_tools(): void
    {
        Config::set('services.payos.mode', 'live');
        [$order, , $user] = $this->pendingOrder();

        $this->actingAs($user)
            ->get(route('student.checkout.mock_gateway', $order->order_code))
            ->assertNotFound();

        $this->actingAs($user)
            ->post(route('student.checkout.simulate', $order->order_code), ['status' => 'success'])
            ->assertNotFound();

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('pending', $order->payment->fresh()->status);
    }

    public function test_removed_payment_gateway_routes_are_not_registered(): void
    {
        $this->get('/payments/momo/callback')->assertNotFound();
        $this->post('/payments/momo/ipn')->assertNotFound();
        $this->get('/payments/vnpay/return')->assertNotFound();
        $this->post('/payments/vnpay/ipn')->assertNotFound();
    }

    private function pendingOrder(): array
    {
        $user = User::factory()->create(['role' => 'student']);
        $order = Order::create([
            'order_code' => 'ORD-PAYOS-'.strtoupper(fake()->bothify('????####')),
            'user_id' => $user->id,
            'subtotal' => 100000,
            'discount_amount' => 0,
            'total_amount' => 100000,
            'status' => 'pending',
            'payment_method' => 'payos',
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => 'bank_transfer',
            'gateway_order_code' => (string) $order->id,
            'amount' => 100000,
            'status' => 'pending',
        ]);

        return [$order, $payment, $user];
    }

    private function signature(array $data): string
    {
        ksort($data);
        $payload = collect($data)->map(fn ($value, $key) => $key.'='.$value)->implode('&');

        return hash_hmac('sha256', $payload, 'test-checksum-key');
    }
}
