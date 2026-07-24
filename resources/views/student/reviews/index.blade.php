<x-student-layout title="Đánh giá của tôi" pageTitle="Đánh giá của tôi" breadcrumb="Học viên / Đánh giá">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">Lịch sử đánh giá khóa học</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Theo dõi trạng thái kiểm duyệt và phản hồi từ giảng viên.</p>
            </div>
            <form method="GET" class="flex gap-2">
                <label for="status" class="sr-only">Trạng thái</label>
                <select id="status" name="status" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    @foreach(\App\Enums\ReviewStatus::cases() as $option)
                        <option value="{{ $option->value }}" @selected($status === $option->value)>{{ $option->label() }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @forelse($reviews as $review)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <a href="{{ route('courses.show', $review->course->slug).'#reviews' }}" class="text-lg font-extrabold text-slate-950 transition-colors hover:text-indigo-700 dark:text-white dark:hover:text-indigo-300">{{ $review->course->title }}</a>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Giảng viên {{ $review->course->instructor?->name }}</p>
                    </div>
                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ $review->status->badgeClasses() }}">{{ $review->status->label() }}</span>
                </div>
                <div class="mt-4 flex items-center gap-2 text-amber-500" aria-label="{{ $review->rating }} trên 5 sao">
                    @for($star = 1; $star <= 5; $star++)
                        <svg class="h-4 w-4 {{ $star <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034 1.07 3.292c.3.921-.755 1.688-1.539 1.118L10 14.44l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292-2.8-2.034c-.783-.57-.38-1.81.588-1.81H7.98l1.07-3.292z"/></svg>
                    @endfor
                    <time class="ml-2 text-xs text-slate-500">{{ $review->created_at->format('d/m/Y H:i') }}</time>
                </div>
                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $review->comment }}</p>
                @if($review->moderation_note && $review->status !== \App\Enums\ReviewStatus::Approved)
                    <p class="mt-4 rounded-xl bg-rose-50 p-3 text-sm text-rose-800 dark:bg-rose-500/10 dark:text-rose-200"><strong>Lý do kiểm duyệt:</strong> {{ $review->moderation_note }}</p>
                @endif
                @php
                    $visibleReply = $review->replies->where('is_hidden', false)->first();
                @endphp
                @if($visibleReply)
                    <div class="mt-4 rounded-xl border-l-4 border-indigo-500 bg-indigo-50 p-4 dark:bg-indigo-500/10">
                        <strong class="text-sm text-slate-950 dark:text-white">Phản hồi từ giảng viên</strong>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-700 dark:text-slate-200">{{ $visibleReply->comment }}</p>
                    </div>
                @endif
                <a href="{{ route('courses.show', $review->course->slug).'#reviews' }}" class="mt-4 inline-flex cursor-pointer rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">Xem hoặc chỉnh sửa</a>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-700 dark:bg-slate-900">
                <p class="font-bold text-slate-700 dark:text-slate-200">Bạn chưa có đánh giá nào.</p>
                <p class="mt-1 text-sm text-slate-500">Bắt đầu học một khóa đã đăng ký để chia sẻ trải nghiệm.</p>
            </div>
        @endforelse

        {{ $reviews->links() }}
    </div>
</x-student-layout>
