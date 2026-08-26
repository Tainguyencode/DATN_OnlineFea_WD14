@props([
    'quizContext',
    'lesson',
])

@if($quizContext)
    <div
        class="learning-quiz-panel min-h-[320px] bg-[#1c1d1f] p-4 sm:p-6 lg:min-h-[calc(100vh-14rem)]"
        data-quiz-player
        data-quiz='@json($quizContext)'
    >
        <div data-quiz-intro class="mx-auto flex min-h-[280px] max-w-2xl flex-col justify-center py-4 text-white">
            <p class="text-xs font-semibold uppercase tracking-wide text-violet-300">Quiz</p>
            <h2 class="mt-2 text-2xl font-bold">{{ $quizContext['title'] }}</h2>
            @if($quizContext['description'])
                <p class="mt-3 text-sm leading-6 text-white/80">{{ $quizContext['description'] }}</p>
            @endif
            <dl class="mt-6 grid gap-3 sm:grid-cols-2">
                <div class="rounded border border-white/10 bg-white/5 p-3">
                    <dt class="text-xs text-white/60">Số câu hỏi</dt>
                    <dd class="mt-1 text-lg font-bold">{{ $quizContext['total_questions'] }}</dd>
                </div>
                <div class="rounded border border-white/10 bg-white/5 p-3">
                    <dt class="text-xs text-white/60">Điểm đạt</dt>
                    <dd class="mt-1 text-lg font-bold">{{ $quizContext['pass_score'] }}%</dd>
                </div>
                @if($quizContext['time_limit_minutes'])
                    <div class="rounded border border-white/10 bg-white/5 p-3">
                        <dt class="text-xs text-white/60">Thời gian</dt>
                        <dd class="mt-1 text-lg font-bold">{{ $quizContext['time_limit_minutes'] }} phút</dd>
                    </div>
                @endif
                <div class="rounded border border-white/10 bg-white/5 p-3">
                    <dt class="text-xs text-white/60">Lần làm</dt>
                    <dd class="mt-1 text-lg font-bold">
                        {{ $quizContext['attempts_count'] }}@if($quizContext['max_attempts'])/{{ $quizContext['max_attempts'] }}@endif
                    </dd>
                </div>
            </dl>
            @if($quizContext['can_take'])
                <button type="button" data-quiz-start class="mt-6 inline-flex h-11 items-center justify-center rounded bg-[#0056D2] px-6 text-sm font-bold text-white transition hover:bg-[#0046B8]">
                    {{ !empty($quizContext['previous_attempts']) ? 'Làm lại bài quiz' : 'Bắt đầu làm bài' }}
                </button>
            @elseif($quizContext['attempt_limit_reached'])
                <p class="mt-6 rounded border border-amber-400/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">Bạn đã hết số lần làm quiz này.</p>
            @else
                <p class="mt-6 rounded border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/80">Đăng nhập và đăng ký khóa học để làm quiz.</p>
            @endif

            @if(!empty($quizContext['previous_attempts']))
                <div class="mt-6 border-t border-white/10 pt-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white/70">Lịch sử làm bài</h3>
                    <div class="mt-3 space-y-2">
                        @foreach($quizContext['previous_attempts'] as $att)
                            <div class="flex items-center justify-between rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-white">Lần {{ $att['attempt_number'] }}</span>
                                    <span class="rounded px-2 py-0.5 font-bold {{ $att['passed'] ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                        {{ number_format($att['percent'], 0) }}% - {{ $att['passed'] ? 'Đạt' : 'Chưa đạt' }}
                                    </span>
                                    <span class="text-white/50">{{ $att['completed_at'] }}</span>
                                </div>
                                <a href="{{ $att['review_url'] }}" class="inline-flex items-center gap-1 font-bold text-sky-400 hover:text-sky-300 hover:underline">
                                    Xem lại bài làm →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div data-quiz-active class="mx-auto max-w-2xl text-white" hidden>
            <div class="mb-4 flex items-center justify-between gap-3">
                <p class="text-sm font-semibold" data-quiz-progress-label>Câu 1 / 1</p>
                <p class="text-sm font-semibold text-violet-300" data-quiz-timer hidden></p>
            </div>
            <div class="mb-3 h-1.5 overflow-hidden rounded-full bg-white/20">
                <div class="h-full rounded-full bg-[#0056D2] transition-all" data-quiz-progress-bar style="width: 0%"></div>
            </div>
            <div data-quiz-question-container></div>
            <div class="mt-6 flex flex-wrap justify-between gap-3">
                <button type="button" data-quiz-prev class="rounded border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10 disabled:opacity-40" disabled>Câu trước</button>
                <button type="button" data-quiz-next class="rounded bg-[#0056D2] px-4 py-2 text-sm font-bold text-white hover:bg-[#0046B8]">Câu tiếp theo</button>
            </div>
        </div>

        <div data-quiz-result class="mx-auto max-w-2xl text-white" hidden></div>
    </div>
@endif
