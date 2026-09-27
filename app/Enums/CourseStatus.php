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
    case PendingUpdate = 'pending_update';
    case RejectedUpdate = 'rejected_update';

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
            self::PendingUpdate => 'Cập nhật chờ duyệt',
            self::RejectedUpdate => 'Bị từ chối cập nhật',
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

    public function chartColor(): string
    {
        return match ($this) {
            self::Draft => '#94a3b8',
            self::PendingReview => '#f59e0b',
            self::Approved => '#0ea5e9',
            self::Rejected => '#ef4444',
            self::Published => '#22c55e',
            self::Suspended => '#f97316',
            self::Archived => '#64748b',
            self::PendingUpdate => '#8b5cf6',
            self::RejectedUpdate => '#e11d48',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
