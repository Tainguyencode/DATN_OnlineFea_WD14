@php
    $layout = auth()->user()?->role === 'instructor' ? 'instructor-layout' : 'student-layout';
@endphp

<x-dynamic-component :component="$layout" title="Ticket hỗ trợ" page-title="Ticket hỗ trợ của tôi">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Ticket hỗ trợ của tôi</h2>
            <p class="mt-1 text-sm text-slate-500">Theo dõi và gửi yêu cầu trợ giúp tới Ban quản trị.</p>
        </div>
        <a href="{{ route('support.tickets.create') }}" class="inline-flex h-10 items-center rounded-xl bg-indigo-600 px-4 text-sm font-bold text-white hover:bg-indigo-700">
            Gửi Ticket mới
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-500">Trạng thái</label>
            <select name="status" class="rounded-lg border-slate-300 text-sm">
                <option value="">Tất cả</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <button class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white">Lọc</button>
    </form>

    <div class="space-y-3">
        @forelse($tickets as $ticket)
            <a href="{{ route('support.tickets.show', $ticket) }}" class="block rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-indigo-300 hover:shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-mono font-semibold text-indigo-600">{{ $ticket->code }}</p>
                        <h3 class="mt-1 text-lg font-bold text-slate-900">{{ $ticket->subject }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $ticket->category?->label() }} · {{ $ticket->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $ticket->status->label() }}</span>
                        <p class="mt-2 text-xs text-slate-500">Ưu tiên: {{ $ticket->priority->label() }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
                Bạn chưa có ticket nào. Hãy gửi ticket khi cần hỗ trợ.
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $tickets->links() }}</div>
</x-dynamic-component>
