<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function index(Course $course): View
    {
        $this->authorizeCourse($course);

        $course->load([
            'courseSections.lessons' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        return view('instructor.courses.curriculum', [
            'course' => $course,
            'lessonTypes' => $this->lessonTypes(),
            'lessonStatuses' => $this->lessonStatuses(),
        ]);
    }

    public function storeSection(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        CourseSection::create([
            ...$validated,
            'course_id' => $course->id,
            'sort_order' => $course->courseSections()->count(),
        ]);

        return back()->with('success', 'Đã thêm chương học.');
    }

    public function updateSection(Request $request, Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeSection($course, $section);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $section->update($validated);

        return back()->with('success', 'Đã cập nhật chương học.');
    }

    public function destroySection(Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeSection($course, $section);

        $section->lessons()->get()->each(fn (Lesson $lesson) => $this->deleteLessonDocument($lesson));
        $section->delete();

        return back()->with('success', 'Đã xóa chương học.');
    }

    public function storeLesson(Request $request, Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeSection($course, $section);

        $validated = $this->validatedLessonData($request);

        if ($request->hasFile('document_file')) {
            $validated['document_file'] = $request->file('document_file')->store('lesson-documents', 'public');
        }

        Lesson::create([
            ...$validated,
            'course_id' => $course->id,
            'section_id' => $section->id,
            'chapter_id' => null,
            'duration_seconds' => $validated['duration'] ?? 0,
            'is_preview' => $request->boolean('is_preview'),
            'sort_order' => $section->lessons()->count(),
            'status' => $validated['status'] ?? 'draft',
        ]);

        return back()->with('success', 'Đã thêm bài học.');
    }

    public function updateLesson(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $validated = $this->validatedLessonData($request);

        if ($request->hasFile('document_file')) {
            $this->deleteLessonDocument($lesson);
            $validated['document_file'] = $request->file('document_file')->store('lesson-documents', 'public');
        }

        $lesson->update([
            ...$validated,
            'duration_seconds' => $validated['duration'] ?? 0,
            'is_preview' => $request->boolean('is_preview'),
            'status' => $validated['status'] ?? 'draft',
        ]);

        return back()->with('success', 'Đã cập nhật bài học.');
    }

    public function destroyLesson(Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $this->deleteLessonDocument($lesson);
        $lesson->delete();

        return back()->with('success', 'Đã xóa bài học.');
    }

    private function validatedLessonData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys($this->lessonTypes()))],
            'video_url' => ['nullable', 'string', 'max:2048'],
            'content' => ['nullable', 'string'],
            'document_file' => ['nullable', 'file', 'max:10240'],
            'duration' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_preview' => ['sometimes', 'boolean'],
            'status' => ['nullable', Rule::in(array_keys($this->lessonStatuses()))],
        ]);
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
