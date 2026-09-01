<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">Duyệt ngành giảng dạy</h1>
                <p class="text-sm text-slate-500">Chỉ duyệt từng ngành; không thay đổi trạng thái hồ sơ instructor tổng thể.</p>
            </div>
            <div class="flex gap-2 text-xs font-bold">
                @foreach(['pending' => 'Chờ duyệt', 'rejected' => 'Bị từ chối', 'approved' => 'Đã duyệt'] as $key => $label)
                    <a href="{{ route('admin.instructors.teaching-fields.index', ['status' => $key]) }}" class="rounded-xl px-3 py-2 {{ $status === $key ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        @forelse($fields as $field)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-wrap justify-between gap-3">
                    <div>
                        <h2 class="font-black text-slate-900 dark:text-white">{{ $field->category?->full_name ?? $field->category?->name }}</h2>
                        <p class="text-sm text-slate-500">{{ $field->profile?->user?->name }} · {{ $field->profile?->user?->email }}</p>
                        @if($field->replacedField)
                            <p class="mt-1 text-xs text-amber-700">Yêu cầu thay thế từ: {{ $field->replacedField->category?->name }}</p>
                        @endif
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $field->approval_status === 'pending' ? 'bg-amber-100 text-amber-800' : ($field->approval_status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800') }}">{{ strtoupper($field->approval_status) }}</span>
                </div>
                <div class="mt-4 text-sm text-slate-600 dark:text-slate-300">{{ $field->requirement_data['summary']['required_count'] - $field->requirement_data['summary']['missing_count'] }}/{{ $field->requirement_data['summary']['required_count'] }} tài liệu bắt buộc</div>
                @if($field->approval_status === 'pending')
                    <div class="mt-4 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.instructors.teaching-fields.approve', $field) }}">@csrf <button class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white">Phê duyệt</button></form>
                        <form method="POST" action="{{ route('admin.instructors.teaching-fields.reject', $field) }}" class="flex gap-2">@csrf <input required minlength="10" name="rejection_reason" placeholder="Lý do từ chối" class="rounded-xl border px-3 py-2 text-xs"><button class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white">Từ chối</button></form>
                    </div>
                @elseif($field->rejection_reason)
                    <p class="mt-3 text-sm text-rose-600">Lý do: {{ $field->rejection_reason }}</p>
                @endif
            </article>
        @empty
            <div class="rounded-2xl bg-white p-8 text-center text-sm text-slate-500 dark:bg-slate-900">Không có yêu cầu ngành phù hợp.</div>
        @endforelse
        {{ $fields->links() }}
    </div>
</x-admin-layout>
