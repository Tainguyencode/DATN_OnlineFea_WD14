<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Models\Withdrawal;
use App\Services\ActivityLogService;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request, PayoutService $payoutService): View
    {
        $status = (string) $request->query('status');
        $search = trim((string) $request->query('search'));

        $query = Withdrawal::with('user')
            ->when($status !== '' && in_array($status, ['pending', 'approved', 'rejected'], true), function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('bank_account_number', 'like', "%{$search}%")
                        ->orWhere('bank_account_name', 'like', "%{$search}%")
                        ->orWhere('transaction_ref', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

        $withdrawals = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_approved' => (float) Withdrawal::where('status', Withdrawal::STATUS_APPROVED)->sum('amount'),
            'total_pending' => (float) Withdrawal::where('status', Withdrawal::STATUS_PENDING)->sum('amount'),
            'count_pending' => Withdrawal::where('status', Withdrawal::STATUS_PENDING)->count(),
            'count_approved' => Withdrawal::where('status', Withdrawal::STATUS_APPROVED)->count(),
            'count_rejected' => Withdrawal::where('status', Withdrawal::STATUS_REJECTED)->count(),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'stats', 'status', 'search'));
    }

    public function approve(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Yêu cầu rút tiền này đã được xử lý từ trước.']);
        }

        $validated = $request->validate([
            'transaction_ref' => ['nullable', 'string', 'max:100'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $txnRef = trim($validated['transaction_ref'] ?? '') ?: 'FT' . date('YmdHis') . rand(100, 999);

        $withdrawal->update([
            'status' => Withdrawal::STATUS_APPROVED,
            'transaction_ref' => $txnRef,
            'admin_note' => trim($validated['admin_note'] ?? '') ?: 'Đã chuyển khoản VietQR/Napas247 thành công.',
            'processed_at' => now(),
        ]);

        // Gửi thông báo cho Giảng viên
        PushNotification::create([
            'user_id' => $withdrawal->user_id,
            'title' => 'Rút tiền thành công! 💰',
            'message' => 'Yêu cầu rút ' . number_format($withdrawal->amount, 0, ',', '.') . ' VNĐ đã được Admin chuyển khoản thành công vào tài khoản ' . $withdrawal->bank_name . ' (' . $withdrawal->bank_account_number . '). Mã GD: ' . $txnRef . '.',
            'type' => 'order_paid',
            'url' => route('instructor.wallet.index'),
        ]);

        ActivityLogService::log(
            auth()->id(),
            'approve_withdrawal',
            Withdrawal::class,
            $withdrawal->id,
            ['amount' => $withdrawal->amount, 'ref' => $txnRef, 'instructor_id' => $withdrawal->user_id],
            $request,
            "Admin đã duyệt và xác nhận chuyển khoản rút tiền #" . $withdrawal->id . " cho " . $withdrawal->user->name
        );

        return back()->with('success', 'Đã duyệt yêu cầu rút tiền #' . $withdrawal->id . ' và thông báo thành công tới giảng viên!');
    }

    public function reject(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Yêu cầu rút tiền này đã được xử lý từ trước.']);
        }

        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'max:500'],
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối yêu cầu rút tiền.',
        ]);

        $withdrawal->update([
            'status' => Withdrawal::STATUS_REJECTED,
            'admin_note' => trim($validated['admin_note']),
            'processed_at' => now(),
        ]);

        // Gửi thông báo cho Giảng viên
        PushNotification::create([
            'user_id' => $withdrawal->user_id,
            'title' => 'Yêu cầu rút tiền bị từ chối ⚠️',
            'message' => 'Yêu cầu rút ' . number_format($withdrawal->amount, 0, ',', '.') . ' VNĐ đã bị từ chối. Lý do: ' . $validated['admin_note'] . '. Số tiền đã được hoàn trả lại số dư khả dụng.',
            'type' => 'course_rejected',
            'url' => route('instructor.wallet.index'),
        ]);

        ActivityLogService::log(
            auth()->id(),
            'reject_withdrawal',
            Withdrawal::class,
            $withdrawal->id,
            ['amount' => $withdrawal->amount, 'reason' => $validated['admin_note']],
            $request,
            "Admin từ chối yêu cầu rút tiền #" . $withdrawal->id . " của " . $withdrawal->user->name
        );

        return back()->with('success', 'Đã từ chối yêu cầu rút tiền #' . $withdrawal->id . '. Số tiền đã được hoàn trả lại số dư khả dụng cho giảng viên.');
    }

    /**
     * Tự động chi chuyển khoản ngân hàng qua PayOS Payout API cho Giảng viên.
     */
    public function autoPayout(Request $request, Withdrawal $withdrawal, PayoutService $payoutService): RedirectResponse
    {
        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Yêu cầu rút tiền này đã được xử lý từ trước.']);
        }

        try {
            $payoutResult = $payoutService->processAutoPayout($withdrawal);
            $txnRef = $payoutResult['transactions'][0]['reference']
                ?? $payoutResult['referenceId']
                ?? $payoutResult['id']
                ?? ('PAYOS-PO-' . time());

            $withdrawal->update([
                'status' => Withdrawal::STATUS_APPROVED,
                'transaction_ref' => $txnRef,
                'admin_note' => 'Đã tự động chuyển khoản qua PayOS Payout API.',
                'processed_at' => now(),
            ]);

            PushNotification::create([
                'user_id' => $withdrawal->user_id,
                'title' => 'Rút tiền tự động thành công! ⚡',
                'message' => 'Yêu cầu rút ' . number_format($withdrawal->amount, 0, ',', '.') . ' VNĐ đã được hệ thống tự động chuyển khoản thành công vào ' . $withdrawal->bank_name . ' (' . $withdrawal->bank_account_number . '). Mã GD: ' . $txnRef . '.',
                'type' => 'order_paid',
                'url' => route('instructor.wallet.index'),
            ]);

            ActivityLogService::log(
                auth()->id(),
                'auto_payout_withdrawal',
                Withdrawal::class,
                $withdrawal->id,
                ['amount' => $withdrawal->amount, 'ref' => $txnRef, 'instructor_id' => $withdrawal->user_id],
                $request,
                "Admin đã kích hoạt tự động chi rút tiền PayOS #" . $withdrawal->id . " cho " . $withdrawal->user->name
            );

            return back()->with('success', 'Đã tự động chuyển khoản ' . number_format($withdrawal->amount, 0, ',', '.') . 'đ qua PayOS cho giảng viên ' . $withdrawal->user->name . ' thành công! Mã GD: ' . $txnRef);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi tự động chi PayOS: ' . $e->getMessage()]);
        }
    }
}
