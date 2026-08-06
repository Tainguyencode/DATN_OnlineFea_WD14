<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LearningReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $reminderMessage,
        public ?string $courseTitle = null,
        public ?string $url = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Duy trì thói quen học tập cùng OnlineFEA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.learning-reminder',
            with: [
                'user' => $this->user,
                'reminderMessage' => $this->reminderMessage,
                'courseTitle' => $this->courseTitle,
                'url' => $this->url,
            ],
        );
    }
}
