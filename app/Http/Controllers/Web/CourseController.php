<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Review;
use App\Services\LearningProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $categoryId = $request->query('category');
        $level = $request->query('level');
        $pricing = $request->query('pricing');
        $rating = $request->query('rating');

        $courses = $this->withFavoriteState($this->publishedCoursesQuery()
            ->with(['instructor:id,name,avatar', 'category:id,name,slug'])
            ->withCount(['lessons', 'courseSections']))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhereHas('instructor', function ($qInst) use ($search) {
                          $qInst->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when(in_array($level, ['beginner', 'intermediate', 'advanced'], true), fn ($query) => $query->where('level', $level))
            ->when($pricing === 'free', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) <= 0'))
            ->when($pricing === 'under_200k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 0 AND COALESCE(discount_price, sale_price, price) <= 200000'))
            ->when($pricing === '200k_500k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) >= 200000 AND COALESCE(discount_price, sale_price, price) <= 500000'))
            ->when($pricing === 'above_500k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 500000'))
            ->when($rating, fn ($query) => $query->where('rating_avg', '>=', (float) $rating))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->select('id', 'name')
            ->withCount([
                'courses' => fn ($query) => $query
                    ->where('status', Course::STATUS_PUBLISHED)
                    ->where('is_published', true),
            ])
            ->orderBy('name')
            ->get();

        $levelOptions = [
            'beginner' => 'Cơ bản',
            'intermediate' => 'Trung cấp',
            'advanced' => 'Nâng cao',
        ];

        return view('courses.index', compact(
            'courses',
            'categories',
            'levelOptions',
            'search',
            'categoryId',
            'level',
            'pricing',
            'rating'
        ));
    }

    public function show(string $slug): View
    {
        $course = Course::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $canBypassCourseVisibility = $this->canBypassCourseVisibility($course);
        abort_unless($this->isPublished($course) || $canBypassCourseVisibility, 404);

        $isEnrolled = $this->isEnrolled($course);
        $canManageCourse = auth()->check()
            && auth()->user()->isInstructor()
            && $course->isOwnedBy(auth()->user());
        $canAccessFullCourse = $isEnrolled || $canManageCourse || $canBypassCourseVisibility;

        $course->load([
            'instructor:id,name,avatar,bio',
            'category:id,name,slug',
            'courseSections.lessons' => fn ($q) => $q
                ->select('id', 'course_id', 'section_id', 'title', 'type', 'video_url', 'video_path', 'video_original_name', 'video_mime', 'video_size', 'content', 'document_file', 'duration', 'duration_seconds', 'is_preview', 'sort_order')
                ->when(!$canAccessFullCourse, fn ($query) => $query->where('is_preview', true))
                ->orderBy('sort_order'),
            'chapters.lessons' => fn ($q) => $q
                ->select('id', 'course_id', 'chapter_id', 'title', 'type', 'video_url', 'video_path', 'video_original_name', 'video_mime', 'video_size', 'content', 'document_file', 'duration', 'duration_seconds', 'is_preview', 'sort_order')
                ->when(!$canAccessFullCourse, fn ($query) => $query->where('is_preview', true))
                ->orderBy('sort_order'),
        ]);
        $course->loadCount('lessons');

        $relatedCourses = $this->withFavoriteState($this->publishedCoursesQuery()
            ->where('id', '!=', $course->id)
            ->when($course->category_id, fn ($query) => $query->where('category_id', $course->category_id))
            ->with(['instructor:id,name,avatar', 'category:id,name'])
            ->withCount('lessons'))
            ->orderByDesc('rating_avg')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        $reviews = Review::where('course_id', $course->id)
            ->with('user:id,name,avatar')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $curriculumSections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;

        $totalLessons = $course->lessons_count;
        $previewLessons = $curriculumSections->flatMap->lessons->where('is_preview', true)->count();
        $totalSections = $curriculumSections->count();

        $isFavorited = auth()->check()
            && auth()->user()->isStudent()
            && $course->isFavoritedBy(auth()->user());

        return view('courses.show', compact(
            'course',
            'curriculumSections',
            'relatedCourses',
            'reviews',
            'totalLessons',
            'previewLessons',
            'totalSections',
            'isEnrolled',
            'canManageCourse',
            'canAccessFullCourse',
            'isFavorited'
        ));
    }

    public function lesson(Course $course, Lesson $lesson): View
    {
        abort_unless($this->lessonBelongsToCourse($course, $lesson), 404);

        $canBypassCourseVisibility = $this->canBypassCourseVisibility($course);
        abort_unless($this->isPublished($course) || $canBypassCourseVisibility, 404);

        $course->load(['instructor:id,name,avatar,bio', 'category:id,name,slug']);
        $lesson->loadMissing(['section:id,course_id,title,sort_order', 'chapter:id,course_id,title,sort_order']);

        $enrollment = auth()->check()
            ? Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->first()
            : null;
        $isEnrolled = (bool) $enrollment;
        $canAccessLesson = $canBypassCourseVisibility || $isEnrolled || $lesson->is_preview;
        $videoSource = null;
        $lessonProgress = null;

        if ($canAccessLesson && $lesson->type === 'video') {
            $videoSource = $lesson->video_path
                ? Storage::disk('public')->url($lesson->video_path)
                : $lesson->video_url;
        }

        if (auth()->check()) {
            $lessonProgress = DB::table('lesson_progress')
                ->where('user_id', auth()->id())
                ->where('lesson_id', $lesson->id)
                ->first();
        }

        return view('courses.lesson', compact(
            'course',
            'lesson',
            'enrollment',
            'lessonProgress',
            'isEnrolled',
            'canAccessLesson',
            'videoSource'
        ));
    }

    public function updateLessonProgress(
        Request $request,
        Course $course,
        Lesson $lesson,
        LearningProgressService $progressService
    ): JsonResponse {
        abort_unless($request->user()?->isStudent(), 403);
        abort_unless($this->lessonBelongsToCourse($course, $lesson), 404);
        abort_unless($this->isPublished($course), 404);

        $enrollmentExists = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        abort_unless($enrollmentExists, 403);

        $validated = $request->validate([
            'watched_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'completed' => ['nullable', 'boolean'],
        ]);

        $watchedSeconds = (int) ($validated['watched_seconds'] ?? 0);
        $durationSeconds = $this->lessonDurationSeconds($lesson);
        $completed = $request->boolean('completed');

        if ($lesson->type === 'video' && $durationSeconds > 0) {
            $completed = $completed || $watchedSeconds >= (int) ceil($durationSeconds * 0.9);
        }

        $progress = $progressService->recordLessonProgress(
            $request->user()->id,
            $course,
            $lesson,
            $watchedSeconds,
            $completed
        );

        return response()->json($progress);
    }

    public function enroll(Course $course): RedirectResponse
    {
        if ($course->status !== Course::STATUS_PUBLISHED || ! $course->is_published) {
            abort(404);
        }

        $user = auth()->user();

        if ($user->isInstructor() && $course->isOwnedBy($user)) {
            return redirect()->route('instructor.courses.curriculum', $course);
        }

        if (! $user->isStudent()) {
            return back()->with('error', 'Chỉ tài khoản học viên mới có thể đăng ký khóa học.');
        }

        $created = false;

        DB::transaction(function () use ($course, $user, &$created) {
            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'status' => 'active',
                    'progress_percent' => 0,
                    'enrolled_at' => now(),
                ]
            );

            if ($enrollment->wasRecentlyCreated) {
                $course->increment('enrollment_count');
                $created = true;
            }
        });

        return redirect()
            ->route('my-courses')
            ->with('success', $created
                ? 'Đăng ký khóa học thành công. Bạn có thể bắt đầu học ngay.'
                : 'Bạn đã đăng ký khóa học này trước đó.');
    }

    private function publishedCoursesQuery()
    {
        return Course::query()
            ->where('status', Course::STATUS_PUBLISHED)
            ->where('is_published', true);
    }

    private function isPublished(Course $course): bool
    {
        return $course->isPublished();
    }

    private function withFavoriteState($query)
    {
        if (! auth()->check() || ! auth()->user()->isStudent()) {
            return $query;
        }

        return $query->withExists([
            'wishlists as is_favorited' => fn ($favoriteQuery) => $favoriteQuery->where('user_id', auth()->id()),
        ]);
    }

    private function canBypassCourseVisibility(Course $course): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();

        return $user->isAdmin() || ($user->isInstructor() && $course->isOwnedBy($user));
    }

    private function isEnrolled(Course $course): bool
    {
        return auth()->check()
            && Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
    }

    private function lessonBelongsToCourse(Course $course, Lesson $lesson): bool
    {
        if ((int) $lesson->course_id === (int) $course->id) {
            return true;
        }

        if ($lesson->section_id && $lesson->section()->where('course_id', $course->id)->exists()) {
            return true;
        }

        return $lesson->chapter_id && $lesson->chapter()->where('course_id', $course->id)->exists();
    }

    private function lessonDurationSeconds(Lesson $lesson): int
    {
        $duration = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);

        return max($duration, 0);
    }
}
