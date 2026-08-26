<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    /**
     * Hiển thị trang Kho Voucher thuộc về học viên hiện tại.
     * Bao gồm cả Voucher được tặng riêng và toàn bộ Voucher công khai do Admin & Giảng viên tạo.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $statusFilter = $request->query('status', 'active');

        // 1. Lấy tất cả voucher cá nhân trong kho của học viên (được tặng riêng hoặc đã lưu)
        $userCoupons = UserCoupon::where('user_id', $userId)
            ->with(['coupon.instructor', 'coupon.course'])
            ->orderByDesc('saved_at')
            ->get();

        $existingCouponIds = $userCoupons->pluck('coupon_id')->filter()->unique()->toArray();

        // 2. Lấy các voucher công khai của Hệ thống (Admin) và Giảng viên
        $publicCoupons = Coupon::where('is_active', true)
            ->where('is_private', false)
            ->whereNotIn('id', $existingCouponIds)
            ->with(['instructor', 'course'])
            ->orderByDesc('created_at')
            ->get();

        // Wrap các public coupon thành UserCoupon giả lập để thống nhất định dạng hiển thị
        $publicUserCoupons = $publicCoupons->map(function (Coupon $coupon) use ($userId) {
            $userCoupon = new UserCoupon;
            $userCoupon->user_id = $userId;
            $userCoupon->coupon_id = $coupon->id;
            $userCoupon->source = $coupon->creator_type === 'instructor' ? 'instructor' : 'system';
            $userCoupon->reason = null;
            $userCoupon->used_at = null;
            $userCoupon->setRelation('coupon', $coupon);

            return $userCoupon;
        });

        // 3. Hợp nhất hai danh sách
        $allCoupons = $userCoupons->concat($publicUserCoupons);

        // 4. Gắn trạng thái tính toán động & Nhãn phạm vi hiển thị cho từng voucher
        $allCoupons->transform(function (UserCoupon $item) use ($userId) {
            $coupon = $item->coupon;

            if (! $coupon) {
                $item->computed_status = 'expired';
                $item->status_label = 'Không khả dụng';
                $item->scope_label = 'Không khả dụng';
                $item->creator_tag = 'Không khả dụng';

                return $item;
            }

            // Tính toán trạng thái
            $isUsed = $item->used_at !== null || $coupon->isUsedByUser($userId);
            $isExpired = ! $isUsed && $coupon->expires_at && $coupon->expires_at->isPast();

            if ($isUsed) {
                $item->computed_status = 'used';
                $item->status_label = 'Đã sử dụng';
            } elseif ($isExpired) {
                $item->computed_status = 'expired';
                $item->status_label = 'Hết hạn';
            } elseif ($coupon->isValid()) {
                $item->computed_status = 'active';
                $item->status_label = 'Còn hiệu lực';
            } else {
                $item->computed_status = 'expired';
                $item->status_label = 'Hết hiệu lực';
            }

            // Gắn Creator Tag
            if ($item->source === 'leaderboard') {
                $item->creator_tag = '🏆 Thưởng TOP tháng';
            } elseif ($item->source === 'admin') {
                $item->creator_tag = '🎁 Admin tặng';
            } elseif ($coupon->creator_type === 'instructor') {
                $item->creator_tag = '👨‍🏫 Giảng viên';
            } else {
                $item->creator_tag = '🌐 Hệ thống';
            }

            // Gắn Scope Label
            if ($coupon->creator_type === 'instructor') {
                $instructorName = $coupon->instructor?->name ?? 'Giảng viên';
                if ($coupon->course) {
                    $item->scope_label = "Khóa học {$coupon->course->title} ({$instructorName})";
                } else {
                    $item->scope_label = "Tất cả khóa học của {$instructorName}";
                }
            } else {
                // Admin / System Coupon
                if ($coupon->course) {
                    $item->scope_label = "Khóa học {$coupon->course->title}";
                } else {
                    $item->scope_label = 'Tất cả khóa học trên hệ thống';
                }
            }

            return $item;
        });

        // Thống kê số lượng theo trạng thái
        $counts = [
            'all' => $allCoupons->count(),
            'active' => $allCoupons->where('computed_status', 'active')->count(),
            'used' => $allCoupons->where('computed_status', 'used')->count(),
            'expired' => $allCoupons->where('computed_status', 'expired')->count(),
        ];

        // Lọc danh sách theo tab filter đang chọn
        $filteredCoupons = match ($statusFilter) {
            'active' => $allCoupons->where('computed_status', 'active'),
            'used' => $allCoupons->where('computed_status', 'used'),
            'expired' => $allCoupons->where('computed_status', 'expired'),
            default => $allCoupons,
        };

        return view('student.vouchers.index', [
            'userCoupons' => $filteredCoupons,
            'currentFilter' => $statusFilter,
            'counts' => $counts,
        ]);
    }
}
