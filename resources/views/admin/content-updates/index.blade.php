<x-admin-layout title="Quản lý Cập nhật Nội dung">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Quản lý Cập nhật Nội dung</h1>
            <p class="mt-1 text-sm text-slate-500">Danh sách các cập nhật thay đổi nội dung (Khóa học, Chương học, Bài học, Quiz) từ Giảng viên</p>
        </div>
        <form method="GET" class="flex gap-2">
            <select name="type" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="">-- Tất cả loại nội dung --</option>
                @foreach($typeOptions as $value => $label)
                    <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" onchange="this.form.submit()">
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">ID</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Khóa học</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Loại / Hành động</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Người gửi</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Thời gian</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Trạng thái</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($updates as $update)
                    @php($candidate = $update->type === \App\Models\ContentUpdate::TYPE_QUIZ
                        ? $quizCandidates->get((int) data_get($update->payload, 'quiz_version_id'))
                        : null)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $update->id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $update->course?->title ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="font-semibold uppercase text-xs text-indigo-600">{{ $update->type }}</span>
                            <span class="text-xs text-slate-500">({{ $update->action }})</span>
                            @if ($update->type === \App\Models\ContentUpdate::TYPE_QUIZ)
                                @if ($candidate)
                                    <div class="mt-1 text-xs text-slate-600">
                                        {{ $candidate->title }} · V{{ $candidate->version }} · {{ $candidate->question_mappings_count }} câu hỏi
                                        @if ($candidate->quiz?->currentPublishedVersion)
                                            · đang áp dụng V{{ $candidate->quiz->currentPublishedVersion->version }}
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $update->creator?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $update->submitted_at?->format('d/m/Y H:i') ?? $update->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            @if($update->status === 'pending')
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800 border border-amber-300">
                                    Pending Update
                                </span>
                            @elseif($update->status === 'draft')
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700 border border-slate-300">
                                    Draft
                                </span>
                            @elseif($update->status === 'approved')
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 border border-emerald-300">
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800 border border-rose-300">
                                    Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ openDetail: false, openReject: false }">
                                <button type="button" @click="openDetail = true" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-200">
                                    Chi tiết
                                </button>

                                @if($update->status === 'pending')
                                    <form method="POST" action="{{ route('admin.content-updates.approve', $update) }}">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn duyệt bản cập nhật này?')" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700">
                                            Duyệt
                                        </button>
                                    </form>
                                    <button type="button" @click="openReject = true" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-700">
                                        Từ chối
                                    </button>

                                    <!-- Reject Modal -->
                                    <div x-show="openReject" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" x-cloak>
                                        <div class="w-full max-w-md rounded-xl bg-white p-6 text-left shadow-xl">
                                            <h3 class="text-lg font-bold text-slate-900">Từ chối Cập nhật #{{ $update->id }}</h3>
                                            <form method="POST" action="{{ route('admin.content-updates.reject', $update) }}" class="mt-4 space-y-4">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700">Lý do từ chối</label>
                                                    <textarea name="rejection_reason" rows="3" required class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 text-sm" placeholder="Nhập lý do từ chối..."></textarea>
                                                </div>
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="openReject = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700">Hủy</button>
                                                    <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700">Xác nhận Từ chối</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif

                                <!-- Detail Modal -->
                                <div x-show="openDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 text-left" x-cloak>
                                    <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl space-y-4">
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                            <h3 class="text-lg font-bold text-slate-900">Chi tiết Cập nhật #{{ $update->id }}</h3>
                                            <span class="text-xs uppercase font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">{{ $update->type }} - {{ $update->action }}</span>
                                        </div>
                                        <div>
                                            @if ($update->type === \App\Models\ContentUpdate::TYPE_QUIZ && isset($candidate) && $candidate)
                                                <div class="mb-4 rounded-lg border border-sky-200 bg-sky-50 p-3 text-sm text-sky-950">
                                                    <p class="font-bold">{{ $candidate->title }} — Quiz V{{ $candidate->version }}</p>
                                                    <p class="mt-1">{{ $candidate->question_mappings_count }} câu hỏi. Phiên bản đang áp dụng: V{{ $candidate->quiz?->currentPublishedVersion?->version ?? '—' }}.</p>
                                                    <p class="mt-1 text-xs font-semibold">Phê duyệt ở Phase 2B0.7 không kích hoạt V2 cho học viên.</p>
                                                </div>
                                            @endif
                                            <h4 class="text-xs font-semibold text-slate-500 uppercase">Dữ liệu cập nhật mới (Payload):</h4>
                                            <pre class="mt-2 max-h-60 overflow-y-auto rounded-lg bg-slate-900 p-3 text-xs font-mono text-emerald-400 whitespace-pre-wrap">{{ json_encode($update->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="button" @click="openDetail = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">Không có bản cập nhật nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($updates->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $updates->links() }}</div>
        @endif
    </div>
</x-admin-layout>
