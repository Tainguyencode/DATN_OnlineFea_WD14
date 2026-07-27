<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonNotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_can_create_lesson_note(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.notes.store', [$course, $lesson]), [
                'content' => 'Ghi chú đầu tiên',
                'timestamp_seconds' => 12,
            ])
            ->assertCreated()
            ->assertJsonPath('note.content', 'Ghi chú đầu tiên')
            ->assertJsonPath('note.timestamp_seconds', 12);

        $this->assertDatabaseHas('lesson_notes', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'content' => 'Ghi chú đầu tiên',
            'timestamp_seconds' => 12,
        ]);
    }

    public function test_unenrolled_student_is_blocked_from_creating_note(): void
    {
        [, $course, $lesson] = $this->courseLessonSetup();
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.notes.store', [$course, $lesson]), [
                'content' => 'Không có quyền',
                'timestamp_seconds' => 10,
            ])
            ->assertForbidden();
    }

    public function test_lesson_note_index_only_returns_owner_notes(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $other = User::factory()->create(['role' => 'student']);
        $this->enroll($other, $course);

        LessonNote::query()->create([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'content' => 'Ghi chú của tôi',
            'timestamp_seconds' => 20,
        ]);
        LessonNote::query()->create([
            'user_id' => $other->id,
            'lesson_id' => $lesson->id,
            'content' => 'Ghi chú người khác',
            'timestamp_seconds' => 30,
        ]);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.notes.index', [$course, $lesson]))
            ->assertOk()
            ->assertJsonCount(1, 'notes')
            ->assertJsonPath('notes.0.content', 'Ghi chú của tôi');
    }

    public function test_student_cannot_update_or_delete_another_students_note(): void
    {
        [$owner, $course, $lesson] = $this->enrolledLessonSetup();
        $other = User::factory()->create(['role' => 'student']);
        $this->enroll($other, $course);

        $note = LessonNote::query()->create([
            'user_id' => $owner->id,
            'lesson_id' => $lesson->id,
            'content' => 'Riêng tư',
            'timestamp_seconds' => 15,
        ]);

        $this->actingAs($other)
            ->patchJson(route('lesson-notes.update', $note), [
                'content' => 'Cố sửa',
                'timestamp_seconds' => 16,
            ])
            ->assertForbidden();

        $this->actingAs($other)
            ->deleteJson(route('lesson-notes.destroy', $note))
            ->assertForbidden();
    }

    public function test_video_note_stores_timestamp_seconds(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup(['type' => 'video', 'duration_seconds' => 600]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.notes.store', [$course, $lesson]), [
                'content' => 'Mốc video',
                'timestamp_seconds' => 342,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('lesson_notes', [
            'lesson_id' => $lesson->id,
            'timestamp_seconds' => 342,
        ]);
    }

    public function test_non_video_lessons_always_store_null_timestamp(): void
    {
        [$student, $course, $documentLesson] = $this->enrolledLessonSetup(['type' => 'document']);
        $quizLesson = $this->lesson($course, ['type' => 'quiz']);

        foreach ([$documentLesson, $quizLesson] as $lesson) {
            $this->actingAs($student)
                ->postJson(route('courses.lessons.notes.store', [$course, $lesson]), [
                    'content' => 'Không có mốc thời gian',
                    'timestamp_seconds' => 99,
                ])
                ->assertCreated()
                ->assertJsonPath('note.timestamp_seconds', null);

            $this->assertDatabaseHas('lesson_notes', [
                'lesson_id' => $lesson->id,
                'timestamp_seconds' => null,
            ]);
        }
    }

    public function test_owner_can_update_content_and_timestamp(): void
    {
        [$student, , $lesson] = $this->enrolledLessonSetup(['duration_seconds' => 400]);
        $note = LessonNote::query()->create([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'content' => 'Cũ',
            'timestamp_seconds' => 10,
        ]);

        $this->actingAs($student)
            ->patchJson(route('lesson-notes.update', $note), [
                'content' => 'Nội dung mới',
                'timestamp_seconds' => 120,
            ])
            ->assertOk()
            ->assertJsonPath('note.content', 'Nội dung mới')
            ->assertJsonPath('note.timestamp_seconds', 120);
    }

    public function test_owner_can_soft_delete_note(): void
    {
        [$student, , $lesson] = $this->enrolledLessonSetup();
        $note = LessonNote::query()->create([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'content' => 'Xóa mềm',
            'timestamp_seconds' => 10,
        ]);

        $this->actingAs($student)
            ->deleteJson(route('lesson-notes.destroy', $note))
            ->assertOk();

        $this->assertSoftDeleted('lesson_notes', ['id' => $note->id]);
    }

    public function test_content_longer_than_2000_characters_is_rejected(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.notes.store', [$course, $lesson]), [
                'content' => str_repeat('a', 2001),
                'timestamp_seconds' => 1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('content');
    }

    public function test_negative_or_out_of_range_timestamp_is_rejected(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup(['duration_seconds' => 100]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.notes.store', [$course, $lesson]), [
                'content' => 'Mốc âm',
                'timestamp_seconds' => -1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('timestamp_seconds');

        $this->actingAs($student)
            ->postJson(route('courses.lessons.notes.store', [$course, $lesson]), [
                'content' => 'Vượt duration',
                'timestamp_seconds' => 101,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('timestamp_seconds');
    }

    public function test_study_notes_page_only_shows_current_users_notes(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $other = User::factory()->create(['role' => 'student']);
        $this->enroll($other, $course);

        LessonNote::query()->create([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'content' => 'Chỉ tôi thấy',
            'timestamp_seconds' => 5,
        ]);
        LessonNote::query()->create([
            'user_id' => $other->id,
            'lesson_id' => $lesson->id,
            'content' => 'Không hiện ra',
            'timestamp_seconds' => 6,
        ]);

        $this->actingAs($student)
            ->get(route('student.lesson-notes.index'))
            ->assertOk()
            ->assertSee('Chỉ tôi thấy')
            ->assertDontSee('Không hiện ra');
    }

    public function test_study_notes_search_course_filter_sort_and_pagination_query_string_work(): void
    {
        [$student, $courseA, $lessonA] = $this->enrolledLessonSetup(['duration_seconds' => 100]);
        [, $courseB, $lessonB] = $this->courseLessonSetup(['duration_seconds' => 100]);
        $this->enroll($student, $courseB);

        LessonNote::query()->create([
            'user_id' => $student->id,
            'lesson_id' => $lessonA->id,
            'content' => 'alpha filter note',
            'timestamp_seconds' => 40,
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);
        LessonNote::query()->create([
            'user_id' => $student->id,
            'lesson_id' => $lessonB->id,
            'content' => 'beta filter note',
            'timestamp_seconds' => 10,
        ]);

        $this->actingAs($student)
            ->get(route('student.lesson-notes.index', ['search' => 'alpha']))
            ->assertOk()
            ->assertSee('alpha filter note')
            ->assertDontSee('beta filter note');

        $this->actingAs($student)
            ->get(route('student.lesson-notes.index', ['course_id' => $courseB->id]))
            ->assertOk()
            ->assertSee('beta filter note')
            ->assertDontSee('alpha filter note');

        $this->actingAs($student)
            ->get(route('student.lesson-notes.index', ['sort' => 'timestamp']))
            ->assertOk()
            ->assertSeeInOrder(['beta filter note', 'alpha filter note']);

        for ($i = 0; $i < 11; $i++) {
            LessonNote::query()->create([
                'user_id' => $student->id,
                'lesson_id' => $lessonA->id,
                'content' => "pagekeep {$i}",
                'timestamp_seconds' => $i,
            ]);
        }

        $this->actingAs($student)
            ->get(route('student.lesson-notes.index', ['search' => 'pagekeep']))
            ->assertOk()
            ->assertSee('search=pagekeep', false);
    }

    private function enrolledLessonSetup(array $lessonAttributes = []): array
    {
        [$student, $course, $lesson] = $this->courseLessonSetup($lessonAttributes);
        $this->enroll($student, $course);

        return [$student, $course, $lesson];
    }

    private function courseLessonSetup(array $lessonAttributes = []): array
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);

        $course = Course::query()->create([
            'instructor_id' => $instructor->id,
            'title' => 'Laravel FEA '.uniqid(),
            'slug' => 'laravel-fea-'.uniqid(),
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'price' => 0,
            'language' => 'vi',
        ]);

        $lesson = $this->lesson($course, $lessonAttributes);

        return [$student, $course, $lesson];
    }

    private function lesson(Course $course, array $attributes = []): Lesson
    {
        $chapter = Chapter::query()->create([
            'course_id' => $course->id,
            'title' => 'Chương '.uniqid(),
            'sort_order' => 1,
        ]);

        $section = CourseSection::query()->create([
            'course_id' => $course->id,
            'title' => 'Phần '.uniqid(),
            'sort_order' => 1,
        ]);

        return Lesson::query()->create(array_merge([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'chapter_id' => $chapter->id,
            'title' => 'Bài học '.uniqid(),
            'type' => 'video',
            'content' => 'Nội dung bài học',
            'duration_seconds' => 600,
            'duration' => 600,
            'is_preview' => false,
            'is_required' => true,
            'sort_order' => 1,
            'status' => 'published',
        ], $attributes));
    }

    private function enroll(User $student, Course $course): Enrollment
    {
        return Enrollment::query()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'progress_percent' => 0,
            'enrolled_at' => now(),
        ]);
    }
}
