<?php

namespace App\Services;

use App\Models\Course;

class CourseValidationService
{
    public function validateForSubmission(Course $course): array
    {
        return [
            'eligible' => true,
            'errors' => [],
            'checklist' => [],
            'missing' => [],
        ];
    }
}
