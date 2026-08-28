<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmLessonImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');
        if (! ($course instanceof Course)) {
            $course = Course::find($course);
        }

        $section = $this->route('section');
        if (! ($section instanceof CourseSection)) {
            $section = CourseSection::find($section);
        }

        return $course instanceof Course
            && $section instanceof CourseSection
            && $course->isOwnedBy($this->user())
            && (int) $section->course_id === (int) $course->id;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'batch_token' => ['required', 'string', 'uuid'],
            'rows' => ['prohibited'],
            'title' => ['prohibited'],
            'type' => ['prohibited'],
            'duration' => ['prohibited'],
            'duration_seconds' => ['prohibited'],
            'sort_order' => ['prohibited'],
            'assignment_due_days' => ['prohibited'],
            'assignment_max_score' => ['prohibited'],
            'assignment_passing_score' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'batch_token.required' => 'Phiên kiểm tra file không còn tồn tại. Vui lòng kiểm tra lại file.',
            'batch_token.string' => 'Phiên kiểm tra file không hợp lệ. Vui lòng kiểm tra lại file.',
            'batch_token.uuid' => 'Phiên kiểm tra file không hợp lệ. Vui lòng kiểm tra lại file.',
            '*.prohibited' => 'Confirm chỉ chấp nhận batch_token từ phiên kiểm tra file.',
        ];
    }
}
