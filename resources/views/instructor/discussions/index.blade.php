<x-instructor-layout title="Trao đổi với học viên" pageTitle="Trao đổi với học viên" breadcrumb="Giảng viên / Trao đổi">
    <div class="space-y-6">
        <!-- BỘ LỌC TÌM KIẾM -->
        <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Khóa học</label>
                <select name="course_id" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Tất cả khóa học</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected($filters['course_id'] === $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Trạng thái</label>
                <select name="status" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" @selected($filters['status'] === 'pending')>Chờ phản hồi</option>
                    <option value="answered" @selected($filters['status'] === 'answered')>Đã trả lời</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full cursor-pointer rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-emerald-700">
                    Lọc câu hỏi
                </button>
            </div>
        </form>

        <!-- DANH SÁCH CÂU HỎI -->
        <div class="space-y-4">
            @forelse($discussions as $disc)
                @php
                    $hasInstructorAnswer = $disc->replies->contains('is_instructor_answer', true);
                @endphp
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 transition hover:shadow-md">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-300">
                                {{ $disc->lesson->course->title }}
                            </span>
                            <span class="text-slate-400 mx-1">&bull;</span>
                            <span class="text-xs text-slate-500 font-medium">
                                Bài học: {{ $disc->lesson->title }}
                            </span>
                            
                            <h3 class="mt-2 font-extrabold text-base text-slate-950 dark:text-white">
                                {{ $disc->title }}
                            </h3>
                            
                            <p class="text-xs text-slate-500 mt-1">
                                Người gửi: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $disc->user?->name ?? 'Học viên ẩn danh' }}</span> &bull; 
                                {{ $disc->created_at->format('H:i d/m/Y') }} ({{ $disc->created_at->diffForHumans() }})
                            </p>
                        </div>
                        
                        <div>
                            @if($hasInstructorAnswer)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                    Đã trả lời
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                                    Chờ phản hồi
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-950 text-sm leading-6 text-slate-700 dark:text-slate-300">
                        <p class="whitespace-pre-line">{{ $disc->content }}</p>
                        
                        @if($disc->attachment_path)
                            <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2 text-xs text-slate-500">
                                <span class="text-slate-400">📎 Tệp đính kèm:</span>
                                @if($disc->attachment_type === 'image')
                                    <span class="bg-blue-50 text-blue-700 border border-blue-100 rounded px-2 py-0.5 font-semibold">Hình ảnh</span>
                                @elseif($disc->attachment_type === 'video')
                                    <span class="bg-purple-50 text-purple-700 border border-purple-100 rounded px-2 py-0.5 font-semibold">Video</span>
                                @else
                                    <span class="bg-gray-50 text-gray-700 border border-gray-100 rounded px-2 py-0.5 font-semibold">Tài liệu</span>
                                @endif
                                <span class="text-slate-400 font-medium italic">({{ Str::limit($disc->attachment_name, 35) }})</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-medium">
                            Có {{ $disc->replies->count() }} lượt trao đổi
                        </span>
                        <a href="{{ route('instructor.discussions.show', $disc) }}" 
                           class="inline-flex items-center gap-1 cursor-pointer rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-emerald-700">
                            Trả lời &rarr;
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-900">
                    Chưa có câu hỏi nào từ học viên cho các khóa học của bạn.
                </div>
            @endforelse

            <div class="mt-4">
                {{ $discussions->links() }}
            </div>
        </div>
    </div>
</x-instructor-layout>
