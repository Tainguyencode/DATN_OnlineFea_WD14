<x-instructor-layout>
    <x-slot:title>Quản lý Bình luận Bài học</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Bình luận bài học</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Theo dõi, phản hồi và quản lý hiển thị các bình luận bài học của học viên.</p>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('instructor.comments.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="course_id" class="block text-xs font-semibold text-slate-500 mb-1">Khóa học</label>
                    <select name="course_id" id="course_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Tất cả khóa học --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ (int)request('course_id') === (int)$course->id ? 'selected' : '' }}>
                                {{ Str::limit($course->title, 50) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-44">
                    <label for="status" class="block text-xs font-semibold text-slate-500 mb-1">Trạng thái</label>
                    <select name="status" id="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Tất cả --</option>
                        <option value="visible" {{ request('status') === 'visible' ? 'selected' : '' }}>Đang hiển thị</option>
                        <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Đã ẩn</option>
                    </select>
                </div>

                <div class="pt-5 flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition">
                        Lọc dữ liệu
                    </button>
                    @if(request()->filled('course_id') || request()->filled('status'))
                        <a href="{{ route('instructor.comments.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            Đặt lại
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table List -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400 font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Học viên</th>
                            <th class="px-4 py-3">Khóa học</th>
                            <th class="px-4 py-3">Nội dung bình luận</th>
                            <th class="px-4 py-3 text-center">Phản hồi</th>
                            <th class="px-4 py-3 text-center">Trạng thái</th>
                            <th class="px-4 py-3">Thời gian</th>
                            <th class="px-4 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($comments as $comment)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $comment->user ? $comment->user->avatarUrl() : 'https://ui-avatars.com/api/?name=User' }}" alt="Avatar" class="rounded-full border border-slate-200" style="width: 32px; height: 32px; object-fit: cover;">
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $comment->user ? $comment->user->name : 'NĐT' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $comment->user ? $comment->user->email : '' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900 dark:text-white max-w-[220px] truncate" title="{{ $comment->lesson->course->title }}">
                                        {{ $comment->lesson->course->title }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 max-w-[280px]">
                                    <p class="line-clamp-2 text-slate-800 dark:text-slate-200 whitespace-pre-line">{{ $comment->content }}</p>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-bold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $comment->replies->count() }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if($comment->is_hidden)
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[11px] font-bold text-rose-700 border border-rose-200">🔒 Đã ẩn</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200">Hiển thị</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-slate-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('instructor.comments.show', $comment) }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            Xem & Trả lời
                                        </a>

                                        <form action="{{ route('comments.toggle-hide', $comment) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-bold transition cursor-pointer {{ $comment->is_hidden ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                                {{ $comment->is_hidden ? 'Bỏ ẩn' : 'Ẩn' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-rose-300 bg-white px-2.5 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                    Không tìm thấy bình luận bài học nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($comments->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $comments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-instructor-layout>
