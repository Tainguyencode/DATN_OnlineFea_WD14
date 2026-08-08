<x-instructor-layout title="Bài tập đã nộp" pageTitle="Bài tập đã nộp" breadcrumb="Giảng viên / Bài tập đã nộp">
    <div class="space-y-6">
        <!-- Bộ lọc -->
        <form method="GET" class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Khóa học</label>
                <select name="course_id" onchange="this.form.submit()" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Tất cả khóa học</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected($courseId === $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Bài tập thực hành</label>
                <select name="assignment_id" onchange="this.form.submit()" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950" @disabled($courses->isEmpty() || !$courseId)>
                    <option value="">Tất cả bài tập</option>
                    @foreach($assignments as $assignment)
                        <option value="{{ $assignment->id }}" @selected($assignmentId === $assignment->id)>{{ $assignment->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Trạng thái</label>
                <select name="status" onchange="this.form.submit()" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Tất cả trạng thái</option>
                    <option value="submitted" @selected($status === 'submitted')>Chưa chấm (Submitted)</option>
                    <option value="graded" @selected($status === 'graded')>Đã chấm (Graded)</option>
                    <option value="resubmit_required" @selected($status === 'resubmit_required' || $status === 'returned')>Yêu cầu nộp lại (Returned)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Tìm học viên</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Tên hoặc email..." class="w-full rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full cursor-pointer rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-emerald-700">
                    Tìm kiếm & Lọc
                </button>
            </div>
        </form>

        <!-- Bảng danh sách bài nộp -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4 font-bold text-slate-700 dark:text-slate-200">Học viên</th>
                            <th class="px-6 py-4 font-bold text-slate-700 dark:text-slate-200">Khóa học / Bài tập</th>
                            <th class="px-6 py-4 font-bold text-slate-700 dark:text-slate-200">Bài làm</th>
                            <th class="px-6 py-4 font-bold text-slate-700 dark:text-slate-200">Trạng thái</th>
                            <th class="px-6 py-4 font-bold text-slate-700 dark:text-slate-200">Điểm</th>
                            <th class="px-6 py-4 font-bold text-slate-700 dark:text-slate-200">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($submissions as $submission)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-950/40">
                                <!-- Học viên -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 rounded-full flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($submission->user->name ?? 'H', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white">{{ $submission->user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $submission->user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Khóa học & Bài tập -->
                                <td class="px-6 py-4">
                                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">
                                        {{ $submission->assignment->lesson->course->title }}
                                    </p>
                                    <p class="font-medium text-slate-900 dark:text-white mt-0.5">
                                        {{ $submission->assignment->title }}
                                    </p>
                                </td>



                                <!-- Bài làm -->
                                <td class="px-6 py-4">
                                    @if($submission->file_path)
                                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0056D2] hover:underline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Tải tệp đính kèm
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">Không có file</span>
                                    @endif
                                </td>

                                <!-- Trạng thái -->
                                <td class="px-6 py-4">
                                    @if($submission->status === 'submitted')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/10 dark:bg-amber-950/20 dark:text-amber-400">
                                            Chưa chấm
                                        </span>
                                    @elseif($submission->status === 'graded')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10 dark:bg-emerald-950/20 dark:text-emerald-400">
                                            Đã chấm
                                        </span>
                                    @elseif($submission->status === 'resubmit_required' || $submission->status === 'returned')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/10 dark:bg-rose-950/20 dark:text-rose-400">
                                            Nộp lại
                                        </span>
                                    @endif
                                </td>

                                <!-- Điểm -->
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-100">
                                    {{ $submission->score !== null ? $submission->score . ' / ' . ($submission->assignment->max_score ?? 100) : '—' }}
                                </td>

                                <!-- Hành động -->
                                <td class="px-6 py-4">
                                    <a href="{{ route('instructor.submissions.show', $submission) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 shadow-sm">
                                        Xem & Chấm điểm
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Không tìm thấy bài nộp nào phù hợp với bộ lọc.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($submissions->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 dark:bg-slate-900">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-instructor-layout>
