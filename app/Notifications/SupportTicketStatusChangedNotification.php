<?php

namespace App\Notifications;

use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly SupportTicket $ticket,
        public readonly SupportTicketStatus $status,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Cập nhật trạng thái ticket '.$this->ticket->code)
            ->greeting('Xin chào '.$notifiable->name.'!')
            ->line('Ticket '.$this->ticket->code.' đã chuyển sang trạng thái: '.$this->status->label().'.')
            ->action('Xem ticket', route('support.tickets.show', $this->ticket));
    }
}
