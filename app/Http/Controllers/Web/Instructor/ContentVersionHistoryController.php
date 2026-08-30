<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\ContentVersionComparisonService;
use App\Services\ContentVersionHistoryService;
use App\Services\ContentVersionRollbackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentVersionHistoryController extends Controller
{
    public function __construct(
        private readonly ContentVersionHistoryService $history,
        private readonly ContentVersionComparisonService $comparison,
        private readonly ContentVersionRollbackService $rollback,
    ) {}

    public function index(Request $request, Course $course): View
    {
        $this->authorizeOwner($course, $request);

        return view('instructor.content-versions.index', [
            'course' => $course,
            'filter' => $request->query('type'),
            'timeline' => $this->history->timeline($course, $request->query('type')),
        ]);
    }

    public function show(Request $request, Course $course, string $type, int $version): View
    {
        $this->authorizeOwner($course, $request);
        $resolved = $this->history->resolve($course, $type, $version);

        return view('instructor.content-versions.show', [
            'course' => $course,
            'detail' => $this->history->detail($course, $type, $resolved),
            'siblings' => $this->history->siblings($course, $type, $resolved),
        ]);
    }

    public function compare(Request $request, Course $course, string $type, int $version): View
    {
        $this->authorizeOwner($course, $request);
        $from = $this->history->resolve($course, $type, $version);
        $siblings = $this->history->siblings($course, $type, $from);
        $toId = (int) $request->query('to', $siblings->firstWhere('status', 'published')?->id);
        $to = $this->history->resolve($course, $type, $toId);

        return view('instructor.content-versions.compare', [
            'course' => $course,
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'fields' => $this->comparison->compare($course, $type, $from, $to),
            'siblings' => $siblings,
        ]);
    }

    public function confirmRollback(Request $request, Course $course, string $type, int $version): View
    {
        $this->authorizeOwner($course, $request);
        $resolved = $this->history->resolve($course, $type, $version);
        $detail = $this->history->detail($course, $type, $resolved);
        abort_unless($detail['rollback_eligible'], 422, 'Phiên bản này không đủ điều kiện khôi phục.');

        return view('instructor.content-versions.rollback', compact('course', 'type', 'resolved', 'detail'));
    }

    public function storeRollback(Request $request, Course $course, string $type, int $version): RedirectResponse
    {
        $this->authorizeOwner($course, $request);
        $validated = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:1000']]);
        $update = $this->rollback->createDraft($course, $type, $version, $request->user(), $validated['reason']);

        return redirect()->route('instructor.courses.versions.show', [$course, $type, $version])
            ->with('success', "Đã tạo yêu cầu khôi phục nháp #{$update->id}. Nội dung hiện tại chưa thay đổi.");
    }

    private function authorizeOwner(Course $course, Request $request): void
    {
        abort_unless($course->isOwnedBy($request->user()), 403);
    }
}
