<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(public readonly string $token) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expireMinutes = (int) config('auth.passwords.users.expire', 60);

        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Đặt lại mật khẩu OnlineFEA')
            ->greeting('Xin chào '.$notifiable->name.'!')
            ->line('Bạn nhận được email này vì hệ thống nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.')
            ->action('Đặt lại mật khẩu', $url)
            ->line("Liên kết đặt lại mật khẩu sẽ hết hạn sau {$expireMinutes} phút.")
            ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.');
    }
}
