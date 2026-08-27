<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Models\Coupon;
use App\Models\MonthlyRewardLog;
use App\Models\Order;
use App\Models\SystemSetting;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');

        $coupons = Coupon::query()
            ->with('instructor:id,name')
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
                'value' => 0,
            ]),
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
            return back()->with('error', 'Không thể xóa mã giảm giá này vì đã có '.$orderCount.' đơn hàng sử dụng mã. Vui lòng tắt trạng thái hoạt động thay vì xóa.');
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

    /**
     * Giao diện cấu hình phần thưởng TOP Bảng Xếp Hạng Tháng & Tuần.
     */
    public function rewardConfigForm(): View
    {
        $configs = [
            1 => [
                'type' => SystemSetting::get('leaderboard_reward_top1_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_reward_top1_value', 40),
                'expiry_days' => SystemSetting::get('leaderboard_reward_top1_expiry_days', 30),
            ],
            2 => [
                'type' => SystemSetting::get('leaderboard_reward_top2_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_reward_top2_value', 30),
                'expiry_days' => SystemSetting::get('leaderboard_reward_top2_expiry_days', 30),
            ],
            3 => [
                'type' => SystemSetting::get('leaderboard_reward_top3_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_reward_top3_value', 20),
                'expiry_days' => SystemSetting::get('leaderboard_reward_top3_expiry_days', 30),
            ],
            '4_9' => [
                'type' => SystemSetting::get('leaderboard_reward_top4_9_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_reward_top4_9_value', 15),
                'expiry_days' => SystemSetting::get('leaderboard_reward_top4_9_expiry_days', 30),
            ],
            '10_50' => [
                'type' => SystemSetting::get('leaderboard_reward_top10_50_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_reward_top10_50_value', 10),
                'expiry_days' => SystemSetting::get('leaderboard_reward_top10_50_expiry_days', 30),
            ],
        ];

        $weeklyConfigs = [
            1 => [
                'type' => SystemSetting::get('leaderboard_weekly_reward_top1_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_weekly_reward_top1_value', 30),
                'expiry_days' => SystemSetting::get('leaderboard_weekly_reward_top1_expiry_days', 7),
            ],
            2 => [
                'type' => SystemSetting::get('leaderboard_weekly_reward_top2_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_weekly_reward_top2_value', 20),
                'expiry_days' => SystemSetting::get('leaderboard_weekly_reward_top2_expiry_days', 7),
            ],
            3 => [
                'type' => SystemSetting::get('leaderboard_weekly_reward_top3_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_weekly_reward_top3_value', 15),
                'expiry_days' => SystemSetting::get('leaderboard_weekly_reward_top3_expiry_days', 7),
            ],
            '4_10' => [
                'type' => SystemSetting::get('leaderboard_weekly_reward_top4_10_type', 'percent'),
                'value' => SystemSetting::get('leaderboard_weekly_reward_top4_10_value', 10),
                'expiry_days' => SystemSetting::get('leaderboard_weekly_reward_top4_10_expiry_days', 7),
            ],
        ];

        return view('admin.coupons.reward_config', compact('configs', 'weeklyConfigs'));
    }

    /**
     * Lưu cấu hình phần thưởng TOP Bảng Xếp Hạng Tháng & Tuần.
     */
    public function rewardConfigStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Monthly rules
            'top1_type' => 'required|in:fixed,percent',
            'top1_value' => 'required|numeric|min:0.01',
            'top1_expiry_days' => 'required|integer|min:1',

            'top2_type' => 'required|in:fixed,percent',
            'top2_value' => 'required|numeric|min:0.01',
            'top2_expiry_days' => 'required|integer|min:1',

            'top3_type' => 'required|in:fixed,percent',
            'top3_value' => 'required|numeric|min:0.01',
            'top3_expiry_days' => 'required|integer|min:1',

            'top4_9_type' => 'required|in:fixed,percent',
            'top4_9_value' => 'required|numeric|min:0.01',
            'top4_9_expiry_days' => 'required|integer|min:1',

            'top10_50_type' => 'required|in:fixed,percent',
            'top10_50_value' => 'required|numeric|min:0.01',
            'top10_50_expiry_days' => 'required|integer|min:1',

            // Weekly rules (Top 1, Top 2, Top 3, Top 4 - 10)
            'weekly_top1_type' => 'required|in:fixed,percent',
            'weekly_top1_value' => 'required|numeric|min:0.01',
            'weekly_top1_expiry_days' => 'required|integer|min:1',

            'weekly_top2_type' => 'required|in:fixed,percent',
            'weekly_top2_value' => 'required|numeric|min:0.01',
            'weekly_top2_expiry_days' => 'required|integer|min:1',

            'weekly_top3_type' => 'required|in:fixed,percent',
            'weekly_top3_value' => 'required|numeric|min:0.01',
            'weekly_top3_expiry_days' => 'required|integer|min:1',

            'weekly_top4_10_type' => 'required|in:fixed,percent',
            'weekly_top4_10_value' => 'required|numeric|min:0.01',
            'weekly_top4_10_expiry_days' => 'required|integer|min:1',
        ], [
            'top1_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 1 (Tháng).',
            'top2_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 2 (Tháng).',
            'top3_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 3 (Tháng).',
            'top4_9_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 4 - TOP 9 (Tháng).',
            'top10_50_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 10 - TOP 50 (Tháng).',

            'weekly_top1_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 1 (Tuần).',
            'weekly_top2_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 2 (Tuần).',
            'weekly_top3_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 3 (Tuần).',
            'weekly_top4_10_value.required' => 'Vui lòng nhập giá trị giảm cho TOP 4 - TOP 10 (Tuần).',
        ]);

        // Save monthly settings
        SystemSetting::set('leaderboard_reward_top1_type', $validated['top1_type']);
        SystemSetting::set('leaderboard_reward_top1_value', $validated['top1_value']);
        SystemSetting::set('leaderboard_reward_top1_expiry_days', $validated['top1_expiry_days']);

        SystemSetting::set('leaderboard_reward_top2_type', $validated['top2_type']);
        SystemSetting::set('leaderboard_reward_top2_value', $validated['top2_value']);
        SystemSetting::set('leaderboard_reward_top2_expiry_days', $validated['top2_expiry_days']);

        SystemSetting::set('leaderboard_reward_top3_type', $validated['top3_type']);
        SystemSetting::set('leaderboard_reward_top3_value', $validated['top3_value']);
        SystemSetting::set('leaderboard_reward_top3_expiry_days', $validated['top3_expiry_days']);

        SystemSetting::set('leaderboard_reward_top4_9_type', $validated['top4_9_type']);
        SystemSetting::set('leaderboard_reward_top4_9_value', $validated['top4_9_value']);
        SystemSetting::set('leaderboard_reward_top4_9_expiry_days', $validated['top4_9_expiry_days']);

        SystemSetting::set('leaderboard_reward_top10_50_type', $validated['top10_50_type']);
        SystemSetting::set('leaderboard_reward_top10_50_value', $validated['top10_50_value']);
        SystemSetting::set('leaderboard_reward_top10_50_expiry_days', $validated['top10_50_expiry_days']);

        // Save weekly settings
        SystemSetting::set('leaderboard_weekly_reward_top1_type', $validated['weekly_top1_type']);
        SystemSetting::set('leaderboard_weekly_reward_top1_value', $validated['weekly_top1_value']);
        SystemSetting::set('leaderboard_weekly_reward_top1_expiry_days', $validated['weekly_top1_expiry_days']);

        SystemSetting::set('leaderboard_weekly_reward_top2_type', $validated['weekly_top2_type']);
        SystemSetting::set('leaderboard_weekly_reward_top2_value', $validated['weekly_top2_value']);
        SystemSetting::set('leaderboard_weekly_reward_top2_expiry_days', $validated['weekly_top2_expiry_days']);

        SystemSetting::set('leaderboard_weekly_reward_top3_type', $validated['weekly_top3_type']);
        SystemSetting::set('leaderboard_weekly_reward_top3_value', $validated['weekly_top3_value']);
        SystemSetting::set('leaderboard_weekly_reward_top3_expiry_days', $validated['weekly_top3_expiry_days']);

        SystemSetting::set('leaderboard_weekly_reward_top4_10_type', $validated['weekly_top4_10_type']);
        SystemSetting::set('leaderboard_weekly_reward_top4_10_value', $validated['weekly_top4_10_value']);
        SystemSetting::set('leaderboard_weekly_reward_top4_10_expiry_days', $validated['weekly_top4_10_expiry_days']);

        return redirect()
            ->route('admin.coupons.reward_config')
            ->with('success', '🏆 Cấu hình phần thưởng TOP Bảng Xếp Hạng Tháng & Tuần đã được lưu thành công.');
    }

    /**
     * Chạy trao thưởng thủ công ngay lập tức từ Admin UI cho tháng chỉ định.
     */
    public function rewardRunNow(Request $request): RedirectResponse
    {
        $period = $request->input('period', now()->subMonth()->format('Y-m'));

        Artisan::call('leaderboard:reward-monthly', [
            '--period' => $period,
        ]);

        $output = Artisan::output();

        return redirect()
            ->route('admin.coupons.reward_history')
            ->with('success', "Đã kích hoạt tiến trình trao thưởng tháng {$period}. Kết quả: ".trim($output));
    }

    /**
     * Chạy trao thưởng thủ công ngay lập tức từ Admin UI cho tuần chỉ định.
     */
    public function rewardWeeklyRunNow(Request $request): RedirectResponse
    {
        $period = $request->input('period', now()->subWeek()->format('o-\WW'));

        Artisan::call('leaderboard:reward-weekly', [
            '--period' => $period,
        ]);

        $output = Artisan::output();

        return redirect()
            ->route('admin.coupons.reward_history')
            ->with('success', "Đã kích hoạt tiến trình trao thưởng tuần {$period}. Kết quả: " . trim($output));
    }

    /**
     * Hiển thị Lịch sử tự động trao thưởng TOP Bảng Xếp Hạng Tháng.
     */
    public function rewardHistory(Request $request): View
    {
        $rewards = MonthlyRewardLog::query()
            ->with(['user:id,name,email,avatar', 'coupon', 'userCoupon'])
            ->orderByDesc('period_key')
            ->orderBy('rank')
            ->paginate(15);

        return view('admin.coupons.reward_history', compact('rewards'));
    }
}
