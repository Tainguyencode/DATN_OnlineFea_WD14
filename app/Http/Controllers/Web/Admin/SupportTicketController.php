<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\SupportTicketCategory;
use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\AdminUpdateSupportTicketRequest;
use App\Http\Requests\Support\ReplySupportTicketRequest;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\User;
use App\Services\SupportTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupportTicketController extends Controller
{
    public function __construct(private readonly SupportTicketService $tickets) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', SupportTicket::class);

        $query = SupportTicket::query()
            ->with(['user:id,name,email', 'assignee:id,name'])
            ->latest();

        if ($request->filled('status') && in_array($request->string('status')->toString(), SupportTicketStatus::values(), true)) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('priority') && in_array($request->string('priority')->toString(), SupportTicketPriority::values(), true)) {
            $query->where('priority', $request->string('priority')->toString());
        }
        if ($request->filled('category') && in_array($request->string('category')->toString(), SupportTicketCategory::values(), true)) {
            $query->where('category', $request->string('category')->toString());
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->integer('assigned_to'));
        }
        if ($request->filled('q')) {
            $q = '%'.$request->string('q')->toString().'%';
            $query->where(function ($builder) use ($q) {
                $builder->where('code', 'like', $q)
                    ->orWhere('subject', 'like', $q)
                    ->orWhereHas('user', fn ($user) => $user->where('email', 'like', $q)->orWhere('name', 'like', $q));
            });
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $tickets = $query->paginate(15)->withQueryString();
        $admins = User::query()->where('role', 'admin')->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.support-tickets.index', [
            'tickets' => $tickets,
            'admins' => $admins,
            'statuses' => SupportTicketStatus::cases(),
            'priorities' => SupportTicketPriority::cases(),
            'categories' => SupportTicketCategory::cases(),
            'filters' => $request->only(['status', 'priority', 'category', 'assigned_to', 'q', 'from', 'to']),
        ]);
    }

    public function show(SupportTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'user:id,name,email,role',
            'assignee:id,name',
            'messages.user:id,name,role',
            'messages.attachments',
            'attachments' => fn ($q) => $q->whereNull('message_id'),
        ]);

        $admins = User::query()->where('role', 'admin')->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.support-tickets.show', [
            'ticket' => $ticket,
            'admins' => $admins,
            'statuses' => SupportTicketStatus::cases(),
            'priorities' => SupportTicketPriority::cases(),
        ]);
    }

    public function reply(ReplySupportTicketRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $files = $request->file('attachments', []);
        if (! is_array($files)) {
            $files = array_filter([$files]);
        }

        $this->tickets->reply(
            $request->user(),
            $ticket,
            (string) $request->validated('message'),
            array_values($files)
        );

        return back()->with('success', 'Đã gửi phản hồi tới người dùng.');
    }

    public function update(AdminUpdateSupportTicketRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['status'])) {
            $this->tickets->updateStatus(
                $request->user(),
                $ticket,
                SupportTicketStatus::from($data['status'])
            );
        }
        if (! empty($data['priority'])) {
            $this->tickets->updatePriority(
                $request->user(),
                $ticket,
                SupportTicketPriority::from($data['priority'])
            );
        }
        if (array_key_exists('assigned_to', $data)) {
            $this->tickets->assign(
                $request->user(),
                $ticket,
                $data['assigned_to'] !== null ? (int) $data['assigned_to'] : null
            );
        }

        return back()->with('success', 'Đã cập nhật ticket.');
    }

    public function downloadAttachment(SupportTicket $ticket, SupportTicketAttachment $attachment): BinaryFileResponse|Response
    {
        $this->authorize('downloadAttachment', [$ticket, $attachment]);

        abort_unless((int) $attachment->ticket_id === (int) $ticket->id, 404);

        $path = $attachment->absolutePath();
        if ($path === null) {
            abort(404, 'File đính kèm không còn tồn tại.');
        }

        return response()->download($path, $attachment->original_name, [
            'Content-Type' => $attachment->mime_type ?: 'application/octet-stream',
        ]);
    }
}
