<x-admin-layout title="Hủy câu hỏi Quiz" page-title="Hủy câu hỏi Quiz">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Yêu cầu hủy câu hỏi Quiz</h1>
            <p class="mt-1 text-sm text-slate-500">Admin duyệt việc loại một câu hỏi khỏi đúng phiên bản đã xuất bản và kích hoạt regrade an toàn.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <label for="status" class="text-sm font-semibold text-slate-700">Trạng thái</label>
            <select id="status" name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-500 focus-visible:ring-2 focus-visible:ring-rose-500/30">
                <option value="pending" @selected($status === 'pending')>Đang chờ</option>
                <option value="active" @selected($status === 'active')>Đã phê duyệt</option>
                <option value="rejected" @selected($status === 'rejected')>Đã từ chối</option>
            </select>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-sm">
                <thead class="border-b border-slate-200 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Câu hỏi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Quiz / phiên bản</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Người gửi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Thời gian</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Tác vụ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invalidations as $invalidation)
                        @php
                            $mapping = $invalidation->mapping;
                            $version = $mapping?->quizVersion;
                            $quiz = $version?->quiz;
                        @endphp
                        <tr class="align-top hover:bg-slate-50">
                            <td class="max-w-[320px] px-4 py-4">
                                <p class="font-bold text-slate-900">#{{ $invalidation->id }} · Câu {{ $mapping?->sort_order + 1 }}</p>
                                <p class="mt-1 line-clamp-2 text-slate-600">{{ $mapping?->questionVersion?->question }}</p>
                            </td>
                            <td class="px-4 py-4 text-slate-600">
                                <p class="font-semibold text-slate-900">{{ $quiz?->title ?? '—' }}</p>
                                <p class="mt-1 text-xs">V{{ $version?->version ?? '—' }} · {{ $quiz?->lesson?->course?->title ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-4 text-slate-600">{{ $invalidation->requestedBy?->name ?? '—' }}</td>
                            <td class="px-4 py-4 text-xs text-slate-500">{{ $invalidation->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.quiz-invalidations.show', $invalidation) }}" class="inline-flex min-h-9 items-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">Xem chi tiết</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-slate-500">Không có yêu cầu ở trạng thái này.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($invalidations->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $invalidations->links() }}</div>
        @endif
    </div>
</x-admin-layout>
