<?php

namespace Tests\Feature;

use App\Enums\SupportTicketCategory;
use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketMessage;
use App\Models\User;
use App\Notifications\SupportTicketCreatedNotification;
use App\Notifications\SupportTicketRepliedNotification;
use App\Services\RoleSyncService;
use App\Services\SupportTicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    public function test_student_can_create_ticket_without_duplicating_initial_message(): void
    {
        Notification::fake();

        $student = $this->makeUser('student');
        $admin = $this->makeUser('admin');

        $response = $this->loginAs($student)
            ->post(route('support.tickets.store'), [
                'subject' => 'Không xem được video',
                'message' => 'Video báo đang xử lý quá lâu.',
                'category' => SupportTicketCategory::Video->value,
                'priority' => SupportTicketPriority::High->value,
            ]);

        $ticket = SupportTicket::query()->first();
        $this->assertNotNull($ticket);
        $response->assertRedirect(route('support.tickets.show', $ticket));

        $this->assertSame(0, SupportTicketMessage::query()->count());
        $this->assertSame('Video báo đang xử lý quá lâu.', $ticket->message);
        $this->assertNotEmpty($ticket->code);
        $this->assertTrue(str_starts_with($ticket->code, 'TK-'));
        $this->assertSame(SupportTicketStatus::Open, $ticket->status);

        Notification::assertSentTo($admin, SupportTicketCreatedNotification::class);
    }

    public function test_student_cannot_view_others_ticket(): void
    {
        $owner = $this->makeUser('student');
        $other = $this->makeUser('student');
        $ticket = $this->makeTicket($owner);

        $this->loginAs($other)
            ->get(route('support.tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_admin_can_reply_and_user_receives_notification(): void
    {
        Notification::fake();

        $student = $this->makeUser('student');
        $admin = $this->makeUser('admin');
        $ticket = $this->makeTicket($student);

        $this->loginAs($admin)
            ->post(route('admin.support-tickets.reply', $ticket), [
                'message' => 'Chúng tôi đang kiểm tra video của bạn.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Chúng tôi đang kiểm tra video của bạn.',
        ]);

        $ticket->refresh();
        $this->assertSame(SupportTicketStatus::InProgress, $ticket->status);
        $this->assertSame($admin->id, $ticket->assigned_to);

        Notification::assertSentTo($student, SupportTicketRepliedNotification::class);
    }

    public function test_user_can_close_and_reopen_ticket(): void
    {
        $student = $this->makeUser('student');
        $ticket = $this->makeTicket($student);

        $this->loginAs($student)
            ->post(route('support.tickets.close', $ticket))
            ->assertRedirect();

        $this->assertSame(SupportTicketStatus::Closed, $ticket->fresh()->status);

        $this->loginAs($student)
            ->post(route('support.tickets.reopen', $ticket))
            ->assertRedirect();

        $this->assertSame(SupportTicketStatus::Open, $ticket->fresh()->status);
    }

    public function test_closed_ticket_cannot_be_replied_by_user(): void
    {
        $student = $this->makeUser('student');
        $ticket = $this->makeTicket($student, [
            'status' => SupportTicketStatus::Closed,
            'closed_at' => now(),
        ]);

        $this->loginAs($student)
            ->post(route('support.tickets.reply', $ticket), [
                'message' => 'Vẫn muốn hỏi thêm.',
            ])
            ->assertForbidden();

        $this->assertSame(0, SupportTicketMessage::query()->where('ticket_id', $ticket->id)->count());
    }

    public function test_user_cannot_mark_resolved_or_assign_admin(): void
    {
        $student = $this->makeUser('student');
        $admin = $this->makeUser('admin');
        $ticket = $this->makeTicket($student);

        $this->loginAs($student)
            ->patch(route('admin.support-tickets.update', $ticket), [
                'status' => SupportTicketStatus::Resolved->value,
                'assigned_to' => $admin->id,
            ]);

        $ticket->refresh();
        $this->assertSame(SupportTicketStatus::Open, $ticket->status);
        $this->assertNull($ticket->assigned_to);
    }

    public function test_admin_can_filter_and_update_priority_assign(): void
    {
        $student = $this->makeUser('student');
        $admin = $this->makeUser('admin');
        $ticket = $this->makeTicket($student, [
            'code' => 'TK-2026-TESTFILT',
            'category' => SupportTicketCategory::Payment,
            'priority' => SupportTicketPriority::Low,
        ]);

        $this->loginAs($admin)
            ->get(route('admin.support-tickets.index', [
                'q' => 'TESTFILT',
                'category' => 'payment',
            ]))
            ->assertOk()
            ->assertSee('TK-2026-TESTFILT');

        $this->loginAs($admin)
            ->patch(route('admin.support-tickets.update', $ticket), [
                'priority' => SupportTicketPriority::High->value,
                'assigned_to' => $admin->id,
                'status' => SupportTicketStatus::InProgress->value,
            ])
            ->assertRedirect();

        $ticket->refresh();
        $this->assertSame(SupportTicketPriority::High, $ticket->priority);
        $this->assertSame($admin->id, $ticket->assigned_to);
        $this->assertSame(SupportTicketStatus::InProgress, $ticket->status);
    }

    public function test_attachment_stores_mime_type_file_size_on_private_disk(): void
    {
        Storage::fake('local');
        Notification::fake();

        $student = $this->makeUser('student');
        $this->makeUser('admin');

        $file = UploadedFile::fake()->create('evidence.pdf', 120, 'application/pdf');

        $ticket = app(SupportTicketService::class)->create($student, [
            'subject' => 'Lỗi quiz',
            'message' => 'Không nộp được bài.',
            'category' => SupportTicketCategory::Quiz->value,
            'priority' => SupportTicketPriority::Medium->value,
        ], [$file]);

        $attachment = SupportTicketAttachment::query()->where('ticket_id', $ticket->id)->first();
        $this->assertNotNull($attachment);
        $this->assertSame('evidence.pdf', $attachment->original_name);
        $this->assertNotEmpty($attachment->mime_type);
        $this->assertGreaterThan(0, (int) $attachment->file_size);
        $this->assertArrayHasKey('mime_type', $attachment->getAttributes());
        $this->assertArrayHasKey('file_size', $attachment->getAttributes());
        $this->assertArrayNotHasKey('mime', $attachment->getAttributes());
        $this->assertArrayNotHasKey('size', $attachment->getAttributes());
        Storage::disk('local')->assertExists($attachment->file_path);
    }

    public function test_email_failure_does_not_rollback_ticket_create(): void
    {
        Storage::fake('local');

        $student = $this->makeUser('student');
        $this->makeUser('admin');

        $this->mock(\Illuminate\Contracts\Notifications\Dispatcher::class, function ($mock) {
            $mock->shouldReceive('send')->andThrow(new \RuntimeException('SMTP down'));
        });

        $ticket = app(SupportTicketService::class)->create($student, [
            'subject' => 'SMTP fail',
            'message' => 'Vẫn phải tạo được ticket.',
            'category' => SupportTicketCategory::Technical->value,
        ]);

        $this->assertDatabaseHas('support_tickets', ['id' => $ticket->id, 'subject' => 'SMTP fail']);
    }

    public function test_other_user_cannot_download_attachment(): void
    {
        Storage::fake('local');

        $owner = $this->makeUser('student');
        $other = $this->makeUser('student');
        $ticket = app(SupportTicketService::class)->create($owner, [
            'subject' => 'Private file',
            'message' => 'Có đính kèm.',
            'category' => SupportTicketCategory::Other->value,
        ], [
            UploadedFile::fake()->create('secret.pdf', 50, 'application/pdf'),
        ]);

        $attachment = SupportTicketAttachment::query()->where('ticket_id', $ticket->id)->firstOrFail();

        $this->loginAs($other)
            ->get(route('support.tickets.attachments.download', [$ticket, $attachment]))
            ->assertForbidden();
    }

    public function test_owner_can_download_attachment_and_missing_file_returns_404(): void
    {
        Storage::fake('local');

        $owner = $this->makeUser('student');
        $ticket = app(SupportTicketService::class)->create($owner, [
            'subject' => 'Download ok',
            'message' => 'Có đính kèm.',
            'category' => SupportTicketCategory::Other->value,
        ], [
            UploadedFile::fake()->create('ok.pdf', 40, 'application/pdf'),
        ]);

        $attachment = SupportTicketAttachment::query()->where('ticket_id', $ticket->id)->firstOrFail();

        $this->loginAs($owner)
            ->get(route('support.tickets.attachments.download', [$ticket, $attachment]))
            ->assertOk();

        Storage::disk('local')->delete($attachment->file_path);

        $this->loginAs($owner)
            ->get(route('support.tickets.attachments.download', [$ticket, $attachment]))
            ->assertNotFound();
    }

    public function test_dangerous_and_oversized_files_are_rejected(): void
    {
        $student = $this->makeUser('student');

        $this->loginAs($student)
            ->post(route('support.tickets.store'), [
                'subject' => 'File nguy hiểm',
                'message' => 'Thử upload exe',
                'category' => SupportTicketCategory::Technical->value,
                'attachments' => [
                    UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream'),
                ],
            ])
            ->assertSessionHasErrors('attachments.0');

        $this->loginAs($student)
            ->post(route('support.tickets.store'), [
                'subject' => 'File quá lớn',
                'message' => 'Thử upload > 5MB',
                'category' => SupportTicketCategory::Technical->value,
                'attachments' => [
                    UploadedFile::fake()->create('huge.pdf', 5121, 'application/pdf'),
                ],
            ])
            ->assertSessionHasErrors('attachments.0');

        $this->assertSame(0, SupportTicket::query()->count());
    }

    public function test_instructor_can_access_support_ticket_list(): void
    {
        $instructor = $this->makeUser('instructor');

        $this->loginAs($instructor)
            ->get(route('support.tickets.index'))
            ->assertOk();
    }

    private function loginAs(User $user): self
    {
        return $this->actingAs($user)->withSession([
            'two_factor_passed_at' => now()->timestamp,
        ]);
    }

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'email_verified_at' => now(),
            'two_factor_enabled' => false,
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeTicket(User $user, array $overrides = []): SupportTicket
    {
        return SupportTicket::query()->create(array_merge([
            'user_id' => $user->id,
            'code' => 'TK-2026-'.strtoupper(substr(md5((string) microtime(true).$user->id), 0, 8)),
            'subject' => 'Ticket mẫu',
            'message' => 'Nội dung ban đầu của ticket.',
            'category' => SupportTicketCategory::Technical,
            'status' => SupportTicketStatus::Open,
            'priority' => SupportTicketPriority::Medium,
        ], $overrides));
    }
}
