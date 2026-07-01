<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $courses = Course::where('instructor_id', auth()->id())
            ->with('category:id,name')
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

        return view('instructor.courses.index', compact('courses', 'statusOptions', 'search', 'status'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('instructor.courses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedCourseData($request);

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
        $this->authorize($course);

        $course->load(['courseSections.lessons', 'category']);
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $statusOptions = $this->statusOptions();

        return view('instructor.courses.edit', compact('course', 'categories', 'statusOptions'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize($course);

        $validated = $this->validatedCourseData($request);

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
        $this->authorize($course);

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
        $this->authorize($course);

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

    public function addChapter(Request $request, Course $course): RedirectResponse
    {
        $this->authorize($course);

        $validated = $request->validate(['title' => 'required|string|max:255']);

        Chapter::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'sort_order' => $course->chapters()->count(),
        ]);

        return back()->with('success', 'Đã thêm chương mới.');
    }

    public function addLesson(Request $request, Chapter $chapter): RedirectResponse
    {
        $this->authorize($chapter->course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,document,quiz,assignment',
            'video_url' => 'nullable|string|max:2048',
            'is_preview' => 'sometimes|boolean',
        ]);

        Lesson::create([
            ...$validated,
            'chapter_id' => $chapter->id,
            'sort_order' => $chapter->lessons()->count(),
            'is_preview' => $request->boolean('is_preview'),
        ]);

        return back()->with('success', 'Đã thêm bài giảng.');
    }

    public function submit(Course $course): RedirectResponse
    {
        $this->authorize($course);

        if (! in_array($course->status, [Course::STATUS_DRAFT, Course::STATUS_REJECTED], true)) {
            return back()->with('error', 'Chỉ khóa học nháp hoặc bị từ chối mới có thể gửi duyệt.');
        }

        $missing = $this->publicationMissingRequirements($course);

        if ($missing !== []) {
            return back()->with('error', 'Chưa thể gửi duyệt: '.implode('; ', $missing).'.');
        }

        $course->update([
            'status' => Course::STATUS_PENDING,
            'is_published' => false,
            'submitted_at' => now(),
            'reject_reason' => null,
        ]);

        return back()->with('success', 'Đã gửi khóa học để admin duyệt.');
    }

    public function students(Course $course): View
    {
        $this->authorize($course);

        $enrollments = Enrollment::where('course_id', $course->id)
            ->with('user:id,name,email,avatar')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('instructor.courses.students', compact('course', 'enrollments'));
    }

    public function revenue(): View
    {
        $courseIds = Course::where('instructor_id', auth()->id())->pluck('id')->toArray();
        $orders = \App\Models\Order::where('status', 'paid')->get();

        $totalRevenue = 0;
        $courseSales = [];

        foreach ($orders as $order) {
            foreach (($order->items ?? []) as $item) {
                $cid = $item['course_id'] ?? null;
                if (in_array($cid, $courseIds)) {
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
        }

        $courseRevenue = collect($courseSales)->values();

        return view('instructor.revenue', compact('totalRevenue', 'courseRevenue'));
    }

    protected function authorize(Course $course): void
    {
        abort_unless($course->isOwnedBy(auth()->user()), 403);
    }

    private function validatedCourseData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'preview_video' => ['nullable', 'string', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'level' => ['nullable', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'language' => ['required', 'string', 'max:10'],
        ]);
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

    private function publicationMissingRequirements(Course $course): array
    {
        $missing = [];

        if (blank($course->title)) {
            $missing[] = 'thiếu tên khóa học';
        }

        if (blank($course->short_description)) {
            $missing[] = 'thiếu mô tả ngắn';
        }

        if (blank($course->description)) {
            $missing[] = 'thiếu mô tả chi tiết';
        }

        if (blank($course->thumbnail)) {
            $missing[] = 'thiếu ảnh thumbnail';
        }

        $hasSection = $course->courseSections()->exists() || $course->chapters()->exists();
        if (! $hasSection) {
            $missing[] = 'thiếu ít nhất 1 chương học';
        }

        $hasLesson = $course->lessons()->exists()
            || Lesson::whereHas('chapter', fn ($query) => $query->where('course_id', $course->id))->exists();

        if (! $hasLesson) {
            $missing[] = 'thiếu ít nhất 1 bài học';
        }

        return $missing;
    }

    private function statusOptions(): array
    {
        return [
            Course::STATUS_DRAFT => 'Nháp',
            Course::STATUS_PENDING => 'Đang chờ duyệt',
            Course::STATUS_PUBLISHED => 'Đã xuất bản',
            Course::STATUS_REJECTED => 'Bị từ chối',
            Course::STATUS_ARCHIVED => 'Đã ẩn',
        ];
    }
}
