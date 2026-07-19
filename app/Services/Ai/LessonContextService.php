<?php

namespace App\Services\Ai;

use App\Models\Lesson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LessonContextService
{
    /**
     * Build plain-text lesson context for AI.
     * Never includes quiz questions or correct answers.
     */
    public function build(Lesson $lesson): string
    {
        $parts = [];

        $title = trim((string) $lesson->title);
        if ($title !== '') {
            $parts[] = 'Tiêu đề bài học: '.$title;
        }

        $content = trim(strip_tags((string) ($lesson->content ?? '')));
        if ($content !== '') {
            $parts[] = "Mô tả / nội dung bài học:\n".$content;
        }

        $subtitleText = $this->subtitlePlainText($lesson);
        if ($subtitleText !== '') {
            $parts[] = "Transcript / phụ đề:\n".$subtitleText;
        }

        return Str::limit(implode("\n\n", $parts), 12000, '');
    }

    public function hasEnoughSource(Lesson $lesson): bool
    {
        $content = trim(strip_tags((string) ($lesson->content ?? '')));
        $subtitle = $this->subtitlePlainText($lesson);

        // Require real learning text — title alone is not enough.
        return mb_strlen($content) >= 20 || mb_strlen($subtitle) >= 40;
    }

    public function sourceHash(Lesson $lesson): string
    {
        return hash('sha256', $this->build($lesson));
    }

    private function subtitlePlainText(Lesson $lesson): string
    {
        if (! DB::getSchemaBuilder()->hasTable('lesson_subtitles')) {
            return '';
        }

        $paths = DB::table('lesson_subtitles')
            ->where('lesson_id', $lesson->id)
            ->pluck('file_path');

        $chunks = [];

        foreach ($paths as $path) {
            $absolute = storage_path('app/public/'.ltrim((string) $path, '/'));
            if (! is_file($absolute)) {
                continue;
            }

            $text = $this->extractPlainTextFromSubtitle((string) file_get_contents($absolute));
            if ($text !== '') {
                $chunks[] = $text;
            }
        }

        return trim(implode(' ', $chunks));
    }

    private function extractPlainTextFromSubtitle(string $raw): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $raw) ?: [];
        $textLines = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, 'WEBVTT') || str_contains($line, '-->') || ctype_digit($line)) {
                continue;
            }

            $textLines[] = strip_tags($line);
        }

        return trim(implode(' ', $textLines));
    }
}
