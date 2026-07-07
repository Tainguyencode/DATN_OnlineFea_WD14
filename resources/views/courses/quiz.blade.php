@extends('layouts.app')

@section('title', $quiz->title . ' - ' . $course->title)

@section('content')
@php
    $totalScore = $quiz->questions->sum('points');
    $oldAnswers = old('answers', []);
    $isChecked = function ($question, $answer) use ($oldAnswers) {
        $selected = $oldAnswers[$question->id] ?? [];
        $selected = is_array($selected) ? $selected : [$selected];

        return in_array((string) $answer->id, array_map('strval', $selected), true);
    };
@endphp

<section class="bg-slate-50 py-8 dark:bg-[#0a0a0a]">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <a href="{{ route('courses.show', $course->slug) }}" class="text-sm font-bold text-indigo-600 hover:underline dark:text-indigo-300">
                    Quay lai khoa hoc
                </a>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-3xl">{{ $quiz->title }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $lesson->title }}</p>
            </div>
            @if($lesson->is_preview)
                <span class="inline-flex w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30">
                    Preview
                </span>
            @endif
        </div>

        @if(session('error'))
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
                <p class="font-bold">Vui long kiem tra lai bai lam.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-[#161615] sm:p-6">
            <div class="grid gap-3 sm:grid-cols-4">
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/70">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Cau hoi</span>
                    <strong class="mt-1 block text-2xl text-slate-950 dark:text-white">{{ $quiz->questions->count() }}</strong>
                </div>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/70">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tong diem</span>
                    <strong class="mt-1 block text-2xl text-slate-950 dark:text-white">{{ $totalScore }}</strong>
                </div>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/70">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Diem dat</span>
                    <strong class="mt-1 block text-2xl text-slate-950 dark:text-white">{{ $quiz->pass_score }}%</strong>
                </div>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/70">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Lan lam</span>
                    <strong class="mt-1 block text-2xl text-slate-950 dark:text-white">
                        {{ $attemptsCount }}@if($quiz->max_attempts)/{{ $quiz->max_attempts }}@endif
                    </strong>
                </div>
            </div>

            @if($quiz->description)
                <div class="mt-5 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $quiz->description }}</div>
            @endif
        </article>

        @if($canSubmit)
            <form method="POST" action="{{ route('learn.lessons.quiz.submit', [$course->slug, $lesson]) }}" class="mt-6 space-y-5">
                @csrf
        @else
            <div class="mt-6 space-y-5">
        @endif

            @forelse($quiz->questions as $question)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-[#161615] sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-500/30">
                                {{ $question->form_type }}
                            </span>
                            <h2 class="mt-3 text-base font-extrabold text-slate-950 dark:text-white">
                                Cau {{ $loop->iteration }}. {{ $question->question }}
                            </h2>
                        </div>
                        <span class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $question->points }} diem</span>
                    </div>

                    <div class="mt-4 space-y-2">
                        @forelse($question->options as $answer)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 text-sm transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900/70">
                                <input
                                    type="{{ $question->type === \App\Models\QuizQuestion::TYPE_MULTIPLE ? 'checkbox' : 'radio' }}"
                                    name="{{ $question->type === \App\Models\QuizQuestion::TYPE_MULTIPLE ? 'answers['.$question->id.'][]' : 'answers['.$question->id.']' }}"
                                    value="{{ $answer->id }}"
                                    @checked($isChecked($question, $answer))
                                    @disabled(! $canSubmit)
                                    class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="leading-6 text-slate-700 dark:text-slate-200">{{ $answer->option_text }}</span>
                            </label>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                Cau hoi nay chua co dap an.
                            </div>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500 dark:border-slate-800 dark:bg-[#161615] dark:text-slate-400">
                    Quiz nay chua co cau hoi.
                </div>
            @endforelse

        @if($canSubmit)
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex h-12 items-center justify-center rounded-xl bg-indigo-600 px-6 text-sm font-extrabold text-white transition hover:bg-indigo-700">
                        Nop bai
                    </button>
                </div>
            </form>
        @else
            </div>

            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                @if($attemptLimitReached)
                    <p class="font-bold">Ban da het so lan lam quiz nay.</p>
                @elseif(! auth()->check())
                    <p class="font-bold">Dang nhap de lam quiz va luu ket qua.</p>
                    <a href="{{ route('login') }}" class="mt-3 inline-flex h-10 items-center rounded-xl bg-indigo-600 px-4 font-extrabold text-white transition hover:bg-indigo-700">Dang nhap</a>
                @elseif(auth()->user()->isStudent() && ! $isEnrolled)
                    <p class="font-bold">Ban can dang ky khoa hoc de nop quiz.</p>
                    <form method="POST" action="{{ route('courses.enroll', $course) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="inline-flex h-10 items-center rounded-xl bg-indigo-600 px-4 font-extrabold text-white transition hover:bg-indigo-700">Dang ky hoc</button>
                    </form>
                @else
                    <p class="font-bold">Tai khoan hien tai khong phai hoc vien cua khoa hoc nay.</p>
                    <a href="{{ auth()->user()->dashboardUrl() }}" class="mt-3 inline-flex h-10 items-center rounded-xl bg-slate-950 px-4 font-extrabold text-white transition hover:bg-slate-800">Vao Dashboard</a>
                @endif
            </div>
        @endif
    </div>
</section>
@endsection
