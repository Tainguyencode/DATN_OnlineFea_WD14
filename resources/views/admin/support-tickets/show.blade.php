<x-admin-layout title="Chi tiết Ticket" page-title="Chi tiết Ticket hỗ trợ">
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="font-mono text-sm font-bold text-rose-600">{{ $ticket->code }}</p>
            <h2 class="text-2xl font-bold text-slate-900">{{ $ticket->subject }}</h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $ticket->user?->name }} ({{ $ticket->user?->email }}) · {{ $ticket->category?->label() }}
            </p>
        </div>
        <a href="{{ route('admin.support-tickets.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Danh sách</a>
    </div>

    <div class="mb-6 grid gap-4 lg:grid-cols-3">
        <form method="POST" action="{{ route('admin.support-tickets.update', $ticket) }}" class="rounded-2xl border border-slate-200 bg-white p-4 lg:col-span-1 space-y-3">
            @csrf
            @method('PATCH')
            <h3 class="text-sm font-bold text-slate-900">Quản lý Ticket</h3>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-500">Trạng thái</label>
                <select name="status" class="w-full rounded-lg border-slate-300 text-sm">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-500">Ưu tiên</label>
                <select name="priority" class="w-full rounded-lg border-slate-300 text-sm">
                    @foreach($priorities as $priority)
                        <option value="{{ $priority->value }}" @selected($ticket->priority === $priority)>{{ $priority->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-500">Người phụ trách</label>
                <select name="assigned_to" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">Chưa gán</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" @selected((int) $ticket->assigned_to === (int) $admin->id)>{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="w-full rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white">Cập nhật</button>
        </form>

        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-2xl border border-rose-100 bg-rose-50/40 p-5">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-bold text-slate-900">{{ $ticket->user?->name }} <span class="font-normal text-slate-500">(Nội dung ban đầu)</span></p>
                    <p class="text-xs text-slate-500">{{ $ticket->created_at?->format('d/m/Y H:i') }}</p>
                </div>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-800">{{ $ticket->message }}</p>
                @if($ticket->attachments->isNotEmpty())
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($ticket->attachments as $file)
                            <a href="{{ route('admin.support-tickets.attachments.download', [$ticket, $file]) }}" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-rose-700 underline">{{ $file->original_name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            @foreach($ticket->messages as $message)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 {{ $message->user?->role === 'admin' ? 'border-l-4 border-l-emerald-500' : '' }}">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-bold text-slate-900">
                            {{ $message->user?->name }}
                            @if($message->user?->role === 'admin')
                                <span class="ml-1 rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-700">Admin</span>
                            @endif
                        </p>
                        <p class="text-xs text-slate-500">{{ $message->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-800">{{ $message->message }}</p>
                    @if($message->attachments->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($message->attachments as $file)
                                <a href="{{ route('admin.support-tickets.attachments.download', [$ticket, $file]) }}" class="rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-semibold text-rose-700 underline">{{ $file->original_name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <form method="POST" action="{{ route('admin.support-tickets.reply', $ticket) }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-5">
                @csrf
                <h3 class="text-sm font-bold text-slate-900">Phản hồi người dùng</h3>
                <textarea name="message" rows="4" class="mt-3 w-full rounded-xl border-slate-300" required maxlength="5000">{{ old('message') }}</textarea>
                <input type="file" name="attachments[]" multiple class="mt-3 w-full text-sm">
                <button class="mt-4 rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white">Gửi phản hồi</button>
            </form>
        </div>
    </div>
</x-admin-layout>
