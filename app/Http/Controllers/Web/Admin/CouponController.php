<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Models\UserCoupon;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
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
     * Hiển thị giao diện Admin tạo & tặng voucher riêng cho học viên.
     */
    public function grantForm(Request $request): View
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        // Subquery tính tổng điểm XP trong tháng hiện tại
        $pointsSubquery = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as period_xp'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('user_id');

        // Lấy danh sách Học viên TOP tháng (được xếp hạng theo XP)
        $topStudents = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->joinSub($pointsSubquery, 'points_table', 'users.id', '=', 'points_table.user_id')
            ->select('users.*', 'points_table.period_xp')
            ->orderByDesc('points_table.period_xp')
            ->get();

        $topUserIds = $topStudents->pluck('id')->toArray();

        // Lấy tất cả học viên còn lại
        $otherStudents = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->whereNotIn('id', $topUserIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $courses = Course::query()
            ->published()
            ->orderBy('title')
            ->get(['id', 'title']);

        $selectedUserId = $request->query('user_id');

        // Mặc định chọn TOP 1 học viên tháng này nếu chưa chỉ định user_id
        if (! $selectedUserId && $topStudents->isNotEmpty()) {
            $selectedUserId = $topStudents->first()->id;
        }

        return view('admin.coupons.grant', compact('topStudents', 'otherStudents', 'courses', 'selectedUserId'));
    }

    /**
     * Xử lý Admin khởi tạo voucher riêng và tặng cho học viên.
     */
    public function grantStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'course_id' => 'nullable|exists:courses,id',
            'expires_at' => 'nullable|date',
            'reason' => 'required|string|max:255',
        ], [
            'user_id.required' => 'Vui lòng chọn học viên nhận voucher.',
            'user_id.exists' => 'Học viên không tồn tại trong hệ thống.',
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.max' => 'Mã voucher không vượt quá 50 ký tự.',
            'code.unique' => 'Mã voucher này đã tồn tại trong hệ thống, vui lòng đổi mã khác.',
            'type.required' => 'Vui lòng chọn loại giảm giá.',
            'type.in' => 'Loại giảm giá không hợp lệ.',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
            'value.min' => 'Giá trị giảm phải lớn hơn 0.',
            'max_uses.integer' => 'Số lượt sử dụng phải là số nguyên.',
            'max_uses.min' => 'Số lượt sử dụng phải lớn hơn hoặc bằng 1.',
            'reason.required' => 'Vui lòng nhập lý do tặng voucher.',
            'reason.max' => 'Lý do tặng không vượt quá 255 ký tự.',
        ]);

        $student = User::where('id', $validated['user_id'])
            ->where('role', 'student')
            ->firstOrFail();

        // 1. Tạo Voucher riêng (is_private = true)
        $coupon = Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_order_amount' => $validated['min_order_amount'] ?? 0,
            'max_uses' => $validated['max_uses'] ?? 1,
            'course_id' => $validated['course_id'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
            'is_private' => true,
            'creator_type' => 'admin',
        ]);

        // 2. Tạo bản ghi liên kết UserCoupon dành riêng cho học viên
        UserCoupon::create([
            'user_id' => $student->id,
            'coupon_id' => $coupon->id,
            'source' => 'admin',
            'reason' => $validated['reason'],
            'granted_by' => auth()->id(),
            'granted_at' => now(),
            'saved_at' => now(),
        ]);

        // 3. Gửi thông báo cho học viên qua NotificationService
        $discountText = $coupon->type === 'percent'
            ? (int) $coupon->value.'%'
            : number_format($coupon->value, 0, ',', '.').'đ';

        app(NotificationService::class)->send(
            $student,
            '🎁 Bạn vừa nhận được voucher!',
            "Admin đã tặng bạn voucher riêng \"{$coupon->code}\" (Giảm {$discountText}). Mã: {$coupon->code}. Lý do: \"{$validated['reason']}\". Voucher đã được thêm vào Kho Voucher của bạn.",
            'voucher_granted',
            route('student.vouchers.index')
        );

        ActivityLogService::log(
            auth()->id(),
            'create_and_grant_private_voucher',
            UserCoupon::class,
            $coupon->id,
            [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'coupon_code' => $coupon->code,
                'reason' => $validated['reason'],
            ],
            $request
        );

        return redirect()
            ->route('admin.coupons.grant_history')
            ->with('success', "🎁 Đã tạo và tặng thành công voucher riêng {$coupon->code} cho học viên {$student->name}.");
    }

    /**
     * Xem lịch sử Admin tặng voucher cho học viên.
     */
    public function grantHistory(Request $request): View
    {
        $grants = UserCoupon::query()
            ->where('source', 'admin')
            ->with(['user:id,name,email,avatar', 'coupon', 'grantedBy:id,name,email'])
            ->orderByDesc('granted_at')
            ->paginate(15);

        return view('admin.coupons.grant_history', compact('grants'));
    }
}
