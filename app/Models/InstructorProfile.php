<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'organization',
        'position',
        'teaching_field',
        'specialty',
        'experience',
        'bio',
        'linkedin_url',
        'github_url',
        'website_url',
        'cv',
        'agree_information',
        'agree_terms',
    ];

    protected $casts = [
        'agree_information' => 'boolean',
        'agree_terms' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
