<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\LessonImportBatch;
use App\Models\User;
use App\Services\LessonImportTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;
use ZipArchive;

class LessonImportPreviewTest extends TestCase
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

    public function test_template_endpoint_returns_verified_xlsx_schema(): void
    {
        $instructor = $this->signInInstructor();
        [$course] = $this->courseWithSection($instructor);

        $response = $this->get(route('instructor.courses.lessons.import.template', $course));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString(
            LessonImportTemplateService::FILENAME,
            (string) $response->headers->get('content-disposition'),
        );

        $path = $this->temporaryPath();
        file_put_contents($path, $response->streamedContent());
        $workbook = IOFactory::load($path);

        $this->assertSame(['_meta', 'Lessons'], $workbook->getSheetNames());
        $this->assertSame('hidden', $workbook->getSheetByName('_meta')->getSheetState());
        $this->assertSame('template_version', $workbook->getSheetByName('_meta')->getCell('A1')->getValue());
        $this->assertSame(1, $workbook->getSheetByName('_meta')->getCell('B1')->getValue());
        $this->assertSame('schema', $workbook->getSheetByName('_meta')->getCell('A2')->getValue());
        $this->assertSame('lesson_import', $workbook->getSheetByName('_meta')->getCell('B2')->getValue());
        $this->assertSame(
            LessonImportTemplateService::HEADERS,
            $workbook->getSheetByName('Lessons')->rangeToArray('A1:H1')[0],
        );
        $this->assertSame('A2', $workbook->getSheetByName('Lessons')->getFreezePane());
        $this->assertSame(
            DataValidation::TYPE_LIST,
            $workbook->getSheetByName('Lessons')->getCell('C2')->getDataValidation()->getType(),
        );

        $workbook->disconnectWorksheets();
    }

    public function test_import_routes_enforce_guest_student_owner_and_section_authorization(): void
    {
        $owner = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        [$course, $section] = $this->courseWithSection($owner);

        $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload([['Q1', 'Quiz', 'quiz']])],
        )->assertUnauthorized();

        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->postJson(
                route('instructor.courses.lessons.import.preview', [$course, $section]),
                ['file' => $this->workbookUpload([['Q1', 'Quiz', 'quiz']])],
            )
            ->assertForbidden();

        $other = $this->signInInstructor();
        $this->actingAs($other)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->postJson(
                route('instructor.courses.lessons.import.preview', [$course, $section]),
                ['file' => $this->workbookUpload([['Q1', 'Quiz', 'quiz']])],
            )
            ->assertForbidden();

        [$otherCourse, $otherSection] = $this->courseWithSection($owner);
        $this->actingAs($owner)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->postJson(
                route('instructor.courses.lessons.import.preview', [$course, $otherSection]),
                ['file' => $this->workbookUpload([['Q1', 'Quiz', 'quiz']])],
            )
            ->assertForbidden();

        $this->assertNotSame($course->id, $otherCourse->id);
        $this->assertDatabaseCount('lesson_import_batches', 0);
    }

    public function test_preview_reuses_phase_zero_course_eligibility_rule(): void
    {
        $instructor = $this->signInInstructor();

        foreach ([Course::STATUS_DRAFT, Course::STATUS_REJECTED] as $status) {
            [$course, $section] = $this->courseWithSection($instructor, $status);

            $this->postJson(
                route('instructor.courses.lessons.import.preview', [$course, $section]),
                ['file' => $this->workbookUpload([['Q_'.$status, 'Quiz '.$status, 'quiz']])],
            )
                ->assertOk()
                ->assertJsonPath('success', true);
        }

        foreach ([
            Course::STATUS_APPROVED,
            Course::STATUS_PUBLISHED,
            Course::STATUS_PENDING,
            Course::STATUS_PENDING_UPDATE,
            Course::STATUS_REJECTED_UPDATE,
            Course::STATUS_ARCHIVED,
            Course::STATUS_SUSPENDED,
        ] as $status) {
            [$course, $section] = $this->courseWithSection(
                $instructor,
                $status,
                $status === Course::STATUS_PUBLISHED,
            );

            $this->postJson(
                route('instructor.courses.lessons.import.preview', [$course, $section]),
                ['file' => $this->workbookUpload([['Q_'.$status, 'Quiz '.$status, 'quiz']])],
            )
                ->assertUnprocessable()
                ->assertJsonPath('success', false);
        }

        $this->assertDatabaseCount('lesson_import_batches', 2);
    }

    public function test_request_rejects_non_xlsx_fake_extension_macro_and_oversized_files(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $route = route('instructor.courses.lessons.import.preview', [$course, $section]);

        $this->postJson($route)->assertJsonValidationErrors('file');

        $this->postJson($route, [
            'file' => UploadedFile::fake()->createWithContent('lessons.csv', "lesson_code,title\nA,Title"),
        ])->assertJsonValidationErrors('file');

        $fakePath = $this->temporaryPath();
        file_put_contents($fakePath, 'not an xlsx workbook');
        $this->postJson($route, [
            'file' => new UploadedFile(
                $fakePath,
                'fake.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true,
            ),
        ])->assertUnprocessable();

        $this->postJson($route, [
            'file' => $this->workbookUpload([['Q1', 'Quiz', 'quiz']], filename: 'macro.xlsm'),
        ])->assertJsonValidationErrors('file');

        $disguisedMacro = $this->workbookUpload([['Q2', 'Quiz macro', 'quiz']]);
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($disguisedMacro->getRealPath()) === true);
        $this->assertTrue($zip->addFromString('xl/vbaProject.bin', 'macro payload'));
        $zip->close();
        $this->postJson($route, ['file' => $disguisedMacro])
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'macro_workbook');

        $this->postJson($route, [
            'file' => UploadedFile::fake()->create(
                'large.xlsx',
                5121,
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ),
        ])->assertJsonValidationErrors('file');

        $this->assertDatabaseCount('lesson_import_batches', 0);
    }

    public function test_parser_rejects_corrupt_workbook_and_structural_schema_errors_without_batch(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $route = route('instructor.courses.lessons.import.preview', [$course, $section]);

        $corruptPath = $this->temporaryPath();
        file_put_contents($corruptPath, "PK\x03\x04corrupt");
        $this->postJson($route, [
            'file' => new UploadedFile(
                $corruptPath,
                'corrupt.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true,
            ),
        ])->assertUnprocessable();

        $cases = [
            ['options' => ['include_meta' => false], 'code' => 'missing_meta'],
            ['options' => ['version' => 99], 'code' => 'unsupported_version'],
            ['options' => ['schema' => 'wrong_schema'], 'code' => 'wrong_schema'],
            ['options' => ['include_lessons' => false], 'code' => 'missing_lessons'],
            ['options' => ['headers' => ['code', ...array_slice(LessonImportTemplateService::HEADERS, 1)]], 'code' => 'invalid_headers'],
        ];

        foreach ($cases as $case) {
            $this->postJson($route, [
                'file' => $this->workbookUpload([['Q1', 'Quiz', 'quiz']], options: $case['options']),
            ])
                ->assertUnprocessable()
                ->assertJsonPath('error_code', $case['code']);
        }

        $this->assertDatabaseCount('lesson_import_batches', 0);
    }

    public function test_formula_cell_is_rejected_without_evaluation_or_batch(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            [
                'file' => $this->workbookUpload(
                    [['FORMULA_1', 'Formula lesson', 'quiz']],
                    options: ['formula' => ['D2' => '=1+1']],
                ),
            ],
        )
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'formula_cell');

        $this->assertDatabaseCount('lesson_import_batches', 0);
    }

    public function test_exactly_100_rows_are_allowed_and_101_rows_are_rejected(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $route = route('instructor.courses.lessons.import.preview', [$course, $section]);

        $rows100 = [];
        for ($index = 1; $index <= 100; $index++) {
            $rows100[] = ['QUIZ_'.$index, 'Quiz '.$index, 'quiz'];
        }

        $this->postJson($route, ['file' => $this->workbookUpload($rows100)])
            ->assertOk()
            ->assertJsonPath('batch.row_count', 100)
            ->assertJsonPath('batch.valid_count', 100);

        $rows101 = [...$rows100, ['QUIZ_101', 'Quiz 101', 'quiz']];
        $this->postJson($route, ['file' => $this->workbookUpload($rows101)])
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'too_many_rows');

        $this->assertDatabaseCount('lesson_import_batches', 1);
    }

    public function test_business_row_errors_are_saved_in_preview_batch(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $rows = [
            ['', 'Missing code', 'quiz'],
            ['DUP', 'First duplicate code', 'quiz'],
            [' dup ', 'Second duplicate code', 'quiz'],
            ['NO_TITLE', '', 'quiz'],
            ['BAD_TYPE', 'Bad type', 'abc'],
            ['BAD_DURATION', 'Bad duration', 'quiz', '1.5'],
        ];

        $response = $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload($rows)],
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('batch.row_count', 6)
            ->assertJsonPath('batch.error_count', 6);

        $this->assertDatabaseHas('lesson_import_batches', [
            'course_id' => $course->id,
            'section_id' => $section->id,
            'error_count' => 6,
            'status' => LessonImportBatch::STATUS_PREVIEWED,
        ]);
    }

    public function test_shell_and_duplicate_title_warnings_while_quiz_shell_remains_valid(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Existing title',
            'type' => Lesson::TYPE_DOCUMENT,
            'content' => 'Existing content',
            'status' => Lesson::STATUS_DRAFT,
        ]);

        $response = $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload([
                ['VIDEO_1', 'Duplicate title', 'video', 300],
                ['DOC_1', 'Duplicate title', 'document'],
                ['ASSIGN_1', 'Existing title', 'assignment'],
                ['QUIZ_1', 'Quiz shell', 'quiz'],
            ])],
        );

        $response->assertOk()
            ->assertJsonPath('batch.warning_count', 3)
            ->assertJsonPath('batch.valid_count', 1)
            ->assertJsonPath('rows.0.status', 'warning')
            ->assertJsonPath('rows.1.status', 'warning')
            ->assertJsonPath('rows.2.status', 'warning')
            ->assertJsonPath('rows.3.status', 'valid');
    }

    public function test_assignment_defaults_and_strict_assignment_rules(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        $response = $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload([
                ['A_DEFAULT', 'Default assignment', 'assignment'],
                ['A_DUE', 'Invalid due', 'assignment', 0, 'Instructions', 3651],
                ['A_SCORE', 'Invalid passing', 'assignment', 0, 'Instructions', 7, 50, 60],
                ['Q_FIELDS', 'Quiz with assignment fields', 'quiz', 0, null, null, 100, 70],
                ['Q_CONTENT', 'Quiz with content', 'quiz', 0, 'Not allowed'],
            ])],
        );

        $response->assertOk()
            ->assertJsonPath('rows.0.data.assignment_due_days', null)
            ->assertJsonPath('rows.0.data.assignment_max_score', 100)
            ->assertJsonPath('rows.0.data.assignment_passing_score', 70)
            ->assertJsonPath('rows.0.status', 'warning')
            ->assertJsonPath('rows.1.status', 'error')
            ->assertJsonPath('rows.2.status', 'error')
            ->assertJsonPath('rows.3.status', 'error')
            ->assertJsonPath('rows.4.status', 'error')
            ->assertJsonPath('batch.error_count', 4);
    }

    public function test_preview_canonicalizes_values_creates_only_batch_and_tracks_expiration(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $lessonCount = Lesson::count();
        $assignmentCount = Assignment::count();
        $contentUpdateCount = ContentUpdate::count();

        $response = $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload([
                [' lesson_01 ', '  Trimmed title  ', ' VIDEO ', '120'],
            ])],
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('rows.0.data.lesson_code', 'LESSON_01')
            ->assertJsonPath('rows.0.data.title', 'Trimmed title')
            ->assertJsonPath('rows.0.data.type', 'video')
            ->assertJsonPath('rows.0.data.duration', 120)
            ->assertJsonPath('rows.0.data.duration_seconds', 120)
            ->assertJsonPath('rows.0.data.status', Lesson::STATUS_DRAFT)
            ->assertJsonPath('rows.0.data.is_preview', false)
            ->assertJsonMissingPath('batch.id');

        $this->assertSame($lessonCount, Lesson::count());
        $this->assertSame($assignmentCount, Assignment::count());
        $this->assertSame($contentUpdateCount, ContentUpdate::count());
        $this->assertDatabaseCount('lesson_import_batches', 1);

        $batch = LessonImportBatch::firstOrFail();
        $this->assertNotSame((string) $batch->id, $batch->token);
        $this->assertSame(64, strlen($batch->file_sha256));
        $this->assertFalse($batch->isExpired());
        $this->assertTrue($batch->expires_at->isFuture());

        $batch->update(['expires_at' => now()->subMinute()]);
        $this->assertTrue($batch->fresh()->isExpired());
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
    private function courseWithSection(
        User $instructor,
        string $status = Course::STATUS_DRAFT,
        bool $isPublished = false,
    ): array {
        $category = Category::create([
            'name' => 'Import category '.uniqid(),
            'slug' => 'import-category-'.uniqid(),
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Import course '.uniqid(),
            'slug' => 'import-course-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Detailed description',
            'objectives' => 'Learning objectives',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => $status,
            'is_published' => $isPublished,
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
     * @param  array<string, mixed>  $options
     */
    private function workbookUpload(
        array $rows,
        string $filename = 'lesson-import.xlsx',
        array $options = [],
    ): UploadedFile {
        $path = $this->temporaryPath();
        $workbook = new Spreadsheet;
        $includeMeta = $options['include_meta'] ?? true;
        $includeLessons = $options['include_lessons'] ?? true;

        $firstSheet = $workbook->getActiveSheet();
        if ($includeMeta) {
            $firstSheet->setTitle('_meta');
            $firstSheet->fromArray([
                ['template_version', $options['version'] ?? LessonImportTemplateService::TEMPLATE_VERSION],
                ['schema', $options['schema'] ?? LessonImportTemplateService::SCHEMA],
            ], null, 'A1');
        } else {
            $firstSheet->setTitle('Other');
        }

        if ($includeLessons) {
            $lessons = $workbook->createSheet();
            $lessons->setTitle('Lessons');
            $lessons->fromArray(
                $options['headers'] ?? LessonImportTemplateService::HEADERS,
                null,
                'A1',
            );

            foreach ($rows as $index => $row) {
                $lessons->fromArray($row, null, 'A'.($index + 2));
            }

            foreach ($options['formula'] ?? [] as $coordinate => $formula) {
                $lessons->setCellValue($coordinate, $formula);
            }
        }

        (new Xlsx($workbook))->save($path);
        $workbook->disconnectWorksheets();

        return new UploadedFile(
            $path,
            $filename,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true,
        );
    }

    private function temporaryPath(): string
    {
        $base = tempnam(sys_get_temp_dir(), 'lesson-import-');
        $path = $base.'.xlsx';
        rename($base, $path);
        $this->temporaryFiles[] = $path;

        return $path;
    }
}
