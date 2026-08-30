<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\ContentVersionComparisonService;
use App\Services\ContentVersionHistoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentVersionHistoryController extends Controller
{
    public function __construct(
        private readonly ContentVersionHistoryService $history,
        private readonly ContentVersionComparisonService $comparison,
    ) {}

    public function index(Request $request, Course $course): View
    {
        return view('admin.content-versions.index', [
            'course' => $course,
            'filter' => $request->query('type'),
            'timeline' => $this->history->timeline($course, $request->query('type')),
        ]);
    }

    public function show(Course $course, string $type, int $version): View
    {
        $resolved = $this->history->resolve($course, $type, $version);

        return view('admin.content-versions.show', [
            'course' => $course,
            'detail' => $this->history->detail($course, $type, $resolved),
            'siblings' => $this->history->siblings($course, $type, $resolved),
        ]);
    }

    public function compare(Request $request, Course $course, string $type, int $version): View
    {
        $from = $this->history->resolve($course, $type, $version);
        $siblings = $this->history->siblings($course, $type, $from);
        $toId = (int) $request->query('to', $siblings->firstWhere('status', 'published')?->id);
        $to = $this->history->resolve($course, $type, $toId);

        return view('admin.content-versions.compare', [
            'course' => $course,
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'fields' => $this->comparison->compare($course, $type, $from, $to),
            'siblings' => $siblings,
        ]);
    }
}
