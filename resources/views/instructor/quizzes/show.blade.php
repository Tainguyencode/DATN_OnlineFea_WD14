<x-instructor-layout :title="'Quiz - ' . $lesson->title" page-title="Quan ly quiz" :breadcrumb="$course->title">

    @php
        $quizTitle = old('title', $quiz->title ?? $lesson->title);
        $typeLabels = [
            'single_choice' => 'Chỉ chọn một',
            'multiple_choice' => 'Chọn nhiều',
            'true_false' => 'Đúng/Sai',
        ];
        $questions = $quiz?->questions ?? collect();
        $quizValidation ??= ['is_complete' => false, 'errors' => ['Quiz chưa sẵn sàng.'], 'warnings' => []];
        $mutationsLocked ??= false;
        $canSaveQuiz = $quiz && !$mutationsLocked;
    @endphp

    <div class="space-y-6 pb-28">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Lesson quiz</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">{{ $lesson->title }}</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ $course->title }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if ($authoringVersion)
                            <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700 ring-1 ring-violet-200">
                                @if ($authoringVersion->status === \App\Models\QuizVersion::STATUS_DRAFT)
                                    Bản đang chỉnh sửa: V{{ $authoringVersion->version }} — Bản nháp
                                @else
                                    Đang xem V{{ $authoringVersion->version }} — Đã xuất bản
                                @endif
                            </span>
                        @endif
                        @if ($publishedVersion)
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                                Đang áp dụng cho học viên: V{{ $publishedVersion->version }}
                            </span>
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                Chưa có phiên bản đã xuất bản
                            </span>
                        @endif
                        @if ($reviewUpdate?->isPending())
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">Đang chờ duyệt</span>
                        @elseif ($reviewUpdate?->isRejected())
                            <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">Bị từ chối — có thể tiếp tục sửa</span>
                        @elseif ($reviewUpdate?->isApproved())
                            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-200">Đã duyệt — chờ kích hoạt an toàn</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('instructor.courses.curriculum', $course) }}"
                    class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    Quay lại đề cương
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('instructor.courses.lessons.quiz.store', [$course, $lesson]) }}"
            class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            <fieldset @disabled($mutationsLocked)>
            <div class="grid gap-4 lg:grid-cols-2">
                <label class="block">
                    <span class="mb-1.5 block text-sm font-bold text-slate-700">Tiêu đề quiz</span>
                    <input type="text" name="title" value="{{ $quizTitle }}" maxlength="255"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('title') border-rose-500 focus:border-rose-500 @else border-slate-300 @enderror">
                    @error('title') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </label>
                <div class="grid gap-4 sm:grid-cols-3">
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-bold text-slate-700">Điểm đạt (%)</span>
                        <input type="number" name="pass_score" value="{{ old('pass_score', $quiz->pass_score ?? 70) }}"
                            min="0" max="100"
                            class="w-full rounded-lg border px-3 py-2.5 text-sm outline-none focus:border-emerald-500 @error('pass_score') border-rose-500 focus:border-rose-500 @else border-slate-300 @enderror">
                        @error('pass_score') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-bold text-slate-700">Thoi gian (phut)</span>
                        <input type="number" name="time_limit_minutes"
                            value="{{ old('time_limit_minutes', $quiz->time_limit_minutes ?? '') }}" min="1"
                            max="1440"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-bold text-slate-700">So lan lam</span>
                        <input type="number" name="max_attempts"
                            value="{{ old('max_attempts', $quiz->max_attempts ?? '') }}" min="1" max="100"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                    </label>
                </div>
            </div>

            <label class="mt-4 block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Mo ta quiz</span>
                <textarea name="description" rows="4"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500">{{ old('description', $quiz->description ?? '') }}</textarea>
            </label>

            @if (!$quizValidation['is_complete'])
                <div id="quiz-save-requirements" role="status"
                    class="mt-4 rounded-lg border border-amber-300 bg-amber-50 p-4 text-amber-950">
                    <p class="text-sm font-bold">Quiz chưa sẵn sàng để bật hoặc gửi duyệt vì còn thiếu:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-medium">
                        @foreach ($quizValidation['errors'] as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($quizValidation['warnings'] !== [])
                <div class="mt-4 rounded-lg border border-sky-200 bg-sky-50 p-4 text-sky-950">
                    <p class="text-sm font-bold">Cần lưu ý:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-medium">
                        @foreach ($quizValidation['warnings'] as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($errors->has('quiz'))
                <div role="alert" class="mt-4 rounded-lg border border-rose-300 bg-rose-50 p-4 text-rose-950">
                    <p class="text-sm font-bold">Không thể lưu quiz:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-medium">
                        @foreach ($errors->get('quiz') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <label
                    class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $desiredIsActive ?? false))
                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    {{ $publishedVersion ? 'Trạng thái mong muốn sau duyệt' : 'Đang bật' }}
                </label>
                <button type="submit" @disabled(!$canSaveQuiz)
                    class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-600">
                    Lưu bản nháp
                </button>
            </div>
            </fieldset>
        </form>

        @if ($mutationsLocked)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900">
                Nội dung của phiên bản này đang được kiểm duyệt hoặc đã được duyệt, nên tạm thời không thể chỉnh sửa.
            </div>
        @endif

        @if ($quiz)
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-950">Danh sach cau hoi</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $questions->count() }} cau hoi</p>
                    </div>
                    <span
                        class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                        Tong diem: {{ $questions->sum('points') }}
                    </span>
                </div>
            </section>

            <div class="space-y-4">
                @forelse($questions as $question)
                    @php
                        $isMultipleChoice = $question->type === \App\Models\QuizQuestion::TYPE_MULTIPLE;
                        $isTrueFalse = $question->type === \App\Models\QuizQuestion::TYPE_TRUE_FALSE;
                    @endphp

                    <article id="question-{{ $question->id }}"
                        class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm scroll-mt-24">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700 ring-1 ring-violet-200">
                                        {{ $typeLabels[$question->form_type] ?? $question->form_type }}
                                    </span>
                                    <span
                                        class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                                        {{ $question->points }} diem
                                    </span>
                                    @if ($question->options->where('is_correct', true)->isEmpty())
                                        <span
                                            class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">
                                            Chua co dap an dung
                                        </span>
                                    @endif
                                </div>
                                <h4 class="mt-2 text-base font-bold text-slate-950"><span data-math-content>{{ $question->question }}</span></h4>
                                @if ($question->explanation)
                                    <p class="mt-2 text-sm leading-6 text-slate-500"><span data-math-content>{{ $question->explanation }}</span></p>
                                @endif
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2">
                                @unless ($mutationsLocked)
                                <details>
                                    <summary
                                        class="inline-flex min-h-10 cursor-pointer list-none items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                        Sửa câu hỏi
                                    </summary>
                                    <form method="POST"
                                        action="{{ route('instructor.quiz-questions.update', $question) }}"
                                        class="mt-3 space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-4 lg:w-[620px]">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="editing_question_id" value="{{ $question->id }}">
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Nội dung câu
                                                hỏi</span>
                                            <textarea name="question_text" rows="3"
                                                class="w-full rounded-lg border px-3 py-2 text-sm outline-none focus:border-emerald-500 @if($errors->has('question_text') && old('editing_question_id') == $question->id) border-rose-500 focus:border-rose-500 @else border-slate-300 @endif">{{ $question->question }}</textarea>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">LaTeX inline: <code>\( x^2 + y^2 \)</code><br>LaTeX riêng dòng: <code>\[ x = \frac{-b \pm \sqrt{b^2-4ac}}{2a} \]</code></p>
                                            @if($errors->has('question_text') && old('editing_question_id') == $question->id)
                                                <p class="mt-1 text-xs font-semibold text-rose-600">{{ $errors->first('question_text') }}</p>
                                            @endif
                                        </label>
                                        <div class="grid gap-3 sm:grid-cols-3">
                                            <label class="block">
                                                <span class="mb-1 block text-xs font-bold text-slate-600">Loai</span>
                                                <select name="question_type"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                                    @foreach ($questionTypes as $value => $label)
                                                        <option value="{{ $value }}"
                                                            @selected($question->form_type === $value)>
                                                            {{ $typeLabels[$value] ?? $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </label>
                                            <label class="block">
                                                <span class="mb-1 block text-xs font-bold text-slate-600">Diem</span>
                                                <input type="number" name="score" value="{{ $question->points }}"
                                                    min="1" max="1000"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                            </label>
                                            <label class="block">
                                                <span class="mb-1 block text-xs font-bold text-slate-600">Thu tu</span>
                                                <input type="number" name="sort_order"
                                                    value="{{ $question->sort_order }}" min="0"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                            </label>
                                        </div>
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Giai thich</span>
                                            <textarea name="explanation" rows="2"
                                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">{{ $question->explanation }}</textarea>
                                        </label>
                                        <button type="submit"
                                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                                            Luu cau hoi
                                        </button>
                                    </form>
                                </details>
                                <form method="POST"
                                    action="{{ route('instructor.quiz-questions.destroy', $question) }}"
                                    onsubmit="return confirm('Xoa cau hoi nay?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex min-h-10 items-center rounded-lg border border-rose-200 px-4 py-2 text-sm font-bold text-rose-700 hover:bg-rose-50">
                                        Xóa
                                    </button>
                                </form>
                                @endunless
                            </div>
                        </div>

                        <form method="POST"
                            action="{{ route('instructor.quiz-questions.answers.update', $question) }}"
                            class="mt-5 space-y-3">
                            @csrf
                            @method('PUT')

                            @forelse ($question->options as $answer)
                                <div
                                    class="grid gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 lg:grid-cols-[minmax(0,1fr)_110px_150px_110px] lg:items-center">
                                    <input type="text" name="answers[{{ $answer->id }}][answer_text]"
                                        value="{{ $answer->option_text }}" @readonly($isTrueFalse) @disabled($mutationsLocked)
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                    <input type="number" name="answers[{{ $answer->id }}][sort_order]"
                                        value="{{ $answer->sort_order }}" min="0" @readonly($isTrueFalse) @disabled($mutationsLocked)
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                    <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                        @if ($isMultipleChoice)
                                            <input type="checkbox" name="correct_answers[]"
                                                value="{{ $answer->id }}" @checked($answer->is_correct)
                                                @disabled($mutationsLocked)
                                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        @else
                                            <input type="radio" name="correct_answer" value="{{ $answer->id }}"
                                                @checked($answer->is_correct)
                                                @disabled($mutationsLocked)
                                                class="border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        @endif
                                        Đáp án đúng
                                    </label>
                                    @if ($isTrueFalse)
                                        <span class="text-xs font-semibold text-slate-500">Có định</span>
                                    @else
                                        <label class="inline-flex items-center gap-2 text-sm font-bold text-rose-700">
                                            <input type="checkbox" name="delete_answers[]"
                                                value="{{ $answer->id }}"
                                                @disabled($mutationsLocked)
                                                class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                            Xóa
                                        </label>
                                    @endif
                                </div>
                            @empty
                                <div
                                    class="rounded-lg border border-dashed border-slate-300 p-4 text-sm font-semibold text-slate-500">
                                    Câu hỏi này chưa có đáp án.
                                </div>
                            @endforelse

                            @if ($question->options->isNotEmpty())
                                <button type="submit"
                                    @disabled($mutationsLocked)
                                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                                    Lưu đáp án
                                </button>
                            @endif
                        </form>

                        @unless ($isTrueFalse || $mutationsLocked)
                            <form method="POST"
                            action="{{ route('instructor.quiz-questions.answers.store', $question) }}"
                            class="mt-3 grid gap-3 rounded-lg border border-dashed border-slate-300 p-3 lg:grid-cols-[minmax(0,1fr)_120px_150px_auto] lg:items-center">
                            @csrf
                            <input type="hidden" name="target_question_id" value="{{ $question->id }}">
                            <div>
                                <input type="text" name="answer_text" placeholder="Nhập đáp án"
                                    class="w-full rounded-lg border px-3 py-2 text-sm outline-none focus:border-emerald-500 @if($errors->has('answer_text') && old('target_question_id') == $question->id) border-rose-500 focus:border-rose-500 @else border-slate-300 @endif">
                                @if($errors->has('answer_text') && old('target_question_id') == $question->id)
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $errors->first('answer_text') }}</p>
                                @endif
                            </div>
                            <input type="number" name="sort_order" value="{{ $question->options->count() }}"
                                min="0"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="checkbox" name="is_correct" value="1"
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Đáp án đúng
                            </label>
                            <button type="submit"
                                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
                                Thêm đáp án
                            </button>
                            </form>
                        @endunless
                    </article>
                @empty
                    <div
                        class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500">
                        Chưa có câu hỏi.
                    </div>
                @endforelse
            </div>

            @unless ($mutationsLocked)
            <button type="button" data-open-question-panel
                class="fixed bottom-6 right-6 z-40 inline-flex min-h-12 items-center justify-center rounded-full bg-slate-950 px-5 text-sm font-bold text-white shadow-2xl shadow-slate-900/25 transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                Thêm câu hỏi
            </button>

            <div data-question-panel class="fixed inset-0 z-50 {{ ($errors->has('question_text') || $errors->has('score')) && old('is_creating_question') ? '' : 'hidden' }}">
                <button type="button" data-close-question-panel class="absolute inset-0 bg-slate-950/45"></button>
                <aside
                    class="absolute right-0 top-0 h-full w-full overflow-y-auto bg-white p-5 shadow-2xl sm:w-[540px] sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-950">Thêm câu hỏi</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $lesson->title }}</p>
                        </div>
                        <button type="button" data-close-question-panel
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-xl font-bold text-slate-500 hover:bg-slate-50">
                            x
                        </button>
                    </div>

                    <form method="POST" action="{{ route('instructor.quizzes.questions.store', $quiz) }}"
                        class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="is_creating_question" value="1">
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-bold text-slate-700">Nội dung câu hỏi</span>
                            <textarea name="question_text" rows="4" maxlength="10000"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm outline-none focus:border-emerald-500 @if($errors->has('question_text') && old('is_creating_question')) border-rose-500 focus:border-rose-500 @else border-slate-300 @endif">{{ old('question_text') }}</textarea>
                            <p class="mt-1 text-xs leading-5 text-slate-500">LaTeX inline: <code>\( x^2 + y^2 \)</code><br>LaTeX riêng dòng: <code>\[ x = \frac{-b \pm \sqrt{b^2-4ac}}{2a} \]</code></p>
                            @if($errors->has('question_text') && old('is_creating_question'))
                                <p class="mt-1 text-xs font-semibold text-rose-600">{{ $errors->first('question_text') }}</p>
                            @endif
                        </label>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <label class="block">
                                <span class="mb-1.5 block text-sm font-bold text-slate-700">Loại</span>
                                <select name="question_type"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                                    @foreach ($questionTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $typeLabels[$value] ?? $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block">
                                <span class="mb-1.5 block text-sm font-bold text-slate-700">Điểm</span>
                                <input type="number" name="score" value="{{ old('score', 1) }}" min="1" max="1000"
                                    class="w-full rounded-lg border px-3 py-2.5 text-sm outline-none focus:border-emerald-500 @if($errors->has('score') && old('is_creating_question')) border-rose-500 focus:border-rose-500 @else border-slate-300 @endif">
                                @if($errors->has('score') && old('is_creating_question'))
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $errors->first('score') }}</p>
                                @endif
                            </label>
                            <label class="block">
                                <span class="mb-1.5 block text-sm font-bold text-slate-700">Thứ tự</span>
                                <input type="number" name="sort_order" value="{{ $questions->count() }}"
                                    min="0"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500">
                            </label>
                        </div>
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-bold text-slate-700">Giải thích đáp án</span>
                            <textarea name="explanation" rows="3"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500">{{ old('explanation') }}</textarea>
                        </label>
                        <button type="submit"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">
                            Thêm câu hỏi
                        </button>
                    </form>
                </aside>
            </div>
            @endunless
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollKey = 'instructor-quiz-scroll:{{ $quiz?->id ?? $lesson->id }}';
            const savedY = sessionStorage.getItem(scrollKey);

            if (savedY !== null) {
                window.scrollTo(0, Number.parseInt(savedY, 10) || 0);
                sessionStorage.removeItem(scrollKey);
            }

            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function() {
                    sessionStorage.setItem(scrollKey, String(window.scrollY));
                });
            });

            const panel = document.querySelector('[data-question-panel]');
            const openButton = document.querySelector('[data-open-question-panel]');
            const closeButtons = document.querySelectorAll('[data-close-question-panel]');

            if (panel && openButton) {
                const firstField = panel.querySelector('[name="question_text"]');

                openButton.addEventListener('click', function() {
                    panel.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                    setTimeout(function() {
                        firstField?.focus();
                    }, 50);
                });

                closeButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        panel.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    });
                });
            }
        });
    </script>
</x-instructor-layout>
