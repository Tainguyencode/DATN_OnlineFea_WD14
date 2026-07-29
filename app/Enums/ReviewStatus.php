<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case Visible = 'visible';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Visible => 'Đang hiển thị',
            self::Hidden => 'Đã ẩn',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Visible => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300',
            self::Hidden => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
        };
    }
}
