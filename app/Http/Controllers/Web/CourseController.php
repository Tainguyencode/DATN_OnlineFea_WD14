<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Review;
use App\Models\ReviewHelpful;
use App\Services\CourseRecommendationService;
use App\Services\LearningPlayerService;
use App\Services\LearningProgressService;
use App\Services\RecentlyViewedCourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        return $this->catalog($request);
    }

    public function category(Request $request, Category $category): View
    {
        $category->loadMissing('parent');

        abort_unless($category->status, 404);
        abort_if($category->parent_id && ! $category->parent?->status, 404);

        return $this->catalog($request, $category);
    }

    public function show(
        Request $request,
        string $slug,
        RecentlyViewedCourseService $recentlyViewedCourseService,
        CourseRecommendationService $courseRecommendations
    ): View
    {
        $course = Course::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $canBypassCourseVisibility = $this->canBypassCourseVisibility($course);
        abort_unless($this->isPublished($course) || $canBypassCourseVisibility || $this->isEnrolled($course), 404);

        $isEnrolled = $this->isEnrolled($course);
        $canManageCourse = auth()->check()
            && auth()->user()->isInstructor()
            && $course->isOwnedBy(auth()->user());
        $canAccessFullCourse = $isEnrolled || $canManageCourse || $canBypassCourseVisibility;

        $course->load([
            'instructor:id,name,avatar,bio',
            'category:id,parent_id,name,slug',
            'category.parent:id,name,slug',
            'courseSections.lessons' => fn ($q) => $q
                ->select('id', 'course_id', 'section_id', 'title', 'type', 'video_url', 'video_path', 'video_original_name', 'video_mime', 'video_size', 'content', 'document_file', 'duration', 'duration_seconds', 'is_preview', 'sort_order')
                ->when(! $canAccessFullCourse, fn ($query) => $query->where('is_preview', true))
                ->orderBy('sort_order'),
            'chapters.lessons' => fn ($q) => $q
                ->select('id', 'course_id', 'chapter_id', 'title', 'type', 'video_url', 'video_path', 'video_original_name', 'video_mime', 'video_size', 'content', 'document_file', 'duration', 'duration_seconds', 'is_preview', 'sort_order')
                ->when(! $canAccessFullCourse, fn ($query) => $query->where('is_preview', true))
                ->orderBy('sort_order'),
        ]);
        $course->loadCount('lessons');

        $relatedCourses = $courseRecommendations->getRelatedCourses($course, 4, auth()->user());
        $hasPersonalizedRecommendations = auth()->user()?->isStudent()
            && $relatedCourses->contains(fn ($related) => in_array($related->recommendation_type, ['personal', 'behavior', 'collaborative'], true));
        $recommendationTitle = $hasPersonalizedRecommendations ? 'Đề xuất dành cho bạn' : 'Khóa học liên quan';
        $recommendationSubtitle = $hasPersonalizedRecommendations
            ? 'Gợi ý dựa trên khóa bạn đã xem, đã học, yêu thích và các sở thích tương tự.'
            : 'Một vài lựa chọn gần với chủ đề, trình độ và nhu cầu học của bạn.';

        $reviewRating = $request->integer('review_rating');
        $reviewRating = $reviewRating >= 1 && $reviewRating <= 5 ? $reviewRating : null;
        $reviewSort = $request->query('review_sort') === 'helpful' ? 'helpful' : 'latest';

        $reviews = Review::query()
            ->approved()
            ->where('course_id', $course->id)
            ->with(['user:id,name,avatar', 'replier:id,name'])
            ->rating($reviewRating)
            ->when($reviewSort === 'helpful', fn ($query) => $query->mostHelpful())
            ->when($reviewSort === 'latest', fn ($query) => $query->latest())
            ->paginate(config('reviews.per_page', 8), ['*'], 'reviews_page')
            ->withQueryString();

        $ratingRows = Review::query()
            ->approved()
            ->where('course_id', $course->id)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');
        $ratingDistribution = collect(range(1, 5))->mapWithKeys(
            fn (int $rating) => [$rating => (int) ($ratingRows[$rating] ?? 0)]
        );
        $ratingSummary = [
            'average' => (float) $course->rating_avg,
            'count' => (int) $course->rating_count,
        ];

        $userReview = auth()->check()
            ? Review::query()->where('course_id', $course->id)->where('user_id', auth()->id())->first()
            : null;
        $canReview = auth()->check()
            && Gate::forUser(auth()->user())->allows('create', [Review::class, $course]);
        $canUpdateReview = $userReview && auth()->check()
            ? Gate::forUser(auth()->user())->allows('update', $userReview)
            : false;
        $canDeleteReview = $userReview && auth()->check()
            ? Gate::forUser(auth()->user())->allows('delete', $userReview)
            : false;
        $helpfulReviewIds = auth()->check()
            ? ReviewHelpful::query()
                ->where('user_id', auth()->id())
                ->whereIn('review_id', $reviews->getCollection()->pluck('id'))
                ->pluck('review_id')
                ->all()
            : [];

        $curriculumSections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;

        $totalLessons = $course->lessons_count ?: $curriculumSections->sum(fn ($section) => $section->lessons->count());
        $previewLessons = $curriculumSections->flatMap->lessons->where('is_preview', true)->count();
        $totalSections = $curriculumSections->count();
        $isFavorited = auth()->check()
            && auth()->user()->isStudent()
            && $course->isFavoritedBy(auth()->user());
        $learningEntryUrl = $canAccessFullCourse ? $course->learningEntryUrl() : null;

        $enrollment = auth()->check()
            ? Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first()
            : null;

        $recentlyViewedCourseService->record(auth()->user(), $course);

        return view('courses.show', compact(
            'course',
            'curriculumSections',
            'relatedCourses',
            'reviews',
            'ratingDistribution',
            'ratingSummary',
            'reviewRating',
            'reviewSort',
            'userReview',
            'canReview',
            'canUpdateReview',
            'canDeleteReview',
            'helpfulReviewIds',
            'totalLessons',
            'previewLessons',
            'totalSections',
            'isEnrolled',
            'canManageCourse',
            'canAccessFullCourse',
            'isFavorited',
            'learningEntryUrl',
            'enrollment',
            'recommendationTitle',
            'recommendationSubtitle',
        ));
    }

    public function lesson(
        Course $course,
        Lesson $lesson,
        LearningPlayerService $playerService,
        RecentlyViewedCourseService $recentlyViewedCourseService
    ): View {
        abort_unless($this->lessonBelongsToCourse($course, $lesson), 404);

        $canBypassCourseVisibility = $this->canBypassCourseVisibility($course);
        abort_unless($this->isPublished($course) || $canBypassCourseVisibility || $this->isEnrolled($course), 404);

        $user = auth()->user();
        $player = $playerService->buildPlayerContext($course, $lesson, $user, $canBypassCourseVisibility);

        $videoSource = null;
        if ($player['canAccessLesson'] && $lesson->type === 'video') {
            if ($lesson->video_path && \Illuminate\Support\Str::endsWith($lesson->video_path, '.mp4')) {
                // Sử dụng Cache để tránh gọi Job nhiều lần vì status DB không hỗ trợ 'processing'
                $cacheKey = 'video_processing_' . $lesson->id;
                if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addMinutes(30));
                    \App\Jobs\ConvertVideoToHLS::dispatch($lesson);
                }
            } else {
                $videoSource = $lesson->video_path
                    ? Storage::disk('public')->url($lesson->video_path)
                    : $lesson->video_url;
            }
        }

        $progressUrl = $player['isEnrolled']
            ? route('courses.lessons.progress', [$course, $lesson])
            : null;

        $canUseLessonAi = (bool) $user && (
            $player['isEnrolled']
            || ($user->isInstructor() && $course->isOwnedBy($user))
            || $user->isAdmin()
        );

        $aiSummaryUrl = $canUseLessonAi
            ? route('courses.lessons.ai-summary', [$course, $lesson])
            : null;
        $aiExplainUrl = $canUseLessonAi
            ? route('courses.lessons.ai-explain', [$course, $lesson])
            : null;

        $sectionTitle = $lesson->section?->title ?? $lesson->chapter?->title;

        if ($player['canAccessLesson'] || $player['isEnrolled']) {
            $recentlyViewedCourseService->record($user, $course);
        }

        return view('courses.lesson', [
            'course' => $course,
            'lesson' => $lesson,
            'enrollment' => $player['enrollment'],
            'isEnrolled' => $player['isEnrolled'],
            'canAccessLesson' => $player['canAccessLesson'],
            'canUseLessonAi' => $canUseLessonAi,
            'aiSummaryUrl' => $aiSummaryUrl,
            'aiExplainUrl' => $aiExplainUrl,
            'videoSource' => $videoSource,
            'progressUrl' => $progressUrl,
            'sectionTitle' => $sectionTitle,
            'courseProgress' => $player['courseProgress'],
            'requiredVideoPercent' => $player['requiredVideoPercent'],
            'lessonProgress' => $player['lessonProgress'],
            'lessonState' => $player['lessonState'],
            'curriculumSections' => $player['sections'],
            'navigation' => $player['navigation'],
            'quizContext' => $player['quizContext'],
            'totalLessons' => $player['totalLessons'],
            'completedLessons' => $player['completedLessons'],
        ]);
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
            ->withLearningAccess()
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
            $threshold = $course->requiredVideoPercent() / 100;
            $completed = $completed || $watchedSeconds >= (int) ceil($durationSeconds * $threshold);
        }

        $progress = $progressService->recordLessonProgress(
            $request->user()->id,
            $course,
            $lesson,
            $watchedSeconds,
            $durationSeconds,
            $completed
        );

        return response()->json([
            'success' => true,
            'lesson_progress' => $progress['lesson_progress'],
            'course_progress' => $progress['course_progress'],
            'lesson_completed' => $progress['lesson_completed'],
            'course_completed' => $progress['course_completed'],
        ]);
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
        $reEnrolled = false;

        DB::transaction(function () use ($course, $user, &$created, &$reEnrolled) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment) {
                if ($enrollment->status === Enrollment::STATUS_COMPLETED || $enrollment->completed_at !== null) {
                    $enrollment->update([
                        'status' => Enrollment::STATUS_ACTIVE,
                        'progress_percent' => 0,
                        'completed_at' => null,
                        'enrolled_at' => now(),
                    ]);

                    LessonProgress::where('user_id', $user->id)
                        ->where('course_id', $course->id)
                        ->delete();

                    $reEnrolled = true;
                }
            } else {
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_ACTIVE,
                    'progress_percent' => 0,
                    'enrolled_at' => now(),
                ]);
                $course->increment('enrollment_count');
                $created = true;
            }
        });

        $learningUrl = $course->learningEntryUrl();

        $message = 'Bạn đã đăng ký khóa học này trước đó.';
        if ($created) {
            $message = 'Đăng ký khóa học thành công. Bạn có thể bắt đầu học ngay.';
        } elseif ($reEnrolled) {
            $message = 'Đã đăng ký lại thành công! Bạn có thể bắt đầu học lại từ đầu.';
        }

        return redirect()
            ->to($learningUrl ?? route('student.courses'))
            ->with('success', $message);
    }

    private function catalog(Request $request, ?Category $selectedCategory = null): View
    {
        $search = trim((string) $request->query('search'));
        $level = $request->query('level');
        $pricing = $request->query('pricing');
        $rating = $request->query('rating');
        $selectedCategory ??= $this->resolveCategoryFilter($request->query('category'));

        $courses = $this->withFavoriteState($this->publishedCoursesQuery()
            ->with(['instructor:id,name,avatar', 'category:id,parent_id,name,slug', 'category.parent:id,name,slug'])
            ->withCount(['lessons', 'courseSections']))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('instructor', fn ($instructorQuery) => $instructorQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                if ($selectedCategory->parent_id) {
                    $query->where('category_id', $selectedCategory->id);

                    return;
                }

                $childIds = $selectedCategory->children()
                    ->active()
                    ->pluck('id');

                $query->whereIn('category_id', $childIds);
            })
            ->when(in_array($level, ['beginner', 'intermediate', 'advanced'], true), fn ($query) => $query->where('level', $level))
            ->when($pricing === 'free', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) <= 0'))
            ->when($pricing === 'paid', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 0'))
            ->when($pricing === 'under_200k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 0 AND COALESCE(discount_price, sale_price, price) <= 200000'))
            ->when($pricing === '200k_500k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) >= 200000 AND COALESCE(discount_price, sale_price, price) <= 500000'))
            ->when($pricing === 'above_500k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 500000'))
            ->when(is_numeric($rating), fn ($query) => $query->where('rating_avg', '>=', (float) $rating))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->active()
            ->parent()
            ->with([
                'children' => fn ($query) => $query
                    ->active()
                    ->withCount([
                        'courses' => fn ($courseQuery) => $courseQuery
                            ->where('status', Course::STATUS_PUBLISHED)
                            ->where('is_published', true),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('name'),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'sort_order']);

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
            'selectedCategory',
            'level',
            'pricing',
            'rating'
        ));
    }

    private function resolveCategoryFilter(mixed $value): ?Category
    {
        if (! filled($value)) {
            return null;
        }

        $category = Category::query()
            ->active()
            ->with('parent:id,name,slug,status')
            ->when(
                is_numeric($value),
                fn ($query) => $query->whereKey((int) $value),
                fn ($query) => $query->where('slug', (string) $value),
            )
            ->first();

        if ($category?->parent_id && ! $category->parent?->status) {
            return null;
        }

        return $category;
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
                ->withLearningAccess()
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
