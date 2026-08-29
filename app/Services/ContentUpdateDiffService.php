<?php

namespace App\Services;

use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizVersion;
use Illuminate\Support\Collection;

/**
 * Read-only presentation model for ContentUpdate review screens.
 *
 * It deliberately resolves every current record through the update's course,
 * so a stale or malformed entity_id cannot disclose another course's data.
 */
class ContentUpdateDiffService
{
    /** @return array<string, mixed> */
    public function build(ContentUpdate $update): array
    {
        $update->loadMissing('course.category');
        $payload = is_array($update->payload) ? $update->payload : [];
        $course = $update->course;

        $data = [
            'entity_type' => $update->type,
            'entity_label' => $this->entityLabel($update->type),
            'action' => $update->action,
            'action_label' => $this->actionLabel($update->action),
            'label' => $this->label($update, $payload, $course),
            'current' => [],
            'proposed' => [],
            'fields' => [],
            'warnings' => [],
            'media' => [],
            'metadata' => [
                'submitted_at' => $update->submitted_at,
                'reviewed_at' => $update->reviewed_at,
                'rejection_reason' => $update->rejection_reason,
            ],
        ];

        return match ($update->type) {
            ContentUpdate::TYPE_COURSE => $this->courseDiff($data, $update, $payload, $course),
            ContentUpdate::TYPE_CHAPTER => $this->sectionDiff($data, $update, $payload, $course),
            ContentUpdate::TYPE_LESSON => $this->lessonDiff($data, $update, $payload, $course),
            ContentUpdate::TYPE_QUIZ => $this->quizDiff($data, $update, $payload, $course),
            default => $this->unknownDiff($data),
        };
    }

    /** Cheap list presentation: no entity lookups or full diff construction. */
    public function summary(ContentUpdate $update): string
    {
        $payload = is_array($update->payload) ? $update->payload : [];
        $title = trim((string) ($payload['title'] ?? ''));
        if ($update->action === ContentUpdate::ACTION_DELETE) {
            return 'Yêu cầu xóa '.mb_strtolower($this->entityLabel($update->type)).($title !== '' ? ': '.$title : '');
        }
        if ($update->action === ContentUpdate::ACTION_REORDER) {
            $count = count($payload['lesson_orders'] ?? $payload['section_orders'] ?? $payload['orders'] ?? []);

            return 'Sắp xếp lại'.($count ? " {$count} mục" : ' nội dung');
        }
        if ($update->action === ContentUpdate::ACTION_CREATE) {
            return 'Thêm mới'.($title !== '' ? ': '.$title : '');
        }

        $keys = array_values(array_filter(array_keys($payload), fn (string $key): bool => ! in_array($key, [
            'legacy_chapter_id', 'review_status', 'ai_moderation', 'attachments',
        ], true)));

        return 'Chỉnh sửa '.count($keys).' trường'.($title !== '' ? ': '.$title : '');
    }

    /** @param array<string, mixed> $data @param array<string, mixed> $payload @return array<string, mixed> */
    private function courseDiff(array $data, ContentUpdate $update, array $payload, ?Course $course): array
    {
        if (! $course) {
            return $this->missing($data);
        }

        $fields = [
            'title' => 'Tên khóa học', 'short_description' => 'Mô tả ngắn', 'description' => 'Mô tả chi tiết',
            'objectives' => 'Mục tiêu', 'requirements' => 'Yêu cầu đầu vào', 'target_audience' => 'Đối tượng học',
            'level' => 'Cấp độ', 'language' => 'Ngôn ngữ', 'price' => 'Giá', 'discount_price' => 'Giá khuyến mãi',
            'sale_price' => 'Giá khuyến mãi', 'thumbnail' => 'Ảnh đại diện', 'preview_video' => 'Video giới thiệu',
            'category_id' => 'Danh mục',
        ];

        foreach ($fields as $key => $label) {
            if ($update->action === ContentUpdate::ACTION_UPDATE && ! array_key_exists($key, $payload)) {
                continue;
            }
            $old = $this->courseValue($course, $key);
            $new = $this->payloadValue($payload, $key, $old);
            $this->addField($data, $key, $label, $old, $new, $key === 'description' || $key === 'objectives');
        }

        return $data;
    }

