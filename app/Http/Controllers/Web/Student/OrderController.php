<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Order;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const STATUSES = ['paid', 'pending', 'cancelled', 'failed', 'refunded'];

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $status = in_array($status, self::STATUSES, true) ? $status : null;

        $query = Order::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'items.course:id,title,slug,instructor_id',
                'items.course.instructor:id,name',
                'payment:id,order_id,status,paid_at',
            ]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(fn ($builder) => $builder
                ->where('order_code', 'like', '%'.$search.'%')
                ->orWhereHas('items.course', fn ($courseQuery) => $courseQuery
                    ->where('title', 'like', '%'.$search.'%')));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('student.dashboard.orders.index', compact('orders', 'status'));
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorizeOwner($request, $order);

        $order->load([
            'items.course' => fn ($query) => $query->with('instructor:id,name,avatar')->withCount('lessons'),
            'payment', 'coupon', 'refunds' => fn ($query) => $query->latest(),
        ]);

        $paidAt = $order->payment?->paid_at ?? $order->created_at;
        $refundDeadline = $paidAt?->copy()->addDays(7);
        $maxProgress = (float) Enrollment::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('course_id', $order->items->pluck('course_id'))
            ->max('progress_percent');
        $refundEligibility = [
            'within_window' => $refundDeadline?->isFuture() ?? false,
            'deadline' => $refundDeadline,
            'progress_ok' => $maxProgress < 50,
            'max_progress' => $maxProgress,
            'has_value' => (float) $order->total_amount > 0,
        ];
        $banks = app(PayoutService::class)->getVietNamBanks();

        return view('student.dashboard.orders.show', compact('order', 'banks', 'refundEligibility'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        $cancelled = DB::transaction(function () use ($order): bool {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if (! $lockedOrder->canCancel()) {
                return false;
            }

            $lockedOrder->update(['status' => 'cancelled']);
            $lockedOrder->payment()->where('status', 'pending')->update([
                'status' => 'failed',
                'gateway_response' => ['message' => 'Đơn hàng đã được học viên hủy.'],
            ]);

            return true;
        });

        return redirect()->route('student.orders.show', $order)
            ->with($cancelled ? 'success' : 'error', $cancelled
                ? 'Đã hủy đơn hàng thành công.'
                : 'Không thể hủy: đơn đã được xử lý hoặc đã hủy trước đó.');
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);
    }
}
