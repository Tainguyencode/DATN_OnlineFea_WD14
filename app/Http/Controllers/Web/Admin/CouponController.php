<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');

        $coupons = Coupon::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%");
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', true)->count(),
            'inactive' => Coupon::where('is_active', false)->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats', 'search', 'status'));
    }

    public function create(): View
    {
        return view('admin.coupons.create', [
            'coupon' => new Coupon([
                'type' => 'percent',
                'is_active' => true,
                'min_order_amount' => 0,
                'value' => 0
            ])
        ]);
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $coupon = DB::transaction(function () use ($request) {
            return Coupon::create([
                'code' => strtoupper($request->string('code')->trim()->toString()),
                'type' => $request->string('type')->toString(),
                'value' => $request->float('value'),
                'min_order_amount' => $request->float('min_order_amount'),
                'max_uses' => $request->filled('max_uses') ? $request->integer('max_uses') : null,
                'starts_at' => $request->filled('starts_at') ? $request->input('starts_at') : null,
                'expires_at' => $request->filled('expires_at') ? $request->input('expires_at') : null,
                'is_active' => $request->boolean('is_active', true),
            ]);
        });

        ActivityLogService::log(
            auth()->id(),
            'create_coupon',
            Coupon::class,
            $coupon->id,
            ['code' => $coupon->code],
            $request
        );

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Tạo mã giảm giá thành công.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(StoreCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        DB::transaction(function () use ($request, $coupon) {
            $coupon->update([
                'code' => strtoupper($request->string('code')->trim()->toString()),
                'type' => $request->string('type')->toString(),
                'value' => $request->float('value'),
                'min_order_amount' => $request->float('min_order_amount'),
                'max_uses' => $request->filled('max_uses') ? $request->integer('max_uses') : null,
                'starts_at' => $request->filled('starts_at') ? $request->input('starts_at') : null,
                'expires_at' => $request->filled('expires_at') ? $request->input('expires_at') : null,
                'is_active' => $request->boolean('is_active'),
            ]);
        });

        ActivityLogService::log(
            auth()->id(),
            'update_coupon',
            Coupon::class,
            $coupon->id,
            ['code' => $coupon->code],
            $request
        );

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Cập nhật mã giảm giá thành công.');
    }

    public function toggleStatus(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        ActivityLogService::log(
            auth()->id(),
            'toggle_coupon_status',
            Coupon::class,
            $coupon->id,
            ['code' => $coupon->code, 'is_active' => $coupon->is_active],
            $request
        );

        return back()->with('success', $coupon->is_active ? 'Đã bật mã giảm giá.' : 'Đã tắt mã giảm giá.');
    }

    public function destroy(Request $request, Coupon $coupon): RedirectResponse
    {
        // Chặn xóa nếu mã giảm giá đã được áp dụng trong bất kỳ đơn hàng nào
        $orderCount = Order::where('coupon_id', $coupon->id)->count();
        if ($orderCount > 0) {
            return back()->with('error', 'Không thể xóa mã giảm giá này vì đã có ' . $orderCount . ' đơn hàng sử dụng mã. Vui lòng tắt trạng thái hoạt động thay vì xóa.');
        }

        $couponId = $coupon->id;
        $couponCode = $coupon->code;
        $coupon->delete();

        ActivityLogService::log(
            auth()->id(),
            'delete_coupon',
            Coupon::class,
            $couponId,
            ['code' => $couponCode],
            $request
        );

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Đã xóa mã giảm giá thành công.');
    }
}