    /** @param array<string, mixed> $data @param array<string, mixed> $payload @return array<string, mixed> */
    private function sectionDiff(array $data, ContentUpdate $update, array $payload, ?Course $course): array
    {
        $section = $course ? CourseSection::query()->where('course_id', $course->id)->find($update->entity_id) : null;
        if ($update->action === ContentUpdate::ACTION_REORDER) {
            return $this->reorderDiff($data, $payload, CourseSection::query()->where('course_id', $course?->id)->get(), 'section_orders');
        }
        if ($update->action !== ContentUpdate::ACTION_CREATE && ! $section) {
            return $this->missing($data);
        }

        foreach (['title' => 'Tên chương', 'description' => 'Mô tả', 'sort_order' => 'Vị trí'] as $key => $label) {
            if ($update->action === ContentUpdate::ACTION_UPDATE && ! array_key_exists($key, $payload)) {
                continue;
            }
            $old = $section?->{$key};
            $new = $this->payloadValue($payload, $key, $old);
            $this->addField($data, $key, $label, $old, $new, $key === 'description');
        }

        return $data;
    }

    /** @param array<string, mixed> $data @param array<string, mixed> $payload @return array<string, mixed> */
    private function lessonDiff(array $data, ContentUpdate $update, array $payload, ?Course $course): array
    {
        $lesson = $course ? Lesson::query()->where('course_id', $course->id)->with(['assignment', 'section'])->find($update->entity_id) : null;
        if ($update->action === ContentUpdate::ACTION_REORDER) {
            return $this->reorderDiff($data, $payload, Lesson::query()->where('course_id', $course?->id)->get(), 'lesson_orders');
        }
        if ($update->action !== ContentUpdate::ACTION_CREATE && ! $lesson) {
            return $this->missing($data);
        }

        $fields = [
            'title' => 'Tên bài học', 'type' => 'Loại bài học', 'section_id' => 'Chương học',
            'duration' => 'Thời lượng', 'duration_seconds' => 'Thời lượng', 'content' => 'Nội dung / yêu cầu',
            'document_file' => 'Tài liệu', 'is_preview' => 'Cho xem thử', 'sort_order' => 'Vị trí',
        ];
        foreach ($fields as $key => $label) {
            if ($key === 'duration_seconds' && array_key_exists('duration', $payload)) {
                continue;
            }
            if ($update->action === ContentUpdate::ACTION_UPDATE && ! array_key_exists($key, $payload)) {
                continue;
            }
            $old = $this->lessonValue($lesson, $key, $course);
            $new = $this->lessonPayloadValue($payload, $key, $old, $course);
            $this->addField($data, $key, $label, $old, $new, $key === 'content');
        }

        foreach ([
            'assignment_due_days' => 'Số ngày đến hạn',
            'assignment_max_score' => 'Điểm tối đa',
            'assignment_passing_score' => 'Điểm đạt',
        ] as $key => $label) {
            if ($update->action === ContentUpdate::ACTION_UPDATE && ! array_key_exists($key, $payload)) {
                continue;
            }
            if (($payload['type'] ?? $lesson?->type) !== Lesson::TYPE_ASSIGNMENT && ! array_key_exists($key, $payload)) {
                continue;
            }
            $old = match ($key) {
                'assignment_due_days' => $lesson?->assignment?->due_days,
                'assignment_max_score' => $lesson?->assignment?->max_score,
                default => $lesson?->assignment?->passing_score,
            };
            $this->addField($data, $key, $label, $old, $this->payloadValue($payload, $key, $old));
        }

        $oldVideo = $lesson ? $this->videoMetadata($lesson->getAttributes()) : null;
        $newVideo = $this->videoMetadata($payload + ['type' => $lesson?->type]);
        if ($oldVideo || $newVideo) {
            $data['media']['current_video'] = $oldVideo;
            $data['media']['proposed_video'] = $newVideo;
            if ($newVideo && ! $newVideo['ready']) {
                $data['warnings'][] = 'Video đề xuất chưa xử lý HLS hoàn tất.';
            }
        }

        return $data;
    }

