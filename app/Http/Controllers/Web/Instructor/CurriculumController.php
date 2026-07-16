<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreChapterRequest;
use App\Http\Requests\Instructor\StoreLessonRequest;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function index(Course $course): View
    {
        $this->authorizeCourse($course);

        $course->load([
            'courseSections.lessons' => fn ($query) => $query->orderBy('sort_order')->with(['videoModeration', 'assignment']),
            'chapters.lessons' => fn ($query) => $query->orderBy('sort_order')->with(['videoModeration', 'assignment']),
        ]);

        return view('instructor.courses.curriculum', [
            'course' => $course,
            'lessonTypes' => $this->lessonTypes(),
            'lessonStatuses' => $this->lessonStatuses(),
        ]);
    }

    public function storeSection(StoreChapterRequest $request, Course $course): RedirectResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validated();

        CourseSection::create([
            ...$validated,
            'course_id' => $course->id,
            'sort_order' => $course->courseSections()->count(),
        ]);

        return back()->with('success', 'Đã thêm chương học.');
    }

    public function updateSection(StoreChapterRequest $request, Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeSection($course, $section);

        $section->update($request->validated());

        return back()->with('success', 'Đã cập nhật chương học.');
    }

    public function destroySection(Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeSection($course, $section);

        $section->lessons()->get()->each(fn (Lesson $lesson) => $this->deleteLessonFiles($lesson));
        $section->delete();

        return back()->with('success', 'Đã xóa chương học.');
    }

    public function storeLesson(StoreLessonRequest $request, Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeSection($course, $section);

        $validated = $request->validated();
        $lessonData = $this->lessonData($validated);
        $lessonData = $this->storeLessonDocument($request, $lessonData);
        $lessonData = $this->storeLessonVideo($request, $lessonData);

        $lesson = Lesson::create([
            ...$lessonData,
            'course_id' => $course->id,
            'section_id' => $section->id,
            'chapter_id' => null,
            'duration_seconds' => $lessonData['duration'] ?? 0,
            'is_preview' => $request->boolean('is_preview'),
            'sort_order' => $lessonData['sort_order'] ?? $section->lessons()->count(),
            'status' => $lessonData['status'] ?? 'draft',
        ]);

        $this->syncAssignment($lesson, $validated);

        if ($lesson->type === 'quiz') {
            return redirect()
                ->route('instructor.courses.lessons.quiz.show', [$course, $lesson])
                ->with('success', 'Đã tạo bài quiz. Bạn có thể thêm câu hỏi ngay bên dưới.');
        }

        return back()->with('success', 'Đã thêm bài học.');
    }

    public function updateLesson(StoreLessonRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $validated = $request->validated();
        $lessonData = $this->lessonData($validated);
        $lessonData = $this->storeLessonDocument($request, $lessonData, $lesson);
        $lessonData = $this->storeLessonVideo($request, $lessonData, $lesson);

        $lesson->update([
            ...$lessonData,
            'duration_seconds' => $lessonData['duration'] ?? 0,
            'is_preview' => $request->boolean('is_preview'),
            'sort_order' => $lessonData['sort_order'] ?? $lesson->sort_order,
            'status' => $lessonData['status'] ?? 'draft',
        ]);

        $lesson->refresh();
        $this->syncAssignment($lesson, $validated);

        if ($lesson->type === 'quiz') {
            return redirect()
                ->route('instructor.courses.lessons.quiz.show', [$course, $lesson])
                ->with('success', 'Đã cập nhật bài quiz. Bạn có thể quản lý câu hỏi tại đây.');
        }

        return back()->with('success', 'Đã cập nhật bài học.');
    }

    public function destroyLesson(Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $this->deleteLessonFiles($lesson);
        $lesson->delete();

        return back()->with('success', 'Đã xóa bài học.');
    }

    private function authorizeCourse(Course $course): void
    {
        abort_unless($course->isOwnedBy(auth()->user()), 403);
    }

    private function authorizeSection(Course $course, CourseSection $section): void
    {
        $this->authorizeCourse($course);
        abort_unless((int) $section->course_id === (int) $course->id, 403);
    }

    private function authorizeLesson(Course $course, Lesson $lesson): void
    {
        $this->authorizeCourse($course);
        abort_unless((int) $lesson->course_id === (int) $course->id, 403);
    }

    private function deleteLessonDocument(Lesson $lesson): void
    {
        if ($lesson->document_file) {
            Storage::disk('public')->delete($lesson->document_file);
        }
    }

    private function lessonData(array $validated): array
    {
        unset(
            $validated['video_file'],
            $validated['document_file'],
            $validated['assignment_due_days'],
            $validated['assignment_max_score'],
            $validated['assignment_passing_score']
        );

        if (($validated['type'] ?? null) !== 'video') {
            unset($validated['video_url']);
        }

        if (! in_array($validated['type'] ?? null, ['video', 'document', 'assignment'], true)) {
            unset($validated['content']);
        }

        return $validated;
    }

    private function storeLessonDocument(Request $request, array $validated, ?Lesson $lesson = null): array
    {
        if (! in_array($validated['type'] ?? null, ['document', 'assignment'], true) || ! $request->hasFile('document_file')) {
            return $validated;
        }

        $path = $request->file('document_file')->store('lesson-documents', 'public');

        if ($lesson) {
            $this->deleteLessonDocument($lesson);
        }

        return [
            ...$validated,
            'document_file' => $path,
        ];
    }

    private function storeLessonVideo(Request $request, array $validated, ?Lesson $lesson = null): array
    {
        unset($validated['video_file']);

        if (($validated['type'] ?? null) !== 'video' || ! $request->hasFile('video_file')) {
            return $validated;
        }

        $file = $request->file('video_file');
        $path = $file->store('lesson-videos', 'public');

        if ($lesson) {
            $this->deleteLessonVideo($lesson);
        }

        return [
            ...$validated,
            'video_path' => $path,
            'video_original_name' => $file->getClientOriginalName(),
            'video_mime' => $file->getClientMimeType(),
            'video_size' => $file->getSize(),
        ];
    }

    private function syncAssignment(Lesson $lesson, array $validated): void
    {
        if ($lesson->type !== 'assignment') {
            return;
        }

        $lesson->loadMissing('assignment');
        $description = trim((string) ($validated['content'] ?? ''));
        $existing = $lesson->assignment;

        $lesson->assignment()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'course_id' => $lesson->course_id,
                'title' => $lesson->title,
                'description' => $description !== '' ? $description : $lesson->title,
                'instructions' => $description !== '' ? $description : null,
                'max_score' => $validated['assignment_max_score'] ?? $existing?->max_score ?? 100,
                'passing_score' => $validated['assignment_passing_score'] ?? $existing?->passing_score ?? 70,
                'due_days' => $validated['assignment_due_days'] ?? $existing?->due_days,
                'is_required' => true,
                'allowed_file_types' => $existing?->allowed_file_types ?? 'pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar',
                'maximum_file_size' => $existing?->maximum_file_size ?? 10240,
            ],
        );
    }

    private function deleteLessonFiles(Lesson $lesson): void
    {
        $this->deleteLessonDocument($lesson);
        $this->deleteLessonVideo($lesson);
    }

    private function deleteLessonVideo(Lesson $lesson): void
    {
        if ($lesson->video_path) {
            Storage::disk('public')->delete($lesson->video_path);
        }
    }

    private function lessonTypes(): array
    {
        return [
            'video' => 'Video',
            'document' => 'Tài liệu',
            'quiz' => 'Quiz',
            'assignment' => 'Bài tập',
        ];
    }

    private function lessonStatuses(): array
    {
        return [
            'draft' => 'Nháp',
            'published' => 'Đã sẵn sàng',
        ];
    }
}
