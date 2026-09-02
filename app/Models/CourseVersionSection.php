<?php

namespace App\Models;

use App\Models\Concerns\BelongsToImmutableCourseRelease;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseVersionSection extends Model
{
    use BelongsToImmutableCourseRelease;

    protected $fillable = ['course_version_id', 'course_section_id', 'course_section_version_id', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id')->withoutGlobalScope('not_archived');
    }

    public function sectionVersion(): BelongsTo
    {
        return $this->belongsTo(CourseSectionVersion::class, 'course_section_version_id');
    }
}
