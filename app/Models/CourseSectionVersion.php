<?php

namespace App\Models;

use App\Models\Concerns\HasImmutableVersionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSectionVersion extends Model
{
    use HasImmutableVersionState;

    protected $fillable = ['course_section_id', 'version_number', 'status', 'content_update_id', 'source_version_id', 'title', 'description', 'sort_order', 'created_by', 'published_by', 'published_at', 'superseded_at', 'rejected_at'];

    protected function casts(): array
    {
        return ['version_number' => 'integer', 'source_version_id' => 'integer', 'sort_order' => 'integer', 'published_at' => 'datetime', 'superseded_at' => 'datetime', 'rejected_at' => 'datetime'];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function contentUpdate(): BelongsTo
    {
        return $this->belongsTo(ContentUpdate::class);
    }

    public function sourceVersion(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_version_id');
    }

    public function derivedVersions(): HasMany
    {
        return $this->hasMany(self::class, 'source_version_id');
    }
}
