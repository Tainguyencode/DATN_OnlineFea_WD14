<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'section_id')->orderBy('sort_order');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(CourseSectionVersion::class);
    }

    public function publishedVersion(): BelongsTo
    {
        return $this->belongsTo(CourseSectionVersion::class, 'published_version_id');
    }

    public function draftVersion(): BelongsTo
    {
        return $this->belongsTo(CourseSectionVersion::class, 'draft_version_id');
    }

    public static function descriptionContainsMarkup(mixed $description): bool
    {
        if (! is_string($description) || trim($description) === '') {
            return false;
        }

        if (strip_tags($description) !== $description) {
            return true;
        }

        return preg_match(
            '/(?:\{\{|\{!!|@(?:csrf|method|error|enderror|if|elseif|else|endif|foreach|endforeach|include|php|endphp)\b)/iu',
            $description,
        ) === 1;
    }
}
