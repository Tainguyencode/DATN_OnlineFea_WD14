<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Hoạt động',
            self::Inactive => 'Không hoạt động',
            self::Pending => 'Chờ duyệt',
            self::Blocked => 'Đã khóa',
        };
    }

    public function canLogin(): bool
    {
        return $this === self::Active;
    }
}
