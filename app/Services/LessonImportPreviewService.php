<?php

namespace App\Services;

use App\Exceptions\LessonImportException;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\LessonImportBatch;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class LessonImportPreviewService
{
    public const EXPIRATION_MINUTES = 60;

    public function __construct(
        private readonly LessonImportParser $parser,
        private readonly LessonImportValidator $validator,
    ) {}

    /**
     * @return array{batch: LessonImportBatch, rows: array<int, array<string, mixed>>}
     */
    public function preview(
        UploadedFile $file,
        Course $course,
        CourseSection $section,
        User $user,
    ): array {
        $path = $file->getRealPath();
        $hash = is_string($path) ? hash_file('sha256', $path) : false;
        if (! is_string($hash)) {
            throw new LessonImportException('hash_failed', 'Không thể kiểm tra tính toàn vẹn của file Excel.');
        }

        $parsed = $this->parser->parse($file);
        $validated = $this->validator->validate($parsed['rows'], $section);
        $canonicalRows = $validated['canonical_rows'];
        $reports = $validated['reports'];

        $batch = LessonImportBatch::create([
            'token' => (string) Str::uuid(),
            'user_id' => $user->id,
            'course_id' => $course->id,
            'section_id' => $section->id,
            'original_filename' => Str::limit(
                basename(str_replace('\\', '/', $file->getClientOriginalName())),
                255,
                '',
            ),
            'file_sha256' => $hash,
            'template_version' => $parsed['template_version'],
            'canonical_payload' => [
                'schema' => $parsed['schema'],
                'template_version' => $parsed['template_version'],
                'rows' => $canonicalRows,
            ],
            'validation_report' => [
                'file_errors' => [],
                'rows' => $reports,
            ],
            'row_count' => count($canonicalRows),
            'valid_count' => $validated['valid_count'],
            'warning_count' => $validated['warning_count'],
            'error_count' => $validated['error_count'],
            'status' => LessonImportBatch::STATUS_PREVIEWED,
            'imported_count' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
        ]);

        $responseRows = [];
        foreach ($canonicalRows as $index => $canonical) {
            $responseRows[] = [
                'row_number' => $canonical['row_number'],
                'relative_order' => $canonical['relative_order'],
                'data' => $canonical,
                'status' => $reports[$index]['status'],
                'errors' => $reports[$index]['errors'],
                'warnings' => $reports[$index]['warnings'],
            ];
        }

        return [
            'batch' => $batch,
            'rows' => $responseRows,
        ];
    }
}
