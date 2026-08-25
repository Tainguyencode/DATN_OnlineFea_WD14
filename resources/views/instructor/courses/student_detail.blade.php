<x-instructor-layout :title="'Chi tiết học viên - ' . $student->name" page-title="Tiến độ học viên" :breadcrumb="$course->title">

    @php
        // Eager load the chapter relation in O(1) query to group lessons by chapter without N+1 query loops
        $lessons->loadMissing('chapter');
        $currentChapterId = null;
    @endphp

    <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        
        {{-- Back Button & Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/60">
            <a href="{{ route('instructor.courses.students', $course) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-600 transition-colors group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại danh sách học viên
            </a>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-xs font-semibold text-slate-700">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                Khóa học: <span class="font-bold text-slate-900 ml-1">{{ $course->title }}</span>
            </div>
        </div>

        {{-- Section 1: User Profile Header Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs hover:shadow-sm transition-shadow duration-300">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1.1fr_1fr_1fr] gap-4 items-center">
                
                {{-- Profile Info --}}
                <div class="flex items-center gap-4 min-w-0">
                    <div class="relative shrink-0">
                        <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-slate-50 shadow-md">
                        @if($enrollment->isCourseCompleted())
                            <span class="absolute -bottom-1 -right-1 bg-emerald-500 text-white p-1 rounded-lg shadow-sm ring-2 ring-white">
                                <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                    
                    <div class="space-y-1.5 min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-xl font-black text-slate-900 tracking-tight truncate max-w-[200px]" title="{{ $student->name }}">{{ $student->name }}</h2>
                            @if($enrollment->isCourseCompleted())
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200 shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Hoàn thành
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 border border-blue-200 shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    Đang học
                                </span>
                            @endif
                        </div>
                        
                        <p class="text-xs font-medium text-slate-500 font-mono flex items-center gap-1.5 min-w-0 break-all">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="break-all">{{ $student->email }}</span>
                        </p>
                        
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-slate-400 font-bold font-mono bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                ID: #STU{{ $student->id }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Status Stats - Ngày đăng ký --}}
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/60 shadow-3xs flex flex-col justify-center h-full min-w-0">
                    <span class="text-slate-400 block font-bold uppercase tracking-wider text-[10px]">Ngày đăng ký</span>
                    <strong class="text-slate-800 font-black text-sm block mt-1.5 font-mono">
                        {{ ($enrollment->enrolled_at ?? $enrollment->created_at)->format('H:i · d/m/Y') }}
                    </strong>
                </div>

                {{-- Status Stats - Hoạt động gần nhất --}}
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/60 shadow-3xs flex flex-col justify-center h-full min-w-0 md:col-span-2 lg:col-span-1">
                    <span class="text-slate-400 block font-bold uppercase tracking-wider text-[10px]">Hoạt động gần nhất</span>
                    <strong class="text-slate-800 font-black text-sm block mt-1.5 font-mono">
                        {{ $lastActiveAt ? $lastActiveAt->format('H:i · d/m/Y') : 'Chưa hoạt động' }}
                    </strong>
                </div>
            </div>
        </div>

        {{-- Section 2: Study Warnings & Positivity State --}}
        @if($alerts->isNotEmpty())
            <div class="bg-rose-50/30 rounded-2xl border border-rose-200/80 p-5 shadow-3xs flex gap-4 items-start">
                <div class="bg-rose-100 text-rose-800 p-2.5 rounded-xl shrink-0 mt-0.5 shadow-2xs">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-rose-900 font-extrabold text-sm tracking-tight flex items-center gap-1.5">
                        ⚠️ Cảnh báo học tập ({{ $alerts->count() }})
                    </h3>
                    <ul class="space-y-1 text-xs text-rose-700 font-medium">
                        @foreach($alerts as $alert)
                            <li class="flex items-start gap-2">
                                <span class="text-rose-400 mt-0.5">•</span>
                                <span>{{ preg_replace_callback('/(\d+\.\d+)/', fn($m) => round($m[1]), $alert) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <div class="bg-emerald-50/30 rounded-2xl border border-emerald-250/70 p-5 shadow-3xs flex gap-4 items-start">
                <div class="bg-emerald-100 text-emerald-800 p-2.5 rounded-xl shrink-0 mt-0.5 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="space-y-0.5">
                    <h3 class="text-emerald-900 font-extrabold text-sm tracking-tight">
                        Trạng thái học tập ổn định
                    </h3>
                    <p class="text-xs text-emerald-700/90 font-medium">
                        Học viên đang học tập ổn định, hoàn thành tốt các bài giảng và chưa có dấu hiệu sa sút.
                    </p>
                </div>
            </div>
        @endif

        {{-- Section 3: Overview Stat Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Course Progress --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-sm transition duration-300 flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Tiến độ chung</span>
                    <div class="bg-emerald-100 text-emerald-800 p-2 rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-slate-800">{{ number_format($enrollment->progress_percent, 0) }}</span>
                        <span class="text-sm font-bold text-slate-500">%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-150 mt-3">
                        <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ $enrollment->progress_percent }}%"></div>
                    </div>
                    <span class="text-[11px] text-slate-500 block mt-2 font-medium">
                        Đã hoàn thành <strong class="text-slate-800">{{ $enrollment->completed_lessons }}</strong> / <strong class="text-slate-800">{{ $enrollment->total_lessons }}</strong> bài bắt buộc.
                    </span>
                </div>
            </div>

            {{-- Quiz Score --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-sm transition duration-300 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Điểm Quiz (TB)</span>
                    <div class="bg-indigo-100 text-indigo-800 p-2 rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-slate-800">{{ $avgQuizScore !== null ? $avgQuizScore : '--' }}</span>
                        @if($avgQuizScore !== null)
                            <span class="text-xs font-bold text-slate-500">%</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mt-4.5 font-semibold">
                        <span>Điểm cao nhất:</span>
                        <span class="text-slate-800 font-bold font-mono">{{ $maxQuizScore !== null ? $maxQuizScore . '%' : '--' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1 font-semibold">
                        <span>Làm bài:</span>
                        <span class="text-slate-800 font-bold">
                            {{ $quizAttempts->count() }} / {{ $quizzes->count() }} Quiz
                        </span>
                    </div>
                </div>
            </div>

            {{-- Lab / Assignment Submissions --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-sm transition duration-300 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Bài thực hành</span>
                    <div class="bg-purple-100 text-purple-800 p-2 rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-slate-800">{{ $submittedAssignmentsCount }}</span>
                        <span class="text-sm font-bold text-slate-500">/ {{ $totalAssignments }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mt-4.5 font-semibold">
                        <span>Đã chấm:</span>
                        <span class="text-slate-800 font-bold">{{ $gradedAssignmentsCount }} bài</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1 font-semibold">
                        <span>Điểm trung bình:</span>
                        <span class="text-slate-800 font-bold font-mono">
                            {{ $averageAssignmentScore !== null ? $averageAssignmentScore : '--' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Certificate info --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-sm transition duration-300 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Chứng chỉ</span>
                    <div class="bg-amber-100 text-amber-800 p-2 rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-baseline">
                        <span class="text-xl font-black {{ $certificate ? 'text-amber-600' : 'text-slate-400' }}">
                            {{ $certificate ? 'ĐÃ CẤP' : 'CHƯA CẤP' }}
                        </span>
                    </div>
                    @if($certificate)
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-4.5 font-semibold">
                            <span>Mã số:</span>
                            <span class="text-slate-800 font-mono font-bold">{{ $certificate->certificate_code }}</span>
                        </div>
                    @else
                        <span class="text-[11px] text-slate-400 block italic leading-tight mt-4.5 font-medium">
                            {{ $course->certificate_enabled ? 'Chưa đủ điều kiện nhận.' : 'Khóa học không có chứng chỉ.' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section 4: Main Tabs Area --}}
        <div x-data="{ activeTab: 'lessons' }" class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            {{-- Tabs Selector --}}
            <div class="bg-slate-50 border-b border-slate-200 flex flex-wrap gap-1 p-1.5">
                <button 
                    @click="activeTab = 'lessons'" 
                    :class="activeTab === 'lessons' ? 'bg-white text-emerald-700 font-extrabold shadow-xs border border-slate-200' : 'text-slate-500 hover:text-slate-900 font-bold hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl text-xs transition cursor-pointer flex items-center gap-2"
                >
                    <span>📚</span> <span>Tiến độ bài học</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 tracking-wide">{{ $lessons->count() }}</span>
                </button>
                <button 
                    @click="activeTab = 'quizzes'" 
                    :class="activeTab === 'quizzes' ? 'bg-white text-emerald-700 font-extrabold shadow-xs border border-slate-200' : 'text-slate-500 hover:text-slate-900 font-bold hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl text-xs transition cursor-pointer flex items-center gap-2"
                >
                    <span>📝</span> <span>Kết quả Quiz</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 tracking-wide">{{ $quizzes->count() }}</span>
                </button>
                <button 
                    @click="activeTab = 'assignments'" 
                    :class="activeTab === 'assignments' ? 'bg-white text-emerald-700 font-extrabold shadow-xs border border-slate-200' : 'text-slate-500 hover:text-slate-900 font-bold hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl text-xs transition cursor-pointer flex items-center gap-2"
                >
                    <span>💻</span> <span>Bài thực hành</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 tracking-wide">{{ $totalAssignments }}</span>
                </button>
                <button 
                    @click="activeTab = 'timeline'" 
                    :class="activeTab === 'timeline' ? 'bg-white text-emerald-700 font-extrabold shadow-xs border border-slate-200' : 'text-slate-500 hover:text-slate-900 font-bold hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl text-xs transition cursor-pointer flex items-center gap-2"
                >
                    <span>🕒</span> <span>Hoạt động gần đây</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 tracking-wide">{{ $activities->count() }}</span>
                </button>
            </div>

            {{-- Tabs Content --}}
            <div class="p-6">
                {{-- Tab A: Lessons progress --}}
                <div x-show="activeTab === 'lessons'" class="space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/70 text-slate-500 font-black uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3.5 text-center w-12">STT</th>
                                    <th class="px-5 py-3.5">Bài học</th>
                                    <th class="px-5 py-3.5 text-center w-28">Loại</th>
                                    <th class="px-5 py-3.5 text-center w-40">Tiến độ xem</th>
                                                               @php
                                    $chapters = $course->chapters()->orderBy('sort_order')->get();
                                    $sttCounter = 1;
                                    $uncategorizedLessons = $lessons->filter(fn($l) => is_null($l->chapter_id));
                                @endphp

                                @if($uncategorizedLessons->isNotEmpty())
                                    <tbody x-data="{ isOpen: true }" class="divide-y divide-slate-100">
                                        <tr @click="isOpen = !isOpen" class="cursor-pointer select-none bg-slate-50/70 hover:bg-slate-100/50 transition">
                                            <td colspan="6" class="px-5 py-3 border-t border-slate-200/80">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-base">📁</span>
                                                        <span class="text-slate-800 font-extrabold text-xs uppercase tracking-wider">Bài học chung</span>
                                                        <span class="ml-2 text-[10px] font-bold text-slate-400 bg-white border border-slate-200 px-2 py-0.5 rounded-full">
                                                            {{ $uncategorizedLessons->count() }} bài học
                                                        </span>
                                                    </div>
                                                    <!-- Arrow Icon -->
                                                    <span class="text-slate-400 transition-transform duration-200 shrink-0" :class="isOpen ? 'transform rotate-0' : 'transform -rotate-90'">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        @foreach($uncategorizedLessons as $lesson)
                                            @php
                                                $prog = $lessonProgress->get($lesson->id);
                                                $isComp = $prog?->is_completed ?? false;
                                                $percent = $isComp ? 100 : ($prog?->progress_percent ?? 0);
                                            @endphp
                                            <tr x-show="isOpen" x-transition.opacity.duration.150ms class="hover:bg-slate-50/30 transition border-t border-slate-100">
                                                <td class="px-5 py-4 text-center font-bold text-slate-400">
                                                    {{ $sttCounter++ }}
                                                </td>
                                                <td class="px-5 py-4 font-bold text-slate-900">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span>{{ $lesson->title }}</span>
                                                        @if($lesson->is_required)
                                                            <span class="inline-flex text-[9px] text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-150 font-bold uppercase tracking-wide">Bắt buộc</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    @switch($lesson->type)
                                                        @case('video')
                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-650 bg-blue-50/60 px-2.5 py-0.5 rounded-lg border border-blue-100">📹 Video</span>
                                                            @break
                                                        @case('document')
                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-650 bg-amber-50/60 px-2.5 py-0.5 rounded-lg border border-amber-100">📄 Tài liệu</span>
                                                            @break
                                                        @case('quiz')
                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-650 bg-indigo-50/60 px-2.5 py-0.5 rounded-lg border border-indigo-100">📝 Quiz</span>
                                                            @break
                                                        @case('assignment')
                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-650 bg-purple-50/60 px-2.5 py-0.5 rounded-lg border border-purple-100">💻 Lab</span>
                                                            @break
                                                        @default
                                                            <span class="text-slate-400 font-bold uppercase">{{ $lesson->type }}</span>
                                                    @endswitch
                                                </td>
                                                <td class="px-5 py-4">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-150">
                                                            <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ $percent }}%"></div>
                                                        </div>
                                                        <span class="font-bold text-slate-700 font-mono">{{ number_format($percent, 0) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    @if($isComp)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                                            Hoàn thành
                                                        </span>
                                                    @elseif($percent > 0)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                                                            Đang học
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-500">
                                                            Chưa học
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-4 text-right text-slate-500 font-bold font-mono">
                                                    {{ $prog?->last_watched_at ? $prog->last_watched_at->format('H:i - d/m/Y') : '--' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endif

                                @forelse($chapters as $chap)
                                    @php
                                        $chapLessons = $lessons->filter(fn($l) => $l->chapter_id == $chap->id);
                                    @endphp
                                    <tbody x-data="{ isOpen: {{ $loop->first ? 'true' : 'false' }} }" class="divide-y divide-slate-100">
                                        <tr @click="isOpen = !isOpen" class="cursor-pointer select-none bg-slate-50/70 hover:bg-slate-100/50 transition">
                                            <td colspan="6" class="px-5 py-3 border-t border-slate-200/80">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-base">📁</span>
                                                        <span class="text-slate-800 font-extrabold text-xs uppercase tracking-wider">{{ $chap->title }}</span>
                                                        <span class="ml-2 text-[10px] font-bold text-slate-400 bg-white border border-slate-200 px-2 py-0.5 rounded-full">
                                                            {{ $chapLessons->count() }} bài học
                                                        </span>
                                                    </div>
                                                    <!-- Arrow Icon -->
                                                    <span class="text-slate-400 transition-transform duration-200 shrink-0" :class="isOpen ? 'transform rotate-0' : 'transform -rotate-90'">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>

                                        @forelse($chapLessons as $lesson)
                                            @php
                                                $prog = $lessonProgress->get($lesson->id);
                                                $isComp = $prog?->is_completed ?? false;
                                                $percent = $isComp ? 100 : ($prog?->progress_percent ?? 0);
                                            @endphp
                                            <tr x-show="isOpen" x-transition.opacity.duration.150ms class="hover:bg-slate-50/30 transition border-t border-slate-100">
                                                <td class="px-5 py-4 text-center font-bold text-slate-400">
                                                    {{ $sttCounter++ }}
                                                </td>
                                                <td class="px-5 py-4 font-bold text-slate-900">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span>{{ $lesson->title }}</span>
                                                        @if($lesson->is_required)
                                                            <span class="inline-flex text-[9px] text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-150 font-bold uppercase tracking-wide">Bắt buộc</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    @switch($lesson->type)
                                                        @case('video')
                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-650 bg-blue-50/60 px-2.5 py-0.5 rounded-lg border border-blue-100">📹 Video</span>
                                                            @break
                                                        @case('document')
                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-650 bg-amber-50/60 px-2.5 py-0.5 rounded-lg border border-amber-100">📄 Tài liệu</span>
                                                            @break
                                                        @case('quiz')
                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-650 bg-indigo-50/60 px-2.5 py-0.5 rounded-lg border border-indigo-100">📝 Quiz</span>
                                                            @break
                                                        @case('assignment')
                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-650 bg-purple-50/60 px-2.5 py-0.5 rounded-lg border border-purple-100">💻 Lab</span>
                                                            @break
                                                        @default
                                                            <span class="text-slate-400 font-bold uppercase">{{ $lesson->type }}</span>
                                                    @endswitch
                                                </td>
                                                <td class="px-5 py-4">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-150">
                                                            <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ $percent }}%"></div>
                                                        </div>
                                                        <span class="font-bold text-slate-700 font-mono">{{ number_format($percent, 0) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    @if($isComp)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                                            Hoàn thành
                                                        </span>
                                                    @elseif($percent > 0)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                                                            Đang học
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-500">
                                                            Chưa học
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-4 text-right text-slate-500 font-bold font-mono">
                                                    {{ $prog?->last_watched_at ? $prog->last_watched_at->format('H:i - d/m/Y') : '--' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr x-show="isOpen" x-transition.opacity.duration.150ms class="border-t border-slate-100">
                                                <td colspan="6" class="px-5 py-6 text-center text-slate-400 italic">
                                                    Chương này chưa có bài học.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                @empty
                                    <tbody class="divide-y divide-slate-100">
                                        <tr>
                                            <td colspan="6" class="px-5 py-12 text-center text-slate-500 border-t border-slate-200">
                                                <div class="flex flex-col items-center justify-center p-6 space-y-2">
                                                    <span class="text-3xl">📁</span>
                                                    <p class="font-semibold text-slate-700">Chưa có chương học nào trong khóa học này.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforelse
                        </table>
                    </div>
                </div>

                {{-- Tab B: Quiz outcomes --}}
                <div x-show="activeTab === 'quizzes'" class="space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/70 text-slate-500 font-black uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3.5">Bài kiểm tra</th>
                                    <th class="px-5 py-3.5 text-center w-36">Số lần làm</th>
                                    <th class="px-5 py-3.5 text-center w-36">Điểm đạt tối thiểu</th>
                                    <th class="px-5 py-3.5 text-center w-36">Điểm cao nhất</th>
                                    <th class="px-5 py-3.5 text-center w-36">Điểm gần nhất</th>
                                    <th class="px-5 py-3.5 text-center w-36">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($quizzes as $quiz)
                                    @php
                                        $attempts = $quizAttempts->get($quiz->id, collect());
                                        $attemptsCount = $attempts->count();
                                        $highestScore = $attemptsCount > 0 ? $attempts->max('percent') : null;
                                        $latestAttempt = $attemptsCount > 0 ? $attempts->first() : null;
                                        $hasPassed = $attempts->where('passed', true)->isNotEmpty();
                                    @endphp
                                    <tr class="hover:bg-slate-50/40 transition">
                                        <td class="px-5 py-4 font-bold text-slate-900">
                                            {{ $quiz->title }}
                                        </td>
                                        <td class="px-5 py-4 text-center font-bold text-slate-700 font-mono">
                                            {{ $attemptsCount }}
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold text-slate-500 font-mono">
                                            {{ $quiz->pass_score }}%
                                        </td>
                                        <td class="px-5 py-4 text-center font-bold font-mono">
                                            @if($highestScore !== null)
                                                <span class="text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ number_format($highestScore, 0) }}%</span>
                                            @else
                                                <span class="text-slate-400">--</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold font-mono">
                                            @if($latestAttempt !== null)
                                                <span class="text-slate-600">{{ number_format($latestAttempt->percent, 0) }}%</span>
                                            @else
                                                <span class="text-slate-400">--</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            @if($attemptsCount > 0)
                                                @if($hasPassed)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                                        Đạt
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800">
                                                        Chưa đạt
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-500">
                                                    Chưa làm
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                            <div class="flex flex-col items-center justify-center p-6 space-y-2">
                                                <span class="text-3xl">📝</span>
                                                <p class="font-semibold text-slate-700">Học viên chưa thực hiện bài kiểm tra nào.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab C: Practical assignments --}}
                <div x-show="activeTab === 'assignments'" class="space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/70 text-slate-500 font-black uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3.5">Bài thực hành</th>
                                    <th class="px-5 py-3.5 text-center w-40">Hạn nộp/Ngày nộp</th>
                                    <th class="px-5 py-3.5 text-center w-32">Yêu cầu đạt</th>
                                    <th class="px-5 py-3.5 text-center w-32">Điểm chấm</th>
                                    <th class="px-5 py-3.5 text-center w-36">Trạng thái nộp</th>
                                    <th class="px-5 py-3.5 text-center w-36">Trạng thái chấm</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($assignments as $assignment)
                                    @php
                                        $sub = $submissions->get($assignment->id);
                                        $passingLimit = $assignment->passing_score ?? 70;
                                    @endphp
                                    <tr class="hover:bg-slate-50/40 transition">
                                        <td class="px-5 py-4 font-bold text-slate-900">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span>{{ $assignment->title }}</span>
                                                @if($assignment->is_required)
                                                    <span class="inline-flex text-[9px] text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-150 font-bold uppercase tracking-wide">Bắt buộc</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold text-slate-600 font-mono">
                                            @if($sub && $sub->submitted_at)
                                                <span class="text-slate-800">{{ $sub->submitted_at->format('d/m/Y') }}</span>
                                            @elseif($assignment->due_date)
                                                <span class="text-red-550 font-bold">Hạn: {{ $assignment->due_date->format('d/m/Y') }}</span>
                                            @else
                                                <span class="text-slate-400">--</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center font-bold text-slate-500 font-mono">
                                            {{ $passingLimit }} / {{ $assignment->max_score }}
                                        </td>
                                        <td class="px-5 py-4 text-center font-extrabold font-mono whitespace-nowrap">
                                            @if($sub && $sub->score !== null)
                                                <span class="{{ $sub->score >= $passingLimit ? 'text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-lg' : 'text-red-500 bg-red-50 border border-red-200 px-2 py-0.5 rounded-lg' }}">
                                                    {{ floatval($sub->score) }}/{{ $assignment->max_score }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">--</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold">
                                            @if($sub)
                                                <span class="text-emerald-600 font-bold">Đã nộp</span>
                                            @else
                                                <span class="text-slate-400 italic font-medium">Chưa nộp</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            @if($sub)
                                                @switch($sub->status)
                                                    @case('submitted')
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-800 px-2.5 py-0.5 text-xs font-bold border border-amber-200">
                                                            Chờ chấm
                                                        </span>
                                                        @break
                                                    @case('graded')
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-xs font-bold border border-emerald-200">
                                                            Đã chấm
                                                        </span>
                                                        @break
                                                    @case('returned')
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-500 px-2.5 py-0.5 text-xs font-bold border border-slate-200">
                                                            Đã trả lại
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="font-mono text-slate-400 uppercase font-bold">{{ $sub->status }}</span>
                                                @endswitch
                                            @else
                                                <span class="text-slate-400">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                            <div class="flex flex-col items-center justify-center p-6 space-y-2">
                                                <span class="text-3xl">💻</span>
                                                <p class="font-semibold text-slate-700">Khóa học này hiện không có bài thực hành nào.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab D: Activity timeline --}}
                <div x-show="activeTab === 'timeline'" class="space-y-4">
                    @if($activities->isNotEmpty())
                        <div class="relative pl-8 border-l border-slate-200 ml-4 space-y-6">
                            @foreach($activities as $act)
                                <div class="relative">
                                    {{-- Timeline bullet point indicator with unique icon/style per event --}}
                                    <div class="absolute -left-[41px] top-1 w-6 h-6 rounded-full bg-white border-2 flex items-center justify-center shadow-xs">
                                        @switch($act->type)
                                            @case('enrollment')
                                                <span class="text-[10px]" title="Đăng ký khóa học">👋</span>
                                                @break
                                            @case('lesson_completed')
                                                <span class="text-[10px]" title="Hoàn thành bài học">✅</span>
                                                @break
                                            @case('quiz_attempt')
                                                <span class="text-[10px]" title="Làm Quiz">📝</span>
                                                @break
                                            @case('assignment_submission')
                                                <span class="text-[10px]" title="Nộp bài thực hành">📤</span>
                                                @break
                                            @case('assignment_graded')
                                                <span class="text-[10px]" title="Được chấm điểm">💯</span>
                                                @break
                                            @case('certificate')
                                                <span class="text-[10px]" title="Nhận chứng chỉ">🎓</span>
                                                @break
                                            @default
                                                <span class="text-[10px]">📌</span>
                                        @endswitch
                                    </div>

                                    {{-- Event card styling --}}
                                    <div class="bg-slate-50/40 hover:bg-slate-50 rounded-xl p-4 border border-slate-200/80 shadow-2xs hover:shadow-xs transition duration-200">
                                        <span class="font-bold text-slate-400 block text-[10px] font-mono uppercase tracking-wider mb-1">
                                            {{ $act->time->format('H:i - d/m/Y') }}
                                        </span>
                                        <strong class="text-slate-800 font-extrabold text-sm block">
                                            {{ $act->title }}
                                        </strong>
                                        <p class="text-slate-600 mt-1 font-medium text-xs">
                                            {{ $act->description }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center p-6 space-y-2">
                            <span class="text-3xl">🕒</span>
                            <p class="font-semibold text-slate-500 italic">Chưa ghi nhận hoạt động học tập nào của học viên này.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-instructor-layout>
