<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuizAttemptRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizAttemptRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isInstructorOrAdmin = $user && ($user->isInstructor() || $user->isAdmin());
        abort_unless($isInstructorOrAdmin, 403, 'Bạn không có quyền truy cập trang này.');

        $ownedCourses = $user->isAdmin()
            ? Course::query()->select('id', 'title')->orderBy('title')->get()
            : Course::query()->where('instructor_id', $user->id)->select('id', 'title')->orderBy('title')->get();

        $courseIds = $ownedCourses->pluck('id')->all();

        $query = QuizAttemptRequest::query()
            ->whereIn('course_id', $courseIds)
            ->with(['user', 'course', 'quiz', 'lesson', 'reviewer']);

        if ($request->filled('course_id') && in_array((int) $request->input('course_id'), $courseIds, true)) {
            $query->where('course_id', (int) $request->input('course_id'));
        }

        if ($request->filled('status') && in_array($request->input('status'), [
            QuizAttemptRequest::STATUS_PENDING,
            QuizAttemptRequest::STATUS_APPROVED,
            QuizAttemptRequest::STATUS_REJECTED,
        ], true)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pendingCount = QuizAttemptRequest::query()
            ->whereIn('course_id', $courseIds)
            ->where('status', QuizAttemptRequest::STATUS_PENDING)
            ->count();

        $requests = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('instructor.quiz-attempt-requests.index', [
            'requests' => $requests,
            'ownedCourses' => $ownedCourses,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function approve(Request $request, QuizAttemptRequest $attemptRequest): RedirectResponse
    {
        $user = $request->user();
        $course = $attemptRequest->course;

        abort_unless($course && ($course->isOwnedBy($user) || ($user && $user->isAdmin())), 403, 'Bạn không có quyền xử lý yêu cầu của khóa học này.');
        abort_unless($attemptRequest->isPending(), 422, 'Yêu cầu này đã được xử lý trước đó.');

        $request->validate([
            'extra_attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $extraAttempts = (int) ($request->input('extra_attempts') ?: 1);

        DB::transaction(function () use ($attemptRequest, $extraAttempts, $user) {
            $attemptRequest->update([
                'status' => QuizAttemptRequest::STATUS_APPROVED,
                'extra_attempts_granted' => $extraAttempts,
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', "Đã duyệt cấp thêm {$extraAttempts} lượt làm bài cho học viên {$attemptRequest->user?->name}.");
    }

    public function reject(Request $request, QuizAttemptRequest $attemptRequest): RedirectResponse
    {
        $user = $request->user();
        $course = $attemptRequest->course;

        abort_unless($course && ($course->isOwnedBy($user) || ($user && $user->isAdmin())), 403, 'Bạn không có quyền xử lý yêu cầu của khóa học này.');
        abort_unless($attemptRequest->isPending(), 422, 'Yêu cầu này đã được xử lý trước đó.');

        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $rejectionReason = $request->input('rejection_reason');

        DB::transaction(function () use ($attemptRequest, $rejectionReason, $user) {
            $attemptRequest->update([
                'status' => QuizAttemptRequest::STATUS_REJECTED,
                'rejection_reason' => $rejectionReason,
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', "Đã từ chối yêu cầu cấp lại lượt của học viên {$attemptRequest->user?->name}.");
    }
}
