<x-admin-layout title="Chi tiết hủy câu hỏi" page-title="Chi tiết hủy câu hỏi">
    @php
        $mapping = $invalidation->mapping;
        $version = $mapping?->quizVersion;
        $quiz = $version?->quiz;
        $pending = $invalidation->status === \App\Models\QuizVersionQuestionInvalidation::STATUS_PENDING;
    @endphp

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.quiz-invalidations.index') }}" class="text-sm font-bold text-rose-700 hover:underline">Quay lại danh sách</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Yêu cầu #{{ $invalidation->id }}</h1>
        </div>
        @if($invalidation->status === 'pending')
            <span class="status-badge status-pending">Đang chờ</span>
        @elseif($invalidation->status === 'active')
            <span class="status-badge status-active">Đã phê duyệt</span>
        @else
            <span class="status-badge status-danger">Đã từ chối</span>
        @endif
    </div>

    @if (session('success'))
        <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-900">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $quiz?->title ?? 'Quiz' }} · V{{ $version?->version ?? '—' }}</p>
            <h2 class="mt-3 text-lg font-extrabold text-slate-950">Câu {{ ($mapping?->sort_order ?? 0) + 1 }}</h2>
            <p class="mt-2 whitespace-pre-line text-base leading-7 text-slate-800">{{ $mapping?->questionVersion?->question }}</p>
            <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
                <p class="font-bold">Lý do từ giảng viên</p>
                <p class="mt-1 whitespace-pre-line leading-6">{{ $invalidation->reason }}</p>
            </div>
            @if ($invalidation->rejection_reason)
                <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">
                    <p class="font-bold">Lý do từ chối</p>
                    <p class="mt-1 whitespace-pre-line leading-6">{{ $invalidation->rejection_reason }}</p>
                </div>
            @endif
        </article>

        <aside class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-900">Phạm vi tác động</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Lượt đã hoàn thành</dt><dd class="font-bold text-slate-900">{{ $counts['completed'] }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Lượt đang làm</dt><dd class="font-bold text-slate-900">{{ $counts['in_progress'] }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Người gửi</dt><dd class="text-right font-semibold text-slate-900">{{ $invalidation->requestedBy?->name ?? '—' }}</dd></div>
                </dl>
                <p class="mt-4 text-xs leading-5 text-slate-500">Nội dung, thứ tự, đáp án và snapshot lượt làm không bị thay đổi. Regrade chỉ dùng các answer row bất biến.</p>
            </div>

            @if ($pending)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.quiz-invalidations.approve', $invalidation) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">Phê duyệt và regrade</button>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('admin.quiz-invalidations.reject', $invalidation) }}" class="mt-4 space-y-3">
                        @csrf
                        <label class="block">
                            <span class="mb-1 block text-xs font-bold text-slate-700">Lý do từ chối</span>
                            <textarea name="rejection_reason" rows="3" minlength="5" maxlength="5000" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-rose-500 focus-visible:ring-2 focus-visible:ring-rose-500/30"></textarea>
                        </label>
                        <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-rose-300 px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">Từ chối yêu cầu</button>
                    </form>
                </div>
            @endif
        </aside>
    </div>
</x-admin-layout>
