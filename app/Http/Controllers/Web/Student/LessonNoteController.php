<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\StoreLessonNoteRequest;
use App\Http\Requests\Learning\UpdateLessonNoteRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Services\LessonNoteAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonNoteController extends Controller
{
    public function __construct(
        private readonly LessonNoteAccessService $access,
    ) {}

    public function index(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        $this->access->assertCanUse($request->user(), $course, $lesson);

        $notes = $this->lessonNotesQuery($request->user()->id, $lesson)
            ->get()
            ->map(fn (LessonNote $note) => $this->notePayload($note, $course));

        return response()->json([
            'success' => true,
            'notes' => $notes,
        ]);
    }

    public function store(StoreLessonNoteRequest $request, Course $course, Lesson $lesson): JsonResponse
    {
        $this->access->assertCanUse($request->user(), $course, $lesson);

        $note = LessonNote::query()->create([
            'user_id' => $request->user()->id,
            'lesson_id' => $lesson->id,
            'content' => $request->validated('content'),
            'timestamp_seconds' => $this->normalizedTimestamp($lesson, $request->validated('timestamp_seconds')),
        ]);

        $note->load('lesson');

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu ghi chú.',
            'note' => $this->notePayload($note, $course),
        ], 201);
    }

    public function update(UpdateLessonNoteRequest $request, LessonNote $lessonNote): JsonResponse
    {
        $lessonNote->loadMissing(['lesson.course', 'lesson.section.course', 'lesson.chapter.course']);

        $lessonNote->update([
            'content' => $request->validated('content'),
            'timestamp_seconds' => $this->normalizedTimestamp($lessonNote->lesson, $request->validated('timestamp_seconds')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật ghi chú.',
            'note' => $this->notePayload($lessonNote->fresh(['lesson.course', 'lesson.section.course', 'lesson.chapter.course'])),
        ]);
    }

    public function destroy(Request $request, LessonNote $lessonNote): JsonResponse
    {
        $this->authorize('delete', $lessonNote);

        $lessonNote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ghi chú.',
        ]);
    }

    private function lessonNotesQuery(int $userId, Lesson $lesson)
    {
        return LessonNote::query()
            ->where('user_id', $userId)
            ->where('lesson_id', $lesson->id)
            ->when(
                $lesson->type === 'video',
                fn ($query) => $query->orderBy('timestamp_seconds')->orderBy('created_at'),
                fn ($query) => $query->latest()
            );
    }

    private function normalizedTimestamp(Lesson $lesson, mixed $timestamp): ?int
    {
        if ($lesson->type !== 'video') {
            return null;
        }

        return $timestamp === null ? null : max(0, (int) $timestamp);
    }

    private function notePayload(LessonNote $note, ?Course $course = null): array
    {
        $note->loadMissing(['lesson.course', 'lesson.section.course', 'lesson.chapter.course']);
        $course ??= $note->learningCourse();

        $lessonUrl = $course
            ? route('courses.lessons.show', [$course, $note->lesson])
            : null;

        if ($lessonUrl && $note->timestamp_seconds !== null) {
            $lessonUrl .= (str_contains($lessonUrl, '?') ? '&' : '?').'t='.$note->timestamp_seconds;
        }

        return [
            'id' => $note->id,
            'content' => $note->content,
            'timestamp_seconds' => $note->timestamp_seconds,
            'timestamp_label' => $note->timestampLabel(),
            'created_at' => $note->created_at?->format('d/m/Y H:i'),
            'updated_at' => $note->updated_at?->format('d/m/Y H:i'),
            'course_title' => $course?->title,
            'section_title' => $note->sectionTitle(),
            'lesson_title' => $note->lesson?->title,
            'lesson_type' => $note->lesson?->type,
            'lesson_url' => $lessonUrl,
            'update_url' => route('lesson-notes.update', $note),
            'delete_url' => route('lesson-notes.destroy', $note),
        ];
    }
}
