<?php

namespace App\Models;

use App\Models\Concerns\HasImmutableVersionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseVersion extends Model
{
    use HasImmutableVersionState;

    protected $fillable = ['course_id', 'version_number', 'status', 'title', 'slug', 'short_description', 'description', 'objectives', 'requirements', 'target_audience', 'category_id', 'level', 'language', 'price', 'discount_price', 'sale_price', 'thumbnail', 'preview_video', 'tags', 'created_by', 'published_by', 'published_at', 'superseded_at'];

    protected function casts(): array
    {
        return ['version_number' => 'integer', 'price' => 'decimal:2', 'discount_price' => 'decimal:2', 'sale_price' => 'decimal:2', 'tags' => 'array', 'published_at' => 'datetime', 'superseded_at' => 'datetime'];
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
}
