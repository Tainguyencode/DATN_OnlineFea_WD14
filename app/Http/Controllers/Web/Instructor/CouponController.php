<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreCouponRequest;
use App\Models\Coupon;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $instructor = auth()->user();

        $coupons = Coupon::query()
            ->where('creator_type', 'instructor')
            ->where('instructor_id', $instructor->id)
            ->with('course:id,title')
            ->latest()
            ->paginate(10);

        $stats = Coupon::where('creator_type', 'instructor')
            ->where('instructor_id', $instructor->id)
            ->selectRaw('COUNT(*) as total, SUM(is_active = 1) as active, SUM(is_active = 0) as inactive')
            ->first();

        $stats = [
            'total' => (int) $stats->total,
            'active' => (int) $stats->active,
            'inactive' => (int) $stats->inactive,
        ];

        return view('instructor.coupons.index', compact('coupons', 'stats'));
    }

    public function create(): View
    {
        $instructor = auth()->user();
        $courses = Course::where('instructor_id', $instructor->id)
            ->where('status', 'published')
            ->where('is_published', true)
            ->get(['id', 'title']);

        return view('instructor.coupons.create', [
            'coupon' => new Coupon([
                'type' => 'percent',
                'is_active' => true,
            ]),
            'courses' => $courses,
        ]);
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $instructor = auth()->user();
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);
        $data['creator_type'] = 'instructor';
        $data['instructor_id'] = $instructor->id;
        $data['is_active'] = $request->boolean('is_active', true);

        Coupon::create($data);

        return redirect()
            ->route('instructor.coupons.index')
            ->with('success', "Tạo mã giảm giá {$data['code']} thành công!");
    }

    public function edit(Coupon $coupon): View
    {
        $this->authorizeCouponOwner($coupon);

        $instructor = auth()->user();
        $courses = Course::where('instructor_id', $instructor->id)
            ->where('status', 'published')
            ->where('is_published', true)
            ->get(['id', 'title']);

        return view('instructor.coupons.edit', compact('coupon', 'courses'));
    }

    public function update(StoreCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $this->authorizeCouponOwner($coupon);

        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');

        $coupon->update($data);

        return redirect()
            ->route('instructor.coupons.index')
            ->with('success', "Cập nhật mã giảm giá {$coupon->code} thành công!");
    }

    public function toggleStatus(Request $request, Coupon $coupon): RedirectResponse
    {
        $this->authorizeCouponOwner($coupon);

        $coupon->update(['is_active' => ! $coupon->is_active]);

        $statusMessage = $coupon->is_active ? 'kích hoạt' : 'vô hiệu hóa';

        return back()->with('success', "Đã {$statusMessage} mã {$coupon->code}.");
    }

    public function destroy(Request $request, Coupon $coupon): RedirectResponse
    {
        $this->authorizeCouponOwner($coupon);

        $code = $coupon->code;
        $coupon->delete();

        return redirect()
            ->route('instructor.coupons.index')
            ->with('success', "Đã xóa mã giảm giá {$code}.");
    }

    private function authorizeCouponOwner(Coupon $coupon): void
    {
        if ($coupon->creator_type !== 'instructor' || (int) $coupon->instructor_id !== (int) auth()->id()) {
            abort(403, 'Bạn không có quyền quản lý mã giảm giá này.');
        }
    }
}
