<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonNote;
use App\Services\LessonNoteAccessService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonNoteLibraryController extends Controller
{
    public function __construct(
        private readonly LessonNoteAccessService $access,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isStudent(), 403);

        $accessibleCourseIds = $this->access->accessibleCourseIds($request->user());
        $search = trim((string) $request->query('search'));
        $courseId = $request->integer('course_id') ?: null;
        $sort = $request->query('sort') === 'timestamp' ? 'timestamp' : 'latest';

        $courses = Course::query()
            ->whereIn('id', $accessibleCourseIds)
            ->orderBy('title')
            ->get(['id', 'title']);

        if ($courseId && ! $accessibleCourseIds->contains($courseId)) {
            $courseId = -1;
        }

        $courseFilterIds = $courseId ? collect([$courseId]) : $accessibleCourseIds;

        $notes = LessonNote::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('lesson', fn (Builder $query) => $this->access->scopeLessonsInCourses($query, $courseFilterIds))
            ->with([
                'lesson:id,course_id,section_id,chapter_id,title,type,duration,duration_seconds',
                'lesson.course:id,title,slug',
                'lesson.section:id,course_id,title',
                'lesson.section.course:id,title,slug',
                'lesson.chapter:id,course_id,title',
                'lesson.chapter.course:id,title,slug',
            ])
            ->when($search !== '', fn (Builder $query) => $query->where('content', 'like', "%{$search}%"))
            ->when(
                $sort === 'timestamp',
                fn (Builder $query) => $query
                    ->orderByRaw('timestamp_seconds IS NULL')
                    ->orderBy('timestamp_seconds')
                    ->latest('updated_at'),
                fn (Builder $query) => $query->latest('updated_at')
            )
            ->paginate(10)
            ->withQueryString();

        return view('student.lesson-notes.index', compact('notes', 'courses', 'search', 'courseId', 'sort'));
    }
}
