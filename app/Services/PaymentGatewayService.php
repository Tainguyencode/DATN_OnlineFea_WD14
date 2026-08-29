<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\UserCoupon;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentGatewayService
{
    public function getPaymentUrl(Order $order): string
    {
        if (! in_array($order->payment_method, ['payos', 'bank_transfer'], true)) {
            throw new RuntimeException('Cổng thanh toán không được hỗ trợ.');
        }

        if (config('services.payos.mode') === 'mock') {
            return $order->payment_method === 'bank_transfer'
                ? route('student.checkout.pay', $order->order_code)
                : route('student.checkout.mock_gateway', $order->order_code);
        }

        return $this->createPayOSUrl($order);
    }

    protected function createPayOSUrl(Order $order): string
    {
        $clientId = (string) config('services.payos.client_id');
        $apiKey = (string) config('services.payos.api_key');
        $checksumKey = (string) config('services.payos.checksum_key');

        if ($clientId === '' || $apiKey === '' || $checksumKey === '') {
            throw new RuntimeException('Chưa cấu hình tài khoản PayOS.');
        }

        $gatewayOrderCode = $order->id;
        $amount = (int) round((float) $order->total_amount);
        if ($amount <= 0) {
            throw new RuntimeException('Số tiền thanh toán PayOS phải lớn hơn 0.');
        }

        $description = substr(preg_replace('/[^a-zA-Z0-9 ]/', '', 'Thanh toan '.$order->order_code), 0, 25);
        $params = [
            'amount' => $amount,
            'cancelUrl' => route('student.checkout.failed', $order->order_code),
            'description' => $description,
            'orderCode' => $gatewayOrderCode,
            'returnUrl' => route('student.checkout.success', $order->order_code),
        ];
        ksort($params);
        $params['signature'] = hash_hmac(
            'sha256',
            collect($params)->map(fn ($value, $key) => $key.'='.$value)->implode('&'),
            $checksumKey
        );

        $payment = $order->payment()->firstOrCreate(
            ['order_id' => $order->id],
            ['gateway' => 'bank_transfer', 'amount' => $order->total_amount, 'status' => 'pending']
        );
        $payment->update([
            'gateway' => 'bank_transfer',
            'gateway_order_code' => (string) $gatewayOrderCode,
            'amount' => $order->total_amount,
        ]);

        $response = $this->payOSHttpClient(15)
            ->withHeaders([
                'x-client-id' => $clientId,
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post('https://api-merchant.payos.vn/v2/payment-requests', $params);

        $checkoutUrl = $this->extractPayOSCheckoutUrl($response->json());

        // PayOS may return HTTP 200 without data when the orderCode already exists.
        // Reusing the existing payment link makes repeated clicks idempotent.
        if ($checkoutUrl === null) {
            $existingResponse = $this->payOSHttpClient(10)
                ->withHeaders([
                    'x-client-id' => $clientId,
                    'x-api-key' => $apiKey,
                ])
                ->get("https://api-merchant.payos.vn/v2/payment-requests/{$gatewayOrderCode}");

            $checkoutUrl = $this->extractPayOSCheckoutUrl($existingResponse->json());
        }

        // An old PayOS request can exist without a reusable checkout URL
        // (expired/cancelled links are a common case). Create a fresh request
        // with another numeric orderCode instead of trapping the user in a loop.
        if ($checkoutUrl === null && (string) $response->json('code') === '231') {
            $gatewayOrderCode = $this->newPayOSOrderCode();
            $params['orderCode'] = $gatewayOrderCode;
            unset($params['signature']);
            ksort($params);
            $params['signature'] = hash_hmac(
                'sha256',
                collect($params)->map(fn ($value, $key) => $key.'='.$value)->implode('&'),
                $checksumKey
            );

            $payment->update(['gateway_order_code' => (string) $gatewayOrderCode]);

            $response = $this->payOSHttpClient(15)
                ->withHeaders([
                    'x-client-id' => $clientId,
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api-merchant.payos.vn/v2/payment-requests', $params);

            $checkoutUrl = $this->extractPayOSCheckoutUrl($response->json());
        }

        if ($checkoutUrl === null) {
            $description = $response->json('desc') ?: 'Không thể tạo liên kết thanh toán.';

            Log::warning('PayOS payment link creation failed', [
                'order_id' => $order->id,
                'http_status' => $response->status(),
                'payos_code' => $response->json('code'),
                'payos_description' => $description,
            ]);

            throw new RuntimeException('PayOS từ chối yêu cầu: '.$description);
        }

        return $checkoutUrl;
    }

    private function extractPayOSCheckoutUrl(mixed $payload): ?string
    {
        if (! is_array($payload)) {
            return null;
        }

        $checkoutUrl = data_get($payload, 'data.checkoutUrl');

        return is_string($checkoutUrl) && str_starts_with($checkoutUrl, 'https://')
            ? $checkoutUrl
            : null;
    }

    private function newPayOSOrderCode(): int
    {
        // PayOS orderCode is transported as a JSON number. Keep it below
        // JavaScript's safe-integer limit (16 digits) to preserve the signature.
        return (int) now()->format('ymdHisv');
    }

    public function checkAndUpdatePayOSStatus(Order $order): bool
    {
        if ($order->status === 'paid') {
            return true;
        }

        if (! in_array($order->payment_method, ['payos', 'bank_transfer'], true)) {
            return false;
        }

        $payment = $order->payment;
        $gatewayOrderCode = $payment?->gateway_order_code;
        $clientId = (string) config('services.payos.client_id');
        $apiKey = (string) config('services.payos.api_key');
        if (! $gatewayOrderCode || $clientId === '' || $apiKey === '') {
            return false;
        }

        try {
            $response = $this->payOSHttpClient(10)
                ->withHeaders(['x-client-id' => $clientId, 'x-api-key' => $apiKey])
                ->get("https://api-merchant.payos.vn/v2/payment-requests/{$gatewayOrderCode}");

            if (! $response->successful() || $response->json('data.status') !== 'PAID') {
                return false;
            }

            $responseData = (array) $response->json('data', []);
            $responseSignature = (string) $response->json('signature', '');
            $checksumKey = (string) config('services.payos.checksum_key');
            if ($responseSignature !== '' && ! hash_equals($this->signPayOSData($responseData, $checksumKey), $responseSignature)) {
                Log::warning('PayOS status response has an invalid signature', compact('gatewayOrderCode'));

                return false;
            }

            $receivedAmount = (int) ($responseData['amountPaid'] ?? $responseData['amount'] ?? -1);
            $expectedAmount = (int) round((float) $order->total_amount);
            if ($receivedAmount !== $expectedAmount) {
                Log::warning('PayOS status amount mismatch', compact('gatewayOrderCode', 'receivedAmount', 'expectedAmount'));

                return false;
            }

            $transactions = $response->json('data.transactions', []);
            $reference = (string) ($transactions[0]['reference'] ?? 'PAYOS-'.$gatewayOrderCode);

            return $this->completePayOSPayment($order, $reference, $responseData);
        } catch (\Throwable $exception) {
            Log::error('Không thể đối soát PayOS: '.$exception->getMessage());

            return false;
        }
    }

    public function completePayOSPayment(Order $order, string $transactionId, array $gatewayResponse = []): bool
    {
        return $this->finalizePayment($order, $transactionId, $gatewayResponse, false);
    }

    public function completeFreeOrder(Order $order): bool
    {
        if ((float) $order->total_amount !== 0.0) {
            return false;
        }

        return $this->finalizePayment(
            $order,
            'FREE-'.$order->id,
            ['message' => 'Đơn hàng miễn phí hoặc được giảm giá 100%.'],
            false
        );
    }

    public function processMockPayment(Order $order, string $status, ?string $transactionId = null): bool
    {
        abort_unless(
            app()->environment(['local', 'testing']) && config('services.payos.mode') === 'mock',
            404
        );

        if ($status !== 'success') {
            return DB::transaction(function () use ($order): bool {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
                if ($lockedOrder->status === 'paid') {
                    return true;
                }
                $lockedOrder->update(['status' => 'failed']);
                $lockedOrder->payment()->update([
                    'status' => 'failed',
                    'gateway_response' => ['message' => 'Giao dịch giả lập thất bại.'],
                ]);

                return false;
            });
        }

        return $this->finalizePayment(
            $order,
            $transactionId ?? 'MOCK-'.strtoupper(Str::random(12)),
            ['message' => 'Thanh toán giả lập thành công.'],
            true
        );
    }

    private function finalizePayment(Order $order, string $transactionId, array $gatewayResponse, bool $mock): bool
    {
        return DB::transaction(function () use ($order, $transactionId, $gatewayResponse, $mock): bool {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status === 'paid') {
                return true;
            }
            if ($lockedOrder->status !== 'pending') {
                return false;
            }

            $payment = $lockedOrder->payment()->lockForUpdate()->first();
            if (! $payment || (int) round((float) $payment->amount) !== (int) round((float) $lockedOrder->total_amount)) {
                return false;
            }

            if (Payment::query()
                ->where('transaction_id', $transactionId)
                ->where('order_id', '!=', $lockedOrder->id)
                ->exists()) {
                Log::warning('Rejected reused payment transaction', ['transaction_id' => $transactionId]);

                return false;
            }

            $coupon = $this->lockAndValidateCoupon($lockedOrder);
            if ($lockedOrder->coupon_id && ! $coupon) {
                $lockedOrder->update(['status' => 'failed']);
                $payment->update(['status' => 'failed']);

                return false;
            }

            $lockedOrder->update(['status' => 'paid', 'transaction_id' => $transactionId]);
            $payment->update([
                'status' => 'success',
                'transaction_id' => $transactionId,
                'paid_at' => now(),
                'gateway_response' => $gatewayResponse + ['mock' => $mock],
            ]);

            $this->enrollStudent($lockedOrder);
            $this->clearCart($lockedOrder);
            $coupon?->increment('used_count');
            if ($coupon) {
                UserCoupon::query()
                    ->where('user_id', $lockedOrder->user_id)
                    ->where('coupon_id', $coupon->id)
                    ->whereNull('used_at')
                    ->update(['used_at' => now()]);
            }

            return true;
        });
    }

    private function lockAndValidateCoupon(Order $order): ?Coupon
    {
        if (! $order->coupon_id) {
            return null;
        }

        $coupon = Coupon::query()->lockForUpdate()->find($order->coupon_id);

        return $coupon && $coupon->isValid() && ! $coupon->isUsedByUser($order->user_id)
            ? $coupon
            : null;
    }

    private function signPayOSData(array $data, string $checksumKey): string
    {
        ksort($data);

        return hash_hmac('sha256', collect($data)->map(function ($value, $key): string {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif ($value === null) {
                $value = '';
            }

            return $key.'='.$value;
        })->implode('&'), $checksumKey);
    }

    private function payOSHttpClient(int $timeout)
    {
        $request = Http::timeout($timeout);
        $caBundle = config('services.payos.ca_bundle');

        return is_string($caBundle) && $caBundle !== ''
            ? $request->withOptions(['verify' => $caBundle])
            : $request;
    }

    protected function enrollStudent(Order $order): void
    {
        $student = $order->user;
        $items = $order->items()->with(['course.instructor'])->get();
        $existingEnrollments = Enrollment::query()
            ->where('user_id', $order->user_id)
            ->whereIn('course_id', $items->pluck('course_id'))
            ->get()
            ->keyBy('course_id');

        foreach ($items as $item) {
            $enrollment = $existingEnrollments->get($item->course_id)
                ?? new Enrollment(['user_id' => $order->user_id, 'course_id' => $item->course_id]);
            $isNewOrCancelled = ! $enrollment->exists || $enrollment->status === 'cancelled';

            if ($isNewOrCancelled) {
                $enrollment->fill([
                    'order_id' => $order->id,
                    'status' => Enrollment::STATUS_ACTIVE,
                    'progress_percent' => 0,
                    'completed_lessons' => 0,
                    'completed_at' => null,
                    'enrolled_at' => now(),
                ])->save();
                $item->course?->increment('enrollment_count');
            }

            if ($isNewOrCancelled && $item->course?->instructor) {
                app(NotificationService::class)->send(
                    $item->course->instructor,
                    'Học viên mới mua khóa học',
                    'Học viên '.($student?->name ?? 'Một học viên').' đã đăng ký mua khóa học "'.$item->course->title.'".',
                    'new_enrollment',
                    route('instructor.courses.students', $item->course)
                );
            }
        }
    }

    protected function clearCart(Order $order): void
    {
        $cart = Cart::where('user_id', $order->user_id)->first();
        $cart?->courses()->detach($order->items()->pluck('course_id')->all());
    }

    public function processRefund(Refund $refund, string $method, ?string $adminNote = null, ?string $manualReference = null): bool
    {
        if ($method === 'manual' && blank($manualReference)) {
            throw new RuntimeException('Hoàn tiền thủ công cần có mã giao dịch/đối soát.');
        }

        $prepared = DB::transaction(function () use ($refund, $method, $adminNote): Refund {
            $lockedRefund = Refund::query()->lockForUpdate()->findOrFail($refund->id);
            $order = Order::query()->lockForUpdate()->findOrFail($lockedRefund->order_id);

            if ($lockedRefund->status === 'approved') {
                return $lockedRefund;
            }
            if (! in_array($lockedRefund->status, ['pending', 'processing'], true) || $order->status !== 'paid') {
                throw new RuntimeException('Đơn hàng hoặc yêu cầu hoàn tiền không còn hợp lệ.');
            }

            $lockedRefund->update([
                'status' => 'processing',
                'refund_method' => $method,
                'admin_note' => $adminNote,
            ]);

            return $lockedRefund->fresh();
        });

        if ($prepared->status === 'approved') {
            return true;
        }

        $transactionReference = $method === 'manual' ? trim((string) $manualReference) : null;
        if ($method === 'payos_payout') {
            $withdrawal = new Withdrawal([
                'id' => $prepared->id,
                'user_id' => $prepared->user_id,
                'amount' => $prepared->amount,
                'bank_code' => $prepared->bank_code,
                'bank_account_number' => $prepared->bank_account_number,
                'bank_account_name' => $prepared->bank_account_name,
            ]);
            $result = app(PayoutService::class)->processAutoPayout($withdrawal, 'REFUND-'.$prepared->id);
            $transactionReference = (string) ($result['referenceId'] ?? $result['id'] ?? 'PAYOS-REFUND-'.$prepared->id);
        }

        return DB::transaction(function () use ($prepared, $transactionReference): bool {
            $lockedRefund = Refund::query()->lockForUpdate()->findOrFail($prepared->id);
            $order = Order::query()->lockForUpdate()->findOrFail($lockedRefund->order_id);
            if ($lockedRefund->status === 'approved') {
                return true;
            }
            if ($lockedRefund->status !== 'processing' || $order->status !== 'paid') {
                throw new RuntimeException('Trạng thái hoàn tiền đã thay đổi.');
            }

            $lockedRefund->update([
                'status' => 'approved',
                'transaction_reference' => $transactionReference,
                'processed_at' => now(),
            ]);
            $order->update(['status' => 'refunded']);

            $items = $order->items()->with('course')->get();
            Enrollment::where('user_id', $order->user_id)
                ->whereIn('course_id', $items->pluck('course_id'))
                ->where('status', '!=', 'cancelled')
                ->update(['status' => 'cancelled']);
            Course::query()
                ->whereIn('id', $items->pluck('course_id'))
                ->where('enrollment_count', '>', 0)
                ->decrement('enrollment_count');

            if ($order->user) {
                app(NotificationService::class)->send(
                    $order->user,
                    'Yêu cầu hoàn tiền đã được duyệt',
                    'Yêu cầu hoàn tiền cho đơn hàng #'.$order->order_code.' đã được xử lý.',
                    'refund_approved',
                    route('student.orders.show', $order)
                );
            }

            return true;
        });
    }
}
