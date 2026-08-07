<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public string $reason)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Thông báo về hồ sơ đăng ký Giảng viên')
            ->greeting('Xin chào ' . $notifiable->name . ',')
            ->line('Rất tiếc, đơn đăng ký Giảng viên của bạn chưa đáp ứng đủ tiêu chuẩn yêu cầu.')
            ->line('Lý do từ chối: "' . $this->reason . '"')
            ->line('Bạn có thể cập nhật lại thông tin hồ sơ và gửi yêu cầu xét duyệt lại.')
            ->action('Cập nhật hồ sơ đăng ký', route('instructor.pending'))
            ->line('Cảm ơn bạn đã quan tâm đến nền tảng của chúng tôi.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Hồ sơ Giảng viên chưa được duyệt',
            'message' => 'Hồ sơ đăng ký giảng viên bị từ chối: ' . $this->reason,
            'action_url' => route('instructor.pending'),
            'type' => 'instructor_rejected',
        ];
    }
}
