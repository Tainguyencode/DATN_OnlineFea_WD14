<x-admin-layout title="Chi tiết đánh giá" pageTitle="Chi tiết đánh giá #{{ $review->id }}" breadcrumb="Admin / Đánh giá / Chi tiết">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div><h2 class="text-xl font-extrabold text-slate-950 dark:text-white">{{ $review->course?->title }}</h2><p class="mt-1 text-sm text-slate-500">{{ $review->user?->name }} · {{ $review->user?->email }}</p></div>
                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $review->status->badgeClasses() }}">{{ $review->status->label() }}</span>
            </div>
            <p class="mt-5 font-bold text-amber-600">{{ $review->rating }}/5 sao</p>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $review->comment }}</p>
            @if($review->instructor_reply)<div class="mt-5 rounded-xl bg-indigo-50 p-4 dark:bg-indigo-500/10"><strong>Phản hồi giảng viên</strong><p class="mt-2 whitespace-pre-line text-sm">{{ $review->instructor_reply }}</p></div>@endif
            @if($review->moderation_note)<div class="mt-5 rounded-xl bg-rose-50 p-4 text-rose-900 dark:bg-rose-500/10 dark:text-rose-100"><strong>Ghi chú kiểm duyệt</strong><p class="mt-2 whitespace-pre-line text-sm">{{ $review->moderation_note }}</p></div>@endif
            <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2"><div><dt class="text-slate-500">Đã xác minh đăng ký</dt><dd class="font-bold">{{ $review->verified_purchase ? 'Có' : 'Không' }}</dd></div><div><dt class="text-slate-500">Hữu ích</dt><dd class="font-bold">{{ $review->helpful_count }}</dd></div><div><dt class="text-slate-500">Tạo lúc</dt><dd class="font-bold">{{ $review->created_at->format('d/m/Y H:i') }}</dd></div><div><dt class="text-slate-500">Kiểm duyệt bởi</dt><dd class="font-bold">{{ $review->moderator?->name ?? '—' }}</dd></div></dl>
        </article>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="font-extrabold text-slate-950 dark:text-white">Kiểm duyệt</h3>
                @if($review->status !== \App\Enums\ReviewStatus::Approved)<form method="POST" action="{{ route('admin.student-reviews.approve', $review) }}" class="mt-4">@csrf @method('PATCH')<button class="w-full cursor-pointer rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Duyệt đánh giá</button></form>@endif
                <form method="POST" action="{{ route('admin.student-reviews.reject', $review) }}" class="mt-4 space-y-2">@csrf @method('PATCH')<label class="text-sm font-bold" for="reject-note">Lý do từ chối</label><textarea id="reject-note" name="moderation_note" rows="3" minlength="5" maxlength="1000" required class="w-full rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"></textarea><button class="w-full cursor-pointer rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700">Từ chối</button></form>
                <form method="POST" action="{{ route('admin.student-reviews.hide', $review) }}" class="mt-4 space-y-2">@csrf @method('PATCH')<label class="text-sm font-bold" for="hide-note">Lý do ẩn</label><textarea id="hide-note" name="moderation_note" rows="3" minlength="5" maxlength="1000" required class="w-full rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"></textarea><button class="w-full cursor-pointer rounded-xl bg-slate-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-800">Ẩn đánh giá</button></form>
                @if($review->status === \App\Enums\ReviewStatus::Hidden)<form method="POST" action="{{ route('admin.student-reviews.restore', $review) }}" class="mt-4">@csrf @method('PATCH')<button class="w-full cursor-pointer rounded-xl border border-indigo-300 px-4 py-2.5 text-sm font-bold text-indigo-700 hover:bg-indigo-50 dark:text-indigo-300 dark:hover:bg-indigo-500/10">Khôi phục hiển thị</button></form>@endif
            </div>
            <form method="POST" action="{{ route('admin.student-reviews.destroy', $review) }}" onsubmit="return confirm('Xóa đánh giá này? Hành động không thể hoàn tác từ giao diện.')">@csrf @method('DELETE')<button class="w-full cursor-pointer rounded-xl border border-rose-300 px-4 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-500/10">Xóa đánh giá</button></form>
            <a href="{{ route('admin.student-reviews.index') }}" class="block text-center text-sm font-bold text-indigo-700 hover:underline dark:text-indigo-300">Quay lại danh sách</a>
        </aside>
    </div>
</x-admin-layout>
