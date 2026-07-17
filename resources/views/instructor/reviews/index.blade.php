<x-instructor-layout title="Đánh giá học viên" pageTitle="Đánh giá học viên" breadcrumb="Giảng viên / Đánh giá">
    <div class="space-y-6">
        <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-2 xl:grid-cols-5">
            <select name="course_id" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả khóa học</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected($courseId === $course->id)>{{ $course->title }}</option>@endforeach</select>
            <select name="rating" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả số sao</option>@for($star=5;$star>=1;$star--)<option value="{{ $star }}" @selected($rating === $star)>{{ $star }} sao</option>@endfor</select>
            <select name="status" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả trạng thái</option>@foreach(\App\Enums\ReviewStatus::cases() as $option)<option value="{{ $option->value }}" @selected($status === $option->value)>{{ $option->label() }}</option>@endforeach</select>
            <select name="reply" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả phản hồi</option><option value="replied" @selected($replyState==='replied')>Đã phản hồi</option><option value="unreplied" @selected($replyState==='unreplied')>Chưa phản hồi</option></select>
            <button class="cursor-pointer rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-emerald-700">Lọc đánh giá</button>
        </form>

        @forelse($reviews as $review)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-300">{{ $review->course->title }}</p>
                        <h3 class="mt-1 font-extrabold text-slate-950 dark:text-white">{{ $review->user?->name ?? 'Học viên' }} · {{ $review->rating }}/5 sao</h3>
                        <time class="text-xs text-slate-500">{{ $review->created_at->format('d/m/Y H:i') }}</time>
                    </div>
                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ $review->status->badgeClasses() }}">{{ $review->status->label() }}</span>
                </div>
                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $review->comment }}</p>
                <form method="POST" action="{{ route($review->instructor_reply ? 'instructor.reviews.reply.update' : 'instructor.reviews.reply', [$review->course, $review]) }}" class="mt-5 rounded-xl bg-slate-50 p-4 dark:bg-slate-950">
                    @csrf
                    @if($review->instructor_reply) @method('PUT') @endif
                    <label for="reply-{{ $review->id }}" class="text-sm font-bold text-slate-800 dark:text-slate-100">Phản hồi chính thức</label>
                    <textarea id="reply-{{ $review->id }}" name="instructor_reply" rows="3" minlength="2" maxlength="1500" required class="mt-2 w-full rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-900">{{ old('instructor_reply', $review->instructor_reply) }}</textarea>
                    @error('instructor_reply')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button class="cursor-pointer rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-emerald-700">{{ $review->instructor_reply ? 'Cập nhật phản hồi' : 'Gửi phản hồi' }}</button>
                    </div>
                </form>
                @if($review->instructor_reply)
                    <form method="POST" action="{{ route('instructor.reviews.reply.destroy', [$review->course, $review]) }}" class="mt-2" onsubmit="return confirm('Xóa phản hồi này?')">@csrf @method('DELETE')<button class="cursor-pointer text-sm font-bold text-rose-600 hover:underline">Xóa phản hồi</button></form>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-900">Chưa có đánh giá phù hợp với bộ lọc.</div>
        @endforelse
        {{ $reviews->links() }}
    </div>
</x-instructor-layout>
