<?php

namespace App\Models;

use App\Models\Concerns\HasImmutableVersionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentVersion extends Model
{
    use HasImmutableVersionState;

    protected $fillable = ['assignment_id', 'version_number', 'status', 'title', 'description', 'instructions', 'due_date', 'due_days', 'max_score', 'passing_score', 'is_required', 'allowed_file_types', 'maximum_file_size', 'created_by', 'published_by', 'published_at', 'superseded_at'];

    protected function casts(): array
    {
        return ['version_number' => 'integer', 'due_date' => 'datetime', 'due_days' => 'integer', 'max_score' => 'integer', 'passing_score' => 'integer', 'is_required' => 'boolean', 'maximum_file_size' => 'integer', 'published_at' => 'datetime', 'superseded_at' => 'datetime'];
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
}
