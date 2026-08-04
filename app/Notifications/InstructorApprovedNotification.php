<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorApprovedNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Chúc mừng! Hồ sơ Giảng viên của bạn đã được phê duyệt')
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line('Chúc mừng bạn! Hồ sơ đăng ký Giảng viên của bạn tại hệ thống đã được Ban quản trị xem xét và phê duyệt.')
            ->line('Bây giờ bạn đã có thể bắt đầu tạo khóa học, quản lý nội dung bài học và kết nối với hàng nghìn học viên.')
            ->action('Truy cập Dashboard Giảng viên', route('instructor.dashboard'))
            ->line('Cảm ơn bạn đã đồng hành cùng chúng tôi!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Hồ sơ Giảng viên đã được duyệt',
            'message' => 'Chúc mừng! Hồ sơ giảng viên của bạn đã được Admin chấp thuận. Bạn có thể bắt đầu tạo khóa học ngay.',
            'action_url' => route('instructor.dashboard'),
            'type' => 'instructor_approved',
        ];
    }
}
