<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketRepliedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly SupportTicket $ticket,
        public readonly SupportTicketMessage $reply,
        public readonly User $actor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $notifiable->isAdmin()
            ? route('admin.support-tickets.show', $this->ticket)
            : route('support.tickets.show', $this->ticket);

        return (new MailMessage)
            ->subject('Phản hồi mới cho ticket '.$this->ticket->code)
            ->greeting('Xin chào '.$notifiable->name.'!')
            ->line($this->actor->name.' đã phản hồi ticket '.$this->ticket->code.'.')
            ->line(\Illuminate\Support\Str::limit($this->reply->message, 200))
            ->action('Xem trao đổi', $url);
    }
}
