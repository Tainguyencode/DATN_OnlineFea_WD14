<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\UpdateLessonProgressRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\LessonProgress;
use App\Models\Review;
use App\Models\ReviewHelpful;
use App\Models\Discussion;
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
            ->visible()
            ->where('course_id', $course->id)
            ->whereNull('parent_id')
            ->with(['user:id,name,avatar', 'replies.user:id,name,avatar'])
            ->rating($reviewRating)
            ->when($reviewSort === 'helpful', fn ($query) => $query->mostHelpful())
            ->when($reviewSort === 'latest', fn ($query) => $query->latest())
            ->paginate(config('reviews.per_page', 8), ['*'], 'reviews_page')
            ->withQueryString();

        $ratingRows = Review::query()
            ->visible()
            ->where('course_id', $course->id)
            ->whereNull('parent_id')
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

        $topStudents = \App\Models\User::query()
            ->join('user_points', 'users.id', '=', 'user_points.user_id')
            ->select('users.*', \Illuminate\Support\Facades\DB::raw('SUM(user_points.points) as course_points'))
            ->where('user_points.course_id', $course->id)
            ->groupBy('users.id')
            ->orderByDesc('course_points')
            ->limit(10)
            ->get();

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
            'topStudents',
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

        $canUseLessonNotes = (bool) $user && $user->isStudent() && $player['isEnrolled'];
        $lessonNotes = $canUseLessonNotes
            ? LessonNote::query()
                ->where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->when(
                    $lesson->type === 'video',
                    fn ($query) => $query->orderBy('timestamp_seconds')->orderBy('created_at'),
                    fn ($query) => $query->latest()
                )
                ->get()
            : collect();
        $lessonNotesIndexUrl = $canUseLessonNotes
            ? route('courses.lessons.notes.index', [$course, $lesson])
            : null;
        $lessonNotesStoreUrl = $canUseLessonNotes
            ? route('courses.lessons.notes.store', [$course, $lesson])
            : null;

        $sectionTitle = $lesson->section?->title ?? $lesson->chapter?->title;

        if ($user && ($player['canAccessLesson'] || $player['isEnrolled'])) {
            $recentlyViewedCourseService->record($user, $course);
            app(\App\Services\EngagementService::class)->recordLearningActivity($user);
        }

        $discussions = collect();
        if ($user) {
            if ($user->isInstructor() && (int) $course->instructor_id === (int) $user->id) {
                $discussions = Discussion::where('lesson_id', $lesson->id)
                    ->with(['user', 'replies.user'])
                    ->latest()
                    ->get();
            } elseif ($user->isAdmin()) {
                $discussions = Discussion::where('lesson_id', $lesson->id)
                    ->with(['user', 'replies.user'])
                    ->latest()
                    ->get();
            } elseif ($user->isStudent()) {
                $discussions = Discussion::where('lesson_id', $lesson->id)
                    ->where('user_id', $user->id)
                    ->with(['user', 'replies.user'])
                    ->latest()
                    ->get();
            }
        }

        $activeDiscussion = null;
        $discussionId = request()->integer('discussion_id');
        if ($discussionId > 0 && $discussions->isNotEmpty()) {
            $activeDiscussion = $discussions->firstWhere('id', $discussionId);
            if ($activeDiscussion) {
                $activeDiscussion->load(['user', 'replies.user']);
            }
        }

        $submission = null;
        if (auth()->check() && $lesson->type === 'assignment' && $lesson->assignment) {
            $submission = \App\Models\Submission::query()
                ->where('assignment_id', $lesson->assignment->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        $hasNewContentVersion = false;
        if ($user && $player['isEnrolled']) {
            $progressModel = \App\Models\LessonProgress::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();

            if ($progressModel) {
                if ((int) $lesson->content_version > (int) $progressModel->last_viewed_content_version) {
                    $hasNewContentVersion = true;
                    $progressModel->update(['last_viewed_content_version' => (int) $lesson->content_version]);
                }
            }
        }

        $isOwnerInstructor = $user && $user->isInstructor() && (int) $course->instructor_id === (int) $user->id;
        $isAdmin = $user && $user->isAdmin();

        if ($isOwnerInstructor || $isAdmin) {
            $lessonComments = \App\Models\LessonComment::where('lesson_id', $lesson->id)
                ->whereNull('parent_id')
                ->with(['user', 'replies' => function ($q) {
                    $q->with('user')->oldest();
                }])
                ->latest()
                ->get();
        } else {
            $lessonComments = \App\Models\LessonComment::where('lesson_id', $lesson->id)
                ->whereNull('parent_id')
                ->where('is_hidden', false)
                ->with(['user', 'replies' => function ($q) {
                    $q->where('is_hidden', false)->with('user')->oldest();
                }])
                ->latest()
                ->get();
        }

        return view('courses.lesson', [
            'course' => $course,
            'lesson' => $lesson,
            'hasNewContentVersion' => $hasNewContentVersion,
            'submission' => $submission,
            'enrollment' => $player['enrollment'],
            'isEnrolled' => $player['isEnrolled'],
            'canAccessLesson' => $player['canAccessLesson'],
            'canUseLessonAi' => $canUseLessonAi,
            'canUseLessonNotes' => $canUseLessonNotes,
            'aiSummaryUrl' => $aiSummaryUrl,
            'aiExplainUrl' => $aiExplainUrl,
            'lessonNotes' => $lessonNotes,
            'lessonNotesIndexUrl' => $lessonNotesIndexUrl,
            'lessonNotesStoreUrl' => $lessonNotesStoreUrl,
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
            'discussions' => $discussions,
            'activeDiscussion' => $activeDiscussion,
            'lessonComments' => $lessonComments,
        ]);
    }

    public function updateLessonProgress(
        UpdateLessonProgressRequest $request,
        Course $course,
        Lesson $lesson,
        LearningProgressService $progressService
    ): JsonResponse {
        $user = $request->user();
        abort_unless($user, 401);
        abort_unless($this->lessonBelongsToCourse($course, $lesson), 404);

        $canBypass = $this->canBypassCourseVisibility($course);
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->withLearningAccess()
            ->first();

        if (!$enrollment && $canBypass) {
            $enrollment = Enrollment::firstOrCreate([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ], [
                'status' => Enrollment::STATUS_ACTIVE,
                'progress_percent' => 0,
                'enrolled_at' => now(),
            ]);
        }

        abort_unless($enrollment, 403);

        $validated = $request->validated();

        if ($lesson->type === 'video') {
            $progress = $progressService->recordVideoProgress(
                $request->user()->id,
                $course,
                $lesson,
                $validated,
            );

            if ($progress['stale'] ?? false) {
                return response()->json($progress, 409);
            }
        } else {
            abort_if($lesson->type === 'quiz', 422, 'Quiz progress is updated after quiz submission.');

            $progress = $progressService->recordLessonProgress(
                $request->user()->id,
                $course,
                $lesson,
                0,
                0,
                $request->boolean('completed')
            );
        }

        return response()->json([
            'success' => true,
            'lesson_progress' => $progress['lesson_progress'],
            'course_progress' => $progress['course_progress'],
            'lesson_completed' => $progress['lesson_completed'],
            'course_completed' => $progress['course_completed'],
            'watched_seconds' => $progress['watched_seconds'],
            'last_position_seconds' => $progress['last_position_seconds'] ?? 0,
            'furthest_position_seconds' => $progress['furthest_position_seconds'] ?? 0,
            'completed_lessons' => $progress['completed_lessons'],
            'total_lessons' => $progress['total_lessons'],
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
