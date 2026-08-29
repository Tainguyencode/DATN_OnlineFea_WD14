<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Chapter;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Services\ActivityLogService;
use App\Services\ContentUpdateService;
use App\Services\CourseReviewService;
use App\Services\CurriculumLessonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'objectives' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
        ]);

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

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'sometimes|string',
            'objectives' => 'nullable|string',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
        ]);

        if ($course->isContentApproved()) {
            $update = app(ContentUpdateService::class)->recordPendingUpdate(
                ContentUpdate::TYPE_COURSE,
                ContentUpdate::ACTION_UPDATE,
                $course->id,
                $course->id,
                $validated,
                $request->user(),
            );

            return $this->success($update, 'Đã lưu bản cập nhật khóa học, chờ Admin duyệt.', 202);
        }

        $course->update($validated);

        return $this->success($course->fresh(), 'Cập nhật khóa học thành công');
    }

    public function submitForReview(Request $request, Course $course, CourseReviewService $reviewService): JsonResponse
    {
        $this->authorizeCourse($request, $course);

        $reviewService->submitForReview($course, $request->user());

        return $this->success($course, 'Đã gửi khóa học để duyệt');
    }

    public function storeChapter(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($request, $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        if ($course->isContentApproved()) {
            $update = app(ContentUpdateService::class)->recordPendingUpdate(
                ContentUpdate::TYPE_CHAPTER,
                ContentUpdate::ACTION_CREATE,
                $course->id,
                null,
                array_merge($validated, ['sort_order' => $validated['sort_order'] ?? $course->courseSections()->count()]),
                $request->user(),
            );

            return $this->success($update, 'Đã lưu bản cập nhật chương, chờ Admin duyệt.', 202);
        }

        $chapter = Chapter::create([
            ...$validated,
            'course_id' => $course->id,
            'sort_order' => $validated['sort_order'] ?? $course->chapters()->count(),
        ]);

        return $this->success($chapter, 'Thêm chương thành công', 201);
    }

    public function storeLesson(Request $request, Chapter $chapter, CurriculumLessonService $lessonService): JsonResponse
    {
        $this->authorizeCourse($request, $chapter->course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:video,document,quiz,assignment',
            'video_url' => 'nullable|string',
            'duration_seconds' => 'sometimes|integer|min:0',
            'is_preview' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $course = $chapter->course;
        if ($course->isContentApproved()) {
            $sectionId = Lesson::query()
                ->where('course_id', $course->id)
                ->where('chapter_id', $chapter->id)
                ->whereNotNull('section_id')
                ->value('section_id');
            $section = $sectionId
                ? CourseSection::query()->where('course_id', $course->id)->find($sectionId)
                : CourseSection::query()
                    ->where('course_id', $course->id)
                    ->where('title', $chapter->title)
                    ->where('sort_order', $chapter->sort_order)
                    ->first();
            $update = $lessonService->createForManual(
                $course,
                $section ?? $chapter->id,
                $validated,
                $request->user(),
            );
            app(ContentUpdateService::class)->updateDraft($update, ['chapter_id' => $chapter->id]);

            return $this->success($update, 'Đã lưu bản cập nhật bài học, chờ Admin duyệt.', 202);
        }

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
}
