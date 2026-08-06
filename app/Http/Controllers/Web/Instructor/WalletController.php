<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\ActivityLogService;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function updateBankDetails(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_code' => ['required', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_name' => ['required', 'string', 'max:100'],
        ], [
            'bank_code.required' => 'Vui lòng chọn ngân hàng.',
            'bank_name.required' => 'Vui lòng nhập tên ngân hàng.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
        ]);

        $user = auth()->user();
        $oldBank = [
            'bank_code' => $user->bank_code,
            'bank_name' => $user->bank_name,
            'bank_account_number' => $user->bank_account_number,
            'bank_account_name' => $user->bank_account_name,
        ];

        $user->update([
            'bank_code' => strtoupper(trim($validated['bank_code'])),
            'bank_name' => trim($validated['bank_name']),
            'bank_account_number' => trim($validated['bank_account_number']),
            'bank_account_name' => mb_strtoupper(trim($validated['bank_account_name'])),
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

    public function requestWithdrawal(Request $request, PayoutService $payoutService): RedirectResponse
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
            'amount' => ['required', 'numeric', 'min:10000', 'max:' . $availableBalance],
        ], [
            'amount.required' => 'Vui lòng nhập số tiền cần rút.',
            'amount.numeric' => 'Số tiền rút phải là chữ số hợp lệ.',
            'amount.min' => 'Số tiền rút tối thiểu là 10,000 VNĐ.',
            'amount.max' => 'Số tiền rút vượt quá Số dư khả dụng hiện có (' . number_format($availableBalance, 0, ',', '.') . ' VNĐ).',
        ]);

        $amount = (float) $validated['amount'];

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'bank_code' => $user->bank_code,
            'bank_name' => $user->bank_name,
            'bank_account_number' => $user->bank_account_number,
            'bank_account_name' => $user->bank_account_name,
            'status' => Withdrawal::STATUS_PENDING,
        ]);

        // Thử tự động chi chuyển khoản qua PayOS API ngay lập tức
        try {
            $payoutResult = $payoutService->processAutoPayout($withdrawal);
            $txnRef = $payoutResult['reference'] ?? $payoutResult['id'] ?? ('PAYOS-PO-' . time());

            $withdrawal->update([
                'status' => Withdrawal::STATUS_APPROVED,
                'transaction_ref' => $txnRef,
                'admin_note' => 'Tự động chi tiền tức thì qua PayOS Payout API khi khởi tạo yêu cầu.',
                'processed_at' => now(),
            ]);

            ActivityLogService::log(
                $user->id,
                'request_withdrawal_auto_approved',
                Withdrawal::class,
                $withdrawal->id,
                ['amount' => $amount, 'ref' => $txnRef],
                $request,
                "Giảng viên {$user->name} đã rút tiền thành công " . number_format($amount, 0, ',', '.') . " VNĐ qua PayOS Auto Payout."
            );

            return back()->with('success', '⚡ Rút tiền thành công! Hệ thống đã tự động chuyển khoản ' . number_format($amount, 0, ',', '.') . ' VNĐ trực tiếp về tài khoản ' . $user->bank_name . ' (' . $user->bank_account_number . ') của bạn. Mã GD: ' . $txnRef);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Chưa tự động chi được PayOS cho yêu cầu rút tiền #{$withdrawal->id}: " . $e->getMessage());

            ActivityLogService::log(
                $user->id,
                'request_withdrawal',
                Withdrawal::class,
                $withdrawal->id,
                ['amount' => $amount, 'bank_name' => $user->bank_name, 'account' => $user->bank_account_number, 'auto_payout_error' => $e->getMessage()],
                $request,
                "Giảng viên {$user->name} đã gửi yêu cầu rút tiền " . number_format($amount, 0, ',', '.') . " VNĐ."
            );

            return back()->with('success', 'Đã gửi yêu cầu rút tiền ' . number_format($amount, 0, ',', '.') . ' VNĐ thành công! Quản trị viên sẽ kiểm tra và chuyển khoản cho bạn trong thời gian sớm nhất.');
        }
    }
}
