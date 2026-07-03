<x-instructor-layout :title="'Quiz - '.$lesson->title" page-title="Quan ly quiz" :breadcrumb="$course->title">

@php
    $quizTitle = old('title', $quiz->title ?? $lesson->title);
    $typeLabels = [
        'single_choice' => 'single_choice',
        'multiple_choice' => 'multiple_choice',
        'true_false' => 'true_false',
    ];
@endphp

<div class="space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Lesson quiz</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">{{ $lesson->title }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $course->title }}</p>
            </div>
            <a href="{{ route('instructor.courses.curriculum', $course) }}"
               class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                Quay lai curriculum
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <p class="font-bold">Vui long kiem tra lai thong tin.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('instructor.courses.lessons.quiz.store', [$course, $lesson]) }}"
          class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @csrf
        <div class="grid gap-4 lg:grid-cols-2">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Tieu de quiz</span>
                <input type="text" name="title" value="{{ $quizTitle }}" required maxlength="255"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
            </label>
            <div class="grid gap-4 sm:grid-cols-3">
                <label class="block">
                    <span class="mb-1.5 block text-sm font-bold text-slate-700">Diem dat (%)</span>
                    <input type="number" name="pass_score" value="{{ old('pass_score', $quiz->pass_score ?? 70) }}" min="0" max="100" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-bold text-slate-700">Thoi gian (phut)</span>
                    <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', $quiz->time_limit_minutes ?? '') }}" min="1" max="1440"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-bold text-slate-700">So lan lam</span>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts ?? '') }}" min="1" max="100"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                </label>
            </div>
        </div>

        <label class="mt-4 block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">Mo ta quiz</span>
            <textarea name="description" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500">{{ old('description', $quiz->description ?? '') }}</textarea>
        </label>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <label class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $quiz->is_active ?? true)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Dang bat
            </label>
            <button type="submit"
                    class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-700">
                Luu quiz
            </button>
        </div>
    </form>

    @if($quiz)
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-950">Danh sach cau hoi</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $quiz->questions->count() }} cau hoi</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                    Tong diem: {{ $quiz->questions->sum('points') }}
                </span>
            </div>

            <form method="POST" action="{{ route('instructor.quizzes.questions.store', $quiz) }}" class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                @csrf
                <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px_120px_120px]">
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-bold text-slate-700">Noi dung cau hoi</span>
                        <input type="text" name="question_text" required maxlength="10000"
                               class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-bold text-slate-700">Loai cau hoi</span>
                        <select name="question_type" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                            @foreach($questionTypes as $value => $label)
                                <option value="{{ $value }}">{{ $typeLabels[$value] ?? $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-bold text-slate-700">Diem</span>
                        <input type="number" name="score" value="1" min="1" max="1000" required
                               class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-bold text-slate-700">Thu tu</span>
                        <input type="number" name="sort_order" value="{{ $quiz->questions->count() }}" min="0"
                               class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                    </label>
                </div>
                <label class="mt-4 block">
                    <span class="mb-1.5 block text-sm font-bold text-slate-700">Giai thich dap an</span>
                    <textarea name="explanation" rows="2" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500"></textarea>
                </label>
                <button type="submit" class="mt-4 rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">
                    Them cau hoi
                </button>
            </form>
        </section>

        <div class="space-y-4">
            @forelse($quiz->questions as $question)
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700 ring-1 ring-violet-200">{{ $question->form_type }}</span>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $question->points }} diem</span>
                                <span class="text-xs font-semibold text-slate-500">sort_order: {{ $question->sort_order }}</span>
                            </div>
                            <h4 class="mt-2 text-base font-bold text-slate-950">{{ $question->question }}</h4>
                            @if($question->explanation)
                                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $question->explanation }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            <details>
                                <summary class="inline-flex min-h-10 cursor-pointer list-none items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                    Sua cau hoi
                                </summary>
                                <form method="POST" action="{{ route('instructor.quiz-questions.update', $question) }}" class="mt-3 space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-4 lg:w-[620px]">
                                    @csrf
                                    @method('PUT')
                                    <label class="block">
                                        <span class="mb-1 block text-xs font-bold text-slate-600">Noi dung cau hoi</span>
                                        <textarea name="question_text" rows="3" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">{{ $question->question }}</textarea>
                                    </label>
                                    <div class="grid gap-3 sm:grid-cols-3">
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Loai</span>
                                            <select name="question_type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                                @foreach($questionTypes as $value => $label)
                                                    <option value="{{ $value }}" @selected($question->form_type === $value)>{{ $typeLabels[$value] ?? $label }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Diem</span>
                                            <input type="number" name="score" value="{{ $question->points }}" min="1" max="1000" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                        </label>
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Thu tu</span>
                                            <input type="number" name="sort_order" value="{{ $question->sort_order }}" min="0" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                        </label>
                                    </div>
                                    <label class="block">
                                        <span class="mb-1 block text-xs font-bold text-slate-600">Giai thich</span>
                                        <textarea name="explanation" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">{{ $question->explanation }}</textarea>
                                    </label>
                                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Luu cau hoi</button>
                                </form>
                            </details>
                            <form method="POST" action="{{ route('instructor.quiz-questions.destroy', $question) }}" onsubmit="return confirm('Xoa cau hoi nay?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-rose-200 px-4 py-2 text-sm font-bold text-rose-700 hover:bg-rose-50">
                                    Xoa
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @foreach($question->options as $answer)
                            <form method="POST" action="{{ route('instructor.quiz-answers.update', $answer) }}" class="grid gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 lg:grid-cols-[minmax(0,1fr)_120px_140px_90px_auto] lg:items-center">
                                @csrf
                                @method('PUT')
                                <input type="text" name="answer_text" value="{{ $answer->option_text }}" required
                                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                <input type="number" name="sort_order" value="{{ $answer->sort_order }}" min="0"
                                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                    <input type="checkbox" name="is_correct" value="1" @checked($answer->is_correct) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    Dap an dung
                                </label>
                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-bold text-white hover:bg-emerald-700">Luu</button>
                                <button form="delete-answer-{{ $answer->id }}" type="submit" class="rounded-lg border border-rose-200 px-3 py-2 text-sm font-bold text-rose-700 hover:bg-rose-50">Xoa</button>
                            </form>
                            <form id="delete-answer-{{ $answer->id }}" method="POST" action="{{ route('instructor.quiz-answers.destroy', $answer) }}" onsubmit="return confirm('Xoa dap an nay?')" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach

                        <form method="POST" action="{{ route('instructor.quiz-questions.answers.store', $question) }}" class="grid gap-3 rounded-lg border border-dashed border-slate-300 p-3 lg:grid-cols-[minmax(0,1fr)_120px_140px_auto] lg:items-center">
                            @csrf
                            <input type="text" name="answer_text" placeholder="Nhap dap an" required
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                            <input type="number" name="sort_order" value="{{ $question->options->count() }}" min="0"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="checkbox" name="is_correct" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Dap an dung
                            </label>
                            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">Them dap an</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500">
                    Chua co cau hoi nao.
                </div>
            @endforelse
        </div>
    @endif
</div>

</x-instructor-layout>
