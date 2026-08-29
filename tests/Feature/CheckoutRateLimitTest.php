<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutRateLimitTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.payos.mode', 'live');
        Config::set('services.payos.checksum_key', 'test-checksum-key');
        Config::set('services.payos.client_id', 'test-client');
        Config::set('services.payos.api_key', 'test-api-key');
    }

    public function test_first_checkout_request_passes_and_repeated_requests_are_limited(): void
    {
        [$order, , $user] = $this->pendingOrder();
        Http::fake($this->successfulPayOSResponse());

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->payOrder($order, $user)->assertStatus(302);
        }

        $this->payOrder($order, $user)
            ->assertStatus(429)
            ->assertSee('Thao tác quá nhiều lần')
            ->assertSee('Quay lại')
            ->assertSee('Trang chủ')
            ->assertDontSee('Thử lại Quên mật khẩu')
            ->assertDontSee('Về Đăng nhập');
    }

    public function test_rate_limited_checkout_does_not_call_payos(): void
    {
        [$order, , $user] = $this->pendingOrder();
        Http::fake($this->successfulPayOSResponse());

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->payOrder($order, $user)->assertStatus(302);
        }

        $this->payOrder($order, $user)->assertStatus(429);

        Http::assertSentCount(10);
    }

    public function test_different_users_have_independent_checkout_limits(): void
    {
        [$firstOrder, , $firstUser] = $this->pendingOrder();
        [$secondOrder, , $secondUser] = $this->pendingOrder();
        Http::fake($this->successfulPayOSResponse());

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->payOrder($firstOrder, $firstUser)->assertStatus(302);
        }

        $this->payOrder($secondOrder, $secondUser)->assertStatus(302);
        Http::assertSentCount(11);
    }

    public function test_different_orders_have_independent_limits_for_the_same_user(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        [$firstOrder] = $this->pendingOrder($user);
        [$secondOrder] = $this->pendingOrder($user);
        Http::fake($this->successfulPayOSResponse());

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->payOrder($firstOrder, $user)->assertStatus(302);
        }

        $this->payOrder($secondOrder, $user)->assertStatus(302);
        Http::assertSentCount(11);
    }

    private function pendingOrder(?User $user = null): array
    {
        $user ??= User::factory()->create(['role' => 'student']);
        $order = Order::create([
            'order_code' => 'ORD-RATE-'.strtoupper(fake()->bothify('????####')),
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
            'amount' => 100000,
            'status' => 'pending',
        ]);

        return [$order, $payment, $user];
    }

    private function payOrder(Order $order, User $user)
    {
        return $this->actingAs($user)->post(
            route('student.checkout.process_payment', $order->order_code),
            ['payment_method' => 'payos']
        );
    }

    private function successfulPayOSResponse(): array
    {
        return [
            'api-merchant.payos.vn/*' => Http::response([
                'code' => '00',
                'desc' => 'success',
                'data' => ['checkoutUrl' => 'https://pay.payos.vn/web/test-payment-link'],
            ], 200),
        ];
    }
}
