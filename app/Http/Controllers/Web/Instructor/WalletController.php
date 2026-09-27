<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(PayoutService $payoutService): View
    {
        $user = auth()->user();

        $stats = [
            'total_earnings' => $user->total_earnings,
            'total_withdrawn' => $user->total_withdrawn,
            'pending_withdrawal' => $user->pending_withdrawal,
            'available_balance' => $user->available_balance,
        ];

        $banks = $payoutService->getVietNamBanks();

        $withdrawals = $user->withdrawals()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('instructor.wallet.index', compact('stats', 'banks', 'withdrawals', 'user'));
    }

    public function updateBankDetails(Request $request, PayoutService $payoutService): RedirectResponse
    {
        $banks = collect($payoutService->getVietNamBanks());
        $bankCodes = $banks->pluck('code')->filter()->unique()->values()->all();

        $validated = Validator::make($request->all(), [
            'bank_code' => ['required', 'string', Rule::in($bankCodes)],
            'bank_account_number' => ['required', 'string', 'regex:/^[0-9]{6,20}$/'],
            'bank_account_name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s]+$/u'],
        ], [
            'bank_code.required' => 'Vui lòng chọn ngân hàng.',
            'bank_code.in' => 'Ngân hàng không hợp lệ hoặc không hỗ trợ VietQR.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account_number.regex' => 'Số tài khoản phải gồm từ 6 đến 20 chữ số.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
            'bank_account_name.min' => 'Tên chủ tài khoản phải có ít nhất 3 ký tự.',
            'bank_account_name.regex' => 'Tên chủ tài khoản chỉ được chứa chữ cái và khoảng trắng.',
        ])->validate();

        $selectedBank = $banks->firstWhere('code', $validated['bank_code']);
        $bankName = $selectedBank['shortName'] ?? $selectedBank['name'] ?? null;
        if (! $bankName) {
            return back()->withErrors(['bank_code' => 'Không thể xác định ngân hàng đã chọn.'])->withInput();
        }

        $accountName = mb_strtoupper(Str::ascii(preg_replace('/\s+/u', ' ', trim($validated['bank_account_name']))));

        $user = auth()->user();
        $oldBank = [
            'bank_code' => $user->bank_code,
            'bank_name' => $user->bank_name,
            'bank_account_number' => $user->bank_account_number,
            'bank_account_name' => $user->bank_account_name,
        ];

        $user->update([
            'bank_code' => strtoupper(trim($validated['bank_code'])),
            'bank_name' => $bankName,
            'bank_account_number' => trim($validated['bank_account_number']),
            'bank_account_name' => $accountName,
        ]);

        ActivityLogService::log(
            $user->id,
            'update_bank_details',
            get_class($user),
            $user->id,
            ['old' => $oldBank, 'new' => $validated],
            $request,
            "Giảng viên {$user->name} đã cập nhật thông tin tài khoản ngân hàng."
        );

        return back()->with('success', 'Cập nhật thông tin tài khoản ngân hàng nhận tiền thành công!');
    }

    public function requestWithdrawal(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->bank_name || ! $user->bank_account_number || ! $user->bank_account_name) {
            return back()->withErrors(['bank' => 'Vui lòng cập nhật thông tin tài khoản ngân hàng trước khi tạo yêu cầu rút tiền.']);
        }

        $availableBalance = $user->available_balance;

        if ($availableBalance < 10000) {
            return back()->withErrors(['amount' => 'Số dư khả dụng của bạn chưa đủ hạn mức tối thiểu (10,000 VNĐ) để thực hiện rút tiền.']);
        }

        $validated = $request->validate([
            'idempotency_key' => ['nullable', 'uuid'],
            'amount' => ['required', 'numeric', 'min:10000', 'max:'.$availableBalance],
        ], [
            'amount.required' => 'Vui lòng nhập số tiền cần rút.',
            'amount.numeric' => 'Số tiền rút phải là chữ số hợp lệ.',
            'amount.min' => 'Số tiền rút tối thiểu là 10,000 VNĐ.',
            'amount.max' => 'Số tiền rút vượt quá Số dư khả dụng hiện có ('.number_format($availableBalance, 0, ',', '.').' VNĐ).',
        ]);

        $amount = (float) $validated['amount'];

        $existingWithdrawal = ! empty($validated['idempotency_key'])
            ? Withdrawal::query()
                ->where('user_id', $user->id)
                ->where('idempotency_key', $validated['idempotency_key'])
                ->first()
            : null;
        if ($existingWithdrawal) {
            return back()->with('success', 'Yêu cầu rút tiền này đã được ghi nhận trước đó.');
        }

        $result = DB::transaction(function () use ($user, $amount, $validated) {
            // Lock bản ghi user để tránh đụng độ race-condition khi bấm rút tiền liên tiếp
            $lockedUser = User::query()->lockForUpdate()->find($user->id);
            if (! $lockedUser || $lockedUser->available_balance < $amount) {
                return false;
            }
            if (! empty($validated['idempotency_key']) && Withdrawal::query()
                ->where('user_id', $lockedUser->id)
                ->where('idempotency_key', $validated['idempotency_key'])->exists()) {
                return false;
            }

            $withdrawal = Withdrawal::create([
                'user_id' => $lockedUser->id,
                'idempotency_key' => $validated['idempotency_key'] ?? null,
                'amount' => $amount,
                'bank_code' => $lockedUser->bank_code,
                'bank_name' => $lockedUser->bank_name,
                'bank_account_number' => $lockedUser->bank_account_number,
                'bank_account_name' => $lockedUser->bank_account_name,
                'status' => Withdrawal::STATUS_PENDING,
            ]);

            app(NotificationService::class)->notifyAdmins(
                'Yêu cầu rút tiền mới',
                "Giảng viên {$lockedUser->name} đã gửi yêu cầu rút tiền #{$withdrawal->id}: "
                    .number_format($amount, 0, ',', '.').' VNĐ. Vui lòng kiểm tra và xử lý.',
                'withdrawal_requested',
                route('admin.withdrawals.index', ['status' => Withdrawal::STATUS_PENDING])
            );

            return $withdrawal;
        });

        if (! $result) {
            return back()->withErrors(['amount' => 'Số dư khả dụng không đủ hoặc yêu cầu rút tiền vừa được thực hiện.']);
        }

        ActivityLogService::log(
            $user->id,
            'request_withdrawal',
            Withdrawal::class,
            $result->id,
            ['amount' => $amount, 'bank_name' => $user->bank_name, 'account' => $user->bank_account_number],
            $request,
            "Giảng viên {$user->name} đã gửi yêu cầu rút tiền ".number_format($amount, 0, ',', '.').' VNĐ.'
        );

        return back()->with('success', 'Đã gửi yêu cầu rút tiền '.number_format($amount, 0, ',', '.').' VNĐ thành công! Quản trị viên sẽ kiểm tra và chuyển khoản cho bạn trong thời gian sớm nhất.');
    }
}
