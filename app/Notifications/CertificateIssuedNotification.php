<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Course $course,
        public readonly Certificate $certificate
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Chúc mừng bạn đã nhận được chứng chỉ hoàn thành khóa học!')
            ->greeting('Xin chào '.$notifiable->name.'!')
            ->line('Chúc mừng bạn đã hoàn thành xuất sắc khóa học: "'.$this->course->title.'".')
            ->line('Chứng chỉ hoàn thành khóa học của bạn đã được cấp thành công.')
            ->line('Mã số chứng chỉ của bạn là: '.$this->certificate->certificate_code)
            ->action('Xem chứng chỉ của tôi', route('student.certificates.pdf', $this->certificate))
            ->line('Cảm ơn bạn đã đồng hành và học tập cùng chúng tôi!');
    }
}
