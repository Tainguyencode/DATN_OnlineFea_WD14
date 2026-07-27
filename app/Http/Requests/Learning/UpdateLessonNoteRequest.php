<?php

namespace App\Http\Requests\Learning;

use App\Services\LessonNoteAccessService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateLessonNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lessonNote = $this->route('lessonNote');

        return $lessonNote && ($this->user()?->can('update', $lessonNote) ?? false);
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:2000'],
            'timestamp_seconds' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $lessonNote = $this->route('lessonNote');
            $lesson = $lessonNote?->lesson;

            if (! $lesson || $lesson->type !== 'video' || $this->input('timestamp_seconds') === null) {
                return;
            }

            $duration = app(LessonNoteAccessService::class)->lessonDurationSeconds($lesson);
            $timestamp = (int) $this->input('timestamp_seconds');

            if ($duration > 0 && $timestamp > $duration) {
                $validator->errors()->add('timestamp_seconds', 'Mốc thời gian không được vượt quá thời lượng video.');
            }
        });
    }
}
