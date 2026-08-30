<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Services\ActivityLogService;
use App\Services\CourseReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstructorController extends Controller
{
    use ApiResponse;

    public function myCourses(Request $request): JsonResponse
    {
        $courses = Course::where('instructor_id', $request->user()->id)
            ->with('category:id,name')
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->paginate(12);

        return $this->paginated($courses);
    }

    public function storeCourse(Request $request): JsonResponse
    {
        $validated = $this->validateCoursePayload($request);

        $course = Course::create([
            ...$validated,
            'instructor_id' => $request->user()->id,
            'slug' => Str::slug($validated['title']).'-'.Str::random(6),
            'status' => 'draft',
        ]);

        ActivityLogService::log($request->user()->id, 'create_course', Course::class, $course->id, null, $request);

        return $this->success($course, 'Tạo khóa học thành công', 201);
    }

    public function updateCourse(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($request, $course);

        $validated = $this->validateCoursePayload($request, $course);

        $course->update($validated);

        return $this->success($course->fresh(), 'Cập nhật khóa học thành công');
    }

    public function submitForReview(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($request, $course);

        abort_unless($course->isEditable(), 403, 'Khóa học không ở trạng thái cho phép gửi duyệt.');
        $request->validate(['copyright_agreed' => ['required', 'accepted']]);

        $submissionCheck = $course->submissionCheck();
        if (! $submissionCheck->passes()) {
            return $this->error($submissionCheck->summaryMessage(), 422);
        }

        app(CourseReviewService::class)->submitForReview($course, $request->user());

        return $this->success($course, 'Đã gửi khóa học để duyệt');
    }

    public function storeChapter(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($request, $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $chapter = Chapter::create([
            ...$validated,
            'course_id' => $course->id,
            'sort_order' => $validated['sort_order'] ?? $course->chapters()->count(),
        ]);

        return $this->success($chapter, 'Thêm chương thành công', 201);
    }

    public function storeLesson(Request $request, Chapter $chapter): JsonResponse
    {
        $this->authorizeCourse($request, $chapter->course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string|max:10000',
            'type' => 'required|in:video,document,quiz,assignment',
            'video_url' => 'nullable|url:http,https|max:2048|required_if:type,video|prohibited_unless:type,video',
            'duration_seconds' => 'sometimes|integer|min:0|max:999999',
            'is_preview' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $lesson = Lesson::create([
            ...$validated,
            'course_id' => $chapter->course_id,
            'chapter_id' => $chapter->id,
            'sort_order' => $validated['sort_order'] ?? $chapter->lessons()->count(),
        ]);

        return $this->success($lesson, 'Thêm bài giảng thành công', 201);
    }

    public function students(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($request, $course);

        $students = Enrollment::where('course_id', $course->id)
            ->with('user:id,name,email,avatar')
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->paginated($students);
    }

    public function revenue(Request $request): JsonResponse
    {
        $courseIds = Course::where('instructor_id', $request->user()->id)->pluck('id')->toArray();
        $orders = Order::where('status', 'paid')->get();

        $totalRevenue = 0;
        $totalSales = 0;
        $monthlyRevenue = []; // 'YYYY-MM' => total

        foreach ($orders as $order) {
            $month = $order->created_at->format('Y-m');
            foreach (($order->items ?? []) as $item) {
                $cid = $item['course_id'] ?? null;
                if (in_array($cid, $courseIds)) {
                    $price = $item['price'] ?? 0;
                    $totalRevenue += $price;
                    $totalSales += 1;

                    if (! isset($monthlyRevenue[$month])) {
                        $monthlyRevenue[$month] = 0;
                    }
                    $monthlyRevenue[$month] += $price;
                }
            }
        }

        $monthly = collect($monthlyRevenue)->map(function ($total, $month) {
            return ['month' => $month, 'total' => $total];
        })->sortBy('month')->values()->toArray();

        return $this->success([
            'total_revenue' => $totalRevenue,
            'total_sales' => $totalSales,
            'monthly' => $monthly,
        ]);
    }

    protected function authorizeCourse(Request $request, Course $course): void
    {
        if ($course->instructor_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }

    /** @return array<string, mixed> */
    private function validateCoursePayload(Request $request, ?Course $course = null): array
    {
        $required = $course ? 'sometimes' : 'required';
        $validator = Validator::make($request->all(), [
            'title' => [$required, 'string', 'max:255'],
            'category_id' => [$required, 'integer', Rule::exists('categories', 'id')],
            'description' => [$required, 'string', 'max:10000'],
            'objectives' => ['nullable', 'string', 'max:5000'],
            'level' => [$required, Rule::in(['beginner', 'intermediate', 'advanced'])],
            'price' => [$required, 'numeric', 'multiple_of:1000', 'min:0', 'max:100000000'],
            'sale_price' => ['nullable', 'numeric', 'multiple_of:1000', 'min:0', 'max:100000000'],
            'tags' => ['nullable', 'array', 'max:20'],
            'tags.*' => ['string', 'max:50', 'distinct'],
        ]);

        $validator->after(function ($validator) use ($course, $request): void {
            if ($request->filled('category_id')) {
                $category = Category::with('parent:id,status')->find($request->integer('category_id'));
                if ($category && (! $category->status || ($category->parent_id && ! $category->parent?->status))) {
                    $validator->errors()->add('category_id', 'Danh mục được chọn hoặc danh mục cha đang bị tắt.');
                }
                if ($category && ! $category->parent_id && $category->children()->active()->exists()) {
                    $validator->errors()->add('category_id', 'Vui lòng chọn danh mục con.');
                }
            }

            $price = (float) ($request->input('price', $course?->price ?? 0));
            if ($request->filled('sale_price') && (float) $request->input('sale_price') > $price) {
                $validator->errors()->add('sale_price', 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.');
            }
        });

        return $validator->validate();
    }
}
