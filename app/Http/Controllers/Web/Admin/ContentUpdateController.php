<?php

namespace App\Http\Controllers\Web\Admin;

use App\Exceptions\HistoricalQuizDeletionException;
use App\Http\Controllers\Controller;
use App\Models\ContentUpdate;
use App\Models\QuizVersion;
use App\Services\ContentUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentUpdateController extends Controller
{
    public function __construct(
        protected ContentUpdateService $contentUpdateService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        $type = $request->query('type');
        $courseId = $request->query('course_id');

        $updates = ContentUpdate::query()
            ->with(['course:id,title,slug', 'creator:id,name,email', 'reviewer:id,name,email'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $statusOptions = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Đã từ chối',
        ];

        $typeOptions = [
            ContentUpdate::TYPE_COURSE => 'Khóa học',
            ContentUpdate::TYPE_CHAPTER => 'Chương học',
            ContentUpdate::TYPE_LESSON => 'Bài học',
            ContentUpdate::TYPE_QUIZ => 'Quiz',
        ];

        $quizCandidateIds = $updates->getCollection()
            ->where('type', ContentUpdate::TYPE_QUIZ)
            ->map(fn (ContentUpdate $update): int => (int) data_get($update->payload, 'quiz_version_id'))
            ->filter();
        $quizCandidates = QuizVersion::query()
            ->with('quiz.currentPublishedVersion')
            ->withCount('questionMappings')
            ->whereIn('id', $quizCandidateIds)
            ->get()
            ->keyBy('id');

        return view('admin.content-updates.index', compact(
            'updates',
            'status',
            'type',
            'courseId',
            'statusOptions',
            'typeOptions',
            'quizCandidates',
        ));
    }

    public function approve(ContentUpdate $contentUpdate): RedirectResponse
    {
        try {
            $this->contentUpdateService->applyApprovedUpdate($contentUpdate, auth()->user());
        } catch (HistoricalQuizDeletionException $exception) {
            return back()->withErrors(['content_update' => $exception->getMessage()]);
        }

        return back()->with(
            'success',
            $contentUpdate->type === ContentUpdate::TYPE_QUIZ
                ? 'Đã duyệt và kích hoạt phiên bản Quiz an toàn cho học viên mới.'
                : 'Đã phê duyệt và áp dụng bản cập nhật nội dung thành công.',
        );
    }

    public function reject(Request $request, ContentUpdate $contentUpdate): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:1000',
        ], [
            'rejection_reason.required' => 'Vui lòng nhập lý do từ chối bản cập nhật.',
            'rejection_reason.min' => 'Lý do từ chối phải có ít nhất 5 ký tự.',
        ]);

        $this->contentUpdateService->rejectUpdate($contentUpdate, auth()->user(), $request->input('rejection_reason'));

        return back()->with('success', 'Đã từ chối bản cập nhật nội dung.');
    }
}
