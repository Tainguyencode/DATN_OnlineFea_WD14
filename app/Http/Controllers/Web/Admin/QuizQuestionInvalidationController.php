<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizVersionQuestionInvalidation;
use App\Services\QuizQuestionInvalidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizQuestionInvalidationController extends Controller
{
    public function __construct(private readonly QuizQuestionInvalidationService $service) {}

    public function index(Request $request): View
    {
        $status = $request->query('status', QuizVersionQuestionInvalidation::STATUS_PENDING);
        $statuses = [
            QuizVersionQuestionInvalidation::STATUS_PENDING,
            QuizVersionQuestionInvalidation::STATUS_ACTIVE,
            QuizVersionQuestionInvalidation::STATUS_REJECTED,
        ];
        if (! in_array($status, $statuses, true)) {
            $status = QuizVersionQuestionInvalidation::STATUS_PENDING;
        }

        $invalidations = QuizVersionQuestionInvalidation::query()
            ->with([
                'mapping.quizVersion.quiz.lesson.course',
                'mapping.questionVersion',
                'requestedBy:id,name,email',
                'reviewedBy:id,name,email',
            ])
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.quiz-invalidations.index', compact('invalidations', 'status'));
    }

    public function show(QuizVersionQuestionInvalidation $invalidation): View
    {
        $invalidation->load([
            'mapping.quizVersion.quiz.lesson.course',
            'mapping.quizVersion.quiz.lesson.section.course',
            'mapping.quizVersion.quiz.lesson.chapter.course',
            'mapping.questionVersion.options',
            'requestedBy:id,name,email',
            'invalidatedBy:id,name,email',
            'reviewedBy:id,name,email',
        ]);
        $counts = $this->service->counts($invalidation->mapping);

        return view('admin.quiz-invalidations.show', compact('invalidation', 'counts'));
    }

    public function approve(QuizVersionQuestionInvalidation $invalidation): RedirectResponse
    {
        $result = $this->service->approve($invalidation, auth()->user());
        $message = $result['queued']
            ? 'Đã phê duyệt. Regrade được đưa vào hàng đợi để xử lý theo lô.'
            : 'Đã phê duyệt và hoàn tất regrade các lượt làm bị ảnh hưởng.';

        return redirect()->route('admin.quiz-invalidations.show', $result['invalidation'])->with('success', $message);
    }

    public function reject(Request $request, QuizVersionQuestionInvalidation $invalidation): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:5000'],
        ], [
            'rejection_reason.required' => 'Vui lòng nhập lý do từ chối.',
            'rejection_reason.min' => 'Lý do từ chối phải có ít nhất 5 ký tự.',
        ]);

        $this->service->reject($invalidation, auth()->user(), $validated['rejection_reason']);

        return redirect()->route('admin.quiz-invalidations.show', $invalidation)->with('success', 'Đã từ chối yêu cầu hủy câu hỏi.');
    }
}
