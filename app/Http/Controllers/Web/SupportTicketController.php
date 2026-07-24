<?php

namespace App\Http\Controllers\Web;

use App\Enums\SupportTicketCategory;
use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\ReplySupportTicketRequest;
use App\Http\Requests\Support\StoreSupportTicketRequest;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Services\SupportTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function __construct(private readonly SupportTicketService $tickets) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', SupportTicket::class);

        $query = SupportTicket::query()
            ->where('user_id', $request->user()->id)
            ->with(['assignee:id,name'])
            ->latest();

        if ($request->filled('status') && in_array($request->string('status')->toString(), SupportTicketStatus::values(), true)) {
            $query->where('status', $request->string('status')->toString());
        }

        $tickets = $query->paginate(10)->withQueryString();

        return view('support.tickets.index', [
            'tickets' => $tickets,
            'statuses' => SupportTicketStatus::cases(),
            'filters' => [
                'status' => $request->string('status')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', SupportTicket::class);

        return view('support.tickets.create', [
            'categories' => SupportTicketCategory::cases(),
            'priorities' => SupportTicketPriority::cases(),
        ]);
    }

    public function store(StoreSupportTicketRequest $request): RedirectResponse
    {
        $files = $request->file('attachments', []);
        if (! is_array($files)) {
            $files = array_filter([$files]);
        }

        $ticket = $this->tickets->create(
            $request->user(),
            $request->validated(),
            array_values($files)
        );

        return redirect()
            ->route('support.tickets.show', $ticket)
            ->with('success', 'Đã gửi ticket hỗ trợ '.$ticket->code.'.');
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

        return view('support.tickets.show', compact('ticket'));
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

        return back()->with('success', 'Đã gửi phản hồi.');
    }

    public function close(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('close', $ticket);
        $this->tickets->closeByOwner($request->user(), $ticket);

        return back()->with('success', 'Đã đóng ticket.');
    }

    public function reopen(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('reopen', $ticket);
        $this->tickets->reopenByOwner($request->user(), $ticket);

        return back()->with('success', 'Đã mở lại ticket.');
    }

    public function downloadAttachment(SupportTicket $ticket, SupportTicketAttachment $attachment): BinaryFileResponse|\Illuminate\Http\Response
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
