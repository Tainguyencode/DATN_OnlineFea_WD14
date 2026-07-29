<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && ($user->isStudent() || $user->role === 'instructor' || $user->isAdmin());
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $ticket->isOwnedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->is_active && ($user->isStudent() || $user->role === 'instructor');
    }

    public function reply(User $user, SupportTicket $ticket): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $ticket->isOwnedBy($user) && $ticket->status->value !== 'closed';
    }

    public function close(User $user, SupportTicket $ticket): bool
    {
        return $user->is_active && $ticket->isOwnedBy($user) && $ticket->status->value !== 'closed';
    }

    public function reopen(User $user, SupportTicket $ticket): bool
    {
        return $user->is_active && $ticket->isOwnedBy($user) && $ticket->isClosedLike();
    }

    public function manage(User $user, SupportTicket $ticket): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function updateOriginal(User $user, SupportTicket $ticket): bool
    {
        // Users cannot edit original content after staff has replied.
        if (! $ticket->isOwnedBy($user) || $user->isAdmin()) {
            return false;
        }

        return ! $ticket->hasStaffReply();
    }

    public function downloadAttachment(User $user, SupportTicket $ticket, SupportTicketAttachment $attachment): bool
    {
        if ((int) $attachment->ticket_id !== (int) $ticket->id) {
            return false;
        }

        return $this->view($user, $ticket);
    }
}
