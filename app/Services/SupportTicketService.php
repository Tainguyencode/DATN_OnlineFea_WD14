<?php

namespace App\Services;

use App\Enums\SupportTicketCategory;
use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use App\Models\PushNotification;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketMessage;
use App\Models\User;
use App\Notifications\SupportTicketCreatedNotification;
use App\Notifications\SupportTicketRepliedNotification;
use App\Notifications\SupportTicketStatusChangedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SupportTicketService
{
    /**
     * @param  array{subject: string, message: string, category: string, priority?: string}  $data
     * @param  list<UploadedFile>  $files
     */
    public function create(User $user, array $data, array $files = []): SupportTicket
    {
        $ticket = DB::transaction(function () use ($user, $data, $files) {
            $ticket = SupportTicket::query()->create([
                'user_id' => $user->id,
                'code' => $this->generateUniqueCode(),
                'subject' => $data['subject'],
                'message' => $data['message'],
                'category' => SupportTicketCategory::from($data['category']),
                'priority' => SupportTicketPriority::from($data['priority'] ?? SupportTicketPriority::Medium->value),
                'status' => SupportTicketStatus::Open,
            ]);

            $this->storeAttachments($ticket, null, $user, $files);

            return $ticket->fresh(['attachments']);
        });

        $this->notifyAdminsTicketCreated($ticket);
        $this->pushToAdmins(
            'Ticket hỗ trợ mới',
            "Ticket {$ticket->code}: {$ticket->subject}",
            'support_ticket_created',
            route('admin.support-tickets.show', $ticket)
        );

        return $ticket;
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    public function reply(User $actor, SupportTicket $ticket, string $message, array $files = []): SupportTicketMessage
    {
        $reply = DB::transaction(function () use ($actor, $ticket, $message, $files) {
            $reply = SupportTicketMessage::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $actor->id,
                'message' => $message,
            ]);

            $this->storeAttachments($ticket, $reply, $actor, $files);

            $updates = [
                'last_replied_at' => now(),
                'last_replied_by' => $actor->id,
            ];

            if ($actor->isAdmin() && $ticket->status === SupportTicketStatus::Open) {
                $updates['status'] = SupportTicketStatus::InProgress;
                if (! $ticket->assigned_to) {
                    $updates['assigned_to'] = $actor->id;
                }
            }

            if (! $actor->isAdmin() && $ticket->status === SupportTicketStatus::Resolved) {
                // User reply after resolved keeps status; reopen is explicit.
            }

            $ticket->update($updates);

            return $reply->fresh(['attachments', 'user']);
        });

        $this->notifyReply($ticket->fresh(['user', 'assignee']), $actor, $reply);

        return $reply;
    }

    public function closeByOwner(User $user, SupportTicket $ticket): SupportTicket
    {
        abort_unless($ticket->isOwnedBy($user), 403);

        $ticket->update([
            'status' => SupportTicketStatus::Closed,
            'closed_at' => now(),
        ]);

        $this->notifyStatusSafe($ticket->fresh(['user']), $user, SupportTicketStatus::Closed);

        return $ticket->fresh();
    }

    public function reopenByOwner(User $user, SupportTicket $ticket): SupportTicket
    {
        abort_unless($ticket->isOwnedBy($user), 403);
        abort_unless($ticket->isClosedLike(), 422);

        $ticket->update([
            'status' => SupportTicketStatus::Open,
            'closed_at' => null,
            'resolved_at' => null,
        ]);

        $this->notifyAdminsTicketCreated($ticket->fresh(['user']));
        $this->pushToAdmins(
            'Ticket được mở lại',
            "Ticket {$ticket->code} đã được người dùng mở lại.",
            'support_ticket_reopened',
            route('admin.support-tickets.show', $ticket)
        );

        return $ticket->fresh();
    }

    public function updateStatus(User $admin, SupportTicket $ticket, SupportTicketStatus $status): SupportTicket
    {
        $payload = ['status' => $status];

        if ($status === SupportTicketStatus::Resolved) {
            $payload['resolved_at'] = now();
        }
        if ($status === SupportTicketStatus::Closed) {
            $payload['closed_at'] = now();
        }
        if ($status === SupportTicketStatus::Open || $status === SupportTicketStatus::InProgress) {
            $payload['closed_at'] = null;
            if ($status === SupportTicketStatus::Open) {
                $payload['resolved_at'] = null;
            }
        }
        if ($status === SupportTicketStatus::InProgress && ! $ticket->assigned_to) {
            $payload['assigned_to'] = $admin->id;
        }

        $ticket->update($payload);
        $ticket = $ticket->fresh(['user', 'assignee']);

        $this->notifyStatusSafe($ticket, $admin, $status);
        $this->pushToUser(
            $ticket->user,
            'Cập nhật Ticket hỗ trợ',
            "Ticket {$ticket->code} chuyển sang trạng thái {$status->label()}.",
            'support_ticket_status',
            route('support.tickets.show', $ticket)
        );

        return $ticket;
    }

    public function updatePriority(User $admin, SupportTicket $ticket, SupportTicketPriority $priority): SupportTicket
    {
        $ticket->update(['priority' => $priority]);

        return $ticket->fresh();
    }

    public function assign(User $admin, SupportTicket $ticket, ?int $assigneeId): SupportTicket
    {
        if ($assigneeId !== null) {
            $assignee = User::query()->where('id', $assigneeId)->where('role', 'admin')->firstOrFail();
            $ticket->update([
                'assigned_to' => $assignee->id,
                'status' => $ticket->status === SupportTicketStatus::Open
                    ? SupportTicketStatus::InProgress
                    : $ticket->status,
            ]);
        } else {
            $ticket->update(['assigned_to' => null]);
        }

        return $ticket->fresh(['assignee']);
    }

    public function generateUniqueCode(): string
    {
        $year = now()->format('Y');

        for ($i = 0; $i < 20; $i++) {
            $code = 'TK-'.$year.'-'.strtoupper(Str::random(8));
            if (! SupportTicket::query()->where('code', $code)->exists()) {
                return $code;
            }
        }

        return 'TK-'.$year.'-'.strtoupper(Str::random(12));
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    private function storeAttachments(
        SupportTicket $ticket,
        ?SupportTicketMessage $message,
        User $user,
        array $files
    ): void {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            try {
                $path = $file->store('support-tickets/'.$ticket->id, 'local');
                SupportTicketAttachment::query()->create([
                    'ticket_id' => $ticket->id,
                    'message_id' => $message?->id,
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => (string) ($file->getClientMimeType() ?: 'application/octet-stream'),
                    'file_size' => (int) $file->getSize(),
                ]);
            } catch (Throwable $exception) {
                Log::warning('Support ticket attachment upload failed.', [
                    'ticket_id' => $ticket->id,
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function notifyAdminsTicketCreated(SupportTicket $ticket): void
    {
        try {
            User::query()
                ->where('role', 'admin')
                ->where('is_active', true)
                ->each(function (User $admin) use ($ticket): void {
                    try {
                        $admin->notify(new SupportTicketCreatedNotification($ticket));
                    } catch (Throwable $exception) {
                        Log::warning('Support ticket created email failed.', [
                            'admin_id' => $admin->id,
                            'ticket_id' => $ticket->id,
                            'message' => $exception->getMessage(),
                        ]);
                    }
                });
        } catch (Throwable $exception) {
            Log::warning('Support ticket admin notify loop failed.', [
                'ticket_id' => $ticket->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function notifyReply(SupportTicket $ticket, User $actor, SupportTicketMessage $reply): void
    {
        try {
            if ($actor->isAdmin()) {
                $ticket->user?->notify(new SupportTicketRepliedNotification($ticket, $reply, $actor));
                $this->pushToUser(
                    $ticket->user,
                    'Phản hồi Ticket hỗ trợ',
                    "Admin đã phản hồi ticket {$ticket->code}.",
                    'support_ticket_replied',
                    route('support.tickets.show', $ticket)
                );
            } else {
                $recipients = User::query()->where('role', 'admin')->where('is_active', true)->get();
                if ($ticket->assignee) {
                    $recipients = $recipients->where('id', $ticket->assignee->id)->values();
                    if ($recipients->isEmpty()) {
                        $recipients = collect([$ticket->assignee]);
                    }
                }

                foreach ($recipients as $admin) {
                    try {
                        $admin->notify(new SupportTicketRepliedNotification($ticket, $reply, $actor));
                    } catch (Throwable $exception) {
                        Log::warning('Support ticket reply email to admin failed.', [
                            'admin_id' => $admin->id,
                            'ticket_id' => $ticket->id,
                            'message' => $exception->getMessage(),
                        ]);
                    }
                }

                $this->pushToAdmins(
                    'Phản hồi Ticket từ người dùng',
                    "{$actor->name} đã phản hồi ticket {$ticket->code}.",
                    'support_ticket_replied',
                    route('admin.support-tickets.show', $ticket)
                );
            }
        } catch (Throwable $exception) {
            Log::warning('Support ticket reply notification failed.', [
                'ticket_id' => $ticket->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function notifyStatusSafe(SupportTicket $ticket, User $actor, SupportTicketStatus $status): void
    {
        try {
            if ($ticket->user && ! $actor->is($ticket->user)) {
                $ticket->user->notify(new SupportTicketStatusChangedNotification($ticket, $status));
            }
        } catch (Throwable $exception) {
            Log::warning('Support ticket status email failed.', [
                'ticket_id' => $ticket->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function pushToAdmins(string $title, string $message, string $type, string $url): void
    {
        try {
            User::query()->where('role', 'admin')->where('is_active', true)->each(function (User $admin) use ($title, $message, $type, $url): void {
                PushNotification::query()->create([
                    'user_id' => $admin->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'url' => $url,
                    'is_read' => false,
                ]);
            });
        } catch (Throwable $exception) {
            Log::warning('Support ticket admin push failed.', ['message' => $exception->getMessage()]);
        }
    }

    private function pushToUser(?User $user, string $title, string $message, string $type, string $url): void
    {
        if (! $user) {
            return;
        }

        try {
            PushNotification::query()->create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'url' => $url,
                'is_read' => false,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Support ticket user push failed.', [
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
