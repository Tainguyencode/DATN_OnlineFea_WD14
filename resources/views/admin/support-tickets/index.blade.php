<x-admin-layout title="Ticket hỗ trợ" page-title="Quản lý Ticket hỗ trợ">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Ticket hỗ trợ</h2>
        <p class="mt-1 text-sm text-slate-500">Tiếp nhận, phản hồi và cập nhật trạng thái yêu cầu hỗ trợ.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-4">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Mã, tiêu đề, email..." class="rounded-lg border-slate-300 text-sm md:col-span-2">
        <select name="status" class="rounded-lg border-slate-300 text-sm">
            <option value="">Trạng thái</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <select name="priority" class="rounded-lg border-slate-300 text-sm">
            <option value="">Ưu tiên</option>
            @foreach($priorities as $priority)
                <option value="{{ $priority->value }}" @selected(($filters['priority'] ?? '') === $priority->value)>{{ $priority->label() }}</option>
            @endforeach
        </select>
        <select name="category" class="rounded-lg border-slate-300 text-sm">
            <option value="">Loại vấn đề</option>
            @foreach($categories as $category)
                <option value="{{ $category->value }}" @selected(($filters['category'] ?? '') === $category->value)>{{ $category->label() }}</option>
            @endforeach
        </select>
        <select name="assigned_to" class="rounded-lg border-slate-300 text-sm">
            <option value="">Người phụ trách</option>
            @foreach($admins as $admin)
                <option value="{{ $admin->id }}" @selected((string) ($filters['assigned_to'] ?? '') === (string) $admin->id)>{{ $admin->name }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded-lg border-slate-300 text-sm">
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded-lg border-slate-300 text-sm">
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Lọc</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Mã</th>
                    <th class="px-4 py-3">Tiêu đề</th>
                    <th class="px-4 py-3">Người gửi</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Ưu tiên</th>
                    <th class="px-4 py-3">Phụ trách</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-rose-600">{{ $ticket->code }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $ticket->subject }}</div>
                            <div class="text-xs text-slate-500">{{ $ticket->category?->label() }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ $ticket->user?->name }}</div>
                            <div class="text-xs text-slate-500">{{ $ticket->user?->email }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $ticket->status->label() }}</td>
                        <td class="px-4 py-3">{{ $ticket->priority->label() }}</td>
                        <td class="px-4 py-3">{{ $ticket->assignee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.support-tickets.show', $ticket) }}" class="font-semibold text-rose-600 hover:underline">Xử lý</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-500">Chưa có ticket nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $tickets->links() }}</div>
</x-admin-layout>
