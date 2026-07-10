<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCourseReviewRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\CourseReviewItem;
use App\Models\Enrollment;
use App\Models\HomepageSetting;
use App\Models\Order;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ManageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');
        $instructorId = $request->query('instructor');
        $categoryId = $request->query('category');
        $pricing = (string) $request->query('pricing');
        $sort = (string) $request->query('sort', 'newest');

        $courses = Course::query()
            ->with([
                'instructor:id,name,email',
                'category:id,name',
                'courseSections.lessons:id,course_id,section_id,title',
                'chapters.lessons:id,course_id,chapter_id,title',
            ])
            ->withCount([
                'courseSections as sections_count',
                'chapters as chapters_count',
                'enrollments as active_enrollments_count' => fn ($query) => $query->where('status', 'active'),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($instructorId, fn ($query) => $query->where('instructor_id', $instructorId))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when(in_array($status, Course::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when($pricing === 'free', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) <= 0'))
            ->when($pricing === 'paid', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 0'));

        match ($sort) {
            'oldest' => $courses->orderBy('created_at'),
            'students' => $courses->orderByDesc('active_enrollments_count')->orderByDesc('created_at'),
            default => $courses->orderByDesc('created_at'),
        };

        $courses = $courses->paginate(12)->withQueryString();

        $instructors = User::query()
            ->whereIn('id', Course::query()->select('instructor_id')->whereNotNull('instructor_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $categories = Category::query()
            ->whereIn('id', Course::query()->select('category_id')->whereNotNull('category_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $statusCounts = Course::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.courses.index', [
            'courses' => $courses,
            'instructors' => $instructors,
            'categories' => $categories,
            'statusCounts' => $statusCounts,
            'statusLabels' => $this->statusLabels(),
            'statusBadgeClasses' => $this->statusBadgeClasses(),
            'filters' => compact('search', 'status', 'instructorId', 'categoryId', 'pricing', 'sort'),
        ]);
    }

    public function pendingCourses(): View
    {
        $courses = Course::where('status', Course::STATUS_SUBMITTED)
            ->with([
                'instructor:id,name,email',
                'category:id,name',
                'courseSections.lessons:id,course_id,section_id,duration_seconds,duration',
                'chapters.lessons:id,course_id,chapter_id,duration_seconds,duration',
                'lessons:id,course_id,duration_seconds,duration',
            ])
            ->orderByDesc('submitted_at')
            ->orderBy('created_at')
            ->paginate(10);

        return view('admin.courses.pending', compact('courses'));
    }

    public function show(Course $course): View
    {
        $course->load([
            'instructor:id,name,email,avatar,bio',
            'category:id,name,slug',
            'courseSections.lessons' => fn ($query) => $query->orderBy('sort_order'),
            'chapters.lessons' => fn ($query) => $query->orderBy('sort_order'),
        ])->loadCount([
            'courseSections as sections_count',
            'chapters as chapters_count',
            'lessons',
            'enrollments as active_enrollments_count' => fn ($query) => $query->where('status', 'active'),
        ]);

        $curriculumSections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;

        $totalLessons = $curriculumSections->sum(fn ($section) => $section->lessons->count());
        $previewLessons = $curriculumSections
            ->flatMap(fn ($section) => $section->lessons)
            ->where('is_preview', true)
            ->count();

        $instructorCourseCount = Course::where('instructor_id', $course->instructor_id)->count();
        $instructorStudentCount = Enrollment::query()
            ->where('status', 'active')
            ->whereHas('course', fn ($query) => $query->where('instructor_id', $course->instructor_id))
            ->distinct('user_id')
            ->count('user_id');

        return view('admin.courses.show', [
            'course' => $course,
            'curriculumSections' => $curriculumSections,
            'totalLessons' => $totalLessons,
            'previewLessons' => $previewLessons,
            'instructorCourseCount' => $instructorCourseCount,
            'instructorStudentCount' => $instructorStudentCount,
            'courseRevenue' => $this->courseRevenue($course),
            'statusLabels' => $this->statusLabels(),
            'statusBadgeClasses' => $this->statusBadgeClasses(),
        ]);
    }

    public function review(Course $course): View|RedirectResponse
    {
        if ($course->status !== Course::STATUS_SUBMITTED) {
            return redirect()
                ->route('admin.courses.pending')
                ->with('error', 'Chỉ khóa học đang chờ duyệt mới có thể được kiểm tra tại trang này.');
        }

        $course->load([
            'instructor:id,name,email,avatar,bio',
            'category:id,name',
            'courseSections.lessons' => fn ($query) => $query->orderBy('sort_order'),
            'chapters.lessons' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        $curriculumSections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;

        $allLessons = $curriculumSections->flatMap(fn ($section) => $section->lessons);
        $totalLessons = $allLessons->count();
        $totalVideoDurationSeconds = $course->totalVideoDurationSeconds();
        $totalVideoDurationMinutes = $course->totalVideoDurationMinutes();

        $attachments = $allLessons
            ->flatMap(function ($lesson) {
                $files = collect();

                if (filled($lesson->document_file)) {
                    $files->push([
                        'lesson_title' => $lesson->title,
                        'name' => basename($lesson->document_file),
                        'url' => asset('storage/'.$lesson->document_file),
                        'type' => 'document',
                    ]);
                }

                foreach ($lesson->attachments ?? [] as $attachment) {
                    if (! is_array($attachment)) {
                        continue;
                    }

                    $path = $attachment['path'] ?? $attachment['file'] ?? null;
                    if (! filled($path)) {
                        continue;
                    }

                    $files->push([
                        'lesson_title' => $lesson->title,
                        'name' => $attachment['name'] ?? basename((string) $path),
                        'url' => str_starts_with((string) $path, 'http')
                            ? $path
                            : asset('storage/'.$path),
                        'type' => $attachment['type'] ?? 'file',
                    ]);
                }

                return $files;
            })
            ->values();

        return view('admin.courses.review', [
            'course' => $course,
            'curriculumSections' => $curriculumSections,
            'totalLessons' => $totalLessons,
            'totalVideoDurationSeconds' => $totalVideoDurationSeconds,
            'totalVideoDurationMinutes' => $totalVideoDurationMinutes,
            'attachments' => $attachments,
            'checklistKeys' => CourseReviewItem::ADMIN_CHECKLIST_KEYS,
            'checklistLabels' => CourseReviewItem::ITEM_LABELS,
        ]);
    }

    public function students(Course $course): View
    {
        $course->load([
            'instructor:id,name,email',
            'category:id,name',
            'courseSections.lessons:id,course_id,section_id,title',
            'chapters.lessons:id,course_id,chapter_id,title',
        ]);

        $enrollments = Enrollment::query()
            ->where('course_id', $course->id)
            ->with('user:id,name,email,avatar')
            ->orderByDesc('enrolled_at')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $curriculumSections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;
        $lessonIds = $curriculumSections
            ->flatMap(fn ($section) => $section->lessons->pluck('id'))
            ->values();
        $totalLessons = $lessonIds->count();
        $completedLessonCounts = collect();

        if (Schema::hasTable('lesson_progress') && $lessonIds->isNotEmpty() && $enrollments->isNotEmpty()) {
            $completedLessonCounts = DB::table('lesson_progress')
                ->select('user_id', DB::raw('COUNT(*) as completed_count'))
                ->whereIn('lesson_id', $lessonIds)
                ->whereIn('user_id', $enrollments->getCollection()->pluck('user_id'))
                ->where('is_completed', true)
                ->groupBy('user_id')
                ->pluck('completed_count', 'user_id');
        }

        return view('admin.courses.students', [
            'course' => $course,
            'enrollments' => $enrollments,
            'totalLessons' => $totalLessons,
            'completedLessonCounts' => $completedLessonCounts,
        ]);
    }

    public function approve(Request $request, Course $course): RedirectResponse
    {
        if ($course->status !== Course::STATUS_SUBMITTED) {
            return back()->with('error', 'Chỉ khóa học đang chờ duyệt mới có thể được duyệt.');
        }

        $course->update([
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
            'reject_reason' => null,
            'rejection_reason' => null,
        ]);
        ActivityLogService::log(auth()->id(), 'approve_course', Course::class, $course->id, null, $request);

        return back()->with('success', "Đã duyệt khóa học \"{$course->title}\".");
    }

    public function reject(Request $request, Course $course): RedirectResponse
    {
        $request->merge([
            'reject_reason' => $request->input('reject_reason', $request->input('reason')),
        ]);

        $validated = $request->validate(['reject_reason' => 'required|string|max:1000']);

        if ($course->status !== Course::STATUS_SUBMITTED) {
            return back()->with('error', 'Chỉ khóa học đang chờ duyệt mới có thể bị từ chối.');
        }

        $course->update([
            'status' => Course::STATUS_REJECTED,
            'is_published' => false,
            'reject_reason' => $validated['reject_reason'],
            'rejection_reason' => $validated['reject_reason'],
        ]);
        ActivityLogService::log(auth()->id(), 'reject_course', Course::class, $course->id, null, $request);

        return back()->with('success', 'Đã từ chối khóa học.');
    }

    /**
     * Xử lý quyết định duyệt từ form checklist admin.
     * Lưu CourseReview + CourseReviewItem, cập nhật courses.status.
     */
    public function submitReview(AdminCourseReviewRequest $request, Course $course): RedirectResponse
    {
        if ($course->status !== Course::STATUS_SUBMITTED) {
            return redirect()
                ->route('admin.courses.pending')
                ->with('error', 'Chỉ khóa học đang chờ duyệt mới có thể được kiểm duyệt.');
        }

        $action  = $request->input('action');
        $comment = $request->input('comment');
        $checklist = $request->input('checklist', []);

        DB::transaction(function () use ($course, $action, $comment, $checklist, $request) {
            // 1. Lưu bản ghi review
            $courseReview = CourseReview::create([
                'course_id'   => $course->id,
                'reviewer_id' => auth()->id(),
                'action'      => $action,
                'comment'     => $comment ?: null,
                'reviewed_at' => now(),
            ]);

            // 2. Lưu từng checklist item
            foreach ($checklist as $itemKey => $data) {
                if (! in_array($itemKey, CourseReviewItem::ADMIN_CHECKLIST_KEYS, true)) {
                    continue;
                }

                CourseReviewItem::create([
                    'course_review_id' => $courseReview->id,
                    'item_key'         => $itemKey,
                    'status'           => $data['status'] ?? 'pass',
                    'note'             => $data['note'] ?? null,
                ]);
            }

            // 3. Cập nhật trạng thái khóa học
            $statusMap = [
                CourseReview::ACTION_APPROVED      => Course::STATUS_APPROVED,
                CourseReview::ACTION_NEED_REVISION => Course::STATUS_NEED_REVISION,
                CourseReview::ACTION_REJECTED      => Course::STATUS_REJECTED,
            ];

            $newStatus = $statusMap[$action];

            $courseUpdate = ['status' => $newStatus];

            if ($action === CourseReview::ACTION_APPROVED) {
                // Approved: xóa lý do từ chối cũ (nếu có)
                $courseUpdate['reject_reason']    = null;
                $courseUpdate['rejection_reason'] = null;
            } elseif (in_array($action, [CourseReview::ACTION_NEED_REVISION, CourseReview::ACTION_REJECTED], true)) {
                // Ghi lý do vào cột cũ để giảng viên vẫn thấy trên edit page (backward-compat)
                $courseUpdate['reject_reason']    = $comment;
                $courseUpdate['rejection_reason'] = $comment;
            }

            $course->update($courseUpdate);

            // 4. Activity log
            ActivityLogService::log(
                auth()->id(),
                "review_course_{$action}",
                Course::class,
                $course->id,
                null,
                $request,
            );
        });

        $actionLabels = [
            CourseReview::ACTION_APPROVED      => 'Đã duyệt',
            CourseReview::ACTION_NEED_REVISION => 'Đã yêu cầu chỉnh sửa',
            CourseReview::ACTION_REJECTED      => 'Đã từ chối',
        ];

        $label = $actionLabels[$action] ?? 'Đã xử lý';

        return redirect()
            ->route('admin.courses.pending')
            ->with('success', "{$label} khóa học \"{$course->title}\".");
    }

    /**
     * Xuất bản khóa học đã được duyệt (approved → published).
     */
    public function publish(Request $request, Course $course): RedirectResponse
    {
        if ($course->status !== Course::STATUS_APPROVED) {
            return back()->with('error', 'Chỉ khóa học đã duyệt mới có thể xuất bản.');
        }

        $course->update([
            'status'       => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ]);

        ActivityLogService::log(auth()->id(), 'publish_course', Course::class, $course->id, null, $request);

        return back()->with('success', "Đã xuất bản khóa học \"{$course->title}\".");
    }

    public function archive(Request $request, Course $course): RedirectResponse
    {
        if ($course->status !== Course::STATUS_PUBLISHED) {
            return back()->with('error', 'Chỉ khóa học đã xuất bản mới có thể ẩn/lưu trữ.');
        }

        $course->update([
            'status' => Course::STATUS_ARCHIVED,
            'is_published' => false,
        ]);

        ActivityLogService::log(auth()->id(), 'archive_course', Course::class, $course->id, null, $request);

        return back()->with('success', "Đã ẩn khóa học \"{$course->title}\".");
    }

    public function restore(Request $request, Course $course): RedirectResponse
    {
        if ($course->status !== Course::STATUS_ARCHIVED) {
            return back()->with('error', 'Chỉ khóa học đã ẩn mới có thể khôi phục.');
        }

        $course->update([
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => $course->published_at ?? now(),
        ]);

        ActivityLogService::log(auth()->id(), 'restore_course', Course::class, $course->id, null, $request);

        return back()->with('success', "Đã khôi phục khóa học \"{$course->title}\".");
    }

    public function revenue(): View
    {
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        $totalOrders = Order::where('status', 'paid')->count();

        $monthExpr = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthly = Order::where('status', 'paid')
            ->selectRaw("{$monthExpr} as month, SUM(total_amount) as total, COUNT(*) as count")
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(12)
            ->get();

        return view('admin.revenue', compact('totalRevenue', 'totalOrders', 'monthly'));
    }

    public function activityLogs(): View
    {
        $logs = ActivityLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.activity-logs', compact('logs'));
    }

    public function homepage(): View
    {
        $settings = HomepageSetting::pluck('value', 'key');

        return view('admin.homepage', compact('settings'));
    }

    public function updateHomepage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'banner_title' => 'required|string|max:255',
            'banner_subtitle' => 'required|string|max:500',
            'announcement' => 'nullable|string|max:500',
        ]);

        HomepageSetting::updateOrCreate(
            ['key' => 'banner'],
            ['value' => ['title' => $validated['banner_title'], 'subtitle' => $validated['banner_subtitle']]]
        );

        if ($validated['announcement']) {
            HomepageSetting::updateOrCreate(['key' => 'announcement'], ['value' => $validated['announcement']]);
        }

        return back()->with('success', 'Cập nhật trang chủ thành công!');
    }

    private function statusLabels(): array
    {
        return Course::STATUS_LABELS;
    }

    private function statusBadgeClasses(): array
    {
        return [
            Course::STATUS_DRAFT => 'bg-slate-50 text-slate-700 ring-1 ring-slate-200',
            Course::STATUS_SUBMITTED => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
            Course::STATUS_NEED_REVISION => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200',
            Course::STATUS_APPROVED => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
            Course::STATUS_PUBLISHED => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
            Course::STATUS_REJECTED => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
            Course::STATUS_ARCHIVED => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-200',
        ];
    }

    private function courseRevenue(Course $course): float
    {
        if (Schema::hasTable('order_items')) {
            $revenue = (float) DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', 'paid')
                ->where('order_items.course_id', $course->id)
                ->sum('order_items.price');

            if ($revenue > 0 || ! Schema::hasColumn('orders', 'items')) {
                return $revenue;
            }
        }

        if (! Schema::hasColumn('orders', 'items')) {
            return 0.0;
        }

        return (float) Order::where('status', 'paid')
            ->get(['items'])
            ->sum(function (Order $order) use ($course) {
                return collect($order->items ?? [])
                    ->where('course_id', $course->id)
                    ->sum(fn ($item) => (float) ($item['price'] ?? 0));
            });
    }
}
