<?php

namespace App\Services;

use App\Exceptions\LessonImportException;
use App\Models\FullCourseImportBatch;
use App\Models\User;
use App\Support\FullCourseImportWorkbookSchema as Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FullCourseImportPreviewService
{
    public const EXPIRATION_MINUTES = 60;

    public function __construct(
        private readonly LessonImportParser $parser,
        private readonly FullCourseImportValidator $validator,
    ) {}

    public function preview(UploadedFile $file, User $user): array
    {
        $path = $file->getRealPath();
        $hash = is_string($path) ? hash_file('sha256', $path) : false;
        if (! is_string($hash)) {
            throw new LessonImportException('hash_failed', 'Không thể kiểm tra tính toàn vẹn của file Excel.');
        }
        $parsed = $this->parser->parse($file);
        if (($parsed['template_version'] ?? null) !== Schema::VERSION || ($parsed['schema'] ?? null) !== Schema::SCHEMA) {
            throw new LessonImportException('wrong_template', 'Vui lòng dùng mẫu import toàn bộ khóa học v3.');
        }
        $validated = $this->validator->validate($parsed['sheets']);
        $batch = FullCourseImportBatch::create([
            'token' => (string) Str::uuid(), 'user_id' => $user->id,
            'original_filename' => Str::limit(basename(str_replace('\\', '/', $file->getClientOriginalName())), 255, ''),
            'file_sha256' => $hash, 'canonical_payload' => $validated['canonical_payload'],
            'validation_report' => ['issues' => $validated['issues'], 'summary' => $validated['summary'], 'sheets' => $validated['sheets']],
            'row_count' => $validated['summary']['sections'] + $validated['summary']['lessons'] + $validated['summary']['questions'] + $validated['summary']['options'],
            'valid_count' => $validated['valid_count'], 'warning_count' => $validated['warning_count'], 'error_count' => $validated['error_count'],
            'status' => FullCourseImportBatch::STATUS_PREVIEWED, 'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
        ]);

        return ['batch' => $batch, ...$validated];
    }
}
