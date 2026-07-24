<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly SupportTicket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket hỗ trợ mới: '.$this->ticket->code)
            ->greeting('Xin chào '.$notifiable->name.'!')
            ->line('Có ticket hỗ trợ mới cần xử lý.')
            ->line('Mã: '.$this->ticket->code)
            ->line('Tiêu đề: '.$this->ticket->subject)
            ->action('Xem ticket', route('admin.support-tickets.show', $this->ticket));
    }
}
