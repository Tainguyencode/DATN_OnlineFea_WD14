@extends('layouts.learning')

@section('title', $lesson->title . ' - ' . $course->title)

@section('content')
<div
    class="learning-player"
    data-learning-player
    data-course-progress="{{ $courseProgress }}"
    data-progress-url="{{ $progressUrl }}"
>
    <x-learning.header
        :course="$course"
        :course-progress="$courseProgress"
        :completed-lessons="$completedLessons"
        :total-lessons="$totalLessons"
    />

    <div class="learning-player-body flex min-h-[calc(100vh-3.5rem)] flex-col lg:flex-row">
        <main class="learning-main min-w-0 flex-1" data-learning-main>
            @if(!empty($hasNewContentVersion))
                <div class="bg-amber-500/20 border-b border-amber-500/30 px-6 py-2.5 text-xs font-bold text-amber-300 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Bài học đã được cập nhật nội dung / video mới từ Giảng viên.
                </div>
            @endif
            @if(! $canAccessLesson)
                <div class="flex min-h-[320px] items-center justify-center bg-[#1c1d1f] p-6">
                    <div class="max-w-md rounded border border-amber-400/30 bg-amber-500/10 p-6 text-center text-white">
                        <h2 class="text-lg font-bold">Bài học bị khóa</h2>
                        <p class="mt-2 text-sm text-white/80">Bạn cần đăng ký khóa học để xem bài học này.</p>
                        <div class="mt-5 flex flex-wrap justify-center gap-3">
                            @auth
                                @if(auth()->user()->isStudent())
                                    <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex h-10 items-center rounded bg-[#0056D2] px-5 text-sm font-bold text-white hover:bg-[#0046B8]">Đăng ký học</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="inline-flex h-10 items-center rounded bg-[#0056D2] px-5 text-sm font-bold text-white hover:bg-[#0046B8]">Đăng nhập</a>
                            @endauth
                            <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex h-10 items-center rounded border border-white/20 px-5 text-sm font-semibold text-white hover:bg-white/10">Về khóa học</a>
                        </div>
                    </div>
                </div>
            @elseif($lesson->type === 'quiz')
                <x-learning.quiz-player :quiz-context="$quizContext" :lesson="$lesson" />
            @elseif($lesson->type === 'video')
                <x-learning.video-player
                    :video-source="$videoSource"
                    :lesson="$lesson"
                    :progress-url="$progressUrl"
                    :lesson-progress="$lessonProgress"
                    :required-video-percent="$requiredVideoPercent"
                    :is-enrolled="$isEnrolled"
                />
            @elseif($lesson->type === 'assignment')
                <div class="bg-[#1c1d1f] p-6 lg:p-10 text-white min-h-[400px]">
                    <div class="max-w-3xl mx-auto space-y-6">
                        <!-- Tiêu đề & Yêu cầu bài tập -->
                        <div class="border-b border-white/10 pb-5">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="rounded-full bg-amber-500/20 px-2.5 py-0.5 text-xs font-bold text-amber-300 ring-1 ring-amber-500/30">
                                    BÀI TẬP THỰC HÀNH
                                </span>
                                @if($submission)
                                    <span class="rounded-full bg-indigo-500/20 px-2.5 py-0.5 text-xs font-bold text-indigo-300 ring-1 ring-indigo-500/30">
                                        Lần làm {{ $submission->attempt_number }}/{{ $submission->allowed_attempts ?? 2 }}
                                    </span>
                                    @if($submission->granter)
                                        <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-[11px] font-medium text-emerald-300 ring-1 ring-emerald-500/30">
                                            Được cấp thêm bởi Giảng viên
                                        </span>
                                    @endif
                                @endif
                            </div>
                            <h2 class="text-xl font-extrabold text-white">{{ $lesson->assignment?->title ?? $lesson->title ?? 'Bài tập thực hành' }}</h2>
                            <p class="text-sm text-slate-300 mt-2 whitespace-pre-line leading-relaxed">
                                {{ $lesson->assignment?->description ?? $lesson->content ?? 'Hãy thực hiện yêu cầu của bài tập thực hành dưới đây.' }}
                            </p>
                        </div>

                        <!-- KHU VỰC TÀI LIỆU BÀI TẬP & THỜI GIAN LÀM BÀI -->
                        @php
                            $hasStarted = $submission && $submission->started_at;
                            $deadline = $hasStarted ? $submission->getDeadline() : null;
                            $isExpired = $submission && $submission->isExpired();
                            $isSubmitted = $submission && in_array($submission->status, ['submitted', 'graded'], true);
                            $isGraded = $submission && $submission->status === 'graded';
                            $result = $submission?->result;
                            $canRetake = $submission && $submission->canRetake();
                            $hasExhaustedAttempts = $submission && ! $submission->isPassed() && (($isGraded && $result === 'fail') || $isExpired) && ! $canRetake;
                        @endphp

                        @if(! $hasStarted)
                            <!-- TRẠNG THÁI 1: CHƯA BẮT ĐẦU TẢI TÀI LIỆU -->
                            <div class="rounded-2xl border border-indigo-500/30 bg-gradient-to-br from-indigo-950/60 to-slate-900/80 p-6 shadow-xl space-y-4">
                                <div class="flex items-start gap-4">
                                    <div class="rounded-xl bg-indigo-600/20 p-3 text-indigo-400 ring-1 ring-indigo-500/30 shrink-0">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="text-base font-bold text-white">
                                            Quy định thời gian làm bài: 6 Giờ 
                                            @if($submission)
                                                (Lần {{ $submission->attempt_number }}/{{ $submission->allowed_attempts ?? 2 }})
                                            @endif
                                        </h3>
                                        <p class="text-xs text-slate-300 leading-relaxed">
                                            Thời gian 6 giờ <strong class="text-amber-400">chưa bắt đầu</strong>. Bạn có 6 giờ để hoàn thành và nộp bài tập kể từ khi bấm <strong class="text-white">"Tải tài liệu về"</strong>.
                                        </p>
                                    </div>
                                </div>

                                <div class="pt-2 flex flex-wrap items-center gap-3">
                                    <a href="{{ route('courses.lessons.assignment.download', [$course, $lesson]) }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0056D2] px-6 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-[#0046B8] cursor-pointer">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span>Tải tài liệu về & Bắt đầu làm bài</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- TRẠNG THÁI 2: ĐÃ BẮT ĐẦU LÀM BÀI -> HIỂN THỊ TIMER ĐẾM NGƯỢC -->
                            <div
                                x-data="{
                                    deadline: new Date('{{ $deadline?->toIso8601String() }}').getTime(),
                                    timeLeft: '',
                                    hours: 0,
                                    minutes: 0,
                                    seconds: 0,
                                    isExpired: {{ $isExpired ? 'true' : 'false' }},
                                    timer: null,
                                    init() {
                                        this.updateTimer();
                                        this.timer = setInterval(() => this.updateTimer(), 1000);
                                    },
                                    updateTimer() {
                                        const now = new Date().getTime();
                                        const diff = this.deadline - now;
                                        if (diff <= 0) {
                                            this.isExpired = true;
                                            this.timeLeft = '00:00:00';
                                            this.hours = 0;
                                            this.minutes = 0;
                                            this.seconds = 0;
                                            if (this.timer) clearInterval(this.timer);
                                            return;
                                        }
                                        const h = Math.floor(diff / (1000 * 60 * 60));
                                        const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                        const s = Math.floor((diff % (1000 * 60)) / 1000);
                                        this.hours = h;
                                        this.minutes = m;
                                        this.seconds = s;
                                        this.timeLeft = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                                    }
                                }"
                                class="rounded-2xl border p-5 shadow-xl transition-all duration-300 space-y-4"
                                :class="isExpired ? 'border-rose-500/40 bg-rose-950/20' : ((hours === 0 && minutes < 30) ? 'border-amber-500/40 bg-amber-950/20' : 'border-indigo-500/30 bg-white/5')"
                            >
                                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-4">
                                    <div class="space-y-1">
                                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">
                                            Tiến trình làm bài (Lần {{ $submission->attempt_number }}/{{ $submission->allowed_attempts ?? 2 }})
                                        </span>
                                        <p class="text-xs text-slate-300">
                                            Bắt đầu: <strong class="text-white">{{ $submission->started_at->format('H:i - d/m/Y') }}</strong> ·
                                            Hạn nộp: <strong class="text-amber-400">{{ $deadline?->format('H:i - d/m/Y') }}</strong>
                                        </p>
                                    </div>

                                    @if(! $isSubmitted)
                                        <div class="flex items-center gap-3">
                                            <div class="text-right">
                                                <span class="text-[11px] font-bold text-slate-400 uppercase block">Thời gian còn lại</span>
                                                <span class="font-mono text-xl font-black" :class="isExpired ? 'text-rose-400' : ((hours === 0 && minutes < 30) ? 'text-amber-400 animate-pulse' : 'text-emerald-400')" x-text="timeLeft">
                                                    --:--:--
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- CẢNH BÁO THỜI GIAN -->
                                @if(! $isSubmitted)
                                    <div>
                                        <template x-if="isExpired">
                                            <div class="rounded-xl bg-rose-500/20 border border-rose-500/30 p-3 text-xs font-bold text-rose-300 flex items-center gap-2">
                                                <svg class="h-5 w-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Bạn đã quá thời gian làm bài và chưa nộp bài. Lần làm này được tính là FAIL.</span>
                                            </div>
                                        </template>
                                        <template x-if="!isExpired && hours === 0 && minutes < 30">
                                            <div class="rounded-xl bg-amber-500/20 border border-amber-500/30 p-3 text-xs font-bold text-amber-300 flex items-center gap-2">
                                                <svg class="h-5 w-5 text-amber-400 shrink-0 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span>Còn <span x-text="minutes"></span> phút để nộp bài! Hãy khẩn trương nộp bài trước khi hết hạn.</span>
                                            </div>
                                        </template>
                                        <template x-if="!isExpired && (hours > 0 || minutes >= 30)">
                                            <div class="rounded-xl bg-indigo-500/10 border border-indigo-500/20 p-3 text-xs font-semibold text-indigo-300 flex items-center gap-2">
                                                <svg class="h-4 w-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Còn <span x-text="hours"></span> giờ <span x-text="minutes"></span> phút để hoàn thành và nộp bài tập.</span>
                                            </div>
                                        </template>
                                    </div>
                                @endif

                                <div class="pt-1 flex items-center justify-between">
                                    <a href="{{ route('courses.lessons.assignment.download', [$course, $lesson]) }}" class="inline-flex items-center gap-1.5 text-xs text-sky-400 hover:text-sky-300 hover:underline font-bold">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Tải lại file tài liệu bài tập (không reset thời gian)</span>
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- KẾT QUẢ / TRẠNG THÁI BÀI NỘP GẦN NHẤT -->
                        @if($submission && $submission->submitted_at)
                            <div class="bg-white/5 rounded-xl p-5 space-y-4 border border-white/10">
                                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                    <h3 class="font-bold text-white">Bài làm của bạn (Lần {{ $submission->attempt_number }}/{{ $submission->allowed_attempts ?? 2 }})</h3>
                                    
                                    <div>
                                        @if($submission->status === 'submitted')
                                            <span class="rounded-full bg-amber-500/20 px-3 py-1 text-xs font-bold text-amber-300 ring-1 ring-inset ring-amber-500/30">
                                                Đã nộp bài — Chờ giảng viên chấm
                                            </span>
                                        @elseif($submission->status === 'graded')
                                            @if($submission->result === 'pass')
                                                <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-bold text-emerald-300 ring-1 ring-inset ring-emerald-500/30">
                                                    ✓ ĐẠT — PASS
                                                </span>
                                            @else
                                                <span class="rounded-full bg-rose-500/20 px-3 py-1 text-xs font-bold text-rose-300 ring-1 ring-inset ring-rose-500/30">
                                                    ✕ KHÔNG ĐẠT — FAIL
                                                </span>
                                            @endif
                                        @elseif($submission->status === 'expired')
                                            <span class="rounded-full bg-rose-500/20 px-3 py-1 text-xs font-bold text-rose-300 ring-1 ring-inset ring-rose-500/30">
                                                Quá hạn (FAIL)
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-sm text-slate-300 space-y-2">
                                    <p><strong>Ngày nộp:</strong> {{ $submission->submitted_at?->format('d/m/Y H:i') }}</p>
                                    @if($submission->content)
                                        <div class="bg-black/20 p-3 rounded-lg text-xs font-mono whitespace-pre-line text-slate-300 mt-2">
                                            {{ $submission->content }}
                                        </div>
                                    @endif

                                    @if($submission->file_path)
                                        <div class="mt-2">
                                            <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-white hover:underline font-bold bg-white/10 px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Tải file bài làm đã nộp
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <!-- Phản hồi từ giảng viên -->
                                @if($submission->status === 'graded')
                                    <div class="rounded-xl border p-4 mt-3 {{ $submission->result === 'pass' ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-rose-500/10 border-rose-500/20' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold uppercase tracking-wider {{ $submission->result === 'pass' ? 'text-emerald-400' : 'text-rose-400' }}">
                                                {{ $submission->result === 'pass' ? 'Đạt — Bạn đã hoàn thành bài tập thực hành này (PASS)' : 'Không đạt — Lần làm bài này được đánh giá FAIL' }}
                                            </span>
                                        </div>
                                        @if($submission->feedback)
                                            <p class="text-sm text-slate-300 mt-2 whitespace-pre-line">{{ $submission->feedback }}</p>
                                        @endif
                                        @if($submission->graded_at)
                                            <time class="block text-[10px] text-slate-500 mt-2">Ngày đánh giá: {{ $submission->graded_at->format('d/m/Y H:i') }}</time>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- KHU VỰC LÀM LẠI BÀI TẬP (RETAKE) -->
                        @if($canRetake)
                            <div class="rounded-2xl border border-amber-500/30 bg-amber-950/20 p-5 space-y-3">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="text-sm font-bold text-amber-300">Cơ hội làm lại bài tập</h4>
                                        <p class="text-xs text-slate-300 mt-0.5">
                                            Bạn còn <strong class="text-amber-400">{{ ($submission->allowed_attempts - $submission->attempt_number) }}</strong> lần làm lại. Khi bấm làm lại, bạn sẽ có 6 giờ kể từ thời điểm bấm "Tải tài liệu về".
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('courses.lessons.assignment.retry', [$course, $lesson]) }}" onsubmit="return confirm('Bạn có chắc chắn muốn bắt đầu lần làm lại mới không? Bạn sẽ có 6 giờ làm bài sau khi bấm Tải tài liệu về.')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-5 py-2.5 text-xs font-bold text-slate-950 shadow-md transition hover:bg-amber-400 cursor-pointer">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Làm lại bài
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif($hasExhaustedAttempts)
                            <div class="rounded-2xl border border-rose-500/30 bg-rose-950/20 p-5 space-y-2 text-center">
                                <h4 class="text-sm font-bold text-rose-400 uppercase tracking-wider">Đã sử dụng hết lượt làm bài</h4>
                                <p class="text-xs text-slate-300">
                                    Bạn đã sử dụng hết số lần làm bài quy định (<strong class="text-white">{{ $submission->attempt_number }}/{{ $submission->allowed_attempts ?? 2 }}</strong> lần) và chưa đạt. Vui lòng liên hệ giảng viên phụ trách khóa học nếu bạn cần được cấp thêm lượt làm bài.
                                </p>
                            </div>
                        @endif

                        <!-- FORM NỘP BÀI TẬP -->
                        @if((! $submission || ! $submission->submitted_at) && ! ($submission && $submission->isExpired()))
                            <form method="POST" action="{{ route('courses.lessons.assignment.submit', [$course, $lesson]) }}" enctype="multipart/form-data" class="space-y-4 bg-white/5 rounded-xl p-5 border border-white/10">
                                @csrf
                                <h3 class="font-bold text-white border-b border-white/10 pb-2 flex items-center justify-between">
                                    <span>Nộp bài tập thực hành (Lần {{ $submission->attempt_number ?? 1 }}/{{ $submission->allowed_attempts ?? 2 }})</span>
                                    <span class="text-xs font-normal text-slate-400">Tối đa 10MB</span>
                                </h3>

                                <div>
                                    <label for="code_language" class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Ngôn ngữ nội dung</label>
                                    <select name="code_language" id="code_language" class="mb-3 w-full rounded-xl border-white/10 bg-slate-900 text-sm text-white">
                                        @foreach(['plaintext' => 'Văn bản thường', 'php' => 'PHP', 'javascript' => 'JavaScript', 'typescript' => 'TypeScript', 'python' => 'Python', 'java' => 'Java', 'c' => 'C', 'cpp' => 'C++', 'csharp' => 'C#', 'html' => 'HTML', 'css' => 'CSS', 'sql' => 'SQL', 'json' => 'JSON', 'bash' => 'Bash'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('code_language', $submission?->code_language ?? 'plaintext') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <label for="content" class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Nội dung / Ghi chú bài làm</label>
                                    <textarea name="content" id="content" rows="6" class="w-full rounded-xl bg-slate-900 border-white/10 font-mono text-sm text-white placeholder-slate-500 focus:border-[#0056D2] focus:ring-0" placeholder="Viết mô tả hoặc dán mã nguồn bài làm của bạn vào đây (nếu có)...">{{ old('content', $submission?->content) }}</textarea>
                                    @error('content')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="file" class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Tệp bài làm đính kèm (ZIP, PDF, DOCX, Ảnh...)</label>
                                    <input type="file" name="file" id="file" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0056D2] file:text-white hover:file:bg-[#0046B8] cursor-pointer">
                                    @error('file')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="pt-2">
                                    @if(! $hasStarted)
                                        <button type="button" disabled class="cursor-not-allowed inline-flex items-center justify-center rounded-xl bg-slate-700 px-6 py-2.5 text-sm font-bold text-slate-400 shadow-sm" title="Vui lòng bấm 'Tải tài liệu về' ở trên trước khi nộp bài">
                                            Vui lòng tải tài liệu trước khi nộp
                                        </button>
                                    @else
                                        <button type="submit" class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-[#0056D2] px-6 py-2.5 text-sm font-bold text-white transition hover:bg-[#0046B8] shadow-md">
                                            Gửi bài tập (Lần {{ $submission->attempt_number }})
                                        </button>
                                    @endif
                                </div>
                            </form>
                        @elseif($submission && $submission->isExpired() && ! $submission->submitted_at)
                            <div class="rounded-xl border border-rose-500/30 bg-rose-950/20 p-5 text-center space-y-2">
                                <h4 class="text-sm font-bold text-rose-400 uppercase tracking-wider">Đã hết thời gian làm bài (Lần {{ $submission->attempt_number }})</h4>
                                <p class="text-xs text-slate-400">Bạn đã quá thời hạn 6 giờ để nộp bài tập cho lần này. Form nộp bài đã được khóa.</p>
                            </div>
                        @endif

                        <!-- BẢNG LỊCH SỬ CÁC LẦN LÀM BÀI (ATTEMPTS HISTORY) -->
                        @if(isset($assignmentSubmissions) && $assignmentSubmissions->count() > 1)
                            <div class="bg-white/5 rounded-2xl border border-white/10 p-5 space-y-4">
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-white/10 pb-3">
                                    Lịch sử các lần làm bài ({{ $assignmentSubmissions->count() }} lần)
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="text-slate-400 uppercase border-b border-white/10">
                                            <tr>
                                                <th class="pb-2">Lần làm</th>
                                                <th class="pb-2">Bắt đầu / Hạn nộp</th>
                                                <th class="pb-2">Ngày nộp</th>
                                                <th class="pb-2">Kết quả</th>
                                                <th class="pb-2">Tệp bài làm</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5 text-slate-300">
                                            @foreach($assignmentSubmissions as $attempt)
                                                <tr>
                                                    <td class="py-3 font-bold text-white">
                                                        Lần {{ $attempt->attempt_number }}
                                                        @if($attempt->granter)
                                                            <span class="block text-[10px] text-emerald-400 font-normal">Cấp bởi GV</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3">
                                                        @if($attempt->started_at)
                                                            <span class="block">{{ $attempt->started_at->format('H:i d/m/Y') }}</span>
                                                            <span class="text-[10px] text-slate-500">Hạn: {{ $attempt->getDeadline()?->format('H:i d/m/Y') }}</span>
                                                        @else
                                                            <span class="text-slate-500">Chưa bắt đầu</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3">
                                                        {{ $attempt->submitted_at?->format('H:i d/m/Y') ?? '—' }}
                                                    </td>
                                                    <td class="py-3">
                                                        @if($attempt->result === 'pass')
                                                            <span class="text-emerald-400 font-bold">✓ PASS</span>
                                                        @elseif($attempt->result === 'fail')
                                                            <span class="text-rose-400 font-bold">✕ FAIL</span>
                                                        @elseif($attempt->isExpired())
                                                            <span class="text-rose-400 font-bold">Quá hạn</span>
                                                        @elseif($attempt->status === 'submitted')
                                                            <span class="text-amber-400 font-bold">Chờ chấm</span>
                                                        @else
                                                            <span class="text-slate-500">Đang làm</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3">
                                                        @if($attempt->file_path)
                                                            <a href="{{ Storage::url($attempt->file_path) }}" target="_blank" class="text-sky-400 hover:underline font-medium inline-flex items-center gap-1">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                                Tải file
                                                            </a>
                                                        @else
                                                            <span class="text-slate-500">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($lesson->type === 'document')
                <div class="flex min-h-[320px] items-center justify-center bg-[#1c1d1f] p-6 lg:min-h-[calc(100vh-14rem)]">
                    <div class="max-w-lg text-center text-white">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-white backdrop-blur-sm border border-white/10 shadow-lg">
                            <svg class="h-8 w-8 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold">{{ $lesson->title }}</h2>
                        <p class="mt-2 text-sm text-white/70">Tài liệu học tập / Bài đọc đính kèm</p>
                        @if($lesson->document_file)
                            @php
                                $fileUrl = asset('storage/'.$lesson->document_file);
                                $ext = strtolower(pathinfo($lesson->document_file, PATHINFO_EXTENSION));
                                $downloadName = \Illuminate\Support\Str::slug($lesson->title ?: 'tai-lieu') . ($ext ? '.' . $ext : '');

                                $fileSizeFormatted = null;
                                try {
                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($lesson->document_file)) {
                                        $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($lesson->document_file);
                                        if ($bytes >= 1048576) {
                                            $fileSizeFormatted = number_format($bytes / 1048576, 2) . ' MB';
                                        } elseif ($bytes >= 1024) {
                                            $fileSizeFormatted = number_format($bytes / 1024, 1) . ' KB';
                                        } else {
                                            $fileSizeFormatted = $bytes . ' B';
                                        }
                                    }
                                } catch (\Throwable $e) {}
                            @endphp

                            <div class="mt-4 inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-xs text-white/90 border border-white/10 backdrop-blur-xs">
                                <svg class="h-4 w-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span class="font-semibold truncate max-w-xs">{{ $lesson->title . ($ext ? '.' . $ext : '') }}</span>
                                @if($fileSizeFormatted)
                                    <span class="text-white/60">• {{ $fileSizeFormatted }}</span>
                                @endif
                            </div>

                            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#0056D2] px-6 text-sm font-bold text-white shadow-lg transition hover:bg-[#0046B8]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span>Mở xem tài liệu ({{ strtoupper($ext ?: 'FILE') }})</span>
                                </a>
                                <a href="{{ $fileUrl }}" download="{{ $downloadName }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-white/15 border border-white/20 px-5 text-sm font-bold text-white transition hover:bg-white/25">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Tải về máy</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex min-h-[320px] items-center justify-center bg-[#1c1d1f] p-6 text-white/80">
                    Loại bài học này chưa được hỗ trợ trong player.
                </div>
            @endif

            @if($canAccessLesson)
                <x-learning.lesson-tabs
                    :lesson="$lesson"
                    :course="$course"
                    :section-title="$sectionTitle"
                    :navigation="$navigation"
                    :lesson-state="$lessonState"
                    :is-enrolled="$isEnrolled"
                    :can-access-lesson="$canAccessLesson"
                    :can-use-lesson-ai="$canUseLessonAi"
                    :ai-summary-url="$aiSummaryUrl"
                    :ai-explain-url="$aiExplainUrl"
                    :discussions="$discussions"
                    :active-discussion="$activeDiscussion"
                    :lesson-comments="$lessonComments"
                    :can-use-lesson-notes="$canUseLessonNotes"
                    :lesson-notes="$lessonNotes"
                    :lesson-notes-index-url="$lessonNotesIndexUrl"
                    :lesson-notes-store-url="$lessonNotesStoreUrl"
                />
            @endif
        </main>

        <x-learning.sidebar
            :sections="$curriculumSections"
            :course-progress="$courseProgress"
            :completed-lessons="$completedLessons"
            :total-lessons="$totalLessons"
            :course="$course"
            :lesson="$lesson"
            :is-enrolled="$isEnrolled"
            :course-discussion="$courseDiscussion ?? $activeDiscussion"
        />
    </div>
</div>
@endsection
