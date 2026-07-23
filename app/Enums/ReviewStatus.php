<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ duyệt',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Từ chối',
            self::Hidden => 'Đã ẩn',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-300',
            self::Approved => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300',
            self::Rejected => 'bg-rose-100 text-rose-800 dark:bg-rose-500/10 dark:text-rose-300',
            self::Hidden => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
        };
    }
}
