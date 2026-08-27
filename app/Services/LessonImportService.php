<?php

namespace App\Services;

use App\Exceptions\LessonImportException;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\LessonImportBatch;
use App\Models\User;
use App\Support\LessonImportWorkbookSchema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class LessonImportService
{
    public function __construct(
        private readonly CurriculumLessonService $lessonService,
        private readonly LessonImportValidator $validator,
        private readonly LessonImportV2Validator $v2Validator,
        private readonly LessonImportV2ImportService $v2ImportService,
    ) {}

    /**
     * @return array{token: string, status: string, imported_count: int, section_title: string}
     */
    public function confirm(
        string $token,
        Course $course,
        CourseSection $section,
        User $user,
    ): array {
        $currentRowNumber = null;

        try {
            $outcome = DB::transaction(function () use (
                $token,
                $course,
                $section,
                $user,
                &$currentRowNumber,
            ): array|LessonImportException {
                $batch = LessonImportBatch::query()
                    ->where('token', $token)
                    ->lockForUpdate()
                    ->first();

                if (! $batch) {
                    throw new LessonImportException(
                        'batch_not_found',
                        'Phiên kiểm tra file không còn tồn tại. Vui lòng kiểm tra lại file.',
                        httpStatus: 404,
                    );
                }

                $lockedCourse = Course::query()->whereKey($course->id)->lockForUpdate()->first();
                $lockedSection = CourseSection::query()->whereKey($section->id)->lockForUpdate()->first();

                if (! $lockedCourse
                    || ! $lockedSection
                    || ! $lockedCourse->isOwnedBy($user)
                    || (int) $lockedSection->course_id !== (int) $lockedCourse->id
                    || (int) $batch->user_id !== (int) $user->id
                    || (int) $batch->course_id !== (int) $lockedCourse->id
                    || (int) $batch->section_id !== (int) $lockedSection->id) {
                    throw new LessonImportException(
                        'batch_context_mismatch',
                        'Bạn không thể import phiên kiểm tra này vào khóa học hoặc chương đã chọn.',
                        httpStatus: 403,
                    );
                }

                if ($batch->status === LessonImportBatch::STATUS_COMPLETED) {
                    return $this->completedResult($batch, $lockedSection);
                }

                if ($batch->status === LessonImportBatch::STATUS_IMPORTING) {
                    throw new LessonImportException(
                        'batch_importing',
                        'File đang được import. Vui lòng chờ.',
                        httpStatus: 409,
                    );
                }

                if (! in_array($batch->status, [LessonImportBatch::STATUS_PREVIEWED, LessonImportBatch::STATUS_FAILED], true)) {
                    throw new LessonImportException(
                        'invalid_batch_status',
                        'Phiên kiểm tra file không còn sẵn sàng để import. Vui lòng kiểm tra lại file.',
                        httpStatus: 409,
                    );
                }

                if ($batch->isExpired()) {
                    $batch->forceFill(['status' => LessonImportBatch::STATUS_EXPIRED])->save();

                    return new LessonImportException(
                        'batch_expired',
                        'Phiên kiểm tra file đã hết hạn. Vui lòng chọn file và kiểm tra lại.',
                        httpStatus: 410,
                    );
                }

                if (! $this->lessonService->canCreateDirectly($lockedCourse)) {
                    throw new LessonImportException(
                        'course_not_eligible',
                        'Khóa học không còn ở trạng thái cho phép import bài học. Không có dữ liệu nào được thay đổi.',
                    );
                }

                if ($batch->error_count > 0) {
                    throw new LessonImportException(
                        'batch_has_errors',
                        'File vẫn còn dòng lỗi. Vui lòng sửa file Excel và kiểm tra lại.',
                    );
                }

                if ($batch->row_count <= 0
                    || ! in_array((int) $batch->template_version, [
                        LessonImportWorkbookSchema::VERSION_V1,
                        LessonImportWorkbookSchema::VERSION_V2,
                    ], true)
                    || ! is_array($batch->canonical_payload)) {
                    throw new LessonImportException(
                        'empty_batch',
                        'Phiên kiểm tra file không có bài học nào để import. Vui lòng kiểm tra lại file.',
                    );
                }

                $duplicateExists = LessonImportBatch::query()
                    ->whereKeyNot($batch->id)
                    ->where('user_id', $user->id)
                    ->where('course_id', $lockedCourse->id)
                    ->where('section_id', $lockedSection->id)
                    ->where('file_sha256', $batch->file_sha256)
                    ->where('status', LessonImportBatch::STATUS_COMPLETED)
                    ->exists();

                if ($duplicateExists) {
                    throw new LessonImportException(
                        'duplicate_file',
                        'File này đã được import vào chương này trước đó.',
                        httpStatus: 409,
                    );
                }

                if ((int) $batch->template_version === LessonImportWorkbookSchema::VERSION_V2) {
                    $validated = $this->v2Validator->validateCanonicalPayload(
                        $batch->canonical_payload,
                        $lockedSection,
                    );
                    $payload = $validated['canonical_payload'];
                    $lessonCount = count($payload['lessons']);

                    if ($lessonCount !== $batch->row_count || $lessonCount === 0) {
                        throw new LessonImportException(
                            'canonical_row_count_mismatch',
                            'Dữ liệu kiểm tra đã thay đổi hoặc không còn hợp lệ. Vui lòng kiểm tra lại file.',
                        );
                    }

                    $batch->forceFill(['status' => LessonImportBatch::STATUS_IMPORTING])->save();
                    $resultPayload = $this->v2ImportService->import(
                        $payload,
                        $lockedCourse,
                        $lockedSection,
                        $user,
                    );

                    $batch->forceFill([
                        'status' => LessonImportBatch::STATUS_COMPLETED,
                        'imported_count' => $lessonCount,
                        'result_payload' => $resultPayload,
                        'completed_at' => now(),
                    ])->save();

                    return $this->completedResult($batch, $lockedSection);
                }

                $validated = $this->validator->validateCanonicalPayload(
                    $batch->canonical_payload,
                    $lockedSection,
                );
                $rows = $validated['canonical_rows'];

                if (count($rows) !== $batch->row_count || $rows === []) {
                    throw new LessonImportException(
                        'canonical_row_count_mismatch',
                        'Dữ liệu kiểm tra đã thay đổi hoặc không còn hợp lệ. Vui lòng kiểm tra lại file.',
                    );
                }

                $batch->forceFill(['status' => LessonImportBatch::STATUS_IMPORTING])->save();

                $currentMaxSortOrder = $lockedSection->lessons()->max('sort_order');
                $nextSortOrder = $currentMaxSortOrder === null ? 0 : ((int) $currentMaxSortOrder + 1);

                foreach ($rows as $index => $row) {
                    $currentRowNumber = $row['row_number'];
                    $createData = [
                        ...$row,
                        'sort_order' => $nextSortOrder + $index,
                        'status' => Lesson::STATUS_DRAFT,
                        'is_preview' => false,
                    ];
                    unset($createData['row_number'], $createData['relative_order'], $createData['lesson_code']);

                    $this->lessonService->create($lockedCourse, $lockedSection, $createData);
                }

                $batch->forceFill([
                    'status' => LessonImportBatch::STATUS_COMPLETED,
                    'imported_count' => count($rows),
                    'completed_at' => now(),
                ])->save();

                return $this->completedResult($batch, $lockedSection);
            }, 3);
        } catch (LessonImportException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            $this->persistFailedState($token);

            Log::error('Lesson import confirm failed unexpectedly.', [
                'batch_token' => $token,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'section_id' => $section->id,
                'row_number' => $currentRowNumber,
                'exception' => $exception,
            ]);

            throw new LessonImportException(
                'import_failed',
                'Không thể import bài học. Không có dữ liệu nào được thay đổi.',
                $exception,
                500,
            );
        }

        if ($outcome instanceof LessonImportException) {
            throw $outcome;
        }

        return $outcome;
    }

    /**
     * @return array{token: string, status: string, imported_count: int, section_title: string}
     */
    private function completedResult(LessonImportBatch $batch, CourseSection $section): array
    {
        return [
            'token' => $batch->token,
            'status' => LessonImportBatch::STATUS_COMPLETED,
            'imported_count' => (int) $batch->imported_count,
            'section_title' => trim((string) $section->title),
        ];
    }

    private function persistFailedState(string $token): void
    {
        try {
            LessonImportBatch::query()
                ->where('token', $token)
                ->whereNotIn('status', [
                    LessonImportBatch::STATUS_COMPLETED,
                    LessonImportBatch::STATUS_EXPIRED,
                ])
                ->update(['status' => LessonImportBatch::STATUS_FAILED]);
        } catch (Throwable $exception) {
            Log::warning('Could not persist failed lesson import batch state.', [
                'batch_token' => $token,
                'exception' => $exception,
            ]);
        }
    }
}
