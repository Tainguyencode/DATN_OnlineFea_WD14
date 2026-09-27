<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $status = in_array($status, ['all', 'active', 'used', 'expired'], true) ? $status : 'active';
        $usedCouponIds = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'paid')
            ->whereNotNull('coupon_id')
            ->pluck('coupon_id')
            ->flip();

        $allCoupons = UserCoupon::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('coupon')
            ->with(['coupon.instructor:id,name', 'coupon.course:id,title'])
            ->latest('saved_at')
            ->latest('id')
            ->get()
            ->each(function (UserCoupon $item) use ($usedCouponIds): void {
                $coupon = $item->coupon;
                $used = $item->used_at !== null || $usedCouponIds->has($coupon->id);
                $expired = ! $used && (($coupon->expires_at?->isPast() ?? false) || ! $coupon->isValid());

                $item->computed_status = $used ? 'used' : ($expired ? 'expired' : 'active');
                $item->scope_label = $coupon->course
                    ? 'Khóa học '.$coupon->course->title
                    : ($coupon->instructor
                        ? 'Khóa học của '.$coupon->instructor->name
                        : 'Tất cả khóa học');
            });

        $counts = [
            'all' => $allCoupons->count(),
            'active' => $allCoupons->where('computed_status', 'active')->count(),
            'used' => $allCoupons->where('computed_status', 'used')->count(),
            'expired' => $allCoupons->where('computed_status', 'expired')->count(),
        ];

        $userCoupons = $status === 'all'
            ? $allCoupons
            : $allCoupons->where('computed_status', $status)->values();

        return view('student.dashboard.vouchers.index', compact('userCoupons', 'counts', 'status'));
    }

    public function show(Request $request, Coupon $coupon): View
    {
        $user = $request->user();
        $userCoupon = UserCoupon::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->first();

        $hasUsedOrder = Order::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->where('status', 'paid')
            ->exists();

        if ($coupon->is_private && ! $userCoupon && ! $hasUsedOrder) {
            abort(403, 'Bạn không có quyền truy cập mã giảm giá này.');
        }

        $coupon->load(['instructor:id,name', 'course:id,title,slug,thumbnail,price,discount_price']);

        $isUsed = ($userCoupon && $userCoupon->used_at !== null) || $hasUsedOrder;
        $isExpired = ! $isUsed && (($coupon->expires_at?->isPast() ?? false) || ! $coupon->isValid());
        $computedStatus = $isUsed ? 'used' : ($isExpired ? 'expired' : 'active');

        $scopeLabel = $coupon->course
            ? 'Khóa học: '.$coupon->course->title
            : ($coupon->instructor
                ? 'Tất cả khóa học của giảng viên '.$coupon->instructor->name
                : 'Toàn bộ khóa học trên hệ thống');

        return view('student.dashboard.vouchers.show', compact(
            'coupon',
            'userCoupon',
            'computedStatus',
            'scopeLabel'
        ));
    }
}
