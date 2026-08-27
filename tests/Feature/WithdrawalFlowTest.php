<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WithdrawalFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_instructor_must_add_bank_account_before_requesting_withdrawal(): void
    {
        $instructor = $this->createInstructorWithEarnings();

        $response = $this->actingAs($instructor)->post(route('instructor.wallet.withdraw'), [
            'amount' => 10000,
        ]);

        $response->assertSessionHasErrors('bank');
        $this->assertDatabaseMissing('withdrawals', ['user_id' => $instructor->id]);
    }

    public function test_bank_details_are_validated_and_bank_name_is_derived_server_side(): void
    {
        Cache::put('vietnam_banks_list', [[
            'code' => 'VCB',
            'shortName' => 'Vietcombank',
            'name' => 'Ngân hàng TMCP Ngoại Thương Việt Nam',
            'bin' => '970436',
            'logo' => '',
        ]], 60);
        $instructor = User::factory()->create(['role' => 'instructor']);

        $this->actingAs($instructor)->put(route('instructor.wallet.bank-details.update'), [
            'bank_code' => 'UNKNOWN',
            'bank_account_number' => 'ABC123',
            'bank_account_name' => 'A1',
        ])->assertSessionHasErrors(['bank_code', 'bank_account_number', 'bank_account_name']);

        $this->actingAs($instructor)->put(route('instructor.wallet.bank-details.update'), [
            'bank_code' => 'VCB',
            'bank_name' => 'Forged Bank Name',
            'bank_account_number' => '0123456789',
            'bank_account_name' => 'Lê Nguyễn Anh Tuấn',
        ])->assertSessionHasNoErrors();

        $instructor->refresh();
        $this->assertSame('VCB', $instructor->bank_code);
        $this->assertSame('Vietcombank', $instructor->bank_name);
        $this->assertSame('0123456789', $instructor->bank_account_number);
        $this->assertSame('LE NGUYEN ANH TUAN', $instructor->bank_account_name);
    }

    public function test_pending_withdrawal_reserves_available_balance_and_prevents_overspending(): void
    {
        $instructor = $this->createInstructorWithEarnings(bankConfigured: true);

        $this->actingAs($instructor)->post(route('instructor.wallet.withdraw'), [
            'amount' => 30000,
        ])->assertSessionHasNoErrors();

        $withdrawal = Withdrawal::where('user_id', $instructor->id)->sole();
        $this->assertSame(Withdrawal::STATUS_PENDING, $withdrawal->status);
        $this->assertSame(30000.0, (float) $withdrawal->amount);
        $this->assertSame(10000.0, $instructor->fresh()->available_balance);

        $this->actingAs($instructor)->post(route('instructor.wallet.withdraw'), [
            'amount' => 20000,
        ])->assertSessionHasErrors('amount');

        $this->assertSame(1, Withdrawal::where('user_id', $instructor->id)->count());
    }

    public function test_admin_can_approve_withdrawal_once_and_instructor_is_notified(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = $this->createInstructorWithEarnings(bankConfigured: true);
        $withdrawal = $this->createPendingWithdrawal($instructor, 30000);

        $this->actingAs($admin)->post(route('admin.withdrawals.approve', $withdrawal), [
            'transaction_ref' => 'TEST-PAYOUT-001',
            'admin_note' => 'Đã đối soát chuyển khoản thử nghiệm.',
        ])->assertSessionHasNoErrors();

        $withdrawal->refresh();
        $this->assertSame(Withdrawal::STATUS_APPROVED, $withdrawal->status);
        $this->assertSame('TEST-PAYOUT-001', $withdrawal->transaction_ref);
        $this->assertNotNull($withdrawal->processed_at);
        $this->assertSame(30000.0, $instructor->fresh()->total_withdrawn);
        $this->assertDatabaseHas('push_notifications', [
            'user_id' => $instructor->id,
            'title' => 'Rút tiền thành công! 💰',
        ]);

        $notificationCount = PushNotification::where('user_id', $instructor->id)->count();
        $this->actingAs($admin)->post(route('admin.withdrawals.approve', $withdrawal), [
            'transaction_ref' => 'TEST-PAYOUT-002',
        ])->assertSessionHasErrors('error');

        $this->assertSame('TEST-PAYOUT-001', $withdrawal->fresh()->transaction_ref);
        $this->assertSame($notificationCount, PushNotification::where('user_id', $instructor->id)->count());
    }

    public function test_admin_rejection_releases_reserved_balance_and_notifies_instructor(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = $this->createInstructorWithEarnings(bankConfigured: true);
        $withdrawal = $this->createPendingWithdrawal($instructor, 30000);

        $this->assertSame(10000.0, $instructor->fresh()->available_balance);

        $this->actingAs($admin)->post(route('admin.withdrawals.reject', $withdrawal), [
            'admin_note' => 'Thông tin tài khoản chưa khớp.',
        ])->assertSessionHasNoErrors();

        $withdrawal->refresh();
        $this->assertSame(Withdrawal::STATUS_REJECTED, $withdrawal->status);
        $this->assertSame(40000.0, $instructor->fresh()->available_balance);
        $this->assertDatabaseHas('push_notifications', [
            'user_id' => $instructor->id,
            'title' => 'Yêu cầu rút tiền bị từ chối ⚠️',
        ]);
    }

    public function test_non_admin_cannot_call_withdrawal_approval_tools_directly(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = $this->createInstructorWithEarnings(bankConfigured: true);
        $withdrawal = $this->createPendingWithdrawal($instructor, 30000);

        $this->actingAs($student)
            ->withHeader('Accept', 'application/json')
            ->post(route('admin.withdrawals.approve', $withdrawal), ['transaction_ref' => 'FORGED'])
            ->assertForbidden();

        $this->assertSame(Withdrawal::STATUS_PENDING, $withdrawal->fresh()->status);
        $this->assertNull($withdrawal->fresh()->transaction_ref);
    }

    private function createInstructorWithEarnings(bool $bankConfigured = false): User
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'email_verified_at' => now(),
            'bank_code' => $bankConfigured ? 'VCB' : null,
            'bank_name' => $bankConfigured ? 'Vietcombank' : null,
            'bank_account_number' => $bankConfigured ? '0123456789' : null,
            'bank_account_name' => $bankConfigured ? 'TEST INSTRUCTOR' : null,
        ]);
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::create([
            'name' => 'Withdrawal '.uniqid(),
            'slug' => 'withdrawal-'.uniqid(),
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Withdrawal course '.uniqid(),
            'slug' => 'withdrawal-course-'.uniqid(),
            'short_description' => 'Withdrawal test',
            'description' => 'Withdrawal test',
            'objectives' => 'Test payouts',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'price' => 50000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => 'published',
            'is_published' => true,
        ]);
        $order = Order::create([
            'order_code' => 'WITHDRAW-'.uniqid(),
            'user_id' => $student->id,
            'subtotal' => 50000,
            'discount_amount' => 0,
            'total_amount' => 50000,
            'status' => 'paid',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'price' => 50000,
            'commission_rate' => 20,
            'commission_amount' => 10000,
            'instructor_earning' => 40000,
        ]);

        return $instructor;
    }

    private function createPendingWithdrawal(User $instructor, float $amount): Withdrawal
    {
        return Withdrawal::create([
            'user_id' => $instructor->id,
            'amount' => $amount,
            'bank_code' => $instructor->bank_code,
            'bank_name' => $instructor->bank_name,
            'bank_account_number' => $instructor->bank_account_number,
            'bank_account_name' => $instructor->bank_account_name,
            'status' => Withdrawal::STATUS_PENDING,
        ]);
    }
}
