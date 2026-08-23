<x-instructor-layout title="Trao đổi với học viên" pageTitle="Trao đổi với học viên" breadcrumb="Giảng viên / Trao đổi">
    <div class="space-y-6">
        <!-- THỐNG KÊ NHANH -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('instructor.discussions.index') }}" 
               class="rounded-2xl border p-5 shadow-xs transition hover:shadow-md {{ empty($filters['status']) ? 'border-slate-900 bg-slate-900 text-white dark:border-emerald-500' : 'border-slate-200 bg-white text-slate-900 dark:border-slate-800 dark:bg-slate-900 dark:text-white' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider {{ empty($filters['status']) ? 'text-slate-300' : 'text-slate-500' }}">Tất cả tin nhắn</span>
                    <span class="text-xl">💬</span>
                </div>
                <div class="mt-2 text-2xl font-black">{{ $counts['total'] }}</div>
                <div class="text-xs {{ empty($filters['status']) ? 'text-slate-300' : 'text-slate-500' }} mt-1">Cuộc trao đổi từ học viên</div>
            </a>

            <a href="{{ route('instructor.discussions.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
               class="rounded-2xl border p-5 shadow-xs transition hover:shadow-md {{ ($filters['status'] ?? '') === 'pending' ? 'border-amber-500 bg-amber-500 text-white' : 'border-amber-200 bg-amber-50/70 text-amber-950 dark:border-amber-900/50 dark:bg-slate-900 dark:text-amber-100' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider {{ ($filters['status'] ?? '') === 'pending' ? 'text-amber-100' : 'text-amber-800 dark:text-amber-300' }}">Chưa trả lời</span>
                </div>
                <div class="mt-2 text-2xl font-black {{ ($filters['status'] ?? '') === 'pending' ? 'text-white' : 'text-amber-700 dark:text-amber-400' }}">{{ $counts['pending'] }}</div>
                <div class="text-xs {{ ($filters['status'] ?? '') === 'pending' ? 'text-amber-100' : 'text-amber-700 dark:text-amber-300' }} mt-1">Cần phản hồi cho học viên</div>
            </a>

            <a href="{{ route('instructor.discussions.index', array_merge(request()->query(), ['status' => 'answered'])) }}" 
               class="rounded-2xl border p-5 shadow-xs transition hover:shadow-md {{ ($filters['status'] ?? '') === 'answered' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-emerald-200 bg-emerald-50/70 text-emerald-950 dark:border-emerald-900/50 dark:bg-slate-900 dark:text-emerald-100' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider {{ ($filters['status'] ?? '') === 'answered' ? 'text-emerald-100' : 'text-emerald-800 dark:text-emerald-300' }}">Đã trả lời</span>
                </div>
                <div class="mt-2 text-2xl font-black {{ ($filters['status'] ?? '') === 'answered' ? 'text-white' : 'text-emerald-700 dark:text-emerald-400' }}">{{ $counts['answered'] }}</div>
                <div class="text-xs {{ ($filters['status'] ?? '') === 'answered' ? 'text-emerald-100' : 'text-emerald-700 dark:text-emerald-300' }} mt-1">Đã hoàn tất hỗ trợ</div>
            </a>
        </div>

        <!-- BỘ LỌC TÌM KIẾM VÀ TRẠNG THÁI -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form method="GET" class="grid gap-3 sm:grid-cols-12 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Tìm kiếm</label>
                    <input type="text" 
                           name="search" 
                           value="{{ $filters['search'] ?? '' }}" 
                           placeholder="Tên học viên, nội dung câu hỏi..." 
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Khóa học</label>
                    <select name="course_id" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="">Tất cả khóa học</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(($filters['course_id'] ?? null) == $course->id)>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Trạng thái</label>
                    <select name="status" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="">Tất cả</option>
                        <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>⏳ Chưa trả lời</option>
                        <option value="answered" @selected(($filters['status'] ?? '') === 'answered')>✅ Đã trả lời</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 cursor-pointer rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700 shadow-xs">
                        Lọc
                    </button>
                    @if(!empty($filters['search']) || !empty($filters['course_id']) || !empty($filters['status']))
                        <a href="{{ route('instructor.discussions.index') }}" class="rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 transition" title="Xóa lọc">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- DANH SÁCH CUỘC HỘI THOẠI -->
        <div class="space-y-3">
            @forelse($discussions as $disc)
                @php
                    $isPending = $disc->needsReply();
                    $lastReply = $disc->replies->sortByDesc('created_at')->first();
                    $lastSenderName = $lastReply ? ($lastReply->is_instructor_answer ? 'Bạn (Giảng viên)' : ($lastReply->user?->name ?? 'Học viên')) : ($disc->user?->name ?? 'Học viên');
                    $timeDisplay = $disc->created_at->isToday() ? $disc->created_at->format('H:i') : $disc->created_at->format('d/m/Y H:i');
                @endphp
                <article class="rounded-2xl border {{ $isPending ? 'border-amber-300 bg-amber-50/30 dark:border-amber-900/50' : 'border-slate-200 bg-white dark:border-slate-800' }} p-5 shadow-sm dark:bg-slate-900 transition hover:shadow-md group">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-3.5 min-w-0">
                            <!-- Avatar Học viên -->
                            <div class="shrink-0 mt-0.5">
                                @if($disc->user?->avatar)
                                    <img src="{{ $disc->user->avatarUrl() }}" alt="{{ $disc->user->name }}" class="w-11 h-11 rounded-full object-cover border-2 {{ $isPending ? 'border-amber-400' : 'border-slate-200' }}">
                                @else
                                    <div class="w-11 h-11 rounded-full {{ $isPending ? 'bg-amber-600' : 'bg-slate-700' }} text-white font-bold flex items-center justify-center text-sm border-2 {{ $isPending ? 'border-amber-400' : 'border-slate-200' }}">
                                        {{ strtoupper(mb_substr($disc->user?->name ?? 'H', 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white truncate">
                                        {{ $disc->user?->name ?? 'Học viên ẩn danh' }}
                                    </h4>
                                    <span class="text-slate-400 text-xs">&bull;</span>
                                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 truncate">
                                        {{ $disc->course?->title ?? $disc->lesson?->course?->title }}
                                    </span>
                                    @if($disc->lesson)
                                        <span class="text-slate-400 text-xs">&bull;</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                            Bài: {{ $disc->lesson->title }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-1 text-sm font-bold text-slate-950 dark:text-white group-hover:text-emerald-600 transition truncate">
                                    {{ $disc->title }}
                                </h3>

                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $disc->content }}
                                </p>

                                <div class="flex flex-wrap items-center gap-3 mt-2 text-[11px] text-slate-400">
                                    <span>🕒 Gửi lúc: {{ $timeDisplay }} ({{ $disc->created_at->diffForHumans() }})</span>
                                    <span>&bull;</span>
                                    <span>💬 {{ $disc->replies->count() }} lượt trao đổi</span>
                                    @if($disc->attachment_path)
                                        <span>&bull;</span>
                                        <span class="text-blue-600 dark:text-blue-400 font-semibold">📎 Có tệp đính kèm</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex sm:flex-col items-center sm:items-end justify-between gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-800">
                            <div>
                                @if($isPending)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-800 border border-amber-300 shadow-2xs">
                                        
                                        CHƯA TRẢ LỜI
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                        ĐÃ TRẢ LỜI
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('instructor.discussions.show', $disc) }}" 
                               class="inline-flex items-center gap-1.5 cursor-pointer rounded-xl {{ $isPending ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-slate-900 hover:bg-black text-white dark:bg-emerald-600 dark:hover:bg-emerald-700' }} px-4 py-2 text-xs font-bold transition-all shadow-xs">
                                <span>Mở trò chuyện</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-white p-12 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-900">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-2xl">
                        💬
                    </div>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Không tìm thấy cuộc trao đổi nào</p>
                    <p class="text-xs text-slate-500">Chưa có câu hỏi nào từ học viên khớp với tiêu chí tìm kiếm của bạn.</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $discussions->links() }}
            </div>
        </div>
    </div>
</x-instructor-layout>
