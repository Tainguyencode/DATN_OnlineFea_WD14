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
                        <!-- Tiêu đề & Mô tả bài tập -->
                        <div class="border-b border-white/10 pb-5">
                            <h2 class="text-xl font-extrabold text-white">{{ $lesson->assignment?->title ?? 'Bài tập thực hành' }}</h2>
                            <p class="text-sm text-slate-400 mt-2 whitespace-pre-line">
                                {{ $lesson->assignment?->description ?? 'Hãy thực hiện yêu cầu của bài tập tự luận dưới đây.' }}
                            </p>
                            
                            <div class="mt-4 flex flex-wrap gap-4 text-xs font-semibold text-slate-300 bg-white/5 p-3 rounded-lg w-fit">
                                <div>HẠN NỘP: <span class="text-amber-400">{{ $lesson->assignment?->due_date?->format('d/m/Y H:i') ?? 'Không giới hạn' }}</span></div>
                                <div>ĐIỂM TỐI ĐA: <span class="text-emerald-400">{{ $lesson->assignment?->max_score ?? 100 }}đ</span></div>
                            </div>
                        </div>

                        <!-- Thông báo nếu có -->
                        @if(session('success'))
                            <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-sm font-semibold text-emerald-400">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="rounded-xl bg-rose-500/10 border border-rose-500/20 p-4 text-sm font-semibold text-rose-400">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Giao diện bài làm và chấm điểm -->
                        @if($submission)
                            <div class="bg-white/5 rounded-xl p-5 space-y-4">
                                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                    <h3 class="font-bold text-white">Bài làm của bạn</h3>
                                    
                                    <div>
                                        @if($submission->status === 'submitted')
                                            <span class="rounded-full bg-amber-500/10 px-3 py-1 text-xs font-bold text-amber-400 ring-1 ring-inset ring-amber-500/20">
                                                Chưa chấm điểm
                                            </span>
                                        @elseif($submission->status === 'graded')
                                            <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">
                                                Đã chấm điểm: {{ $submission->score }} / {{ $lesson->assignment?->max_score ?? 100 }}đ
                                            </span>
                                        @elseif($submission->status === 'resubmit_required' || $submission->status === 'returned')
                                            <span class="rounded-full bg-rose-500/10 px-3 py-1 text-xs font-bold text-rose-400 ring-1 ring-inset ring-rose-500/20">
                                                Yêu cầu làm lại
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
                                            <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-[#0056D2] hover:underline font-bold bg-white/10 px-3 py-1.5 rounded-lg text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Tải file bài làm đã nộp
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <!-- Feedback từ giảng viên -->
                                @if($submission->feedback)
                                    <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-lg p-4 mt-3">
                                        <span class="block text-xs font-bold text-emerald-400 uppercase">Nhận xét từ Giảng viên</span>
                                        <p class="text-sm text-slate-300 mt-1 whitespace-pre-line">{{ $submission->feedback }}</p>
                                        @if($submission->graded_at)
                                            <time class="block text-[10px] text-slate-500 mt-2">Ngày chấm: {{ $submission->graded_at->format('d/m/Y H:i') }}</time>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Form nộp bài / Nộp lại bài làm -->
                        @if(!$submission || $submission->status === 'resubmit_required' || $submission->status === 'returned')
                            <form method="POST" action="{{ route('courses.lessons.assignment.submit', [$course, $lesson]) }}" enctype="multipart/form-data" class="space-y-4 bg-white/5 rounded-xl p-5">
                                @csrf
                                <h3 class="font-bold text-white border-b border-white/10 pb-2">
                                    {{ $submission ? 'Làm lại & Nộp lại bài tập' : 'Nộp bài tập tự luận' }}
                                </h3>

                                <div>
                                    <label for="content" class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Nội dung / Ghi chú bài làm</label>
                                    <textarea name="content" id="content" rows="6" class="w-full rounded-xl bg-slate-900 border-white/10 text-sm text-white placeholder-slate-500 focus:border-[#0056D2] focus:ring-0" placeholder="Viết mô tả hoặc dán mã nguồn bài làm của bạn vào đây (nếu có)...">{{ old('content') }}</textarea>
                                    @error('content')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="file" class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Tải lên tệp đính kèm (ZIP, PDF, DOCX, Ảnh... - Tối đa 10MB)</label>
                                    <input type="file" name="file" id="file" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0056D2] file:text-white hover:file:bg-[#0046B8] cursor-pointer">
                                    @error('file')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-[#0056D2] px-6 py-2.5 text-sm font-bold text-white transition hover:bg-[#0046B8]">
                                    {{ $submission ? 'Gửi lại bài làm' : 'Gửi bài tập' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @elseif($lesson->type === 'document')
                <div class="flex min-h-[320px] items-center justify-center bg-[#1c1d1f] p-6 lg:min-h-[calc(100vh-14rem)]">
                    <div class="max-w-lg text-center text-white">
                        <h2 class="text-xl font-bold">{{ $lesson->title }}</h2>
                        <p class="mt-2 text-sm text-white/80">Bài đọc / tài liệu</p>
                        @if($lesson->document_file)
                            <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="mt-5 inline-flex h-11 items-center rounded bg-[#0056D2] px-6 text-sm font-bold text-white hover:bg-[#0046B8]">Mở tài liệu</a>
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

                <x-learning.ai-study-assistant
                    :lesson="$lesson"
                    :course="$course"
                    :can-use-lesson-ai="$canUseLessonAi"
                    :ai-chat-url="$aiChatUrl"
                />
            @endif
        </main>

        <x-learning.sidebar
            :sections="$curriculumSections"
            :course-progress="$courseProgress"
            :completed-lessons="$completedLessons"
            :total-lessons="$totalLessons"
        />
    </div>
</div>
@endsection