    /** @param array<string, mixed> $data @param array<string, mixed> $payload @return array<string, mixed> */
    private function quizDiff(array $data, ContentUpdate $update, array $payload, ?Course $course): array
    {
        $quizId = (int) ($payload['quiz_id'] ?? $update->entity_id);
        $quiz = Quiz::query()->whereKey($quizId)->whereHas('lesson', fn ($query) => $query->where('course_id', $course?->id))->first();
        $candidate = QuizVersion::query()
            ->with('questionMappings.questionVersion.options')
            ->find((int) ($payload['quiz_version_id'] ?? 0));
        $published = $quiz?->currentPublishedVersion()->with('questionMappings.questionVersion.options')->first();
        if (! $quiz || ! $candidate || (int) $candidate->quiz_id !== (int) $quiz->id) {
            return $this->missing($data);
        }

        foreach (['title' => 'Tên Quiz', 'description' => 'Mô tả', 'pass_score' => 'Điểm đạt', 'time_limit_minutes' => 'Thời gian làm bài', 'max_attempts' => 'Số lần làm'] as $key => $label) {
            $this->addField($data, $key, $label, $published?->{$key}, $candidate->{$key}, $key === 'description');
        }

        $current = $this->questionsByIdentity($published?->questionMappings ?? collect());
        $proposed = $this->questionsByIdentity($candidate->questionMappings);
        $added = array_diff_key($proposed, $current);
        $removed = array_diff_key($current, $proposed);
        $changed = [];
        foreach (array_intersect_key($proposed, $current) as $id => $question) {
            if ($question['fingerprint'] !== $current[$id]['fingerprint']) {
                $changed[] = ['old' => $current[$id], 'new' => $question];
            }
        }
        $data['quiz_questions'] = [
            'current_count' => count($current), 'proposed_count' => count($proposed),
            'added' => array_values($added), 'removed' => array_values($removed), 'changed' => $changed,
        ];

        return $data;
    }

    /** @param array<string, mixed> $data @param Collection<int, mixed> $records @param array<string, mixed> $payload @return array<string, mixed> */
    private function reorderDiff(array $data, array $payload, Collection $records, string $preferredKey): array
    {
        $orders = $payload[$preferredKey] ?? $payload['orders'] ?? [];
        if (! is_array($orders)) {
            $data['warnings'][] = 'Dữ liệu sắp xếp không hợp lệ.';

            return $data;
        }
        $byId = $records->keyBy('id');
        $current = $records->sortBy('sort_order')->values()->map(fn ($record) => ['id' => $record->id, 'label' => $record->title, 'position' => $record->sort_order])->all();
        $proposed = collect($orders)->map(function ($order, $index) use ($byId) {
            $id = (int) (is_array($order) ? ($order['id'] ?? $order['lesson_id'] ?? $order['section_id'] ?? 0) : $order);
            $record = $byId->get($id);

            return $record ? ['id' => $id, 'label' => $record->title, 'position' => is_array($order) ? ($order['sort_order'] ?? $index + 1) : $index + 1] : null;
        })->filter()->values()->all();
        $data['current'] = $current;
        $data['proposed'] = $proposed;
        $data['fields'][] = ['key' => 'order', 'label' => 'Thứ tự nội dung', 'old' => $current, 'new' => $proposed, 'changed' => $current !== $proposed, 'long_text' => false];

        return $data;
    }

    /** @param array<string, mixed> $data */
    private function unknownDiff(array $data): array
    {
        $data['warnings'][] = 'Không hỗ trợ loại cập nhật này.';

        return $data;
    }

    /** @param array<string, mixed> $data */
    private function missing(array $data): array
    {
        $data['warnings'][] = 'Không thể xác định nội dung hiện tại trong khóa học này.';

        return $data;
    }

    /** @param array<string, mixed> $data */
    private function addField(array &$data, string $key, string $label, mixed $old, mixed $new, bool $longText = false): void
    {
        $field = ['key' => $key, 'label' => $label, 'old' => $this->display($old, $key), 'new' => $this->display($new, $key), 'changed' => $old !== $new, 'long_text' => $longText];
        if ($data['action'] === ContentUpdate::ACTION_CREATE || $field['changed'] || $data['action'] === ContentUpdate::ACTION_DELETE) {
            $data['fields'][] = $field;
        }
    }

    private function courseValue(Course $course, string $key): mixed
    {
        return $key === 'category_id' ? $course->category?->name : $course->{$key};
    }

    private function lessonValue(?Lesson $lesson, string $key, ?Course $course): mixed
    {
        if (! $lesson) {
            return null;
        }
        if ($key === 'section_id') {
            return $lesson->section?->title;
        }

        return $lesson->{$key};
    }

