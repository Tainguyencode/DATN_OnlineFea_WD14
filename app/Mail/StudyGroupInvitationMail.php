<?php

namespace App\Mail;

use App\Models\StudyGroup;
use App\Models\StudyGroupInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudyGroupInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $invitedUser,
        public User $inviter,
        public StudyGroup $studyGroup,
        public StudyGroupInvitation $invitation,
        public string $actionUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Lời mời tham gia nhóm học: {$this->studyGroup->name} - OnlineFEA",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.study-group-invitation',
            with: [
                'invitedUser' => $this->invitedUser,
                'inviter' => $this->inviter,
                'studyGroup' => $this->studyGroup,
                'invitation' => $this->invitation,
                'actionUrl' => $this->actionUrl,
            ],
        );
    }
}
