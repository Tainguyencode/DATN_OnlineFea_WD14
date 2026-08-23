<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LearningPath;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningPathController extends Controller
{
    /**
     * Danh sách tất cả lộ trình học tập công khai.
     */
    public function index(Request $request): View
    {
        $query = LearningPath::query()
            ->withCount('courses')
            ->with(['courses' => function ($q) {
                $q->published()->withCount('lessons');
            }]);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('target_role', 'like', "%{$search}%");
            });
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        $learningPaths = $query->latest()->paginate(9)->withQueryString();

        $stats = [
            'total_paths' => LearningPath::count(),
            'total_courses' => LearningPath::withCount('courses')->get()->sum('courses_count'),
            'total_students' => Enrollment::distinct('user_id')->count('user_id'),
        ];

        return view('learning-paths.index', compact('learningPaths', 'stats'));
    }

    /**
     * Chi tiết mốc lộ trình theo Giai đoạn (Visual Stage-based Roadmap) & Khóa học gợi ý.
     */
    public function show(string $slug): View
    {
        $learningPath = LearningPath::where('slug', $slug)
            ->with(['courses' => function ($q) {
                $q->published()
                    ->with([
                        'instructor:id,name,avatar',
                        'category:id,name,slug',
                        'chapters' => fn ($cq) => $cq->orderBy('sort_order')->with(['lessons' => fn ($lq) => $lq->orderBy('sort_order')]),
                        'courseSections' => fn ($sq) => $sq->orderBy('sort_order')->with(['lessons' => fn ($lq) => $lq->orderBy('sort_order')]),
                    ])
                    ->withCount('lessons');
            }])
            ->firstOrFail();

        $userEnrollments = collect();
        $overallProgress = 0;
        $completedCoursesCount = 0;

        if (auth()->check()) {
            $courseIds = $learningPath->courses->pluck('id');
            $userEnrollments = Enrollment::where('user_id', auth()->id())
                ->whereIn('course_id', $courseIds)
                ->get()
                ->keyBy('course_id');

            if ($learningPath->courses->isNotEmpty()) {
                $totalProgress = 0;
                foreach ($learningPath->courses as $course) {
                    $enrollment = $userEnrollments->get($course->id);
                    if ($enrollment) {
                        $percent = (float) ($enrollment->progress_percent ?? 0);
                        $totalProgress += $percent;
                        if ($percent >= 100 || $enrollment->completed_at !== null) {
                            $completedCoursesCount++;
                        }
                    }
                }
                $overallProgress = round($totalProgress / $learningPath->courses->count());
            }
        }

        // Group courses by stage_name
        $groupedCourses = $learningPath->courses->groupBy(function ($course) {
            return $course->pivot->stage_name ?: 'Giai đoạn học tập';
        });

        // Gợi ý khóa học liên quan / bổ trợ kỹ năng ngoài lộ trình
        $pathCourseIds = $learningPath->courses->pluck('id');
        $categoryIds = $learningPath->courses->pluck('category_id')->filter()->unique();

        $relatedCourses = Course::published()
            ->whereNotIn('id', $pathCourseIds)
            ->when($categoryIds->isNotEmpty(), function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            })
            ->with(['instructor:id,name,avatar', 'category:id,name,slug'])
            ->withCount('lessons')
            ->inRandomOrder()
            ->take(3)
            ->get();

        if ($relatedCourses->count() < 3) {
            $extraCourses = Course::published()
                ->whereNotIn('id', $pathCourseIds->merge($relatedCourses->pluck('id')))
                ->with(['instructor:id,name,avatar', 'category:id,name,slug'])
                ->withCount('lessons')
                ->latest()
                ->take(3 - $relatedCourses->count())
                ->get();

            $relatedCourses = $relatedCourses->merge($extraCourses);
        }

        return view('learning-paths.show', compact(
            'learningPath',
            'userEnrollments',
            'overallProgress',
            'completedCoursesCount',
            'groupedCourses',
            'relatedCourses'
        ));
    }
}
