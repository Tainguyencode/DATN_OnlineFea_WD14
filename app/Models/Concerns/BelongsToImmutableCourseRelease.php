<?php

namespace App\Models\Concerns;

use App\Models\CourseVersion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

trait BelongsToImmutableCourseRelease
{
    protected static function bootBelongsToImmutableCourseRelease(): void
    {
        $guard = function ($mapping): void {
            foreach (array_unique(array_filter([$mapping->course_version_id, $mapping->getOriginal('course_version_id')])) as $id) {
                $release = CourseVersion::findOrFail($id);
                if (! $release->isDraft() || $release->manifest_built_at) {
                    throw ValidationException::withMessages(['release' => 'A sealed release manifest is immutable.']);
                }
            }
        };
        static::saving($guard);
        static::deleting($guard);
    }

    public function courseVersion(): BelongsTo
    {
        return $this->belongsTo(CourseVersion::class);
    }
}
