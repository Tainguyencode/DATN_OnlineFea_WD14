<x-instructor-layout :title="$course->title" page-title="Danh sách học viên" :breadcrumb="$course->title">

    <div x-data="{
        showStudentModal: false,
        activeStudent: null,
        
        openStudentDetail(data) {
            this.activeStudent = data;
            this.showStudentModal = true;
        }
    }" class="space-y-6">

        {{-- Top Bar & Header --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    🎓 {{ $course->title }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Tổng số: <strong class="text-emerald-600 font-bold">{{ $enrollments->total() }} học viên</strong> đã ghi danh mua khóa học này</p>
            </div>

            <form method="GET" action="{{ route('instructor.courses.students', $course) }}" class="flex items-center gap-2">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Tìm tên hoặc email học viên..."
                        class="w-64 font-medium text-xs rounded-xl border-slate-300 py-2.5 pl-9 pr-3 text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition cursor-pointer">
                    Lọc
                </button>
            </form>
        </div>

        {{-- Students List Table --}}
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Học viên</th>
                            <th class="px-6 py-3.5">Email</th>
                            <th class="px-6 py-3.5 text-center">Tiến độ bài học</th>
                            <th class="px-6 py-3.5 text-center">Điểm Quiz (TB)</th>
                            <th class="px-6 py-3.5 text-center">Thực hành</th>
                            <th class="px-6 py-3.5 text-center">Trạng thái</th>
                            <th class="px-6 py-3.5">Ngày đăng ký mua</th>
                            <th class="px-6 py-3.5 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($enrollments as $enrollment)
                            @php
                                $uId = $enrollment->user_id;
                                $qScore = $quizStats[$uId] ?? null;
                                $lStat = $labStats[$uId] ?? null;
                                $lastProg = $latestProgress->get($uId);
                                $lastLessonTitle = $lastProg?->lesson?->title ?? 'Chưa tham gia học';
                                $isCompleted = $enrollment->isCourseCompleted();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-emerald-100 text-emerald-800 font-black rounded-xl flex items-center justify-center text-xs shadow-xs uppercase shrink-0">
                                            {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block text-sm">{{ $enrollment->user->name }}</span>
                                            <span class="text-[11px] text-slate-400 font-mono">ID: #STU{{ $enrollment->user_id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600 font-mono">{{ $enrollment->user->email }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                            <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ number_format($enrollment->progress_percent, 0) }}%"></div>
                                        </div>
                                        <span class="font-bold text-slate-800">{{ number_format($enrollment->progress_percent, 0) }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($qScore !== null)
                                        <span class="inline-block text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg">
                                            {{ $qScore }}%
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Chưa làm</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($lStat !== null)
                                        <span class="inline-block text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg">
                                            {{ $lStat['score'] }}/{{ $lStat['max'] }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Chưa nộp</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($isCompleted)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold text-emerald-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Hoàn thành
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-[11px] font-bold text-blue-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                            Đang học
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-medium">
                                    {{ ($enrollment->enrolled_at ?? $enrollment->created_at)->format('H:i - d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        type="button"
                                        @click="openStudentDetail({{ json_encode([
                                            'id' => $enrollment->user_id,
                                            'name' => $enrollment->user->name,
                                            'email' => $enrollment->user->email,
                                            'course_title' => $course->title,
                                            'enrolled_at' => ($enrollment->enrolled_at ?? $enrollment->created_at)->format('H:i:s - d/m/Y'),
                                            'progress' => number_format($enrollment->progress_percent, 0) . '%',
                                            'last_lesson' => $lastLessonTitle,
                                            'quiz_score' => $qScore !== null ? $qScore . '%' : 'Chưa tham gia trắc nghiệm',
                                            'lab_score' => $lStat !== null ? $lStat['score'] . '/' . $lStat['max'] . ' điểm' : 'Chưa nộp bài thực hành',
                                            'status' => $isCompleted ? 'Đã hoàn thành khóa học' : 'Đang học tập chủ động',
                                            'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->format('H:i:s - d/m/Y') : 'Chưa hoàn thành',
                                        ]) }})"
                                        class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Chưa có học viên nào ghi danh</p>
                                    <p class="mt-1 text-xs text-slate-400">Học viên mua khóa học sẽ xuất hiện ở đây.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($enrollments->hasPages())
                <div class="p-4 border-t border-slate-100">{{ $enrollments->links() }}</div>
            @endif
        </div>

        {{-- Modal Chi tiết Mua khóa học & Học viên --}}
        <div
            x-show="showStudentModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="student-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="showStudentModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="showStudentModal = false"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                ></div>

                <div
                    x-show="showStudentModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm">
                                🎓
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900" id="student-modal-title">
                                    Chi tiết Đăng ký Mua Khóa học
                                </h3>
                                <p class="text-xs text-slate-500 font-mono" x-text="activeStudent ? activeStudent.name : ''"></p>
                            </div>
                        </div>
                        <button type="button" @click="showStudentModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <template x-if="activeStudent">
                        <div class="mt-5 space-y-4">
                            {{-- Header Banner --}}
                            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Khóa học đăng ký mua:</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="activeStudent.course_title"></span>
                            </div>

                            {{-- Student & Purchase Specs --}}
                            <div class="rounded-2xl border border-slate-200 divide-y divide-slate-100 bg-slate-50/50 text-xs">
                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Tên học viên:</span>
                                    <span class="font-bold text-slate-900 text-sm" x-text="activeStudent.name"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Email tài khoản:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="activeStudent.email"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Thời gian mua/ghi danh:</span>
                                    <span class="font-semibold text-slate-800" x-text="activeStudent.enrolled_at"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Tiến độ hoàn thành:</span>
                                    <span class="font-bold text-emerald-600 text-sm" x-text="activeStudent.progress"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Bài học xem gần nhất:</span>
                                    <span class="font-medium text-slate-800 truncate max-w-[200px]" x-text="activeStudent.last_lesson"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Kết quả Trắc nghiệm (Quiz):</span>
                                    <span class="font-bold text-slate-900" x-text="activeStudent.quiz_score"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Bài nộp thực hành:</span>
                                    <span class="font-bold text-slate-900" x-text="activeStudent.lab_score"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Trạng thái khóa học:</span>
                                    <span class="font-bold text-slate-900" x-text="activeStudent.status"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="mt-6 flex justify-end">
                        <button
                            type="button"
                            @click="showStudentModal = false"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition cursor-pointer"
                        >
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-instructor-layout>
