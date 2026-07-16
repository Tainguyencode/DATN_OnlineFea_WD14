<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\RecentlyViewedCourse;
use App\Services\RecentlyViewedCourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class RecentlyViewedCourseController extends Controller
{
    public function __construct(
        private readonly RecentlyViewedCourseService $recentlyViewedCourseService,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isStudent(), 403);

        $histories = $this->recentlyViewedCourseService
            ->queryVisibleForUser($request->user())
            ->paginate(9)
            ->withQueryString();

        $enrollmentMap = $this->enrollmentMap(
            $request->user()->id,
            collect($histories->items())->pluck('course_id')
        );

        return view('student.recently-viewed-courses.index', compact('histories', 'enrollmentMap'));
    }

    public function destroy(Request $request, int $recentlyViewedCourse): RedirectResponse
    {
        abort_unless($request->user()?->isStudent(), 403);

        $deleted = RecentlyViewedCourse::query()
            ->where('user_id', $request->user()->id)
            ->whereKey($recentlyViewedCourse)
            ->delete();

        return back()->with(
            $deleted ? 'success' : 'error',
            $deleted ? 'Đã xóa khóa học khỏi lịch sử xem gần đây.' : 'Không tìm thấy lịch sử phù hợp để xóa.'
        );
    }

    public function clear(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isStudent(), 403);

        RecentlyViewedCourse::query()
            ->where('user_id', $request->user()->id)
            ->delete();

        return back()->with('success', 'Đã xóa toàn bộ lịch sử xem gần đây.');
    }

    private function enrollmentMap(int $userId, Collection $courseIds): Collection
    {
        return Enrollment::query()
            ->where('user_id', $userId)
            ->whereIn('course_id', $courseIds->filter()->unique()->values())
            ->withLearningAccess()
            ->get()
            ->keyBy('course_id');
    }
}
