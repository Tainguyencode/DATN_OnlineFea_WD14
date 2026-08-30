@php
    $routePrefix = $isAdmin ? 'admin.courses.versions' : 'instructor.courses.versions';
    $version = $detail['version'];
@endphp

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a class="text-sm font-bold text-indigo-700" href="{{ route($routePrefix.'.index', $course) }}">← Lịch sử phiên bản</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-950">{{ $detail['entity_label'] }} · V{{ $detail['version_number'] }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $detail['type_label'] }} · {{ $detail['is_current'] ? 'Đang xuất bản' : $detail['status_label'] }}</p>
            @if($detail['is_archived'])
                <span class="mt-2 inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">Đã lưu trữ</span>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route($routePrefix.'.compare', [$course, $detail['type'], $version->id]) }}" class="rounded-lg border border-indigo-200 px-4 py-2 text-sm font-bold text-indigo-700">So sánh với hiện tại</a>
            @if(!$isAdmin && $detail['rollback_eligible'])
                <a href="{{ route('instructor.courses.versions.rollback.confirm', [$course, $detail['type'], $version->id]) }}" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-bold text-white">Khôi phục từ phiên bản này</a>
            @endif
        </div>
    </div>

    <section class="grid gap-3 rounded-xl border border-slate-200 bg-white p-5 text-sm sm:grid-cols-2">
        <p><strong>Trạng thái:</strong> {{ $detail['is_current'] ? 'Đang xuất bản' : $detail['status_label'] }}</p>
        <p><strong>Nguồn:</strong> {{ $detail['origin'] }}@if($detail['source_version_number']) từ V{{ $detail['source_version_number'] }}@endif</p>
        <p><strong>Người tạo:</strong> {{ $detail['creator_name'] }}</p>
        <p><strong>Người duyệt:</strong> {{ $detail['publisher_name'] }}</p>
        <p><strong>Ngày tạo:</strong> {{ optional($detail['created_at'])->format('d/m/Y H:i') ?? 'Không xác định' }}</p>
        <p><strong>Ngày xuất bản:</strong> {{ optional($detail['published_at'])->format('d/m/Y H:i') ?? '—' }}</p>
    </section>

    @if($update = $detail['content_update'])
        <section class="rounded-xl border border-indigo-200 bg-indigo-50 p-5 text-sm text-indigo-950">
            <h2 class="font-bold">Nguồn phê duyệt ContentUpdate #{{ $update->id }}</h2>
            <p class="mt-2">Trạng thái: {{ $update->status }} · Gửi duyệt: {{ optional($update->submitted_at)->format('d/m/Y H:i') ?? '—' }} · Duyệt: {{ optional($update->reviewed_at)->format('d/m/Y H:i') ?? '—' }}</p>
            <p class="mt-1">Reviewer: {{ $update->reviewer?->name ?? 'Không xác định' }}</p>
            @if(data_get($update->metadata, 'rollback_reason'))<p class="mt-2"><strong>Lý do khôi phục:</strong> {{ data_get($update->metadata, 'rollback_reason') }}</p>@endif
            @if($update->rejection_reason)<p class="mt-2 text-rose-800"><strong>Lý do từ chối:</strong> {{ $update->rejection_reason }}</p>@endif
        </section>
    @endif

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <h2 class="border-b border-slate-200 px-5 py-4 font-bold text-slate-950">Snapshot bất biến</h2>
        <dl class="divide-y divide-slate-100">
            @foreach($detail['fields'] as $field)
                <div class="grid gap-2 px-5 py-4 sm:grid-cols-3">
                    <dt class="text-sm font-bold text-slate-600">{{ $field['label'] }}</dt>
                    <dd class="whitespace-pre-line break-words text-sm text-slate-900 sm:col-span-2">{{ is_array($field['value']) ? json_encode($field['value'], JSON_UNESCAPED_UNICODE) : ($field['value'] ?? '—') }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
</div>
