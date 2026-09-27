<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentVersion;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseVersion;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Models\Submission;
use App\Models\User;
use App\Services\ContentVersionBackfillService;
use App\Services\ContentVersionService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContentVersionFoundationTest extends TestCase
{
    use RefreshDatabase;

    private function publishedCurriculum(): array
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'Kỹ thuật', 'slug' => 'ky-thuat']);
        $course = Course::create(['instructor_id' => $instructor->id, 'category_id' => $category->id, 'title' => 'Khóa học V1', 'slug' => 'khoa-hoc-v1', 'short_description' => 'Ngắn', 'description' => 'Mô tả V1', 'objectives' => 'Mục tiêu', 'price' => 100000, 'language' => 'vi', 'level' => 'beginner', 'status' => Course::STATUS_PUBLISHED, 'is_published' => true, 'published_at' => now()]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương V1', 'description' => 'Mô tả chương', 'sort_order' => 1]);
        $lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Bài video V1', 'type' => Lesson::TYPE_VIDEO, 'content' => 'Nội dung V1', 'video_path' => 'lesson-hls/v1/playlist.m3u8', 'original_video_key' => 'source/v1.mp4', 'hls_manifest_key' => 'hls/v1/playlist.m3u8', 'video_original_name' => 'v1.mp4', 'duration_seconds' => 600, 'duration' => 600, 'sort_order' => 1, 'status' => Lesson::STATUS_PUBLISHED]);
        $assignmentLesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Bài tập V1', 'type' => Lesson::TYPE_ASSIGNMENT, 'content' => 'Nộp file', 'duration_seconds' => 300, 'duration' => 300, 'sort_order' => 2, 'status' => Lesson::STATUS_PUBLISHED]);
        $assignment = Assignment::create(['course_id' => $course->id, 'lesson_id' => $assignmentLesson->id, 'title' => 'Bài tập V1', 'description' => 'Mô tả bài tập', 'instructions' => 'Nộp file PDF', 'due_days' => 7, 'max_score' => 100, 'passing_score' => 70]);

        return [$instructor, $course, $section, $lesson, $assignment];
    }

    public function test_initial_snapshots_set_identity_pointers_and_preserve_live_rows(): void
    {
        [$instructor, $course, $section, $lesson, $assignment] = $this->publishedCurriculum();
        $versions = app(ContentVersionService::class);

        $courseVersion = $versions->createInitialCourseVersion($course, $instructor);
        $sectionVersion = $versions->createInitialSectionVersion($section, $instructor);
        $lessonVersion = $versions->createInitialLessonVersion($lesson, $instructor);
        $assignmentVersion = $versions->createInitialAssignmentVersion($assignment, $instructor);

        $this->assertSame($courseVersion->id, $course->fresh()->published_version_id);
        $this->assertSame($sectionVersion->id, $section->fresh()->published_version_id);
        $this->assertSame($lessonVersion->id, $lesson->fresh()->published_version_id);
        $this->assertSame($assignmentVersion->id, $assignment->fresh()->published_version_id);
        $this->assertSame('Khóa học V1', $courseVersion->title);
        $this->assertSame('lesson-hls/v1/playlist.m3u8', $lessonVersion->video_path);
        $this->assertSame(70, $assignmentVersion->passing_score);
    }

    public function test_snapshots_remain_unchanged_when_legacy_live_rows_change(): void
    {
        [$instructor, $course, , $lesson, $assignment] = $this->publishedCurriculum();
        $versions = app(ContentVersionService::class);
        $courseVersion = $versions->createInitialCourseVersion($course, $instructor);
        $lessonVersion = $versions->createInitialLessonVersion($lesson, $instructor);
        $assignmentVersion = $versions->createInitialAssignmentVersion($assignment, $instructor);

        $course->update(['title' => 'Khóa học V2']);
        $lesson->update(['content' => 'Nội dung V2', 'video_path' => 'lesson-hls/v2/playlist.m3u8']);
        $assignment->update(['instructions' => 'Nộp video', 'passing_score' => 80]);

        $this->assertSame('Khóa học V1', $courseVersion->fresh()->title);
        $this->assertSame('Nội dung V1', $lessonVersion->fresh()->content);
        $this->assertSame('lesson-hls/v1/playlist.m3u8', $lessonVersion->fresh()->video_path);
        $this->assertSame('Nộp file PDF', $assignmentVersion->fresh()->instructions);
        $this->assertSame(70, $assignmentVersion->fresh()->passing_score);
    }

    public function test_draft_clone_numbering_and_canonical_immutability_guard(): void
    {
        [$instructor, , , $lesson] = $this->publishedCurriculum();
        $versions = app(ContentVersionService::class);
        $v1 = $versions->createInitialLessonVersion($lesson, $instructor);
        $v2 = $versions->cloneLessonVersion($lesson, $instructor);
        $v3 = $versions->cloneLessonVersion($lesson, $instructor);

        $this->assertSame(1, $v1->version_number);
        $this->assertSame(2, $v2->version_number);
        $this->assertSame($v2->id, $v3->id);
        $versions->updateDraft($v2, ['title' => 'Bài video V2', 'content' => 'Nội dung V2']);
        $this->assertSame('Bài video V1', $v1->fresh()->title);
        $this->assertSame('Nội dung V1', $v1->fresh()->content);
        $this->expectException(ValidationException::class);
        $versions->updateDraft($v1, ['title' => 'Không được phép']);
    }

    public function test_unique_version_numbers_are_per_identity_and_submission_binding_is_nullable(): void
    {
        [$instructor, , , $lesson, $assignment] = $this->publishedCurriculum();
        $versions = app(ContentVersionService::class);
        $versions->createInitialLessonVersion($lesson, $instructor);
        $other = Lesson::create(['course_id' => $lesson->course_id, 'section_id' => $lesson->section_id, 'title' => 'Bài khác', 'type' => Lesson::TYPE_DOCUMENT, 'duration' => 1, 'status' => Lesson::STATUS_PUBLISHED]);
        $otherV1 = $versions->createInitialLessonVersion($other, $instructor);
        $this->assertSame(1, $otherV1->version_number);

        try {
            LessonVersion::create(['lesson_id' => $lesson->id, 'version_number' => 1, 'status' => LessonVersion::STATUS_DRAFT, 'title' => 'Trùng', 'type' => Lesson::TYPE_DOCUMENT]);
            $this->fail('The per-lesson unique version number was not enforced.');
        } catch (QueryException) {
            $this->assertTrue(true);
        }

        $student = User::factory()->create(['role' => 'student']);
        $legacy = Submission::create(['assignment_id' => $assignment->id, 'user_id' => $student->id, 'attempt_number' => 1, 'allowed_attempts' => 1, 'status' => 'submitted']);
        $assignmentVersion = $versions->createInitialAssignmentVersion($assignment, $instructor);
        $bound = Submission::create(['assignment_id' => $assignment->id, 'assignment_version_id' => $assignmentVersion->id, 'user_id' => User::factory()->create(['role' => 'student'])->id, 'attempt_number' => 1, 'allowed_attempts' => 1, 'status' => 'submitted']);

        $this->assertNull($legacy->assignment_version_id);
        $this->assertSame($assignmentVersion->id, $bound->assignment_version_id);
        $this->assertTrue(Schema::hasColumn('submissions', 'assignment_version_id'));
    }

    public function test_backfill_creates_published_v1_snapshots_once_and_skips_drafts(): void
    {
        [, $course, $section, $lesson, $assignment] = $this->publishedCurriculum();
        $draft = Course::create(['instructor_id' => $course->instructor_id, 'title' => 'Nháp', 'slug' => 'nhap', 'price' => 0, 'language' => 'vi', 'status' => Course::STATUS_DRAFT]);
        $backfill = app(ContentVersionBackfillService::class);

        $first = $backfill->backfillPublished();
        $second = $backfill->backfillPublished();

        $this->assertSame(1, $first['courses_created']);
        $this->assertSame(1, $first['sections_created']);
        $this->assertSame(2, $first['lessons_created']);
        $this->assertSame(1, $first['assignments_created']);
        $this->assertSame(0, $second['courses_created'] + $second['sections_created'] + $second['lessons_created'] + $second['assignments_created']);
        $this->assertNotNull($course->fresh()->published_version_id);
        $this->assertNotNull($section->fresh()->published_version_id);
        $this->assertNotNull($lesson->fresh()->published_version_id);
        $this->assertNotNull($assignment->fresh()->published_version_id);
        $this->assertNull($draft->fresh()->published_version_id);
        $this->assertSame(1, CourseVersion::where('course_id', $course->id)->count());
        $this->assertSame(1, AssignmentVersion::where('assignment_id', $assignment->id)->count());
    }
}
