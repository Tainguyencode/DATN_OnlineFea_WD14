<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorController extends Controller
{
    /**
     * Danh sách giảng viên (public).
     */
    public function index(Request $request): View
    {
        $search    = trim($request->get('search', ''));
        $specialty = $request->get('specialty', '');
        $sort      = $request->get('sort', 'newest');

        // ── Subquery đếm học viên — dùng trong ORDER BY, không dùng trong SELECT ──
        $studentsOrderSub = Enrollment::selectRaw('COUNT(DISTINCT enrollments.user_id)')
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->whereColumn('courses.instructor_id', 'users.id')
            ->where(function ($q) {
                $q->where('courses.is_published', true)
                  ->orWhereIn('courses.status', [Course::STATUS_PUBLISHED, Course::STATUS_PENDING_UPDATE]);
            });

        // ── Query chính (KHÔNG dùng addSelect correlated subquery để tránh
        //    phá COUNT query của paginate) ─────────────────────────────────────
        $query = User::query()
            ->where('role', 'instructor')
            ->where('instructor_status', 'approved')
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('account_status')->orWhereNotIn('account_status', ['locked', 'suspended']))
            ->withCount([
                'courses as courses_count' => fn ($q) => $q->published(),
            ])
            ->with([
                'courses' => fn ($q) => $q->published()
                    ->with('category:id,name,slug,parent_id')
                    ->select(['id', 'instructor_id', 'category_id', 'rating_avg', 'rating_count']),
            ]);

        // ── Tìm kiếm theo tên / email ────────────────────────────────────────
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ── Lọc theo chuyên môn (category) ───────────────────────────────────
        if (!empty($specialty)) {
            $query->whereHas('courses', fn ($q) => $q->published()
                ->where('category_id', $specialty)
            );
        }

        // ── Sắp xếp ──────────────────────────────────────────────────────────
        match ($sort) {
            'courses'  => $query->orderByDesc('courses_count'),
            'students' => $query->orderByDesc($studentsOrderSub),  // subquery chỉ trong ORDER BY
            'rating'   => $query->orderByDesc(
                Course::selectRaw('AVG(rating_avg)')
                    ->whereColumn('instructor_id', 'users.id')
                    ->where(function ($q) {
                        $q->where('is_published', true)
                          ->orWhereIn('status', [Course::STATUS_PUBLISHED, Course::STATUS_PENDING_UPDATE]);
                    })
                    ->limit(1)
            ),
            'name'     => $query->orderBy('name'),
            default    => $query->orderByDesc('created_at'),
        };

        $instructors = $query->paginate(12)->withQueryString();

        // ── Tính students_count + average_rating sau paginate ────────────────
        foreach ($instructors as $instructor) {
            // Số học viên
            $instructor->students_count = Enrollment::join('courses', 'courses.id', '=', 'enrollments.course_id')
                ->where('courses.instructor_id', $instructor->id)
                ->where(function ($q) {
                    $q->where('courses.is_published', true)
                      ->orWhereIn('courses.status', [Course::STATUS_PUBLISHED, Course::STATUS_PENDING_UPDATE]);
                })
                ->distinct('enrollments.user_id')
                ->count('enrollments.user_id');

            // Rating trung bình
            $avgRating = $instructor->courses->whereNotNull('rating_avg')->avg('rating_avg');
            $instructor->average_rating     = round((float) $avgRating, 1);
            $instructor->total_rating_count = (int) $instructor->courses->sum('rating_count');
        }

        // ── Toàn bộ danh mục cha + con trong DB (không lọc active) ──────────
        $specialties = Category::query()
            ->whereNull('parent_id')          // chỉ lấy danh mục cha
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('student.instructors.index', compact('instructors', 'specialties', 'search', 'specialty', 'sort'));
    }

    /**
     * Trang hồ sơ giảng viên (public).
     */
    public function show(Request $request, User $user): View
    {
        // Chỉ cho xem profile instructor đã duyệt và còn active, không bị khóa
        abort_unless(
            $user->role === 'instructor'
            && $user->instructor_status === 'approved'
            && $user->is_active
            && ! $user->isLocked(),
            404
        );

        // ── Load courses của instructor ───────────────────────────────────────
        $coursesQuery = Course::published()
            ->where('instructor_id', $user->id)
            ->with(['category:id,name,slug,parent_id', 'category.parent:id,name,slug'])
            ->withCount('lessons')
            ->orderByDesc('published_at');

        $courses = $coursesQuery->paginate(8)->withQueryString();

        // ── Thống kê tổng hợp ────────────────────────────────────────────────
        $totalCourses  = Course::published()
            ->where('instructor_id', $user->id)
            ->count();

        $totalStudents = Enrollment::whereHas('course', fn ($q) => $q->published()
            ->where('instructor_id', $user->id)
        )
            ->distinct('user_id')
            ->count('user_id');

        $avgRatingData = Course::published()
            ->where('instructor_id', $user->id)
            ->whereNotNull('rating_avg')
            ->selectRaw('AVG(rating_avg) as avg_rating, SUM(rating_count) as total_reviews')
            ->first();

        $avgRating    = round((float) ($avgRatingData->avg_rating ?? 0), 1);
        $totalReviews = (int) ($avgRatingData->total_reviews ?? 0);

        return view('student.instructors.show', compact(
            'user', 'courses',
            'totalCourses', 'totalStudents', 'avgRating', 'totalReviews'
        ));
    }
}
