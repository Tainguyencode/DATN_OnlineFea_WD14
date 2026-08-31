<?php

namespace App\Services;

use App\Exceptions\LessonImportException;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\FullCourseImportBatch;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FullCourseImportConfirmService
{
    public function __construct(
        private readonly FullCourseImportValidator $validator,
        private readonly CurriculumLessonService $lessons,
        private readonly QuizContentService $quizContent,
        private readonly InstructorCourseCategoryAccess $courseCategoryAccess,
    ) {}

    /** @return array{batch: FullCourseImportBatch, course: Course, idempotent: bool} */
    public function confirm(string $token, User $actor): array
    {
        if (! $actor->isInstructor()) {
            throw new LessonImportException('instructor_required', 'Chỉ giảng viên mới có thể tạo khóa học.', null, 403);
        }

        return DB::transaction(function () use ($token, $actor): array {
            $batch = FullCourseImportBatch::query()->where('token', $token)->lockForUpdate()->first();
            if (! $batch) {
                throw new LessonImportException('batch_not_found', 'Không tìm thấy phiên xem trước import.', null, 404);
            }
            if ((int) $batch->user_id !== (int) $actor->id) {
                throw new LessonImportException('batch_forbidden', 'Bạn không có quyền xác nhận phiên import này.', null, 403);
            }
            if ($batch->status === FullCourseImportBatch::STATUS_COMPLETED) {
                $courseId = (int) data_get($batch->result_payload, 'course_id');
                $course = Course::query()->find($courseId);
                if (! $course) {
                    throw new LessonImportException('completed_course_missing', 'Không thể tìm thấy khóa học đã tạo.', null, 409);
                }

                return ['batch' => $batch, 'course' => $course, 'idempotent' => true];
            }
            if ($batch->status !== FullCourseImportBatch::STATUS_PREVIEWED) {
                throw new LessonImportException('batch_unavailable', 'Phiên import đang được xử lý hoặc không thể xác nhận.', null, 409);
            }
            if ($batch->expires_at->isPast()) {
                throw new LessonImportException('batch_expired', 'Phiên xem trước đã hết hạn. Vui lòng xem trước lại workbook.', null, 410);
            }
            if ($batch->error_count > 0) {
                throw new LessonImportException('batch_has_errors', 'Workbook còn lỗi và chưa thể tạo khóa học.');
            }

            // Revalidate trusted persisted canonical data while the batch is locked.
            $validated = $this->validator->validateCanonicalPayload($batch->canonical_payload);
            $payload = $validated['canonical_payload'];
            $batch->update(['status' => FullCourseImportBatch::STATUS_PROCESSING]);

            $category = Category::query()
                ->selectableForCourse()
                ->where('slug', $payload['course']['category_slug'])
                ->lockForUpdate()
                ->first();
            if (! $category) {
                throw new LessonImportException('invalid_category_slug', 'Danh mục đã không còn khả dụng. Vui lòng xem trước lại workbook.');
            }
            if (! $this->courseCategoryAccess->canTeachCategory($actor, (int) $category->id)) {
                throw new LessonImportException('category_forbidden', 'Bạn không có quyền tạo khóa học thuộc ngành này.', null, 403);
            }

            $course = $this->createCourse($payload['course'], $category, $actor);
            $sections = $this->createSections($course, $payload['sections']);
            $lessonMap = $this->createLessons($course, $sections, $payload['lessons']);
            $quizGraph = $this->createQuizzes($lessonMap, $payload['quizzes'], $payload['questions'], $payload['options']);

            $result = [
                'course_id' => $course->id,
                'course_slug' => $course->slug,
                'sections' => collect($sections)->map(fn (CourseSection $section): int => $section->id)->all(),
                'lessons' => collect($lessonMap)->map(fn (Lesson $lesson): int => $lesson->id)->all(),
                'quizzes' => $quizGraph['quizzes'],
                'questions' => $quizGraph['questions'],
                'summary' => $validated['summary'],
            ];
            $batch->update([
                'status' => FullCourseImportBatch::STATUS_COMPLETED,
                'completed_at' => now(),
                'result_payload' => $result,
            ]);

            return ['batch' => $batch->fresh(), 'course' => $course, 'idempotent' => false];
        });
    }

    /** @param array<string, mixed> $data */
    private function createCourse(array $data, Category $category, User $actor): Course
    {
        return Course::create([
            'instructor_id' => $actor->id,
            'category_id' => $category->id,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title']),
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'objectives' => $data['objectives'],
            'level' => $data['level'] ?: null,
            'language' => $data['language'],
            'price' => $data['price'],
            'discount_price' => $data['sale_price'],
            'sale_price' => $data['sale_price'],
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /** @param array<int, array<string, mixed>> $rows @return array<string, CourseSection> */
    private function createSections(Course $course, array $rows): array
    {
        $sections = [];
        foreach ($rows as $row) {
            $sections[$row['section_code']] = CourseSection::create([
                'course_id' => $course->id,
                'title' => $row['title'],
                'description' => $row['description'],
                'sort_order' => max(0, (int) $row['order'] - 1),
            ]);
        }

        return $sections;
    }

    /** @param array<string, CourseSection> $sections @param array<int, array<string, mixed>> $rows @return array<string, Lesson> */
    private function createLessons(Course $course, array $sections, array $rows): array
    {
        $lessons = [];
        foreach ($rows as $row) {
            $section = $sections[$row['section_code']] ?? null;
            if (! $section) {
                throw new LessonImportException('orphan_lesson_section', 'Workbook chứa bài học không thuộc chương hợp lệ.');
            }
            $lessons[$row['lesson_code']] = $this->lessons->create($course, $section, [
                'title' => $row['title'],
                'type' => $row['type'],
                'duration' => $row['duration_seconds'],
                'duration_seconds' => $row['duration_seconds'],
                'content' => $row['content'],
                'assignment_due_days' => $row['assignment_due_days'],
                'assignment_max_score' => $row['assignment_max_score'],
                'assignment_passing_score' => $row['assignment_passing_score'],
                'sort_order' => max(0, (int) $row['order'] - 1),
                'status' => Lesson::STATUS_PUBLISHED,
                'is_preview' => false,
            ]);
        }

        return $lessons;
    }

    /**
     * @param  array<string, Lesson>  $lessons
     * @param  array<int, array<string, mixed>>  $quizzes
     * @param  array<int, array<string, mixed>>  $questions
     * @param  array<int, array<string, mixed>>  $options
     * @return array{quizzes: array<string, array{quiz_id: int, quiz_version_id: int}>, questions: array<string, array{question_id: int, question_version_id: int}>}
     */
    private function createQuizzes(array $lessons, array $quizzes, array $questions, array $options): array
    {
        $questionsByLesson = collect($questions)->groupBy('lesson_code');
        $optionsByQuestion = collect($options)->groupBy('question_code');
        $quizMap = [];
        $questionMap = [];

        foreach ($quizzes as $metadata) {
            $lesson = $lessons[$metadata['lesson_code']] ?? null;
            if (! $lesson || $lesson->type !== Lesson::TYPE_QUIZ) {
                throw new LessonImportException('orphan_quiz', 'Workbook chứa quiz không thuộc bài quiz hợp lệ.');
            }
            $quiz = $this->quizContent->saveMetadata($lesson, $metadata, false);
            foreach ($questionsByLesson->get($metadata['lesson_code'], []) as $question) {
                $questionOptions = $optionsByQuestion->get($question['question_code'], collect())
                    ->map(fn (array $option): array => [
                        'identity' => $option['option_code'],
                        'option_text' => $option['option_text'],
                        'is_correct' => $option['is_correct'],
                        'sort_order' => $option['relative_order'],
                    ])->all();
                $created = $this->quizContent->createQuestion($quiz, [
                    'question_text' => $question['question'],
                    'question_type' => $question['type'],
                    'score' => $question['points'],
                    'explanation' => $question['explanation'],
                    'sort_order' => $question['relative_order'],
                ], $questionOptions);
                $questionMap[$question['question_code']] = [
                    'question_id' => $created->id,
                    'question_version_id' => $created->authoringVersion->id,
                ];
            }
            $quiz = $this->quizContent->saveMetadata($lesson, $metadata, (bool) $metadata['is_active']);
            $quizMap[$metadata['lesson_code']] = [
                'quiz_id' => $quiz->id,
                'quiz_version_id' => $quiz->currentDraftVersion()->firstOrFail()->id,
            ];
        }

        return ['quizzes' => $quizMap, 'questions' => $questionMap];
    }

    private function uniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title) ?: 'course';
        $slug = $baseSlug;
        $counter = 2;
        while (Course::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
