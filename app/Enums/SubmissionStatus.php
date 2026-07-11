<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Graded = 'graded';
    case ResubmitRequired = 'resubmit_required';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nháp',
            self::Submitted => 'Đã nộp',
            self::Graded => 'Đã chấm',
            self::ResubmitRequired => 'Cần nộp lại',
        };
    }
}
