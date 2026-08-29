<x-admin-layout title="Xem thay đổi nội dung">
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('admin.content-updates.index') }}" class="text-sm font-semibold text-indigo-700 hover:underline">← Quay lại danh sách cập nhật</a>
                <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $diff['label'] }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $diff['entity_label'] }} · {{ $diff['action_label'] }} · {{ $contentUpdate->course?->title }}</p>
            </div>
            <span class="rounded-full px-3 py-1 text-sm font-bold {{ $contentUpdate->isPending() ? 'bg-amber-100 text-amber-800' : ($contentUpdate->isApproved() ? 'bg-emerald-100 text-emerald-800' : ($contentUpdate->isRejected() ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700')) }}">
                {{ ['draft' => 'Nháp', 'pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Bị từ chối'][$contentUpdate->status] ?? $contentUpdate->status }}
            </span>
        </div>

        @foreach($diff['warnings'] as $warning)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900">{{ $warning }}</div>
        @endforeach

        @if($contentUpdate->isRejected() && filled($contentUpdate->rejection_reason))
            <section class="rounded-xl border border-rose-200 bg-rose-50 p-5">
                <h2 class="font-bold text-rose-950">Lý do từ chối</h2>
                <p class="mt-2 whitespace-pre-line text-sm text-rose-900">{{ $contentUpdate->rejection_reason }}</p>
            </section>
        @endif

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-bold text-slate-900">{{ $contentUpdate->action === 'delete' ? 'Nội dung hiện tại sẽ bị xóa' : ($contentUpdate->action === 'create' ? 'Nội dung đề xuất thêm mới' : 'So sánh nội dung') }}</h2></div>
            <div class="divide-y divide-slate-100">
                @forelse($diff['fields'] as $field)
                    <div class="p-5">
                        <h3 class="text-sm font-bold text-slate-800">{{ $field['label'] }}</h3>
                        @if($contentUpdate->action !== 'create')
                            <div class="mt-3 rounded-lg bg-slate-50 p-3"><p class="text-xs font-bold uppercase text-slate-500">Hiện tại</p><p class="mt-1 whitespace-pre-line text-sm text-slate-800">{{ is_array($field['old']) ? json_encode($field['old'], JSON_UNESCAPED_UNICODE) : ($field['old'] ?? '—') }}</p></div>
                        @endif
                        @if($contentUpdate->action !== 'delete')
                            <div class="mt-3 rounded-lg border border-indigo-100 bg-indigo-50 p-3"><p class="text-xs font-bold uppercase text-indigo-700">Đề xuất</p><p class="mt-1 whitespace-pre-line text-sm text-indigo-950">{{ is_array($field['new']) ? json_encode($field['new'], JSON_UNESCAPED_UNICODE) : ($field['new'] ?? '—') }}</p></div>
                        @endif
                    </div>
                @empty
                    <div class="p-5 text-sm text-slate-500">Không có trường dữ liệu có thể so sánh.</div>
                @endforelse
            </div>
        </section>

        @if(!empty($diff['media']['current_video']) || !empty($diff['media']['proposed_video']))
            <section class="grid gap-4 md:grid-cols-2">
                @foreach(['current_video' => 'Video hiện tại', 'proposed_video' => 'Video đề xuất'] as $key => $heading)
                    @if($media = data_get($diff, 'media.'.$key))
                        <div class="rounded-xl border border-slate-200 bg-white p-5"><h2 class="font-bold text-slate-900">{{ $heading }}</h2><p class="mt-2 text-sm text-slate-700">{{ $media['filename'] }}</p><p class="mt-1 text-xs font-semibold {{ $media['ready'] ? 'text-emerald-700' : 'text-amber-700' }}">{{ $media['ready'] ? 'Sẵn sàng phát HLS' : 'Chưa sẵn sàng HLS' }}</p></div>
                    @endif
                @endforeach
            </section>
        @endif

        @if(isset($diff['quiz_questions']))
            <section class="rounded-xl border border-slate-200 bg-white p-5"><h2 class="font-bold text-slate-900">Thay đổi câu hỏi Quiz</h2><p class="mt-2 text-sm text-slate-700">Hiện tại: {{ $diff['quiz_questions']['current_count'] }} câu · Đề xuất: {{ $diff['quiz_questions']['proposed_count'] }} câu</p><p class="mt-1 text-sm text-slate-700">Thêm: {{ count($diff['quiz_questions']['added']) }} · Xóa: {{ count($diff['quiz_questions']['removed']) }} · Chỉnh sửa: {{ count($diff['quiz_questions']['changed']) }}</p></section>
        @endif

        @if($contentUpdate->isPending())
            <section class="flex flex-wrap gap-3 rounded-xl border border-slate-200 bg-white p-5">
                <form method="POST" action="{{ route('admin.content-updates.approve', $contentUpdate) }}">@csrf <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white">Duyệt</button></form>
                <form method="POST" action="{{ route('admin.content-updates.reject', $contentUpdate) }}" class="flex flex-1 gap-2">@csrf <input required name="rejection_reason" minlength="5" maxlength="1000" placeholder="Lý do từ chối" class="min-w-56 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm"><button class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-bold text-white">Từ chối</button></form>
            </section>
        @endif

        <details class="rounded-xl border border-slate-200 bg-white p-5"><summary class="cursor-pointer text-sm font-bold text-slate-700">Thông tin kỹ thuật</summary><pre class="mt-4 overflow-auto rounded-lg bg-slate-950 p-4 text-xs text-slate-100">{{ json_encode($contentUpdate->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></details>
    </div>
</x-admin-layout>
