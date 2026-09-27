<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\InstructorTeachingField;
use App\Models\User;
use App\Notifications\InstructorApprovedNotification;
use App\Notifications\InstructorRejectedNotification;
use App\Services\ActivityLogService;
use App\Services\CourseReviewService;
use App\Services\InstructorRequirementService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InstructorApplicationController extends Controller
{
    public function supplements(Request $request): View
    {
        $documents = InstructorCertificate::query()
            ->where('status', 'pending')
            ->whereHas('user', fn ($query) => $query->where('instructor_status', 'approved'))
            ->whereHas('teachingField', fn ($query) => $query->where('approval_status', InstructorTeachingField::STATUS_APPROVED))
            ->with(['user:id,name,email,username,instructor_status', 'teachingField.category.parent', 'requirement'])
            ->latest('uploaded_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.instructors.supplements.index', compact('documents'));
    }

    public function index(Request $request): View
    {
        $status = $request->query('status', '');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $categoryId = $request->query('category_id');

        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        if ($dateFrom && $dateTo && Carbon::parse($dateFrom)->gt(Carbon::parse($dateTo))) {
            throw ValidationException::withMessages([
                'date_from' => 'Ngày bắt đầu không được lớn hơn ngày kết thúc.',
            ]);
        }

        $query = User::where('users.role', 'instructor')
            ->with(['instructorProfile.category', 'instructorProfile.teachingCategories', 'instructorApplication', 'instructorCertificates', 'approver']);

        // 1. Lọc theo trạng thái ứng tuyển (từ Filter Tabs hoặc Dropdown)
        if ($status === 'new_updates') {
            $query->pendingInstructorReview()->where('users.needs_admin_review', true);
        } elseif ($status === 'pending') {
            $query->pendingInstructorReview();
        } elseif (in_array($status, ['approved', 'rejected'], true)) {
            $query->where('users.instructor_status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('users.created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('users.created_at', '<=', $dateTo);
        }

        if ($categoryId) {
            $cat = Category::with('children')->find($categoryId);
            $categoryIds = $cat ? array_merge([$cat->id], $cat->children->pluck('id')->all()) : [(int) $categoryId];

            $query->whereHas('instructorProfile', function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds)
                    ->orWhereHas('teachingCategories', function ($tq) use ($categoryIds) {
                        $tq->whereIn('categories.id', $categoryIds);
                    });
            });
        }

        // 2. Bộ lọc: Tìm kiếm theo tên, email, sđt (users và instructor_profiles)
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('instructorProfile', function ($qp) use ($search) {
                        $qp->where('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Backward compatibility for older bookmarked links using a single registration date.
        if ($request->filled('date')) {
            $date = $request->query('date');
            $query->whereDate('created_at', $date);
        }

        $applications = $query
            ->orderByDesc('users.needs_admin_review')
            ->orderByRaw("CASE WHEN users.instructor_status = 'pending' THEN 1 ELSE 2 END")
            ->orderByDesc('users.updated_at')
            ->paginate(15)
            ->withQueryString();

        $requirementService = app(InstructorRequirementService::class);
        $applications->getCollection()->each(function (User $application) use ($requirementService): void {
            $summary = $requirementService->getRequirementsForInstructor($application)['summary'];
            $requiredCount = $summary['required_count'];
            $completedCount = $summary['required_submitted_count'];

            $application->setAttribute('certificate_progress', [
                'required_count' => $requiredCount,
                'completed_count' => $completedCount,
                'percentage' => $requiredCount > 0 ? (int) round(($completedCount / $requiredCount) * 100) : null,
            ]);
        });

        // Thống kê được hiển thị trên trang statistics riêng.
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        // Counts remain necessary for the management-page status tabs only.
        $counts = [
            'all' => User::where('role', 'instructor')->count(),
            'new_updates' => User::query()->pendingInstructorReview()->where('needs_admin_review', true)->count(),
            'pending' => User::query()->pendingInstructorReview()->count(),
            'approved' => User::where('role', 'instructor')->where('instructor_status', 'approved')->count(),
            'rejected' => User::where('role', 'instructor')->where('instructor_status', 'rejected')->count(),
        ];

        return view('admin.instructors.applications.index', [
            'applications' => $applications,
            'status' => $status,
            'counts' => $counts,
            'categories' => $categories,
            'search' => $request->query('search'),
            'categoryId' => $categoryId,
            'date' => $request->query('date'),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function statistics(Request $request): View
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $month = $request->query('month');
        $week = $request->query('week');

        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'month' => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'week' => ['nullable', 'regex:/^\d{4}-W(0[1-9]|[1-4]\d|5[0-3])$/'],
        ]);

        if ($dateFrom && $dateTo && Carbon::parse($dateFrom)->gt(Carbon::parse($dateTo))) {
            throw ValidationException::withMessages([
                'date_from' => 'Ngày bắt đầu không được lớn hơn ngày kết thúc.',
            ]);
        }

        if ($month && $week) {
            throw ValidationException::withMessages([
                'month' => 'Vui lòng chọn một trong hai bộ lọc tháng hoặc tuần.',
            ]);
        }

        $statisticsQuery = User::query()->where('role', 'instructor');
        if ($dateFrom) {
            $statisticsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $statisticsQuery->whereDate('created_at', '<=', $dateTo);
        }
        if ($month) {
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $statisticsQuery->whereBetween('created_at', [$monthStart, $monthStart->copy()->endOfMonth()]);
        }
        if ($week) {
            [$weekYear, $weekNumber] = sscanf($week, '%d-W%d');
            $weekStart = Carbon::now()->setISODate($weekYear, $weekNumber)->startOfWeek();
            $statisticsQuery->whereBetween('created_at', [$weekStart, $weekStart->copy()->endOfWeek()]);
        }

        $counts = [
            'all' => (clone $statisticsQuery)->count(),
            'new_updates' => (clone $statisticsQuery)->pendingInstructorReview()->where('needs_admin_review', true)->count(),
            'pending' => (clone $statisticsQuery)->pendingInstructorReview()->count(),
            'approved' => (clone $statisticsQuery)->where('instructor_status', 'approved')->count(),
            'rejected' => (clone $statisticsQuery)->where('instructor_status', 'rejected')->count(),
        ];

        $growthUsers = (clone $statisticsQuery)->get(['created_at', 'approved_at']);
        $firstGrowthYear = (int) ($growthUsers->min(fn (User $user) => $user->created_at?->year) ?: now()->year);
        $growthYears = collect(range($firstGrowthYear, now()->year))->sortDesc()->values();
        $growthYear = $growthYears->contains($request->integer('growth_year'))
            ? $request->integer('growth_year')
            : now()->year;
        $growthData = collect(range(1, 12))->map(function (int $month) use ($growthUsers, $growthYear): array {
            $start = now()->setYear($growthYear)->startOfYear()->addMonths($month - 1);
            $end = $start->copy()->endOfMonth();

            return [
                'label' => 'T'.$start->month,
                'full_label' => $start->format('m/Y'),
                'registered' => $growthUsers->filter(fn (User $user) => $user->created_at?->between($start, $end))->count(),
                'approved' => $growthUsers->filter(fn (User $user) => $user->approved_at?->between($start, $end))->count(),
                'cumulative' => $growthUsers->filter(fn (User $user) => $user->created_at?->lte($end))->count(),
            ];
        })->values();
        $yearRegistered = (int) $growthData->sum('registered');
        $previousYearRegistered = $growthUsers->filter(fn (User $user) => $user->created_at?->year === $growthYear - 1)->count();
        $growthRate = $previousYearRegistered > 0 ? round((($yearRegistered - $previousYearRegistered) / $previousYearRegistered) * 100, 1) : ($yearRegistered > 0 ? 100 : 0);

        return view('admin.instructors.statistics', compact('counts', 'growthData', 'growthRate', 'growthYear', 'growthYears', 'yearRegistered', 'dateFrom', 'dateTo', 'month', 'week'));
    }

    public function show(User $user): View|RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return redirect()->route('admin.instructors.applications.index')
                ->with('error', 'Người dùng này không phải là Giảng viên.');
        }

        $hadNewUpdates = $user->needs_admin_review;

        if ($user->needs_admin_review) {
            $user->markAdminReviewed();
        }

        $user->load(['instructorProfile.category', 'instructorApplication', 'instructorCertificates.reviewer', 'instructorCertificates.requirement', 'approver']);

        $requirementService = app(InstructorRequirementService::class);
        $requirementData = $requirementService->getRequirementsForInstructor($user);
        $approvalEligibility = $requirementService->getAdminApprovalEligibility($user);
        $requirementData['summary']['can_approve'] = $approvalEligibility['can_submit'];
        $requirementData['summary']['missing_titles'] = $approvalEligibility['missing_titles'];

        return view('admin.instructors.applications.show', [
            'application' => $user,
            'profile' => $user->instructorProfile,
            'certificates' => $user->instructorCertificates,
            'hadNewUpdates' => $hadNewUpdates,
            'requirementData' => $requirementData,
        ]);
    }

    public function viewCertificate(Request $request, User $user): BinaryFileResponse
    {
        if (! $request->user()?->isAdmin() && $request->user()?->id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        // Find the latest certificate from instructorCertificates or fallback to instructorApplication
        $firstCert = $user->instructorCertificates()->first();
        if ($firstCert) {
            return $this->viewCertificateItem($request, $firstCert);
        }

        $user->load(['instructorApplication', 'instructorProfile']);
        $relativePath = $user->instructorApplication?->certificate_path;

        if (! $relativePath) {
            abort(404, 'Ứng viên chưa tải lên chứng chỉ.');
        }

        if (Storage::disk('local')->exists($relativePath)) {
            $filePath = Storage::disk('local')->path($relativePath);
            $mimeType = Storage::disk('local')->mimeType($relativePath) ?? 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.basename($relativePath).'"',
            ]);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            $filePath = Storage::disk('public')->path($relativePath);
            $mimeType = Storage::disk('public')->mimeType($relativePath) ?? 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.basename($relativePath).'"',
            ]);
        }

        abort(404, 'Tệp chứng chỉ không tồn tại trên hệ thống.');
    }

    public function viewCertificateItem(Request $request, InstructorCertificate $certificate): BinaryFileResponse|RedirectResponse
    {
        if (! $request->user()?->isAdmin() && $request->user()?->id !== $certificate->user_id) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        if ($certificate->isUrlSource()) {
            abort_unless(filled($certificate->document_url), 404, 'URL tài liệu không tồn tại.');

            return redirect()->away($certificate->document_url);
        }

        $relativePath = $certificate->file_path;

        if (Storage::disk('local')->exists($relativePath)) {
            $filePath = Storage::disk('local')->path($relativePath);
            $mimeType = $certificate->mime_type ?: Storage::disk('local')->mimeType($relativePath) ?: 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.basename($certificate->original_name).'"',
            ]);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            $filePath = Storage::disk('public')->path($relativePath);
            $mimeType = $certificate->mime_type ?: Storage::disk('public')->mimeType($relativePath) ?: 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.basename($certificate->original_name).'"',
            ]);
        }

        abort(404, 'Tệp chứng chỉ không tồn tại trên hệ thống.');
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $adminId = $request->user()->id;
        $requirements = app(InstructorRequirementService::class);
        $approval = DB::transaction(function () use ($user, $adminId, $requirements): array {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            abort_unless($lockedUser->role === 'instructor', 422, 'Người dùng không phải giảng viên.');
            if (! $lockedUser->isGlobalReviewPending()) {
                return ['approved' => false, 'error' => 'Hồ sơ không còn ở hàng đợi chờ duyệt.'];
            }

            $profile = $lockedUser->instructorProfile()->lockForUpdate()->first();
            $fields = $profile?->teachingFields()->orderBy('id')->lockForUpdate()->get() ?? collect();
            $lockedCertificates = $lockedUser->instructorCertificates()->orderBy('id')->lockForUpdate()->get();
            $application = $lockedUser->instructorApplication()->lockForUpdate()->first();
            if (! $application || ! $application->isPending()) {
                return ['approved' => false, 'error' => 'Đơn xét duyệt không còn ở trạng thái chờ duyệt.'];
            }

            if ($profile) {
                $profile->unsetRelation('teachingCategories');
            }
            $lockedUser->setRelation('instructorProfile', $profile);
            $lockedUser->setRelation('instructorCertificates', $lockedCertificates);
            $lockedUser->setRelation('instructorApplication', $application);

            // Re-check under the same locks used by upload/submit/review so an
            // application cannot be approved from stale requirement state.
            $completeness = $requirements->checkCanApproveInstructor($lockedUser);
            if (! $completeness['can_approve']) {
                return ['approved' => false, 'missing_titles' => $completeness['missing_titles'], 'error' => null];
            }

            $lockedUser->update([
                'instructor_status' => 'approved',
                'needs_admin_review' => false,
                'admin_last_reviewed_at' => now(),
                'approved_at' => now(),
                'approved_by' => $adminId,
                'rejected_reason' => null,
            ]);

            // This is the initial, whole-profile approval path. It promotes the
            // instructor's existing initial fields only; later field requests are
            // reviewed individually.
            if ($profile) {
                $initialFields = $fields->where('approval_status', InstructorTeachingField::STATUS_DRAFT);
                foreach ($initialFields as $field) {
                    $field->update([
                        'approval_status' => InstructorTeachingField::STATUS_APPROVED,
                        'reviewed_at' => now(),
                        'reviewed_by' => $adminId,
                        'rejection_reason' => null,
                    ]);
                }

                if (! $fields->contains(fn (InstructorTeachingField $field) => $field->isApproved() && $field->is_primary)) {
                    $primary = $initialFields->firstWhere('category_id', $profile->category_id) ?? $initialFields->first();
                    if ($primary) {
                        $primary->update(['is_primary' => true]);
                        $profile->update(['category_id' => $primary->category_id]);
                    }
                }
            }

            $requirementIds = $requirements->getCurrentRequirementIds($lockedUser);
            $lockedUser->instructorCertificates()
                ->where('status', 'pending')
                ->whereIn('requirement_id', $requirementIds)
                ->update([
                    'status' => 'approved',
                    'reviewed_at' => now(),
                    'reviewed_by' => $adminId,
                ]);

            $application->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $adminId,
            ]);

            return ['approved' => true];
        }, 3);

        if (! $approval['approved']) {
            if ($approval['error'] ?? null) {
                return back()->with('error', $approval['error']);
            }

            $missingList = implode(', ', $approval['missing_titles'] ?? []);

            return back()->with('error', "Không thể duyệt hồ sơ. Giảng viên còn thiếu tài liệu bắt buộc của ngành: {$missingList}.");
        }

        $user->refresh();

        try {
            $user->notify(new InstructorApprovedNotification);
            app(NotificationService::class)->send(
                $user,
                'Chúc mừng! Hồ sơ Giảng viên đã được phê duyệt',
                'Chúc mừng! Hồ sơ giảng viên của bạn đã được Admin chấp thuận. Bạn có thể bắt đầu tạo khóa học.',
                'instructor_approved',
                route('instructor.dashboard')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi email Duyệt giảng viên thất bại: '.$e->getMessage());
        }

        // Tự động kích hoạt xuất bản toàn bộ các khóa học đã được Admin duyệt nội dung trước đó của giảng viên
        $publishedCoursesCount = app(CourseReviewService::class)->syncInstructorApprovedCourses($user);

        ActivityLogService::log($adminId, 'approve_instructor', User::class, $user->id, [
            'auto_published_courses_count' => $publishedCoursesCount,
        ], $request);

        $successMessage = 'Đã duyệt hồ sơ Giảng viên "'.$user->name.'" thành công!';
        if ($publishedCoursesCount > 0) {
            $successMessage .= " Đồng thời tự động kích hoạt xuất bản {$publishedCoursesCount} khóa học đã được duyệt trước đó.";
        }

        return redirect()->route('admin.instructors.applications.index')
            ->with('success', $successMessage);
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $request->validate([
            'rejected_reason' => ['required', 'string', 'max:1000'],
        ], [
            'rejected_reason.required' => 'Vui lòng nhập lý do từ chối hồ sơ.',
            'rejected_reason.max' => 'Lý do từ chối không quá 1000 ký tự.',
        ]);

        $reason = $request->input('rejected_reason');
        $adminId = $request->user()->id;

        $rejected = DB::transaction(function () use ($user, $reason, $adminId): bool {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            if (! $lockedUser->isGlobalReviewPending()) {
                return false;
            }

            $profile = $lockedUser->instructorProfile()->lockForUpdate()->first();
            $profile?->teachingFields()->orderBy('id')->lockForUpdate()->get();
            $certificates = $lockedUser->instructorCertificates()->orderBy('id')->lockForUpdate()->get();
            $application = $lockedUser->instructorApplication()->lockForUpdate()->first();
            if (! $application || ! $application->isPending()) {
                return false;
            }

            $lockedUser->update([
                'instructor_status' => 'rejected',
                'needs_admin_review' => false,
                'admin_last_reviewed_at' => now(),
                'rejected_reason' => $reason,
            ]);
            InstructorCertificate::query()->whereKey($certificates->where('status', 'pending')->pluck('id'))->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_at' => now(),
                'reviewed_by' => $adminId,
            ]);
            $application->update([
                'status' => 'rejected',
                'admin_notes' => $reason,
                'reviewed_at' => now(),
                'reviewed_by' => $adminId,
            ]);

            return true;
        }, 3);

        if (! $rejected) {
            return back()->with('error', 'Hồ sơ không còn ở hàng đợi chờ duyệt.');
        }

        try {
            $user->notify(new InstructorRejectedNotification($reason));
            app(NotificationService::class)->send(
                $user,
                'Thông báo về hồ sơ Giảng viên',
                'Hồ sơ đăng ký giảng viên của bạn chưa được duyệt: '.$reason,
                'instructor_rejected',
                route('instructor.pending')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi email Từ chối giảng viên thất bại: '.$e->getMessage());
        }

        ActivityLogService::log($adminId, 'reject_instructor', User::class, $user->id, [
            'reason' => $reason,
        ], $request);

        return redirect()->route('admin.instructors.applications.index')
            ->with('success', 'Đã từ chối hồ sơ Giảng viên "'.$user->name.'".');
    }

    public function reviewDocument(Request $request, User $user, InstructorCertificate $certificate): RedirectResponse
    {
        if ($certificate->user_id !== $user->id) {
            abort(404, 'Tài liệu không thuộc về giảng viên này.');
        }
        if (! $certificate->isPending()) {
            abort(422, 'Chỉ có thể xét duyệt tài liệu đã gửi.');
        }

        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000'],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái xét duyệt.',
            'rejection_reason.required_if' => 'Vui lòng nhập lý do từ chối tài liệu.',
        ]);

        $status = $request->input('status');
        $reason = $request->input('rejection_reason');
        $adminId = $request->user()->id;

        DB::transaction(function () use ($user, $certificate, $status, $reason, $adminId): void {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $profile = $lockedUser->instructorProfile()->lockForUpdate()->first();
            $profile?->teachingFields()->orderBy('id')->lockForUpdate()->get();
            $certificates = $lockedUser->instructorCertificates()->orderBy('id')->lockForUpdate()->get();
            $locked = $certificates->firstWhere('id', $certificate->id);
            abort_unless($locked, 404);
            abort_unless((int) $locked->user_id === (int) $user->id, 404, 'Tài liệu không thuộc về giảng viên này.');
            abort_unless($locked->isPending(), 422, 'Chỉ có thể xét duyệt tài liệu đã gửi.');
            $locked->update([
                'status' => $status,
                'rejection_reason' => $status === 'rejected' ? $reason : null,
                'reviewed_at' => now(),
                'reviewed_by' => $adminId,
            ]);
        });

        ActivityLogService::log($adminId, 'review_instructor_document', InstructorCertificate::class, $certificate->id, [
            'status' => $status,
            'reason' => $reason,
        ], $request);

        $docName = $certificate->title ?: $certificate->original_name;
        if ($status === 'rejected') {
            app(NotificationService::class)->send(
                $user,
                'Tài liệu minh chứng bị từ chối',
                "Tài liệu '{$docName}' của bạn đã bị từ chối: {$reason}. Vui lòng cập nhật tài liệu khác.",
                'instructor_document_rejected',
                route('instructor.profile')
            );
        } else {
            app(NotificationService::class)->send(
                $user,
                'Tài liệu minh chứng đã được duyệt',
                "Tài liệu '{$docName}' của bạn đã được Admin chấp thuận.",
                'instructor_document_approved',
                route('instructor.profile')
            );
        }

        return back()->with('success', 'Đã cập nhật trạng thái tài liệu "'.$docName.'" thành công.');
    }

    public function approveReactivation(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $adminId = $request->user()->id;

        $user->unlockAccount('active', 'approved');

        ActivityLogService::log($adminId, 'approve_instructor_reactivation', User::class, $user->id, [], $request);

        try {
            app(NotificationService::class)->send(
                $user,
                'Tài khoản Giảng viên đã được mở khóa',
                'Chúc mừng! Yêu cầu cấp lại quyền giảng viên của bạn đã được Ban quản trị phê duyệt. Bạn có thể tiếp tục sử dụng đầy đủ các chức năng.',
                'instructor_reactivation_approved',
                route('instructor.dashboard')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo mở khóa tài khoản giảng viên thất bại: '.$e->getMessage());
        }

        return back()->with('success', 'Đã phê duyệt mở khóa và cấp lại quyền giảng viên cho "'.$user->name.'".');
    }

    public function rejectReactivation(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ], [
            'notes.required' => 'Vui lòng nhập lý do từ chối yêu cầu cấp lại quyền.',
        ]);

        $notes = $request->input('notes');
        $adminId = $request->user()->id;

        $user->update([
            'reactivation_status' => 'rejected',
            'locked_reason' => $notes,
        ]);

        ActivityLogService::log($adminId, 'reject_instructor_reactivation', User::class, $user->id, [
            'reason' => $notes,
        ], $request);

        try {
            app(NotificationService::class)->send(
                $user,
                'Yêu cầu cấp lại quyền Giảng viên bị từ chối',
                'Yêu cầu mở khóa và cấp lại quyền giảng viên của bạn chưa được chấp thuận: '.$notes,
                'instructor_reactivation_rejected',
                route('instructor.profile')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo từ chối cấp lại giảng viên thất bại: '.$e->getMessage());
        }

        return back()->with('success', 'Đã từ chối yêu cầu cấp lại quyền giảng viên cho "'.$user->name.'".');
    }
}