    /** @param array<string, mixed> $payload */
    private function lessonPayloadValue(array $payload, string $key, mixed $fallback, ?Course $course): mixed
    {
        if ($key === 'section_id' && array_key_exists($key, $payload)) {
            return CourseSection::query()->where('course_id', $course?->id)->whereKey($payload[$key])->value('title') ?? 'Chương không xác định';
        }

        return $this->payloadValue($payload, $key, $fallback);
    }

    /** @param array<string, mixed> $payload */
    private function payloadValue(array $payload, string $key, mixed $fallback): mixed
    {
        return array_key_exists($key, $payload) ? $payload[$key] : $fallback;
    }

    /** @param array<string, mixed> $values @return array<string, mixed>|null */
    private function videoMetadata(array $values): ?array
    {
        if (($values['type'] ?? null) !== Lesson::TYPE_VIDEO) {
            return null;
        }
        $source = $values['video_original_name'] ?? null;
        $source ??= ! empty($values['original_video_key']) ? basename((string) $values['original_video_key']) : null;
        $source ??= ! empty($values['video_path']) ? basename((string) $values['video_path']) : ($values['video_url'] ?? null);
        $internal = filled($values['original_video_key'] ?? null) || filled($values['video_path'] ?? null) || filled($values['hls_manifest_key'] ?? null);
        $ready = ! $internal || (($values['processing_status'] ?? null) === 'completed' && (filled($values['hls_manifest_key'] ?? null) || str_ends_with((string) ($values['video_path'] ?? ''), '.m3u8')));

        return ['filename' => $source ?: 'Chưa có nguồn video', 'processing_status' => $values['processing_status'] ?? null, 'ready' => $ready];
    }

    /** @param Collection<int, mixed> $mappings @return array<int, array<string, mixed>> */
    private function questionsByIdentity(Collection $mappings): array
    {
        return $mappings->mapWithKeys(function ($mapping) {
            $version = $mapping->questionVersion;
            $options = $version?->options?->map(fn ($option) => [$option->option_text, (bool) $option->is_correct, $option->sort_order])->all() ?? [];
            $question = ['id' => $mapping->question_id, 'text' => $version?->question, 'type' => $version?->type, 'points' => $version?->points, 'explanation' => $version?->explanation, 'options' => $options];
            $question['fingerprint'] = md5(json_encode($question, JSON_UNESCAPED_UNICODE));

            return [(int) $mapping->question_id => $question];
        })->all();
    }

    private function display(mixed $value, string $key): mixed
    {
        if (in_array($key, ['duration', 'duration_seconds'], true) && is_numeric($value)) {
            $seconds = (int) $value;

            return intdiv($seconds, 60).' phút'.($seconds % 60 ? ' '.($seconds % 60).' giây' : '');
        }
        if (in_array($key, ['is_preview'], true) && is_bool($value)) {
            return $value ? 'Có' : 'Không';
        }
        if (in_array($key, ['document_file', 'thumbnail'], true) && is_string($value) && $value !== '') {
            return basename($value);
        }
        if (in_array($key, ['price', 'discount_price', 'sale_price'], true) && is_numeric($value)) {
            return number_format((float) $value, 0, ',', '.').' đ';
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function label(ContentUpdate $update, array $payload, ?Course $course): string
    {
        return match ($update->type) {
            ContentUpdate::TYPE_COURSE => 'Khóa học: '.($course?->title ?? 'Không xác định'),
            ContentUpdate::TYPE_CHAPTER => 'Chương học: '.($payload['title'] ?? 'Không xác định'),
            ContentUpdate::TYPE_LESSON => 'Bài học: '.($payload['title'] ?? 'Không xác định'),
            ContentUpdate::TYPE_QUIZ => 'Quiz: '.($payload['title'] ?? 'Không xác định'),
            default => 'Nội dung cập nhật',
        };
    }

    private function entityLabel(string $type): string
    {
        return [ContentUpdate::TYPE_COURSE => 'Khóa học', ContentUpdate::TYPE_CHAPTER => 'Chương học', ContentUpdate::TYPE_LESSON => 'Bài học', ContentUpdate::TYPE_QUIZ => 'Quiz'][$type] ?? $type;
    }

    private function actionLabel(string $action): string
    {
        return [ContentUpdate::ACTION_CREATE => 'Thêm mới', ContentUpdate::ACTION_UPDATE => 'Chỉnh sửa', ContentUpdate::ACTION_DELETE => 'Xóa', ContentUpdate::ACTION_REORDER => 'Sắp xếp lại'][$action] ?? $action;
    }
}
