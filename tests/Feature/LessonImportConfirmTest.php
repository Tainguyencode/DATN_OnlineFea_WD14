<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\LessonImportBatch;
use App\Models\User;
use App\Services\LessonImportTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Tests\TestCase;

class LessonImportConfirmTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> */
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_confirm_imports_all_supported_types_in_order_as_ready_non_preview_shells(): void
    {
        Queue::fake();
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $section->update(['title' => 'Chương 1']);

        foreach ([0, 1, 5] as $sortOrder) {
            Lesson::create([
                'course_id' => $course->id,
                'section_id' => $section->id,
                'title' => 'Existing '.$sortOrder,
                'type' => Lesson::TYPE_QUIZ,
                'duration' => 0,
                'duration_seconds' => 0,
                'sort_order' => $sortOrder,
                'status' => Lesson::STATUS_DRAFT,
            ]);
        }

        $preview = $this->preview($course, $section, [
            ['VID_01', 'Video shell', 'video', 300],
            ['DOC_01', 'Document lesson', 'document', 0, 'Nội dung tài liệu'],
            ['QUIZ_01', 'Quiz shell', 'quiz'],
            ['ASM_01', 'Assignment lesson', 'assignment', 0, 'Yêu cầu bài tập', 7, 120, 80],
        ]);
        $token = $preview->json('batch.token');

        $response = $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$course, $section]),
            ['batch_token' => $token],
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('batch.token', $token)
            ->assertJsonPath('batch.status', LessonImportBatch::STATUS_COMPLETED)
            ->assertJsonPath('batch.imported_count', 4)
            ->assertJsonPath('redirect_url', route('instructor.courses.curriculum', $course))
            ->assertSessionHas('success', 'Đã import 4 bài học vào Chương 1.');

        $imported = Lesson::query()
            ->where('section_id', $section->id)
            ->whereIn('title', ['Video shell', 'Document lesson', 'Quiz shell', 'Assignment lesson'])
            ->orderBy('sort_order')
            ->get();

        $this->assertSame(
            ['Video shell', 'Document lesson', 'Quiz shell', 'Assignment lesson'],
            $imported->pluck('title')->all(),
        );
        $this->assertSame([6, 7, 8, 9], $imported->pluck('sort_order')->all());
        $this->assertSame(
            [Lesson::TYPE_VIDEO, Lesson::TYPE_DOCUMENT, Lesson::TYPE_QUIZ, Lesson::TYPE_ASSIGNMENT],
            $imported->pluck('type')->all(),
        );

        foreach ($imported as $lesson) {
            $this->assertSame($course->id, $lesson->course_id);
            $this->assertSame($section->id, $lesson->section_id);
            $this->assertSame(Lesson::STATUS_PUBLISHED, $lesson->status);
            $this->assertFalse($lesson->is_preview);
        }

        $video = $imported->firstWhere('type', Lesson::TYPE_VIDEO);
        $this->assertSame(300, $video->duration_seconds);
        $this->assertFalse($video->hasVideoSource());
        $this->assertNull($video->video_path);
        $this->assertNull($video->video_url);

        $document = $imported->firstWhere('type', Lesson::TYPE_DOCUMENT);
        $this->assertSame('Nội dung tài liệu', $document->content);
        $this->assertNull($document->document_file);

        $quiz = $imported->firstWhere('type', Lesson::TYPE_QUIZ);
        $this->assertFalse($quiz->quiz()->exists());

        $assignmentLesson = $imported->firstWhere('type', Lesson::TYPE_ASSIGNMENT);
        $assignment = $assignmentLesson->assignment()->firstOrFail();
        $this->assertSame($course->id, $assignment->course_id);
        $this->assertSame(7, $assignment->due_days);
        $this->assertSame(120, $assignment->max_score);
        $this->assertSame(80, $assignment->passing_score);
        $this->assertSame('Yêu cầu bài tập', $assignment->description);

        $batch = LessonImportBatch::where('token', $token)->firstOrFail();
        $this->assertSame(LessonImportBatch::STATUS_COMPLETED, $batch->status);
        $this->assertSame($batch->row_count, $batch->imported_count);
        $this->assertNotNull($batch->completed_at);
        $this->assertDatabaseCount('content_updates', 0);
        Queue::assertNothingPushed();
    }

    public function test_same_completed_token_is_idempotent_even_after_preview_expiry(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $token = $this->preview($course, $section, [
            ['Q1', 'Idempotent quiz', 'quiz'],
        ])->json('batch.token');
        $url = route('instructor.courses.lessons.import.confirm', [$course, $section]);

        $this->postJson($url, ['batch_token' => $token])->assertOk();
        LessonImportBatch::where('token', $token)->update(['expires_at' => now()->subMinute()]);
        $second = $this->postJson($url, ['batch_token' => $token]);

        $second->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('batch.status', LessonImportBatch::STATUS_COMPLETED)
            ->assertJsonPath('batch.imported_count', 1);
        $this->assertSame(1, Lesson::where('title', 'Idempotent quiz')->count());
    }

    public function test_error_and_expired_batches_are_rejected_without_lesson_writes(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $url = route('instructor.courses.lessons.import.confirm', [$course, $section]);

        $errorToken = $this->preview($course, $section, [
            ['BAD_01', '', 'quiz'],
        ])->json('batch.token');

        $this->postJson($url, ['batch_token' => $errorToken])
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'batch_has_errors');

        $expiredToken = $this->preview($course, $section, [
            ['Q2', 'Expired quiz', 'quiz'],
        ])->json('batch.token');
        LessonImportBatch::where('token', $expiredToken)->update(['expires_at' => now()->subSecond()]);

        $this->postJson($url, ['batch_token' => $expiredToken])
            ->assertStatus(410)
            ->assertJsonPath('error_code', 'batch_expired');

        $this->assertDatabaseCount('lessons', 0);
        $this->assertSame(
            LessonImportBatch::STATUS_EXPIRED,
            LessonImportBatch::where('token', $expiredToken)->value('status'),
        );
    }

    public function test_confirm_rechecks_batch_user_course_and_section_snapshots(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $otherSection = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Other section',
            'sort_order' => 1,
        ]);
        [$otherCourse, $otherCourseSection] = $this->courseWithSection($instructor);
        $token = $this->preview($course, $section, [
            ['Q1', 'Protected quiz', 'quiz'],
        ])->json('batch.token');
        $batch = LessonImportBatch::where('token', $token)->firstOrFail();

        $otherUser = User::factory()->create();
        $batch->update(['user_id' => $otherUser->id]);
        $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$course, $section]),
            ['batch_token' => $token],
        )->assertForbidden()->assertJsonPath('error_code', 'batch_context_mismatch');

        $batch->update(['user_id' => $instructor->id]);
        $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$course, $otherSection]),
            ['batch_token' => $token],
        )->assertForbidden()->assertJsonPath('error_code', 'batch_context_mismatch');

        $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$otherCourse, $otherCourseSection]),
            ['batch_token' => $token],
        )->assertForbidden()->assertJsonPath('error_code', 'batch_context_mismatch');

        $this->assertDatabaseCount('lessons', 0);
    }

    public function test_confirm_rechecks_course_eligibility_and_never_creates_content_update(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $token = $this->preview($course, $section, [
            ['Q1', 'Status changed quiz', 'quiz'],
        ])->json('batch.token');

        $course->update(['status' => Course::STATUS_PENDING]);

        $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$course, $section]),
            ['batch_token' => $token],
        )->assertUnprocessable()->assertJsonPath('error_code', 'course_not_eligible');

        $this->assertDatabaseCount('lessons', 0);
        $this->assertDatabaseCount('content_updates', 0);
    }

    public function test_duplicate_file_is_blocked_only_for_same_user_course_and_section(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $otherSection = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section 2',
            'sort_order' => 1,
        ]);
        $token = $this->preview($course, $section, [
            ['Q1', 'Reusable file quiz', 'quiz'],
        ])->json('batch.token');
        $url = route('instructor.courses.lessons.import.confirm', [$course, $section]);
        $this->postJson($url, ['batch_token' => $token])->assertOk();

        $completed = LessonImportBatch::where('token', $token)->firstOrFail();
        $sameSectionBatch = $this->cloneAsPreviewedBatch($completed, $section);
        $this->postJson($url, ['batch_token' => $sameSectionBatch->token])
            ->assertConflict()
            ->assertJsonPath('error_code', 'duplicate_file');
        $this->assertSame(1, Lesson::where('title', 'Reusable file quiz')->count());

        $otherSectionBatch = $this->cloneAsPreviewedBatch($completed, $otherSection);
        $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$course, $otherSection]),
            ['batch_token' => $otherSectionBatch->token],
        )->assertOk()->assertJsonPath('batch.imported_count', 1);

        $this->assertSame(2, Lesson::where('title', 'Reusable file quiz')->count());
        $this->assertDatabaseHas('lessons', [
            'section_id' => $otherSection->id,
            'title' => 'Reusable file quiz',
        ]);
    }

    public function test_canonical_payload_is_revalidated_and_empty_batches_are_rejected(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $url = route('instructor.courses.lessons.import.confirm', [$course, $section]);

        $tamperedToken = $this->preview($course, $section, [
            ['Q1', 'First quiz', 'quiz'],
            ['Q2', 'Second quiz', 'quiz'],
        ])->json('batch.token');
        $tamperedBatch = LessonImportBatch::where('token', $tamperedToken)->firstOrFail();
        $payload = $tamperedBatch->canonical_payload;
        $payload['rows'][1]['relative_order'] = 0;
        $tamperedBatch->update(['canonical_payload' => $payload]);

        $this->postJson($url, ['batch_token' => $tamperedToken])
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'invalid_canonical_payload');

        $emptyToken = $this->preview($course, $section, [
            ['Q3', 'Empty later', 'quiz'],
        ])->json('batch.token');
        LessonImportBatch::where('token', $emptyToken)->update([
            'row_count' => 0,
            'canonical_payload' => [
                'schema' => LessonImportTemplateService::SCHEMA,
                'template_version' => LessonImportTemplateService::TEMPLATE_VERSION,
                'rows' => [],
            ],
        ]);

        $this->postJson($url, ['batch_token' => $emptyToken])
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'empty_batch');
        $this->assertDatabaseCount('lessons', 0);
    }

    public function test_mid_batch_technical_failure_rolls_back_everything_and_failed_batch_can_retry(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $token = $this->preview($course, $section, [
            ['A1', 'Assignment before failure', 'assignment', 0, 'Instructions', 3, 100, 60],
            ['D1', 'Trigger failure', 'document', 0, 'Document content'],
        ])->json('batch.token');
        $url = route('instructor.courses.lessons.import.confirm', [$course, $section]);
        $eventName = 'eloquent.creating: '.Lesson::class;

        Event::listen($eventName, function (Lesson $lesson): void {
            if ($lesson->title === 'Trigger failure') {
                throw new RuntimeException('Simulated mid-batch database write failure.');
            }
        });

        try {
            $this->postJson($url, ['batch_token' => $token])
                ->assertInternalServerError()
                ->assertJsonPath('error_code', 'import_failed')
                ->assertJsonPath('message', 'Không thể import bài học. Không có dữ liệu nào được thay đổi.');
        } finally {
            Event::forget($eventName);
        }

        $this->assertDatabaseCount('lessons', 0);
        $this->assertDatabaseCount('assignments', 0);
        $this->assertSame(
            LessonImportBatch::STATUS_FAILED,
            LessonImportBatch::where('token', $token)->value('status'),
        );

        $this->postJson($url, ['batch_token' => $token])
            ->assertOk()
            ->assertJsonPath('batch.imported_count', 2);
        $this->assertDatabaseCount('lessons', 2);
        $this->assertDatabaseCount('assignments', 1);
    }

    public function test_confirm_accepts_only_batch_token_and_handles_missing_or_importing_batch_safely(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $token = $this->preview($course, $section, [
            ['Q1', 'Contract quiz', 'quiz'],
        ])->json('batch.token');
        $url = route('instructor.courses.lessons.import.confirm', [$course, $section]);

        $this->postJson($url, [
            'batch_token' => $token,
            'rows' => [['title' => 'Browser supplied row']],
        ])->assertUnprocessable()->assertJsonValidationErrors('rows');

        $this->postJson($url, ['batch_token' => (string) Str::uuid()])
            ->assertNotFound()
            ->assertJsonPath('error_code', 'batch_not_found');

        LessonImportBatch::where('token', $token)->update(['status' => LessonImportBatch::STATUS_IMPORTING]);
        $this->postJson($url, ['batch_token' => $token])
            ->assertConflict()
            ->assertJsonPath('error_code', 'batch_importing');

        $this->assertDatabaseCount('lessons', 0);
    }

    public function test_confirm_route_enforces_instructor_and_route_ownership_middleware(): void
    {
        $owner = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        [$course, $section] = $this->courseWithSection($owner);
        $url = route('instructor.courses.lessons.import.confirm', [$course, $section]);

        $this->postJson($url, ['batch_token' => (string) Str::uuid()])->assertUnauthorized();

        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->postJson($url, ['batch_token' => (string) Str::uuid()])
            ->assertForbidden();

        $otherInstructor = $this->signInInstructor();
        $this->postJson($url, ['batch_token' => (string) Str::uuid()])->assertForbidden();
        $this->assertNotSame($owner->id, $otherInstructor->id);
    }

    private function signInInstructor(?User $user = null): User
    {
        $user ??= User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)->withSession(['two_factor_passed_at' => now()->timestamp]);

        return $user;
    }

    /**
     * @return array{0: Course, 1: CourseSection}
     */
    private function courseWithSection(User $instructor): array
    {
        $category = Category::create([
            'name' => 'Confirm category '.uniqid(),
            'slug' => 'confirm-category-'.uniqid(),
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Confirm course '.uniqid(),
            'slug' => 'confirm-course-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Detailed description',
            'objectives' => 'Learning objectives',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'sort_order' => 0,
        ]);

        return [$course, $section];
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     */
    private function preview(Course $course, CourseSection $section, array $rows): TestResponse
    {
        $response = $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload($rows)],
        );

        $response->assertOk()->assertJsonPath('success', true);

        return $response;
    }

    private function cloneAsPreviewedBatch(
        LessonImportBatch $completed,
        CourseSection $section,
    ): LessonImportBatch {
        $batch = $completed->replicate();
        $batch->forceFill([
            'token' => (string) Str::uuid(),
            'section_id' => $section->id,
            'status' => LessonImportBatch::STATUS_PREVIEWED,
            'imported_count' => 0,
            'completed_at' => null,
            'expires_at' => now()->addHour(),
        ]);
        $batch->save();

        return $batch;
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     */
    private function workbookUpload(array $rows): UploadedFile
    {
        $base = tempnam(sys_get_temp_dir(), 'lesson-import-confirm-');
        $path = $base.'.xlsx';
        rename($base, $path);
        $this->temporaryFiles[] = $path;

        $workbook = new Spreadsheet;
        $meta = $workbook->getActiveSheet();
        $meta->setTitle('_meta');
        $meta->fromArray([
            ['template_version', LessonImportTemplateService::TEMPLATE_VERSION],
            ['schema', LessonImportTemplateService::SCHEMA],
        ], null, 'A1');

        $lessons = $workbook->createSheet();
        $lessons->setTitle('Lessons');
        $lessons->fromArray(LessonImportTemplateService::HEADERS, null, 'A1');
        foreach ($rows as $index => $row) {
            $lessons->fromArray($row, null, 'A'.($index + 2));
        }

        (new Xlsx($workbook))->save($path);
        $workbook->disconnectWorksheets();

        return new UploadedFile(
            $path,
            'lesson-import-confirm.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true,
        );
    }
}
