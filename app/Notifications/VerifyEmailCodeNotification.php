<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailCodeNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mã xác thực tài khoản OnlineFEA')
            ->greeting('Xin chào '.$notifiable->name.'!')
            ->line('Mã xác thực của bạn là: '.$this->code)
            ->line('Mã có hiệu lực trong 10 phút.')
            ->line('Vui lòng không chia sẻ mã này với bất kỳ ai.')
            ->line('Nếu bạn không yêu cầu mã xác thực, hãy bỏ qua email này.');
    }
}
