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
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::where('instructor_id', auth()->id())
            ->with('category:id,name')
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('instructor.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('instructor.courses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'objectives' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
        ]);

        $course = Course::create([
            ...$validated,
            'instructor_id' => auth()->id(),
            'slug' => Str::slug($validated['title']).'-'.Str::random(6),
            'status' => 'draft',
        ]);

        return redirect()->route('instructor.courses.edit', $course)
            ->with('success', 'Tạo khóa học thành công! Hãy thêm chương và bài giảng.');
    }

    public function edit(Course $course): View
    {
        $this->authorize($course);
        $course->load(['chapters.lessons', 'category']);
        $categories = Category::orderBy('name')->get();

        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize($course);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'sometimes|string',
            'objectives' => 'nullable|string',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
        ]);

        $course->update($validated);

        return back()->with('success', 'Cập nhật khóa học thành công!');
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
            'video_url' => 'nullable|string',
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

        if ($course->chapters()->count() === 0) {
            return back()->with('error', 'Cần ít nhất 1 chương trước khi gửi duyệt.');
        }

        $course->update(['status' => 'pending']);

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
        $courseSales = []; // course_id => ['total' => ..., 'sales' => ..., 'course' => ...]

        foreach ($orders as $order) {
            foreach (($order->items ?? []) as $item) {
                $cid = $item['course_id'] ?? null;
                if (in_array($cid, $courseIds)) {
                    $price = $item['price'] ?? 0;
                    $totalRevenue += $price;

                    if (!isset($courseSales[$cid])) {
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
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
    }
}
