<?php

namespace App\Models;

use App\Models\Concerns\HasImmutableVersionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseVersion extends Model
{
    use HasImmutableVersionState;

    public function sectionMappings(): HasMany
    {
        return $this->hasMany(CourseVersionSection::class)->orderBy('sort_order')->orderBy('id');
    }

    public function lessonMappings(): HasMany
    {
        return $this->hasMany(CourseVersionLesson::class)->orderBy('sort_order')->orderBy('id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    protected $fillable = ['manifest_built_at', 'required_video_percent', 'required_lesson_percent', 'minimum_quiz_score', 'require_all_quizzes', 'require_all_assignments', 'certificate_enabled', 'course_id', 'version_number', 'status', 'content_update_id', 'source_version_id', 'title', 'slug', 'short_description', 'description', 'objectives', 'requirements', 'target_audience', 'category_id', 'level', 'language', 'price', 'discount_price', 'sale_price', 'thumbnail', 'preview_video', 'tags', 'created_by', 'published_by', 'published_at', 'superseded_at', 'rejected_at'];

    protected function casts(): array
    {
        return ['manifest_built_at' => 'datetime', 'required_video_percent' => 'integer', 'required_lesson_percent' => 'integer', 'minimum_quiz_score' => 'integer', 'require_all_quizzes' => 'boolean', 'require_all_assignments' => 'boolean', 'certificate_enabled' => 'boolean', 'version_number' => 'integer', 'source_version_id' => 'integer', 'price' => 'decimal:2', 'discount_price' => 'decimal:2', 'sale_price' => 'decimal:2', 'tags' => 'array', 'published_at' => 'datetime', 'superseded_at' => 'datetime', 'rejected_at' => 'datetime'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
