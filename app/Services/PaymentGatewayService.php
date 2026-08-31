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
use Carbon\CarbonImmutable;
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

    protected function createPayOSUrl(Order $order, bool $allowLegacyRecovery = true): string
    {
        $clientId = (string) config('services.payos.client_id');
        $apiKey = (string) config('services.payos.api_key');
        $checksumKey = (string) config('services.payos.checksum_key');

        if ($clientId === '' || $apiKey === '' || $checksumKey === '') {
            Log::error('PayOS configuration is incomplete', [
                'missing' => array_keys(array_filter([
                    'client_id' => $clientId === '',
                    'api_key' => $apiKey === '',
                    'checksum_key' => $checksumKey === '',
                ])),
            ]);

            throw new RuntimeException('Chưa cấu hình tài khoản PayOS.');
        }

        $payment = $order->prepareGatewayPayment('bank_transfer', $this->newPayOSReference());
        $order->refresh();
        $gatewayOrderCode = (int) $payment->gateway_order_code;
        $amount = (int) round((float) $payment->amount);
        if ($amount <= 0) {
            throw new RuntimeException('Số tiền thanh toán PayOS phải lớn hơn 0.');
        }

        $description = $this->buildPayOSDescription($order);
        $params = [
            'amount' => $amount,
            'cancelUrl' => route('student.checkout.failed', $order->order_code),
            'description' => $description,
            'orderCode' => $gatewayOrderCode,
            'returnUrl' => route('student.checkout.success', $order->order_code),
        ];
        $params['signature'] = $this->signPayOSData($params, $checksumKey);

        try {
            $response = $this->payOSHttpClient(15)
                ->withHeaders([
                    'x-client-id' => $clientId,
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api-merchant.payos.vn/v2/payment-requests', $params);
        } catch (\Throwable $exception) {
            Log::error('PayOS payment-link request failed', [
                'order_id' => $order->id,
                'gateway_order_code' => $gatewayOrderCode,
                'exception_class' => $exception::class,
            ]);

            throw new RuntimeException('Không thể kết nối đến PayOS.');
        }

        $checkoutUrl = $this->extractPayOSCheckoutUrl($response->json());

        // PayOS may return HTTP 200 without data when the orderCode already exists.
        // Reusing the existing payment link makes repeated clicks idempotent.
        if ($checkoutUrl === null) {
            try {
                $existingResponse = $this->payOSHttpClient(10)
                    ->withHeaders([
                        'x-client-id' => $clientId,
                        'x-api-key' => $apiKey,
                    ])
                    ->get("https://api-merchant.payos.vn/v2/payment-requests/{$gatewayOrderCode}");
            } catch (\Throwable $exception) {
                Log::warning('PayOS existing-link lookup failed', ['order_id' => $order->id, 'exception_class' => $exception::class]);
                throw new RuntimeException('Không thể đối soát liên kết PayOS. Vui lòng thử lại sau.');
            }

            $existingPayload = $existingResponse->json();
            // Legacy references used local IDs, which can collide after a database reset.
            // Only replace one when an authenticated lookup confirms a DIFFERENT quoted
            // amount, never on timeouts, partial payments or missing/ambiguous responses.
            if ($allowLegacyRecovery
                && (string) $gatewayOrderCode === (string) $order->id
                && (string) $response->json('code') === '231'
                && $existingResponse->successful()
                && is_array($existingPayload)
                && $this->isSuccessfulPayOSResponse($existingPayload)
                && data_get($existingPayload, 'data.status') === 'PAID'
                && (string) data_get($existingPayload, 'data.orderCode') === (string) $gatewayOrderCode
                && is_numeric(data_get($existingPayload, 'data.amount'))
                && (float) data_get($existingPayload, 'data.amountPaid', -1) === (float) data_get($existingPayload, 'data.amount')
                && (float) data_get($existingPayload, 'data.amount') !== (float) $amount
            ) {
                $signature = (string) ($existingPayload['signature'] ?? '');
                if ($signature !== '' && ! hash_equals($this->signPayOSData($existingPayload['data'], $checksumKey), $signature)) {
                    throw new RuntimeException('Không thể xác minh thông tin đơn PayOS cũ.');
                }
                DB::transaction(function () use ($order, $gatewayOrderCode, $amount, $existingPayload): void {
                    $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
                    $lockedPayment = $lockedOrder->payment()->lockForUpdate()->firstOrFail();
                    if ($lockedOrder->status !== 'pending' || $lockedPayment->status !== 'pending'
                        || (string) $lockedPayment->gateway_order_code !== (string) $gatewayOrderCode
                        || (float) $lockedPayment->amount !== (float) $amount) {
                        throw new RuntimeException('Trạng thái thanh toán vừa thay đổi. Vui lòng tải lại trang.');
                    }
                    $lockedPayment->update([
                        'gateway_order_code' => $this->newPayOSReference(),
                        'gateway_response' => array_merge((array) $lockedPayment->gateway_response, [
                            'legacy_reference_collision' => [
                                'reference' => (string) $gatewayOrderCode,
                                'remote_amount' => data_get($existingPayload, 'data.amount'),
                                'expected_amount' => $amount,
                            ],
                        ]),
                    ]);
                });

                return $this->createPayOSUrl($order->fresh(), false);
            }

            if (is_numeric(data_get($existingPayload, 'data.amount'))
                && (float) data_get($existingPayload, 'data.amount') !== (float) $amount) {
                throw new RuntimeException('Liên kết PayOS không khớp số tiền của đơn hàng. Cần đối soát trước khi thanh toán.');
            }
            $checkoutUrl = $this->extractPayOSCheckoutUrl($existingPayload);
        }

        // Except for the confirmed legacy collision above, retain references on retries:
        // even if its HTTP response was lost. Terminal links require support/reconciliation.
        if ($checkoutUrl === null || ! $this->isValidPayOSCheckoutUrl($checkoutUrl)) {
            $descriptionText = $response->json('desc') ?: 'Không thể tạo liên kết thanh toán.';

            Log::warning('PayOS payment link creation failed', [
                'order_id' => $order->id,
                'http_status' => $response->status(),
                'payos_code' => $response->json('code'),
                'payos_description' => $descriptionText,
            ]);

            throw new RuntimeException('PayOS từ chối yêu cầu: '.$descriptionText);
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

    private function newPayOSReference(): string
    {
        // Numeric and below JavaScript's MAX_SAFE_INTEGER; independent of local DB IDs.
        do {
            $reference = (string) random_int(100000000000000, 999999999999999);
        } while (Payment::where('gateway_order_code', $reference)->exists());

        return $reference;
    }

    /**
     * PayOS limits descriptions to nine characters for channels without a linked PayOS bank account.
     * The numeric orderCode remains the authoritative payment identifier.
     */
    private function buildPayOSDescription(Order $order): string
    {
        $orderId = (string) $order->id;

        return strlen($orderId) <= 7
            ? 'OD'.str_pad($orderId, 7, '0', STR_PAD_LEFT)
            : 'OD'.substr(hash('sha256', $orderId), 0, 7);
    }

    private function isSuccessfulPayOSResponse(array $responseData): bool
    {
        if ((string) ($responseData['code'] ?? '') !== '00') {
            return false;
        }

        return ! array_key_exists('success', $responseData) || $responseData['success'] === true;
    }

    private function isValidPayOSCheckoutUrl(mixed $checkoutUrl): bool
    {
        if (! is_string($checkoutUrl) || filter_var($checkoutUrl, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parts = parse_url($checkoutUrl);

        return is_array($parts)
            && strtolower((string) ($parts['scheme'] ?? '')) === 'https'
            && filled($parts['host'] ?? null);
    }

    private function payOSResponseSummary($response, Order $order, string $description): array
    {
        $responseData = $response->json();
        $data = is_array($responseData) && is_array($responseData['data'] ?? null)
            ? $responseData['data']
            : null;
        $checkoutUrl = is_array($data) ? ($data['checkoutUrl'] ?? null) : null;
        $message = is_array($responseData)
            ? ($responseData['desc'] ?? $responseData['message'] ?? null)
            : null;

        return [
            'order_id' => $order->id,
            'gateway_order_code' => $order->id,
            'amount' => (int) round((float) $order->total_amount),
            'description_length' => strlen($description),
            'http_status' => $response->status(),
            'response_keys' => is_array($responseData) ? array_keys($responseData) : [],
            'code' => is_array($responseData) ? ($responseData['code'] ?? null) : null,
            'message' => is_scalar($message) ? Str::limit((string) $message, 200) : null,
            'data_exists' => $data !== null,
            'data_keys' => $data !== null ? array_keys($data) : [],
            'checkout_url_present' => is_string($checkoutUrl) && $checkoutUrl !== '',
            'checkout_url_host' => is_string($checkoutUrl) ? parse_url($checkoutUrl, PHP_URL_HOST) : null,
        ];
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

            $responseData = $response->json();
            if (! is_array($responseData)
                || ! $response->successful()
                || ! $this->isSuccessfulPayOSResponse($responseData)
                || ($responseData['data']['status'] ?? null) !== 'PAID') {
                return false;
            }

            $responseData = (array) ($responseData['data'] ?? []);
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

    public function reconcilePayOSCancelReturn(Order $order): void
    {
        $payment = $order->payment;
        if ($order->status !== 'pending' || ! $payment?->gateway_order_code || $payment->gateway !== 'bank_transfer') {
            return;
        }
        try {
            $response = $this->payOSHttpClient(10)->withHeaders([
                'x-client-id' => (string) config('services.payos.client_id'),
                'x-api-key' => (string) config('services.payos.api_key'),
            ])->get('https://api-merchant.payos.vn/v2/payment-requests/'.$payment->gateway_order_code);
            $payload = $response->json();
            $data = data_get($payload, 'data');
            if (! $response->successful() || ! is_array($payload) || ! $this->isSuccessfulPayOSResponse($payload)
                || ! is_array($data) || (string) ($data['orderCode'] ?? '') !== (string) $payment->gateway_order_code
                || (float) ($data['amount'] ?? -1) !== (float) $payment->amount) {
                return;
            }
            $signature = (string) ($payload['signature'] ?? '');
            if ($signature !== '' && ! hash_equals($this->signPayOSData($data, (string) config('services.payos.checksum_key')), $signature)) {
                return;
            }
            if (($data['status'] ?? '') === 'PAID') {
                $this->checkAndUpdatePayOSStatus($order);
                return;
            }
            if (($data['status'] ?? '') !== 'CANCELLED' || (float) ($data['amountPaid'] ?? -1) !== 0.0) {
                return;
            }
            DB::transaction(function () use ($order, $payment, $data): void {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
                $lockedPayment = $lockedOrder->payment()->lockForUpdate()->firstOrFail();
                if ($lockedOrder->status !== 'pending' || $lockedPayment->status !== 'pending'
                    || $lockedPayment->gateway_order_code !== $payment->gateway_order_code) {
                    return;
                }
                $lockedOrder->update(['status' => 'cancelled']);
                $lockedPayment->update(['status' => 'failed', 'gateway_response' => $data]);
            });
        } catch (\Throwable $exception) {
            Log::warning('PayOS cancellation reconciliation unavailable', ['order_id' => $order->id, 'exception_class' => $exception::class]);
        }
    }

    public function completePayOSPayment(Order $order, string $transactionId, array $gatewayResponse = []): bool
    {
        return $this->finalizePayment($order, $transactionId, $gatewayResponse, false);
    }

    public function completeMomoPayment(Order $order, string $transactionId, array $gatewayResponse = []): bool
    {
        return $this->finalizePayment($order, $transactionId, $gatewayResponse, false, [
            'gateway' => 'momo',
            'bank_code' => $gatewayResponse['payType'] ?? null,
            'transaction_no' => $gatewayResponse['transId'] ?? null,
            'response_code' => isset($gatewayResponse['resultCode']) ? (string) $gatewayResponse['resultCode'] : null,
            'transaction_date' => isset($gatewayResponse['responseTime']) ? CarbonImmutable::createFromTimestampMs((int) $gatewayResponse['responseTime']) : null,
        ]);
    }

    public function failMomoPayment(Order $order, array $gatewayResponse): void
    {
        DB::transaction(function () use ($order, $gatewayResponse): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $payment = $lockedOrder->payment()->lockForUpdate()->first();

            if (! $payment || $payment->status === 'success' || $lockedOrder->status === 'paid') {
                return;
            }

            $payment->update([
                'gateway' => 'momo',
                'status' => 'failed',
                'bank_code' => $gatewayResponse['payType'] ?? null,
                'transaction_no' => $gatewayResponse['transId'] ?? null,
                'response_code' => isset($gatewayResponse['resultCode']) ? (string) $gatewayResponse['resultCode'] : null,
                'transaction_date' => isset($gatewayResponse['responseTime']) ? CarbonImmutable::createFromTimestampMs((int) $gatewayResponse['responseTime']) : null,
                'gateway_response' => $gatewayResponse,
            ]);
        });
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

    private function finalizePayment(Order $order, string $transactionId, array $gatewayResponse, bool $mock, array $paymentAttributes = []): bool
    {
        return DB::transaction(function () use ($order, $transactionId, $gatewayResponse, $mock, $paymentAttributes): bool {
            app(InstructorFinanceService::class)->lockOrderInstructors($order);
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status === 'paid') {
                return true;
            }
            // Verified late callbacks must still reconcile legacy cancelled/failed orders.
            if (! in_array($lockedOrder->status, ['pending', 'cancelled', 'failed'], true)
                || (($mock || (float) $lockedOrder->total_amount === 0.0) && $lockedOrder->status !== 'pending')) {
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

            // Honour the quoted discount after real money has arrived. Coupon expiry
            // or another buyer consuming the last use must not erase a received payment.
            $coupon = $lockedOrder->coupon_id
                ? Coupon::query()->lockForUpdate()->find($lockedOrder->coupon_id)
                : null;
            if (($mock || (float) $lockedOrder->total_amount === 0.0)
                && $lockedOrder->coupon_id && (! $coupon || ! $coupon->canBeUsedBy($lockedOrder->user_id))) {
                $lockedOrder->update(['status' => 'failed']);
                $payment->update(['status' => 'failed']);

                return false;
            }

            $lockedOrder->update(['status' => 'paid', 'transaction_id' => $transactionId]);
            $payment->update(array_merge([
                'status' => 'success',
                'transaction_id' => $transactionId,
                'paid_at' => now(),
                'gateway_response' => $gatewayResponse + ['mock' => $mock],
            ], $paymentAttributes));

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
            ->lockForUpdate()->get()
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
            app(InstructorFinanceService::class)->lockOrderInstructors($refund->order);
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
            app(InstructorFinanceService::class)->lockOrderInstructors($prepared->order);
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
            $enrollments = Enrollment::where('user_id', $order->user_id)
                ->whereIn('course_id', $items->pluck('course_id'))
                ->withLearningAccess()->lockForUpdate()->get();
            foreach ($enrollments as $enrollment) {
                // A second valid purchase must keep access, progress and completion.
                $remainingOrder = Order::query()
                    ->where('user_id', $order->user_id)
                    ->where('status', 'paid')
                    ->where('id', '!=', $order->id)
                    ->whereHas('items', fn ($query) => $query->where('course_id', $enrollment->course_id))
                    ->orderBy('id')->lockForUpdate()->first();
                if ($remainingOrder) {
                    if ((int) $enrollment->order_id === (int) $order->id) {
                        $enrollment->update(['order_id' => $remainingOrder->id]);
                    }
                    continue;
                }
                $enrollment->update(['status' => 'cancelled']);
                Course::whereKey($enrollment->course_id)->where('enrollment_count', '>', 0)
                    ->decrement('enrollment_count');
            }

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
