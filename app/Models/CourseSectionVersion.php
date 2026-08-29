<?php

namespace App\Models;

use App\Models\Concerns\HasImmutableVersionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSectionVersion extends Model
{
    use HasImmutableVersionState;

    protected $fillable = ['course_section_id', 'version_number', 'status', 'title', 'description', 'sort_order', 'created_by', 'published_by', 'published_at', 'superseded_at'];

    protected function casts(): array
    {
        return ['version_number' => 'integer', 'sort_order' => 'integer', 'published_at' => 'datetime', 'superseded_at' => 'datetime'];
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
}
