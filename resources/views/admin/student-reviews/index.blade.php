<x-admin-layout title="Quản lý đánh giá" pageTitle="Quản lý đánh giá khóa học" breadcrumb="Admin / Đánh giá học viên">
    <div class="space-y-6">
        <form method="GET" action="{{ route('admin.student-reviews.index') }}"
            class="grid gap-3 rounded-lg border border-slate-200 bg-white p-5 shadow-sm
           dark:border-slate-800 dark:bg-slate-900
           md:grid-cols-2
           xl:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)_220px_180px]"
            onsubmit="this.querySelectorAll('input, select').forEach((field) => {
        if (!field.value) field.disabled = true
    })">
            <input type="search" name="keyword" value="{{ $filters['keyword'] ?? '' }}"
                placeholder="Học viên, khóa học, nội dung..."
                class="rounded-md border border-black px-3 py-2 text-sm
               focus:border-black focus:ring-1 focus:ring-black
               dark:border-slate-500 dark:bg-slate-950 dark:text-white">

            <select name="course_id"
                class="cursor-pointer rounded-md border border-black px-3 py-2 text-sm
               focus:border-black focus:ring-1 focus:ring-black
               dark:border-slate-500 dark:bg-slate-950 dark:text-white">
                <option value="">Tất cả khóa học</option>

                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected(($filters['course_id'] ?? '') == $course->id)>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>

            <select name="status"
                class="cursor-pointer rounded-md border border-black px-3 py-2 text-sm
               focus:border-black focus:ring-1 focus:ring-black
               dark:border-slate-500 dark:bg-slate-950 dark:text-white">
                <option value="">Tất cả trạng thái</option>

                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 cursor-pointer rounded-md bg-rose-600 px-4 py-2
                   text-sm font-bold text-white transition-colors
                   hover:bg-rose-700">
                    Áp dụng
                </button>

                <a href="{{ route('admin.student-reviews.index') }}"
                    class="rounded-md border border-black px-4 py-2 text-sm
                   font-bold text-slate-700 hover:bg-slate-50
                   dark:border-slate-500 dark:text-slate-200
                   dark:hover:bg-slate-800">
                    Xóa lọc
                </a>
            </div>
        </form>
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm dark:divide-slate-800">
                    <thead
                        class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-950 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3">ID / Học viên</th>
                            <th class="px-4 py-3">Khóa học</th>
                            <th class="px-4 py-3">Đánh giá</th>
                            <th class="px-4 py-3">Trạng thái</th>
                            <th class="px-4 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($reviews as $review)
                            <tr class="align-top">
                                <td class="px-4 py-4"><strong
                                        class="text-slate-950 dark:text-white">#{{ $review->id }} ·
                                        {{ $review->user?->name }}</strong>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $review->user?->email }}<br>{{ $review->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="max-w-xs font-bold text-slate-800 dark:text-slate-100">
                                        {{ $review->course?->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">GV:
                                        {{ $review->course?->instructor?->name }}</p>
                                </td>
                                <td class="px-4 py-4"><span class="font-bold text-amber-600">{{ $review->rating }}/5
                                        sao</span>
                                    <p class="mt-1 max-w-sm text-slate-600 dark:text-slate-300">
                                        {{ \Illuminate\Support\Str::limit($review->comment, 120) }}</p>
                                    @if ($review->replies->isNotEmpty())
                                        <span
                                            class="mt-2 inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">Đã
                                            phản hồi</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4"><span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $review->status->badgeClasses() }}">{{ $review->status->label() }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                                        <a href="{{ route('admin.student-reviews.show', $review) }}"
                                            class="font-bold text-indigo-700 hover:underline dark:text-indigo-300">
                                            Chi tiết
                                        </a>

                                        @if ($review->isHidden())
                                            <form method="POST"
                                                action="{{ route('admin.student-reviews.restore', $review) }}"
                                                class="inline-flex">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="cursor-pointer font-bold text-emerald-700 hover:underline dark:text-emerald-300">
                                                    Hiện lại
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST"
                                                action="{{ route('admin.student-reviews.hide', $review) }}"
                                                class="inline-flex">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="cursor-pointer font-bold text-slate-700 hover:underline dark:text-slate-300">
                                                    Ẩn
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST"
                                            action="{{ route('admin.student-reviews.destroy', $review) }}"
                                            class="inline-flex"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này không?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="cursor-pointer font-bold text-rose-700 hover:underline dark:text-rose-300">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Không có đánh giá phù
                                    hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($reviews->hasPages())
                <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">{{ $reviews->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
