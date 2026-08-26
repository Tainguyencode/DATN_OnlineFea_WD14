<x-instructor-layout :title="'Bài làm Quiz - ' . $student->name" page-title="Chi tiết bài làm Quiz" :breadcrumb="$course->title">
@php
    $questions = $review['questions'] ?? [];
    $allAttempts = $review['all_attempts'] ?? [];
    $currentAttemptNumber = $review['attempt_number'] ?? 1;
@endphp

<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    {{-- Header & Back button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/60">
        <a href="{{ route('instructor.courses.students.detail', [$course, $student]) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-600 transition-colors group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại thông tin học viên ({{ $student->name }})
        </a>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-xs font-semibold text-slate-700">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
            Khóa học: <span class="font-bold text-slate-900 ml-1">{{ $course->title }}</span>
        </div>
    </div>

    {{-- Student & Quiz Info Card --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            {{-- Student Info --}}
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-lg font-black text-white shadow-md">
                    {{ strtoupper(mb_substr($student->name, 0, 2, 'UTF-8')) }}
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">Học viên</span>
                    <h2 class="text-xl font-black text-slate-900">{{ $student->name }}</h2>
                    <p class="text-xs text-slate-500 font-medium">{{ $student->email }}</p>
                </div>
            </div>

            {{-- Attempt Outcome Badge --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="text-left sm:text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Thời gian làm bài</span>
                    <p class="text-xs font-bold text-slate-700">
                        {{ $attempt->completed_at?->format('d/m/Y H:i:s') ?? $attempt->created_at?->format('d/m/Y H:i:s') }}
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-black {{ $attempt->passed ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                    @if($attempt->passed)
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>KẾT QUẢ: ĐẠT</span>
                    @else
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>KẾT QUẢ: CHƯA ĐẠT</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Multiple Attempts Switcher --}}
        @if(count($allAttempts) > 1)
            <div class="mt-6 pt-5 border-t border-slate-100">
                <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5">
                    Chọn lần làm bài để xem chi tiết:
                </span>
                <div class="flex flex-wrap gap-2">
                    @foreach($allAttempts as $att)
                        @php
                            $isCurrent = (int)$att['id'] === (int)$attempt->id;
                        @endphp
                        <a href="{{ route('instructor.courses.students.quiz-attempt', [$course, $student, $quiz, $att['id']]) }}"
                           class="inline-flex items-center gap-2 rounded-xl px-3.5 py-1.5 text-xs font-bold transition border {{ $isCurrent ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200' }}">
                            <span>Lần {{ $att['attempt_number'] }}</span>
                            <span class="px-1.5 py-0.2 rounded text-[11px] {{ $isCurrent ? 'bg-white/20 text-white' : ($att['passed'] ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ number_format((float)$att['percent'], 0) }}%
                            </span>
                            @if($isCurrent)
                                <span class="text-[9px] uppercase bg-white text-indigo-700 font-black px-1 rounded">Đang xem</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Bài Quiz</span>
            <strong class="mt-1 block text-base font-black text-slate-900 truncate" title="{{ $quiz->title }}">{{ $quiz->title }}</strong>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Điểm số</span>
            <strong class="mt-1 block text-2xl font-black text-slate-900">{{ $attempt->score }} / {{ $attempt->total_score }}đ</strong>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tỷ lệ đạt</span>
            <strong class="mt-1 block text-2xl font-black {{ $attempt->passed ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ number_format((float)$attempt->percent, 1) }}%
            </strong>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Số câu đúng</span>
            <strong class="mt-1 block text-2xl font-black text-slate-900">
                {{ $review['correct_questions_count'] ?? 0 }} / {{ $review['total_questions'] ?? count($questions) }}
            </strong>
        </div>
    </div>

    {{-- Questions Detail Section --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900">Chi tiết từng câu hỏi (Lần làm #{{ $currentAttemptNumber }})</h3>
            <span class="text-xs font-semibold text-slate-500">Tổng cộng {{ count($questions) }} câu hỏi</span>
        </div>

        @foreach($questions as $q)
            <div class="rounded-2xl border {{ $q['is_correct'] ? 'border-emerald-200 bg-white' : 'border-rose-200 bg-white' }} p-5 sm:p-6 shadow-2xs transition">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-md px-2.5 py-0.5 text-xs font-extrabold ring-1 {{ $q['is_correct'] ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-rose-50 text-rose-700 ring-rose-200' }}">
                                @if($q['is_correct'])
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    ĐÚNG
                                @else
                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    SAI
                                @endif
                            </span>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                {{ $q['form_type'] ?? $q['type'] }}
                            </span>
                        </div>
                        <h4 class="mt-2.5 text-base font-bold text-slate-900 leading-relaxed">
                            Câu {{ $q['question_number'] }}. {{ $q['question'] }}
                        </h4>
                    </div>
                    <span class="shrink-0 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700">
                        {{ $q['points'] }} điểm
                    </span>
                </div>

                {{-- Options --}}
                <div class="mt-4 space-y-2.5">
                    @foreach($q['options'] as $option)
                        @php
                            $isSelected = $option['is_selected'];
                            $isCorrect = $option['is_correct'];

                            if ($isSelected && $isCorrect) {
                                $boxClass = 'border-emerald-500 bg-emerald-50/70 text-emerald-950 ring-1 ring-emerald-500/40';
                            } elseif ($isSelected && !$isCorrect) {
                                $boxClass = 'border-rose-500 bg-rose-50/70 text-rose-950 ring-1 ring-rose-500/40';
                            } elseif (!$isSelected && $isCorrect) {
                                $boxClass = 'border-emerald-400 bg-emerald-50/30 text-emerald-900 border-dashed';
                            } else {
                                $boxClass = 'border-slate-200 text-slate-700 bg-slate-50/30';
                            }
                        @endphp

                        <div class="flex items-start justify-between gap-3 rounded-xl border p-3.5 text-sm {{ $boxClass }}">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <div class="mt-0.5 shrink-0">
                                    @if($isSelected && $isCorrect)
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-black shadow-xs">✓</span>
                                    @elseif($isSelected && !$isCorrect)
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-600 text-white text-xs font-black shadow-xs">✗</span>
                                    @elseif(!$isSelected && $isCorrect)
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-700 text-xs font-black ring-1 ring-emerald-500/40">✓</span>
                                    @else
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-300 text-slate-400 text-xs"></span>
                                    @endif
                                </div>
                                <span class="leading-6 font-medium">{{ $option['option_text'] }}</span>
                            </div>

                            <div class="flex shrink-0 flex-wrap justify-end gap-1.5">
                                @if($isSelected && $isCorrect)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-600 px-2 py-0.5 text-xs font-bold text-white shadow-xs">
                                        ✓ Học viên chọn đúng
                                    </span>
                                @elseif($isSelected && !$isCorrect)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-rose-600 px-2 py-0.5 text-xs font-bold text-white shadow-xs">
                                        ✗ Học viên chọn sai
                                    </span>
                                @elseif(!$isSelected && $isCorrect)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-0.5 text-xs font-bold">
                                        ✓ Đáp án đúng
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Explanation --}}
                @if(!empty($q['explanation']))
                    <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-700 border border-slate-200/80 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="text-slate-900 font-bold">Giải thích:</strong>
                            <span class="ml-1">{{ $q['explanation'] }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Footer Actions --}}
    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
        <a href="{{ route('instructor.courses.students.detail', [$course, $student]) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
            ← Quay lại chi tiết học viên
        </a>
        <a href="{{ route('instructor.courses.students', $course) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-800 transition">
            Danh sách học viên khóa học
        </a>
    </div>
</div>
</x-instructor-layout>
