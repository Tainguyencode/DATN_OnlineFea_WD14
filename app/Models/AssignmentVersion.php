<?php

namespace App\Models;

use App\Models\Concerns\HasImmutableVersionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentVersion extends Model
{
    use HasImmutableVersionState;

    protected $fillable = ['assignment_id', 'version_number', 'status', 'content_update_id', 'source_version_id', 'title', 'description', 'instructions', 'due_date', 'due_days', 'max_score', 'passing_score', 'is_required', 'allowed_file_types', 'maximum_file_size', 'created_by', 'published_by', 'published_at', 'superseded_at', 'rejected_at'];

    protected function casts(): array
    {
        return ['version_number' => 'integer', 'source_version_id' => 'integer', 'due_date' => 'datetime', 'due_days' => 'integer', 'max_score' => 'integer', 'passing_score' => 'integer', 'is_required' => 'boolean', 'maximum_file_size' => 'integer', 'published_at' => 'datetime', 'superseded_at' => 'datetime', 'rejected_at' => 'datetime'];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
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
