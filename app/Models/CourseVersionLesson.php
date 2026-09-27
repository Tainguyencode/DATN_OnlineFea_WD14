<?php

namespace App\Models;

use App\Models\Concerns\BelongsToImmutableCourseRelease;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseVersionLesson extends Model
{
    use BelongsToImmutableCourseRelease;

    protected $fillable = ['course_version_id', 'course_section_id', 'lesson_id', 'lesson_version_id', 'sort_order', 'is_required', 'quiz_version_id', 'assignment_version_id'];

    protected $casts = ['sort_order' => 'integer', 'is_required' => 'boolean'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id')->withoutGlobalScope('not_archived');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class)->withoutGlobalScope('not_archived');
    }

    public function lessonVersion(): BelongsTo
    {
        return $this->belongsTo(LessonVersion::class);
    }

    public function quizVersion(): BelongsTo
    {
        return $this->belongsTo(QuizVersion::class);
    }

    public function assignmentVersion(): BelongsTo
    {
        return $this->belongsTo(AssignmentVersion::class);
    }
}
