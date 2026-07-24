@php
    $layout = auth()->user()?->role === 'instructor' ? 'instructor-layout' : 'student-layout';
@endphp

<x-dynamic-component :component="$layout" title="Chi tiết Ticket" page-title="Chi tiết Ticket">
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="font-mono text-sm font-bold text-indigo-600">{{ $ticket->code }}</p>
            <h2 class="text-2xl font-bold text-slate-900">{{ $ticket->subject }}</h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $ticket->category?->label() }} · {{ $ticket->status->label() }} · Ưu tiên {{ $ticket->priority->label() }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('close', $ticket)
                <form method="POST" action="{{ route('support.tickets.close', $ticket) }}">
                    @csrf
                    <button class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Đóng ticket</button>
                </form>
            @endcan
            @can('reopen', $ticket)
                <form method="POST" action="{{ route('support.tickets.reopen', $ticket) }}">
                    @csrf
                    <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Mở lại</button>
                </form>
            @endcan
            <a href="{{ route('support.tickets.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Danh sách</a>
        </div>
    </div>

    <div class="space-y-4">
        {{-- Original ticket content --}}
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-bold text-slate-900">{{ $ticket->user?->name }} <span class="font-normal text-slate-500">(Nội dung ban đầu)</span></p>
                <p class="text-xs text-slate-500">{{ $ticket->created_at?->format('d/m/Y H:i') }}</p>
            </div>
            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-800">{{ $ticket->message }}</p>
            @if($ticket->attachments->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($ticket->attachments as $file)
                            <a href="{{ route('support.tickets.attachments.download', [$ticket, $file]) }}" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 underline">{{ $file->original_name }}</a>
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
                            <a href="{{ route('support.tickets.attachments.download', [$ticket, $file]) }}" class="rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 underline">{{ $file->original_name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @can('reply', $ticket)
        <form method="POST" action="{{ route('support.tickets.reply', $ticket) }}" enctype="multipart/form-data" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
            @csrf
            <h3 class="text-sm font-bold text-slate-900">Phản hồi</h3>
            <textarea name="message" rows="4" class="mt-3 w-full rounded-xl border-slate-300" required maxlength="5000" placeholder="Nhập nội dung phản hồi...">{{ old('message') }}</textarea>
            @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <input type="file" name="attachments[]" multiple class="mt-3 w-full text-sm">
            <button class="mt-4 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Gửi phản hồi</button>
        </form>
    @endcan
</x-dynamic-component>
