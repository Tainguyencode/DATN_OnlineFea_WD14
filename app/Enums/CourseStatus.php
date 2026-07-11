<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Published = 'published';
    case Suspended = 'suspended';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nháp',
            self::PendingReview => 'Chờ duyệt',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Bị từ chối',
            self::Published => 'Đã xuất bản',
            self::Suspended => 'Tạm ngừng',
            self::Archived => 'Đã lưu trữ',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Rejected], true);
    }

    public function isPublic(): bool
    {
        return $this === self::Published;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
