<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\QuizVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ContentVersionComparisonService
{
    /** @return array<int, array{key: string, label: string, old: mixed, new: mixed}> */
    public function compare(Course $course, string $type, Model $from, Model $to): array
    {
        if ($this->identityId($type, $from) !== $this->identityId($type, $to)) {
            throw ValidationException::withMessages(['version' => 'Hai phiên bản phải thuộc cùng một nội dung.']);
        }

        $old = $this->values($course, $type, $from);
        $new = $this->values($course, $type, $to);

        return collect(array_unique([...array_keys($old), ...array_keys($new)]))
            ->map(function (string $key) use ($old, $new): array {
                $oldField = $old[$key] ?? ['label' => $key, 'value' => null];
                $newField = $new[$key] ?? ['label' => $oldField['label'], 'value' => null];

                return [
                    'key' => $key,
                    'label' => $newField['label'] ?? $oldField['label'],
                    'old' => $oldField['value'],
                    'new' => $newField['value'],
                ];
            })
            ->filter(fn (array $field): bool => $field['old'] !== $field['new'])
            ->values()
            ->all();
    }

    /** @return array<string, array{label: string, value: mixed}> */
    public function values(Course $course, string $type, Model $version): array
    {
        return match ($type) {
            ContentUpdate::TYPE_COURSE => $this->courseValues($version),
            ContentUpdate::TYPE_CHAPTER => $this->sectionValues($version),
            ContentUpdate::TYPE_LESSON => $this->lessonValues($version),
            ContentUpdate::TYPE_ASSIGNMENT => $this->assignmentValues($version),
            ContentUpdate::TYPE_QUIZ => $this->quizValues($version),
            default => throw ValidationException::withMessages(['version' => 'Loại phiên bản không được hỗ trợ.']),
        };
    }

    private function courseValues(Model $version): array
    {
        return $this->fields($version, [
            'title' => 'Tên khóa học', 'slug' => 'Đường dẫn', 'short_description' => 'Mô tả ngắn', 'description' => 'Mô tả',
            'objectives' => 'Mục tiêu', 'requirements' => 'Yêu cầu', 'target_audience' => 'Đối tượng',
            'level' => 'Trình độ', 'language' => 'Ngôn ngữ', 'price' => 'Giá',
            'discount_price' => 'Giá khuyến mãi', 'sale_price' => 'Giá bán', 'tags' => 'Thẻ',
        ]) + [
            'category_id' => ['label' => 'Danh mục', 'value' => Category::query()->whereKey($version->category_id)->value('name') ?? 'Không xác định'],
            'thumbnail' => ['label' => 'Ảnh đại diện', 'value' => $this->safeFilename($version->thumbnail)],
            'preview_video' => ['label' => 'Video giới thiệu', 'value' => $this->safeFilename($version->preview_video)],
        ];
    }

    private function sectionValues(Model $version): array
    {
        return $this->fields($version, [
            'title' => 'Tên chương', 'description' => 'Mô tả', 'sort_order' => 'Vị trí',
        ]);
    }

    private function lessonValues(Model $version): array
    {
        $values = $this->fields($version, [
            'title' => 'Tên bài học', 'type' => 'Loại bài học', 'content' => 'Nội dung',
            'duration_seconds' => 'Thời lượng (giây)', 'section_id' => 'ID chương', 'sort_order' => 'Vị trí',
            'is_preview' => 'Cho xem thử', 'is_required' => 'Bắt buộc',
            'video_url' => 'URL video', 'video_path' => 'Đường dẫn video',
            'original_video_key' => 'Khóa video gốc', 'hls_manifest_key' => 'Khóa HLS manifest',
            'hls_playlist' => 'HLS playlist', 'hls_path' => 'Đường dẫn HLS',
            'video_original_name' => 'Tên tệp video gốc', 'video_mime' => 'Định dạng video',
            'video_size' => 'Dung lượng video (byte)', 'attachments' => 'Tệp đính kèm',
            'subtitles' => 'Phụ đề', 'legacy_chapter_id' => 'ID chương cũ',
        ]);
        $values['document_file'] = ['label' => 'Tài liệu', 'value' => $this->safeFilename($version->document_file)];
        $values['video_source'] = ['label' => 'Nguồn video', 'value' => $this->videoSource($version)];
        $values['video_file'] = ['label' => 'Tệp video', 'value' => $this->safeFilename($version->video_original_name ?: $version->video_path)];
        $values['hls_ready'] = ['label' => 'Trạng thái HLS', 'value' => filled($version->hls_manifest_key) || str_ends_with((string) $version->video_path, '.m3u8') ? 'Sẵn sàng' : 'Không áp dụng / chưa sẵn sàng'];

        return $values;
    }

    private function assignmentValues(Model $version): array
    {
        return $this->fields($version, [
            'title' => 'Tên bài tập', 'description' => 'Mô tả', 'instructions' => 'Hướng dẫn',
            'due_date' => 'Hạn nộp', 'due_days' => 'Số ngày đến hạn', 'max_score' => 'Điểm tối đa',
            'passing_score' => 'Điểm đạt', 'is_required' => 'Bắt buộc',
            'allowed_file_types' => 'Loại tệp cho phép', 'maximum_file_size' => 'Dung lượng tối đa',
        ]);
    }

    private function quizValues(Model $version): array
    {
        return $this->fields($version, [
            'title' => 'Tên Quiz', 'description' => 'Mô tả', 'pass_score' => 'Điểm đạt',
            'time_limit_minutes' => 'Thời gian (phút)', 'max_attempts' => 'Số lần làm',
        ]) + [
            'question_count' => ['label' => 'Số câu hỏi', 'value' => $version instanceof QuizVersion ? $version->questionMappings()->count() : null],
        ];
    }

    /** @param array<string, string> $map @return array<string, array{label: string, value: mixed}> */
    private function fields(Model $version, array $map): array
    {
        return collect($map)->mapWithKeys(function (string $label, string $key) use ($version): array {
            $value = $version->{$key};
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('d/m/Y H:i');
            }
            if (is_bool($value)) {
                $value = $value ? 'Có' : 'Không';
            }
            if (in_array($key, ['price', 'discount_price', 'sale_price'], true) && is_numeric($value)) {
                $value = number_format((float) $value, 0, ',', '.').' đ';
            }

            return [$key => ['label' => $label, 'value' => $value]];
        })->all();
    }

    private function safeFilename(?string $value): ?string
    {
        return filled($value) ? basename(str_replace('\\', '/', $value)) : null;
    }

    private function videoSource(Model $version): string
    {
        if (filled($version->video_url) && filter_var($version->video_url, FILTER_VALIDATE_URL)) {
            return 'URL ngoài: '.$version->video_url;
        }
        if (filled($version->original_video_key) || filled($version->video_path)) {
            return 'Video tải lên';
        }

        return 'Không có nguồn';
    }

    private function identityId(string $type, Model $version): int
    {
        return (int) match ($type) {
            ContentUpdate::TYPE_COURSE => $version->course_id,
            ContentUpdate::TYPE_CHAPTER => $version->course_section_id,
            ContentUpdate::TYPE_LESSON => $version->lesson_id,
            ContentUpdate::TYPE_ASSIGNMENT => $version->assignment_id,
            ContentUpdate::TYPE_QUIZ => $version->quiz_id,
            default => 0,
        };
    }
}
