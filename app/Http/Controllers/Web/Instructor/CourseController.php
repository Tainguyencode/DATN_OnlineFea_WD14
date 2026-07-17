<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Data\CourseSubmissionCheckResult;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreChapterRequest;
use App\Http\Requests\Instructor\StoreCourseRequest;
use App\Http\Requests\Instructor\StoreLessonRequest;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Services\CourseReviewService;
use App\Services\CourseSubmissionValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $courses = Course::where('instructor_id', auth()->id())
            ->with(['category:id,parent_id,name', 'category.parent:id,name'])
            ->withCount('enrollments')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, Course::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $statusOptions = $this->statusOptions();
        $submissionChecks = $this->buildSubmissionChecks($courses->getCollection());

        return view('instructor.courses.index', compact('courses', 'statusOptions', 'search', 'status', 'submissionChecks'));
    }

    public function create(): View
    {
        $categories = $this->categoryGroups();

        return view('instructor.courses.create', compact('categories'));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course = Course::create([
            ...$validated,
            'instructor_id' => auth()->id(),
            'slug' => $this->uniqueSlug($validated['title']),
            'sale_price' => $validated['discount_price'] ?? null,
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
            'published_at' => null,
        ]);

        return redirect()->route('instructor.courses.edit', $course)
            ->with('success', 'Tạo khóa học thành công. Khóa học đang được lưu ở trạng thái nháp.');
    }

    public function edit(Course $course): View
    {
        $this->ensureOwned($course);

        $course->load([
            'courseSections.lessons' => fn ($query) => $query->orderBy('sort_order')->with('videoModeration'),
            'chapters.lessons' => fn ($query) => $query->orderBy('sort_order')->with('videoModeration'),
            'category.parent',
            'courseReviews' => fn ($q) => $q->orderByDesc('submission_number'),
            'courseReviews.reviewer:id,name,email',
        ]);
        $categories = $this->categoryGroups();
        $statusOptions = $this->statusOptions();
        $submissionCheck = $course->submissionCheck();
        $courseReviews = $course->courseReviews;

        return view('instructor.courses.edit', compact('course', 'categories', 'statusOptions', 'submissionCheck', 'courseReviews'));
    }

    public function update(StoreCourseRequest $request, Course $course): RedirectResponse
    {
        $this->ensureOwned($course);

        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $this->deleteThumbnail($course);
            $validated['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course->update([
            ...$validated,
            'sale_price' => $validated['discount_price'] ?? null,
        ]);

        return back()->with('success', 'Đã lưu nháp khóa học.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->ensureOwned($course);

        if ($this->hasBusinessRecords($course)) {
            $course->update([
                'status' => Course::STATUS_ARCHIVED,
                'is_published' => false,
                'published_at' => null,
            ]);

            return redirect()->route('instructor.courses.index')
                ->with('success', 'Khóa học đã có dữ liệu học viên hoặc đơn hàng nên được chuyển sang trạng thái lưu trữ.');
        }

        $this->deleteThumbnail($course);
        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Đã xóa khóa học.');
    }

    public function archive(Course $course): RedirectResponse
    {
        $this->ensureOwned($course);

        if ($course->status !== Course::STATUS_PUBLISHED) {
            return back()->with('error', 'Chỉ có thể ẩn khóa học đang được xuất bản.');
        }

        $course->update([
            'status' => Course::STATUS_ARCHIVED,
            'is_published' => false,
            'published_at' => null,
        ]);

        return back()->with('success', 'Đã ẩn khóa học khỏi trang học viên.');
    }

    public function addChapter(StoreChapterRequest $request, Course $course): RedirectResponse
    {
        $this->ensureOwned($course);
        $validated = $request->validated();

        Chapter::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'sort_order' => $course->chapters()->count(),
        ]);

        return back()->with('success', 'Đã thêm chương mới.');
    }

    public function addLesson(StoreLessonRequest $request, Chapter $chapter): RedirectResponse
    {
        $this->ensureOwned($chapter->course);

        $validated = $request->validated();

        Lesson::create([
            ...$validated,
            'chapter_id' => $chapter->id,
            'sort_order' => $chapter->lessons()->count(),
            'is_preview' => $request->boolean('is_preview'),
        ]);

        return back()->with('success', 'Đã thêm bài giảng.');
    }

    public function submit(Course $course, CourseReviewService $reviewService): RedirectResponse
    {
        $this->authorize('submit', $course);

        if (! $course->submissionCheck()->passes()) {
            return back()->with('error', 'Khóa học chưa đủ điều kiện để gửi duyệt.');
        }

        $reviewService->submitForReview($course, auth()->user());

        return redirect()
            ->route('instructor.courses.index')
            ->with('success', 'Đã gửi khóa học để admin duyệt.');
    }

    public function submitPage(Course $course): RedirectResponse
    {
        $this->ensureOwned($course);

        if ($course->isEditable()) {
            return redirect()->route('instructor.courses.edit', $course);
        }

        return redirect()->route('instructor.courses.index');
    }

    public function students(Course $course): View
    {
        $this->ensureOwned($course);

        $enrollments = Enrollment::where('course_id', $course->id)
            ->with('user:id,name,email,avatar')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('instructor.courses.students', compact('course', 'enrollments'));
    }

    public function revenue(Request $request): View
    {
        $courseIds = Course::where('instructor_id', auth()->id())->pluck('id')->toArray();
        $query = Order::where('status', 'paid');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->input('month'));
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }

        $orders = $query->get();

        $totalRevenue = 0;
        $courseSales = [];

        foreach ($orders as $order) {
            $items = $order->items;
            if (! is_iterable($items)) {
                continue;
            }

            foreach ($items as $item) {
                $cid = $item['course_id'] ?? null;
                if (! in_array($cid, $courseIds, true)) {
                    continue;
                }

                $price = $item['price'] ?? 0;
                $totalRevenue += $price;

                if (! isset($courseSales[$cid])) {
                    $courseSales[$cid] = [
                        'course_id' => $cid,
                        'total' => 0,
                        'sales' => 0,
                        'course' => Course::find($cid),
                    ];
                }

                $courseSales[$cid]['total'] += $price;
                $courseSales[$cid]['sales'] += 1;
            }
        }

        $courseRevenue = collect($courseSales)->map(fn ($item) => (object) $item)->values();
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ];

        return view('instructor.revenue', compact('totalRevenue', 'courseRevenue', 'filters'));
    }

    protected function ensureOwned(Course $course): void
    {
        abort_unless($course->isOwnedBy(auth()->user()), 403);
    }

    private function uniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title) ?: 'course';
        $slug = $baseSlug;
        $counter = 2;

        while (Course::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function deleteThumbnail(Course $course): void
    {
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }
    }

    private function hasBusinessRecords(Course $course): bool
    {
        return $course->enrollments()->exists()
            || DB::table('cart_items')->where('course_id', $course->id)->exists()
            || DB::table('order_items')->where('course_id', $course->id)->exists();
    }

    /**
     * @param  Collection<int, Course>  $courses
     * @return array<int, CourseSubmissionCheckResult>
     */
    private function buildSubmissionChecks($courses): array
    {
        $validator = app(CourseSubmissionValidator::class);
        $checks = [];

        foreach ($courses as $course) {
            $checks[$course->id] = $validator->validate($course);
        }

        return $checks;
    }

    private function statusOptions(): array
    {
        return Course::STATUS_LABELS;
    }

    private function categoryGroups()
    {
        return Category::query()
            ->active()
            ->parent()
            ->whereHas('children', fn ($query) => $query->active())
            ->with([
                'children' => fn ($query) => $query
                    ->active()
                    ->orderBy('sort_order')
                    ->orderBy('name'),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'sort_order']);
    }
}
