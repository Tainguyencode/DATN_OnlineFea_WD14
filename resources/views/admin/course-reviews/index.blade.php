<x-admin-layout title="Kiểm duyệt khóa học" page-title="Kiểm duyệt khóa học" breadcrumb="Danh sách khóa học chờ admin xem xét">
    <div class="space-y-6">
        {{-- ========================================================================= --}}
        {{-- HEADER & FILTERS                                                          --}}
        {{-- ========================================================================= --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">Kiểm duyệt khóa học</h1>
                <p class="mt-1 text-sm text-slate-500">Danh sách các khóa học chờ Admin kiểm tra nội dung & phê duyệt xuất bản</p>
            </div>

            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="instructor_status" value="{{ $instructorStatus }}">
                <div class="flex items-center gap-2">
                    <label for="course_status_select" class="text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Trạng thái khóa học:</label>
                    <select id="course_status_select" name="status" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-[#0056D2] focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200" onchange="this.form.submit()">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- ========================================================================= --}}
        {{-- INSTRUCTOR STATUS FILTER TABS                                             --}}
        {{-- ========================================================================= --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3 dark:border-slate-800">
            {{-- All --}}
            <a href="{{ route('admin.course-reviews.index', ['status' => $status, 'instructor_status' => 'all']) }}"
               class="rounded-xl px-4 py-2 text-xs font-bold transition duration-150 {{ $instructorStatus === 'all' ? 'bg-[#0056D2] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                Tất cả ({{ $instructorCounts['all'] }})
            </a>

            {{-- Approved Instructor --}}
            <a href="{{ route('admin.course-reviews.index', ['status' => $status, 'instructor_status' => 'approved']) }}"
               class="rounded-xl px-4 py-2 text-xs font-bold transition duration-150 {{ $instructorStatus === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                 GV đã duyệt ({{ $instructorCounts['approved'] }})
            </a>

            {{-- Pending Instructor --}}
            <a href="{{ route('admin.course-reviews.index', ['status' => $status, 'instructor_status' => 'pending']) }}"
               class="relative rounded-xl px-4 py-2 text-xs font-bold transition duration-150 {{ $instructorStatus === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-amber-700 hover:bg-amber-50 dark:bg-slate-800 dark:text-amber-300 dark:hover:bg-slate-700' }}">
                <span> GV chưa duyệt hồ sơ</span>
                @if($instructorCounts['pending'] > 0)
                    <span class="ml-1.5 rounded-full {{ $instructorStatus === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-500 text-white' }} px-2 py-0.5 text-[10px] font-black">
                        {{ $instructorCounts['pending'] }}
                    </span>
                @endif
            </a>

            {{-- Rejected Instructor --}}
            <a href="{{ route('admin.course-reviews.index', ['status' => $status, 'instructor_status' => 'rejected']) }}"
               class="rounded-xl px-4 py-2 text-xs font-bold transition duration-150 {{ $instructorStatus === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-rose-50 hover:text-rose-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                 GV bị từ chối ({{ $instructorCounts['rejected'] }})
            </a>

            {{-- Locked Instructor --}}
            @if($instructorCounts['locked'] > 0 || $instructorStatus === 'locked')
                <a href="{{ route('admin.course-reviews.index', ['status' => $status, 'instructor_status' => 'locked']) }}"
                   class="rounded-xl px-4 py-2 text-xs font-bold transition duration-150 {{ $instructorStatus === 'locked' ? 'bg-slate-800 text-white shadow-sm dark:bg-slate-700' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                     GV đang bị khóa ({{ $instructorCounts['locked'] }})
                </a>
            @endif
        </div>

        {{-- ========================================================================= --}}
        {{-- COURSES TABLE                                                             --}}
        {{-- ========================================================================= --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-black">Khóa học</th>
                            <th class="px-6 py-4 font-black">Trạng thái khóa học</th>
                            <th class="px-6 py-4 font-black">Giảng viên & Hồ sơ</th>
                            <th class="px-6 py-4 font-black">Danh mục</th>
                            <th class="px-6 py-4 font-black">Ngày gửi</th>
                            <th class="px-6 py-4 font-black text-center">Lần gửi</th>
                            <th class="px-6 py-4 font-black text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($courses as $course)
                            <tr class="transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 {{ $course->instructor?->instructor_status !== 'approved' ? 'bg-amber-50/20 dark:bg-amber-950/10' : '' }}">
                                {{-- Course Title & Info --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-16 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">
                                            @if($course->thumbnail)
                                                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-700 to-slate-900 text-[10px] font-black text-white">
                                                    Fea LMS
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 max-w-sm">
                                            <a href="{{ route('admin.courses.review', $course) }}" class="font-bold text-slate-900 hover:text-[#0056D2] dark:text-white dark:hover:text-blue-400 line-clamp-1">
                                                {{ $course->title }}
                                            </a>
                                            <div class="text-xs text-slate-400 mt-0.5">
                                                {{ $course->totalLessonsCount() }} bài học
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Course Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($course->status === 'pending_update')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-300 dark:bg-amber-950/50 dark:border-amber-700 dark:text-amber-300">
                                             Cập nhật chờ duyệt
                                        </span>
                                    @elseif($course->status === 'pending_review')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-900 border border-blue-300 dark:bg-blue-950/50 dark:border-blue-700 dark:text-blue-300">
                                             Mới chờ duyệt
                                        </span>
                                    @elseif($course->status === 'approved' || $course->status === 'published')
                                        @if($course->instructor?->instructor_status === 'approved' && ! $course->instructor?->isLocked())
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-300 dark:bg-emerald-950/50 dark:border-emerald-700 dark:text-emerald-300">
                                                 Đã duyệt & Xuất bản
                                            </span>
                                        @else
                                            <span class="inline-flex flex-col gap-0.5">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-300 dark:bg-amber-950/50 dark:border-amber-700 dark:text-amber-300">
                                                     Đã duyệt nội dung
                                                </span>
                                                <span class="text-[10px] text-amber-700 dark:text-amber-400 font-semibold pl-1">
                                                    (Chờ duyệt GV để public)
                                                </span>
                                            </span>
                                        @endif
                                    @elseif($course->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-800 border border-rose-300 dark:bg-rose-950/50 dark:border-rose-700 dark:text-rose-300">
                                             Từ chối
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $statusOptions[$course->status] ?? $course->status }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Instructor & Profile Status --}}
                                <td class="px-6 py-4 min-w-[240px]">
                                    <div class="flex items-start gap-3">
                                        <img src="{{ $course->instructor?->avatarUrl() ?? 'https://ui-avatars.com/api/?name='.urlencode($course->instructor?->name ?? 'Instructor') }}"
                                             alt="{{ $course->instructor?->name }}"
                                             class="h-9 w-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-sm shrink-0 mt-0.5">
                                        <div class="min-w-0 space-y-1">
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                                    {{ $course->instructor?->name ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-slate-400">
                                                    {{ $course->instructor?->email }}
                                                </div>
                                            </div>

                                            {{-- Trạng thái hồ sơ giảng viên --}}
                                            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                                @if($course->instructor?->isLocked())
                                                    <span class="inline-flex items-center rounded-full bg-slate-200 px-2 py-0.5 text-[11px] font-bold text-slate-800 border border-slate-400 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300">
                                                         Đang bị khóa
                                                    </span>
                                                @elseif($course->instructor?->instructor_status === 'approved')
                                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800 border border-emerald-300 dark:bg-emerald-950/40 dark:border-emerald-700 dark:text-emerald-300">
                                                         Đã duyệt
                                                    </span>
                                                @elseif($course->instructor?->instructor_status === 'rejected')
                                                    <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-bold text-rose-800 border border-rose-300 dark:bg-rose-950/40 dark:border-rose-700 dark:text-rose-300">
                                                         Bị từ chối
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-800 border border-amber-300 dark:bg-amber-950/40 dark:border-amber-700 dark:text-amber-300">
                                                         Chưa duyệt hồ sơ
                                                    </span>
                                                @endif

                                                {{-- BADGE CẢNH BÁO NỔI BẬT NẾU CHƯA DUYỆT GIẢNG VIÊN --}}
                                                @if($course->instructor?->instructor_status !== 'approved')
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 text-[11px] font-black text-amber-700 dark:text-amber-300 whitespace-nowrap">
                                                        ⚠️ Giảng viên chưa được duyệt
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                    {{ $course->category?->name ?? '—' }}
                                </td>

                                {{-- Submitted Date --}}
                                <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $course->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                                    </div>
                                    @if($course->submitted_at)
                                        <div class="text-[11px] text-slate-400">
                                            {{ $course->submitted_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Submission Count --}}
                                <td class="px-6 py-4 text-xs font-bold text-slate-700 dark:text-slate-300 text-center">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                                        {{ $course->submission_count ?? 1 }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.courses.review', $course) }}"
                                       class="inline-flex items-center gap-1.5 rounded-xl bg-[#0056D2] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#00419e]">
                                        <span>Xem chi tiết</span>
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    Không có khóa học nào phù hợp với bộ lọc hiện tại.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($courses->hasPages())
                <div class="border-t border-slate-200 p-4 dark:border-slate-800">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
