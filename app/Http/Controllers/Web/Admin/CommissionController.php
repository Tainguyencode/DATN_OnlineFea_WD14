<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $rateFilter = (string) $request->query('rate_filter');

        $defaultRate = (float) SystemSetting::get('default_commission_rate', config('course.default_commission_rate', 20.00));

        // Overall stats across paid order items
        $totalCommissionEarned = (float) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'paid')
            ->sum('order_items.commission_amount');

        $totalInstructorEarnings = (float) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'paid')
            ->sum('order_items.instructor_earning');

        $totalGrossSales = (float) DB::table('orders')
            ->where('status', 'paid')
            ->sum('total_amount');

        // Instructor query
        $instructorsQuery = User::query()
            ->where('role', 'instructor')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($rateFilter === 'custom', fn ($q) => $q->whereNotNull('commission_rate'))
            ->when($rateFilter === 'default', fn ($q) => $q->whereNull('commission_rate'));

        $totalInstructors = User::where('role', 'instructor')->count();
        $customRateCount = User::where('role', 'instructor')->whereNotNull('commission_rate')->count();

        $instructors = $instructorsQuery
            ->withCount('courses')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Calculate sales stats per instructor in current page
        $instructorIds = $instructors->pluck('id')->toArray();

        $instructorSalesData = [];
        if (! empty($instructorIds)) {
            $salesStats = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('courses', 'courses.id', '=', 'order_items.course_id')
                ->whereIn('courses.instructor_id', $instructorIds)
                ->where('orders.status', 'paid')
                ->select(
                    'courses.instructor_id',
                    DB::raw('SUM(order_items.commission_amount + order_items.instructor_earning) as total_sales'),
                    DB::raw('SUM(order_items.commission_amount) as total_commission'),
                    DB::raw('SUM(order_items.instructor_earning) as total_earning'),
                    DB::raw('COUNT(DISTINCT order_items.order_id) as total_orders')
                )
                ->groupBy('courses.instructor_id')
                ->get()
                ->keyBy('instructor_id');

            $instructorSalesData = $salesStats->mapWithKeys(function ($stat, $instId): array {
                return [$instId => [
                    'total_sales' => (float) $stat->total_sales,
                    'total_commission' => (float) $stat->total_commission,
                    'total_earning' => (float) $stat->total_earning,
                    'total_orders' => (int) $stat->total_orders,
                ]];
            })->all();
        }

        $stats = [
            'total_commission' => $totalCommissionEarned,
            'total_instructor_earnings' => $totalInstructorEarnings,
            'total_gross' => $totalGrossSales,
            'default_rate' => $defaultRate,
            'total_instructors' => $totalInstructors,
            'custom_rate_count' => $customRateCount,
        ];

        return view('admin.commissions.index', [
            'instructors' => $instructors,
            'instructorSalesData' => $instructorSalesData,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'rate_filter' => $rateFilter,
            ],
        ]);
    }

    public function updateDefaultRate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'default_commission_rate.required' => 'Vui lòng nhập tỷ lệ chiết khấu mặc định.',
            'default_commission_rate.numeric' => 'Tỷ lệ chiết khấu phải là con số.',
            'default_commission_rate.min' => 'Tỷ lệ chiết khấu tối thiểu là 0%.',
            'default_commission_rate.max' => 'Tỷ lệ chiết khấu tối đa là 100%.',
        ]);

        $oldRate = SystemSetting::get('default_commission_rate', config('course.default_commission_rate', 20.00));
        $newRate = (float) $validated['default_commission_rate'];

        SystemSetting::set('default_commission_rate', $newRate);

        ActivityLogService::log(
            auth()->id(),
            'update_default_commission_rate',
            SystemSetting::class,
            null,
            ['old_rate' => $oldRate, 'new_rate' => $newRate],
            $request,
            "Cập nhật tỷ lệ chiết khấu mặc định từ {$oldRate}% thành {$newRate}%"
        );

        return back()->with('success', "Cập nhật tỷ lệ chiết khấu mặc định hệ thống thành {$newRate}% thành công!");
    }

    public function updateInstructorRate(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->withErrors(['error' => 'Người dùng này không phải là Giảng viên.']);
        }

        $validated = $request->validate([
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ], [
            'commission_rate.numeric' => 'Tỷ lệ chiết khấu phải là con số.',
            'commission_rate.min' => 'Tỷ lệ chiết khấu tối thiểu là 0%.',
            'commission_rate.max' => 'Tỷ lệ chiết khấu tối đa là 100%.',
        ]);

        $oldRate = $user->commission_rate;
        $newRate = $validated['commission_rate'] !== null && $validated['commission_rate'] !== ''
            ? (float) $validated['commission_rate']
            : null;

        $user->update(['commission_rate' => $newRate]);

        ActivityLogService::log(
            auth()->id(),
            'update_instructor_commission_rate',
            User::class,
            $user->id,
            ['instructor_id' => $user->id, 'old_rate' => $oldRate, 'new_rate' => $newRate],
            $request,
            "Cập nhật tỷ lệ chiết khấu cho giảng viên {$user->name} (#{$user->id})"
        );

        $msg = $newRate !== null
            ? "Đã cập nhật tỷ lệ chiết khấu riêng cho {$user->name} thành {$newRate}%!"
            : "Đã khôi phục tỷ lệ chiết khấu của {$user->name} về mặc định hệ thống!";

        return back()->with('success', $msg);
    }
}
