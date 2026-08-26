<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Services\NotificationService;
use App\Services\PaymentGatewayService;
use App\Services\PayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    /**
     * Danh sách tất cả các Yêu cầu Hoàn tiền
     */
    public function index(Request $request)
    {
        $query = Refund::with(['order', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', fn ($oq) => $oq->where('order_code', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhere('bank_account_number', 'like', "%{$search}%");
            });
        }

        $refunds = $query->latest()->paginate(15)->withQueryString();
        $banks = app(PayoutService::class)->getVietNamBanks();

        return view('admin.refunds.index', compact('refunds', 'banks'));
    }

    /**
     * Xem Chi tiết Yêu cầu Hoàn tiền
     */
    public function show(Refund $refund)
    {
        $refund->load(['order.items.course', 'user']);
        $banks = app(PayoutService::class)->getVietNamBanks();

        return view('admin.refunds.show', compact('refund', 'banks'));
    }

    /**
     * Duyệt yêu cầu sau khi quản trị viên đã hoàn tiền thủ công.
     */
    public function approve(Request $request, Refund $refund)
    {
        if (! in_array($refund->status, ['pending', 'processing'], true)) {
            return back()->with('error', 'Yêu cầu hoàn tiền này đã được xử lý trước đó.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
            'transaction_reference' => ['required', 'string', 'min:4', 'max:100'],
        ], [
            'transaction_reference.required' => 'Vui lòng nhập mã giao dịch/đối soát khi xác nhận hoàn tiền thủ công.',
        ]);

        try {
            app(PaymentGatewayService::class)->processRefund(
                $refund,
                'manual',
                $validated['admin_note'] ?? null,
                $validated['transaction_reference'] ?? null
            );

            return redirect()->route('admin.refunds.index')
                ->with('success', "Đã xác nhận hoàn tiền thủ công cho đơn hàng #{$refund->order->order_code}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xử lý hoàn tiền: '.$e->getMessage());
        }
    }

    /**
     * Từ chối Yêu cầu Hoàn tiền
     */
    public function reject(Request $request, Refund $refund)
    {
        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'admin_note.required' => 'Vui lòng cung cấp lý do từ chối hoàn tiền.',
        ]);

        $rejected = DB::transaction(function () use ($refund, $validated): ?Refund {
            $lockedRefund = Refund::query()->lockForUpdate()->find($refund->id);
            if (! $lockedRefund || $lockedRefund->status !== 'pending') {
                return null;
            }

            $lockedRefund->update([
                'status' => 'rejected',
                'admin_note' => trim($validated['admin_note']),
                'processed_at' => now(),
            ]);

            return $lockedRefund;
        });

        if (! $rejected) {
            return back()->with('error', 'Yêu cầu hoàn tiền này đã được xử lý trước đó.');
        }

        $refund = $rejected;

        // Gửi thông báo cho Học viên
        if ($refund->user) {
            app(NotificationService::class)->send(
                $refund->user,
                'Yêu cầu hoàn tiền đã bị từ chối',
                "Yêu cầu hoàn tiền cho đơn hàng #{$refund->order->order_code} không được chấp nhận. Lý do: {$validated['admin_note']}",
                'refund_rejected',
                route('student.orders.show', $refund->order)
            );
        }

        return redirect()->route('admin.refunds.index')
            ->with('success', "Đã từ chối yêu cầu hoàn tiền cho đơn hàng #{$refund->order->order_code}.");
    }
}
