@php
    $routePrefix = $isAdmin ? 'admin.courses.versions' : 'instructor.courses.versions';
    $number = fn($version) => $type === 'quiz' ? $version->version : $version->version_number;
@endphp

<div class="mx-auto max-w-5xl space-y-6">
    <div>
        <a class="text-sm font-bold text-indigo-700" href="{{ route($routePrefix.'.show', [$course, $type, $from->id]) }}">← Chi tiết phiên bản</a>
        <h1 class="mt-2 text-2xl font-bold text-slate-950">So sánh V{{ $number($from) }} → V{{ $number($to) }}</h1>
        <p class="mt-1 text-sm text-slate-600">Chỉ hiển thị trường thay đổi; dữ liệu lấy trực tiếp từ hai snapshot.</p>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4">
        <label class="text-sm font-bold text-slate-700">So sánh với
            <select name="to" class="mt-1 rounded-lg border border-slate-300 px-3 py-2">
                @foreach($siblings as $candidate)
                    <option value="{{ $candidate->id }}" @selected($candidate->id === $to->id)>V{{ $number($candidate) }} · {{ $candidate->status }}</option>
                @endforeach
            </select>
        </label>
        <button class="rounded-lg bg-indigo-700 px-4 py-2 text-sm font-bold text-white">So sánh</button>
    </form>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        @forelse($fields as $field)
            <div class="border-b border-slate-100 p-5 last:border-0">
                <h2 class="text-sm font-bold text-slate-800">{{ $field['label'] }}</h2>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div class="rounded-lg bg-slate-50 p-3"><p class="text-xs font-bold text-slate-500">V{{ $number($from) }}</p><p class="mt-1 whitespace-pre-line break-words text-sm">{{ $field['old'] ?? '—' }}</p></div>
                    <div class="rounded-lg bg-indigo-50 p-3"><p class="text-xs font-bold text-indigo-700">V{{ $number($to) }}</p><p class="mt-1 whitespace-pre-line break-words text-sm">{{ $field['new'] ?? '—' }}</p></div>
                </div>
            </div>
        @empty
            <p class="p-6 text-center text-slate-500">Hai phiên bản không có khác biệt trong hợp đồng hiển thị.</p>
        @endforelse
    </section>
</div>
