<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Instructor = 'instructor';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Học viên',
            self::Instructor => 'Giảng viên',
            self::Admin => 'Quản trị viên',
            self::SuperAdmin => 'Super Admin',
        };
    }

    public static function adminRoles(): array
    {
        return [self::Admin->value, self::SuperAdmin->value];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
