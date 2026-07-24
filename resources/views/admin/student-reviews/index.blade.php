<x-admin-layout title="Kiểm duyệt đánh giá" pageTitle="Kiểm duyệt đánh giá" breadcrumb="Admin / Đánh giá học viên">
    <div class="space-y-6">
        <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:grid-cols-2 xl:grid-cols-4">
            <input name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="Học viên, khóa học, nội dung..." class="rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
            <select name="course_id" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả khóa học</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected(($filters['course_id'] ?? '') == $course->id)>{{ $course->title }}</option>@endforeach</select>
            <select name="instructor_id" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả giảng viên</option>@foreach($instructors as $instructor)<option value="{{ $instructor->id }}" @selected(($filters['instructor_id'] ?? '') == $instructor->id)>{{ $instructor->name }}</option>@endforeach</select>
            <select name="status" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả trạng thái</option>@foreach($statusOptions as $value=>$label)<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>@endforeach</select>
            <select name="rating" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả số sao</option>@for($star=5;$star>=1;$star--)<option value="{{ $star }}" @selected(($filters['rating'] ?? '') == $star)>{{ $star }} sao</option>@endfor</select>
            <select name="reply" class="cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><option value="">Tất cả phản hồi</option><option value="replied" @selected(($filters['reply'] ?? '')==='replied')>Đã phản hồi</option><option value="unreplied" @selected(($filters['reply'] ?? '')==='unreplied')>Chưa phản hồi</option></select>
            <div class="grid grid-cols-2 gap-2"><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950"></div>
            <div class="flex gap-2"><button class="flex-1 cursor-pointer rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-rose-700">Áp dụng</button><a href="{{ route('admin.student-reviews.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Xóa lọc</a></div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm dark:divide-slate-800">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-950 dark:text-slate-400"><tr><th class="px-4 py-3">ID / Học viên</th><th class="px-4 py-3">Khóa học</th><th class="px-4 py-3">Đánh giá</th><th class="px-4 py-3">Trạng thái</th><th class="px-4 py-3 text-right">Hành động</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($reviews as $review)
                            <tr class="align-top">
                                <td class="px-4 py-4"><strong class="text-slate-950 dark:text-white">#{{ $review->id }} · {{ $review->user?->name }}</strong><p class="mt-1 text-xs text-slate-500">{{ $review->user?->email }}<br>{{ $review->created_at->format('d/m/Y H:i') }}</p></td>
                                <td class="px-4 py-4"><p class="max-w-xs font-bold text-slate-800 dark:text-slate-100">{{ $review->course?->title }}</p><p class="mt-1 text-xs text-slate-500">GV: {{ $review->course?->instructor?->name }}</p></td>
                                <td class="px-4 py-4"><span class="font-bold text-amber-600">{{ $review->rating }}/5 sao</span><p class="mt-1 max-w-sm text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($review->comment, 120) }}</p>@if($review->replies->isNotEmpty())<span class="mt-2 inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">Đã phản hồi</span>@endif</td>
                                <td class="px-4 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $review->status->badgeClasses() }}">{{ $review->status->label() }}</span></td>
                                <td class="px-4 py-4"><div class="flex flex-col items-end gap-2"><a href="{{ route('admin.student-reviews.show', $review) }}" class="font-bold text-indigo-700 hover:underline dark:text-indigo-300">Chi tiết</a>@if($review->status !== \App\Enums\ReviewStatus::Approved)<form method="POST" action="{{ route('admin.student-reviews.approve', $review) }}">@csrf @method('PATCH')<button class="cursor-pointer font-bold text-emerald-700 hover:underline dark:text-emerald-300">Duyệt</button></form>@endif</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Không có đánh giá phù hợp.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reviews->hasPages())<div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">{{ $reviews->links() }}</div>@endif
        </div>
    </div>
</x-admin-layout>
