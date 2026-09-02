<?php

namespace App\Models\Concerns;

use Illuminate\Validation\ValidationException;

trait HasImmutableVersionState
{
    protected static function bootHasImmutableVersionState(): void
    {
        static::updating(function ($version): void {
            $storedStatus = $version->newQuery()->whereKey($version->id)->value('status');
            if (in_array($storedStatus, ['published', 'superseded', 'rejected'], true)) {
                $allowed = $storedStatus === 'published' && $version->status === 'superseded'
                    ? ['status', 'superseded_at', 'updated_at'] : ['updated_at'];
                if (array_diff(array_keys($version->getDirty()), $allowed)) {
                    throw ValidationException::withMessages(['version' => 'Historical version content is immutable.']);
                }
            }
        });
        static::deleting(function ($version): void {
            if ($version->newQuery()->whereKey($version->id)->value('status') !== 'draft') {
                throw ValidationException::withMessages(['version' => 'Historical versions cannot be deleted.']);
            }
        });
    }

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SUPERSEDED = 'superseded';

    public const STATUS_REJECTED = 'rejected';

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isSuperseded(): bool
    {
        return $this->status === self::STATUS_SUPERSEDED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
