<?php

namespace App\Enums;

enum SupportTicketCategory: string
{
    case Account = 'account';
    case Payment = 'payment';
    case CourseAccess = 'course_access';
    case Video = 'video';
    case Quiz = 'quiz';
    case Certificate = 'certificate';
    case Refund = 'refund';
    case Instructor = 'instructor';
    case CopyrightReport = 'copyright_report';
    case Technical = 'technical';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Account => 'Tài khoản',
            self::Payment => 'Thanh toán',
            self::CourseAccess => 'Truy cập khóa học',
            self::Video => 'Video bài học',
            self::Quiz => 'Quiz',
            self::Certificate => 'Chứng chỉ',
            self::Refund => 'Hoàn tiền',
            self::Instructor => 'Giảng viên',
            self::CopyrightReport => 'Báo cáo bản quyền',
            self::Technical => 'Kỹ thuật',
            self::Other => 'Khác',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
