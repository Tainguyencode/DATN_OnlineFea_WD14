<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use App\Services\CourseSubmissionValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SubmitCourseForReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Course $course */
        $course = $this->route('course');

        return $course->isOwnedBy($this->user());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    public function validateSubmissionRequirements(): void
    {
        /** @var Course $course */
        $course = $this->route('course');

        if (! $course->canBeSubmittedForReview()) {
            throw ValidationException::withMessages([
                'submission' => ['Khóa học hiện không thể gửi duyệt.'],
            ]);
        }

        $result = app(CourseSubmissionValidator::class)->validate($course);

        if ($result->passes()) {
            return;
        }

        throw ValidationException::withMessages([
            'submission' => $result->errorMessages(),
        ]);
    }
}
