<x-instructor-layout :title="'Nội dung - '.$course->title" page-title="Quản lý nội dung khóa học" :breadcrumb="$course->title">

@php
    $typeStyles = [
        'video' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'document' => 'bg-sky-50 text-sky-700 border-sky-200',
        'quiz' => 'bg-violet-50 text-violet-700 border-violet-200',
        'assignment' => 'bg-amber-50 text-amber-700 border-amber-200',
    ];

    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
        'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];

    $formatDuration = function ($seconds) {
        $seconds = (int) $seconds;
        if ($seconds <= 0) {
            return 'Chưa đặt';
        }

        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        return $minutes > 0 ? $minutes.' phút'.($remaining ? ' '.$remaining.' giây' : '') : $remaining.' giây';
    };

    $totalVideoLessons = $course->lessons()->where('type', 'video')->count();
    $videoReadinessBlockers = $course->videoReadinessBlockers();
    $hasVideoReadinessBlockers = $videoReadinessBlockers !== [];
    $hasIncompleteHls = $course->hasIncompleteHlsVideos();
    $submissionCheck = $course->submissionCheck();
    $canSubmitCourse = $course->canBeSubmittedForReview() && ! $hasVideoReadinessBlockers && $submissionCheck->passes();
    $videoBlockerTitle = $videoReadinessBlockers[0]['title'] ?? null;
    $readinessItems = $submissionCheck->items();
    $passedReadinessItems = count(array_filter($readinessItems, fn ($item) => $item['passed']));
    $readinessProgress = count($readinessItems) > 0
        ? (int) round(($passedReadinessItems / count($readinessItems)) * 100)
        : 0;
    $courseHasBeenSubmitted = in_array($course->status, ['pending_review', 'approved', 'published'], true);
    $pendingContentUpdates = \App\Models\ContentUpdate::query()
        ->where('course_id', $course->id)
        ->whereIn('status', [
            \App\Models\ContentUpdate::STATUS_DRAFT,
            \App\Models\ContentUpdate::STATUS_PENDING,
            \App\Models\ContentUpdate::STATUS_REJECTED,
        ])
        ->orderBy('id')
        ->get();
@endphp

<div class="curriculum-builder space-y-4">
    <section class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-5">
        <div class="grid gap-4 md:grid-cols-4">
            @foreach([
                ['Thông tin khóa học', 'Hoàn thành', true],
                ['Xây dựng nội dung', 'Đang thực hiện', true],
                ['Kiểm tra', $submissionCheck->passes() ? 'Hoàn thành' : 'Chưa hoàn thành', $submissionCheck->passes()],
                ['Gửi duyệt', $courseHasBeenSubmitted ? 'Đã gửi' : 'Chưa bắt đầu', $courseHasBeenSubmitted],
            ] as $step)
                <div class="relative flex items-center gap-3 {{ ! $loop->last ? 'md:after:absolute md:after:left-[calc(100%-6px)] md:after:top-5 md:after:h-px md:after:w-5 md:after:bg-slate-200 dark:md:after:bg-slate-700' : '' }}">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border text-sm font-black {{ $loop->iteration === 2 ? 'border-blue-500 bg-blue-600 text-white shadow-lg shadow-blue-500/20' : ($step[2] ? 'border-emerald-400 bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10' : 'border-slate-200 bg-slate-100 text-slate-500 dark:border-slate-700 dark:bg-slate-800') }}">
                        @if($step[2] && $loop->iteration !== 2)
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $loop->iteration }}. {{ $step[0] }}</p>
                        <p class="mt-0.5 truncate text-xs {{ $loop->iteration === 2 ? 'font-semibold text-blue-600 dark:text-blue-300' : 'text-slate-500 dark:text-slate-400' }}">{{ $step[1] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @if($course->publishedVersion?->version_number)
            <div class="mt-4 flex items-center gap-2 border-t border-blue-100 pt-4 text-xs font-bold text-emerald-700 dark:border-slate-700 dark:text-emerald-300">
                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                    Đang xuất bản: V{{ $course->publishedVersion->version_number }}
                </span>
            </div>
        @endif
    </section>

    @if($pendingContentUpdates->isNotEmpty())
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-500/40 dark:bg-amber-500/10 sm:p-5">
            <h2 class="text-sm font-black text-amber-950 dark:text-amber-100">
                Các thay đổi nội dung ({{ $pendingContentUpdates->count() }})
            </h2>
            <ul class="mt-3 space-y-2 text-xs text-amber-950 dark:text-amber-100">
                @foreach($pendingContentUpdates as $pUpdate)
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-200 bg-white/70 p-3 dark:border-amber-500/30 dark:bg-slate-900/60">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="rounded bg-indigo-50 px-2 py-0.5 text-[11px] font-bold uppercase text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">{{ $pUpdate->type }}</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">({{ $pUpdate->action }})</span>
                                <strong class="font-bold text-slate-900 dark:text-white">
                                    @if(isset($pUpdate->payload['title']))
                                        "{{ $pUpdate->payload['title'] }}"
                                    @else
                                        #{{ $pUpdate->entity_id }}
                                    @endif
                                </strong>
                                <span class="font-bold {{ $pUpdate->isRejected() ? 'text-rose-700 dark:text-rose-300' : ($pUpdate->isPending() ? 'text-blue-800 dark:text-blue-300' : 'text-amber-800 dark:text-amber-300') }}">
                                    {{ $pUpdate->isRejected() ? 'Bị từ chối' : ($pUpdate->isPending() ? 'Chờ duyệt' : 'Nháp') }}
                                </span>
                                @php
                                    $versionContext = app(\App\Services\ContentUpdateDiffService::class)->versionContext($pUpdate);
                                @endphp
                                @if(($versionContext['current'] ?? null) !== null)
                                    <span class="font-semibold text-emerald-700 dark:text-emerald-300">Đang xuất bản: V{{ $versionContext['current'] }}</span>
                                @endif
                                @if(($versionContext['proposed'] ?? null) !== null)
                                    <span class="font-semibold {{ $pUpdate->isRejected() ? 'text-rose-700 dark:text-rose-300' : ($pUpdate->isPending() ? 'text-blue-800 dark:text-blue-300' : 'text-amber-800 dark:text-amber-300') }}">
                                        {{ $pUpdate->isRejected() ? 'V'.$versionContext['proposed'].' — Bị từ chối' : ('Đề xuất: V'.$versionContext['proposed'].' — '.($pUpdate->isPending() ? 'Chờ duyệt' : 'Nháp')) }}
                                    </span>
                                @endif
                            </div>
                            @if($pUpdate->rejection_reason)
                                <p class="mt-1 text-[11px] font-semibold text-rose-700 dark:text-rose-300">
                                    Lý do từ chối: {{ $pUpdate->rejection_reason }}
                                </p>
                            @endif
                        </div>

                        @if($pUpdate->isRejected())
                            <form method="POST" action="{{ route('instructor.courses.content-updates.revise', [$course, $pUpdate]) }}">
                                @csrf
                                <button type="submit" class="cursor-pointer rounded-lg bg-rose-700 px-3 py-1.5 text-[11px] font-bold text-white transition hover:bg-rose-800">
                                    Tạo bản chỉnh sửa mới
                                </button>
                            </form>
                        @elseif($pUpdate->isPending())
                            <span class="rounded-lg bg-blue-100 px-3 py-1.5 text-[11px] font-bold text-blue-800 dark:bg-blue-500/10 dark:text-blue-300">
                                Chỉ đọc trong khi chờ duyệt
                            </span>
                        @else
                            <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-[11px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                Có thể chỉnh sửa từ biểu mẫu nội dung
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- HÀNG CHỜ UPLOAD VIDEO LÊN S3 --}}
    <div id="global-video-upload-queue-panel" class="hidden"></div>

    <div class="grid items-start gap-4 xl:grid-cols-[minmax(0,1fr)_320px]">
        <aside class="order-2 space-y-3 xl:sticky xl:top-24 xl:max-h-[calc(100vh-7rem)] xl:overflow-y-auto xl:pr-1">
            <section class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-end justify-between gap-3">
                    <div>
                        <h2 class="text-base font-black text-slate-950 dark:text-white">Mức độ hoàn thiện</h2>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Điều kiện trước khi gửi duyệt</p>
                    </div>
                    <strong class="text-2xl font-black text-slate-950 dark:text-white">{{ $passedReadinessItems }}<span class="text-base text-slate-400">/{{ count($readinessItems) }}</span></strong>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        <div class="h-full rounded-full bg-blue-600 transition-[width] duration-300" style="width: {{ $readinessProgress }}%"></div>
                    </div>
                    <span class="text-xs font-black text-blue-600 dark:text-blue-300">{{ $readinessProgress }}%</span>
                </div>
                <ul class="mt-4 divide-y divide-dashed divide-slate-200 dark:divide-slate-700">
                    @foreach($readinessItems as $item)
                        <li class="flex items-start gap-2.5 py-2.5 text-xs">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-black text-white {{ $item['passed'] ? 'bg-emerald-500' : 'bg-amber-500' }}">
                                {{ $item['passed'] ? '✓' : '!' }}
                            </span>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-700 dark:text-slate-200">{{ $item['label'] }}</p>
                                @if(! $item['passed'] && filled($item['message']))
                                    <p class="mt-0.5 leading-5 text-amber-700 dark:text-amber-300">{{ $item['message'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            <div id="common-hls-banner-wrapper"
                 class="rounded-2xl border p-4 shadow-sm transition-all duration-300 {{ $totalVideoLessons === 0 ? 'hidden' : ($hasVideoReadinessBlockers ? 'border-amber-300 bg-amber-50 text-amber-900 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200' : 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200') }}">
                <div class="flex items-start gap-3">
                    <span id="common-hls-icon" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-current/20 text-sm font-black">i</span>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide">Trạng thái video</p>
                        <p id="common-hls-message" class="mt-1 text-xs font-semibold leading-5">
                            @if($hasVideoReadinessBlockers)
                                Còn video chưa sẵn sàng: {{ $videoBlockerTitle }}.
                            @elseif(!$submissionCheck->passes())
                                {{ $submissionCheck->summaryMessage() }}
                            @elseif($totalVideoLessons > 0)
                                Tất cả video đã được xử lý bảo mật thành công.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <section class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        Thông tin khóa học
                    </a>
                    @if($course->canBeSubmittedForReview())
                        <form method="POST" action="{{ route('instructor.courses.submit', $course) }}" id="curriculumSubmitForm">
                            @csrf
                            <input type="hidden" name="copyright_agreed" value="1">
                            <button type="submit"
                                    id="curriculum-submit-review-btn"
                                    {{ !$canSubmitCourse ? 'disabled' : '' }}
                                    @if(!$canSubmitCourse)
                                        title="{{ $hasVideoReadinessBlockers ? 'Khóa học chưa thể gửi duyệt vì video chưa sẵn sàng: '.$videoBlockerTitle : $submissionCheck->summaryMessage() }}"
                                    @endif
                                    class="inline-flex min-h-11 w-full items-center justify-center rounded-xl px-4 text-sm font-black transition {{ $hasVideoReadinessBlockers || !$canSubmitCourse ? 'cursor-not-allowed bg-slate-200 text-slate-400 dark:bg-slate-800 dark:text-slate-600' : 'cursor-pointer bg-blue-600 text-white shadow-lg shadow-blue-500/20 hover:bg-blue-700' }}">
                                {{ in_array($course->status, ['need_revision', 'rejected'], true) ? 'Gửi duyệt lại' : 'Gửi duyệt' }}
                            </button>
                        </form>
                    @endif
                </div>
                @if(! $canSubmitCourse)
                    <p class="mt-3 text-center text-xs leading-5 text-slate-500 dark:text-slate-400">Hoàn thành các mục còn thiếu để gửi khóa học.</p>
                @endif
            </section>
        </aside>

        <main class="order-1 min-w-0 space-y-4">
            <section class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950 dark:text-white">Chương trình khóa học</h2>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tổ chức chương, bài học và nội dung giảng dạy của {{ $course->title }}.</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl bg-blue-50 px-3 py-2 dark:bg-blue-500/10"><strong class="block text-base text-blue-700 dark:text-blue-300">{{ $course->courseSections->count() }}</strong><span class="text-[10px] font-semibold text-slate-500">Chương</span></div>
                        <div class="rounded-xl bg-blue-50 px-3 py-2 dark:bg-blue-500/10"><strong id="overview-total-lessons" class="block text-base text-blue-700 dark:text-blue-300">{{ $course->courseSections->sum(fn ($section) => $section->lessons->count()) }}</strong><span class="text-[10px] font-semibold text-slate-500">Bài học</span></div>
                        <div class="rounded-xl bg-blue-50 px-3 py-2 dark:bg-blue-500/10"><strong class="block text-base text-blue-700 dark:text-blue-300">{{ $course->courseSections->flatMap->lessons->where('is_preview', true)->count() }}</strong><span class="text-[10px] font-semibold text-slate-500">Xem thử</span></div>
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-2 border-t border-blue-100 pt-4 dark:border-slate-700 sm:flex-row">
                    <label class="relative min-w-0 flex-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                        <input type="search" data-curriculum-search placeholder="Tìm chương hoặc bài học" class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </label>
                    <button type="button" data-lesson-import-open aria-controls="lesson-import-dialog" aria-haspopup="dialog" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h10M18 15v6m-3-3h6"/></svg>
                        Nhập từ Excel
                    </button>
                    <button type="button" onclick="const panel=document.getElementById('add-course-section'); panel.classList.toggle('hidden'); if(!panel.classList.contains('hidden')) panel.scrollIntoView({behavior:'smooth',block:'center'});" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-black text-white shadow-lg shadow-blue-500/20 transition hover:bg-blue-700">+ Thêm chương</button>
                </div>
            </section>

            <div id="curriculumCopyrightModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="curriculum-copyright-title">
                <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" aria-hidden="true" onclick="closeCurriculumCopyrightModal()"></div>
                    <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                    <div class="relative inline-block w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-2xl sm:my-8 sm:align-middle">
                        <div class="bg-white px-6 pb-4 pt-6 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600 sm:mx-0">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                                <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 id="curriculum-copyright-title" class="text-base font-bold leading-6 text-slate-900">CAM KẾT BẢN QUYỀN</h3>
                                    <div class="mt-4 max-h-60 space-y-3 overflow-y-auto rounded-xl border border-slate-200/60 bg-slate-50 p-4 text-xs leading-relaxed text-slate-600">
                                        <p class="font-medium text-slate-700">Tôi xác nhận rằng toàn bộ video, hình ảnh, âm thanh, tài liệu và các nội dung khác trong khóa học là do tôi sở hữu hoặc tôi có đầy đủ quyền sử dụng theo quy định của pháp luật.</p>
                                        <p>Tôi chịu hoàn toàn trách nhiệm đối với mọi khiếu nại, tranh chấp hoặc vi phạm liên quan đến quyền sở hữu trí tuệ và bản quyền phát sinh từ khóa học này.</p>
                                        <p>Tôi hiểu rằng Fea Learning chỉ cung cấp nền tảng đăng tải khóa học. Nếu phát hiện hoặc nhận được khiếu nại hợp lệ về bản quyền, nền tảng có quyền từ chối duyệt, tạm khóa hoặc gỡ bỏ khóa học.</p>
                                    </div>
                                    <form id="curriculumCopyrightSubmitForm" method="POST" action="{{ route('instructor.courses.submit', $course) }}" class="mt-5">
                                        @csrf
                                        <label class="flex cursor-pointer items-start gap-3 text-sm">
                                            <input id="curriculumCopyrightAgreement" name="copyright_agreed" type="checkbox" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                            <span class="font-semibold text-slate-700">Tôi đã đọc và đồng ý với cam kết bản quyền.</span>
                                        </label>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                            <button type="button" onclick="closeCurriculumCopyrightModal()" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition-colors hover:bg-slate-50">Hủy</button>
                            <button type="button" id="curriculumCopyrightConfirm" onclick="submitCurriculumCopyrightForm()" disabled class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">Xác nhận gửi duyệt</button>
                        </div>
                    </div>
                </div>
            </div>

    <form id="add-course-section" method="POST" action="{{ route('instructor.courses.sections.store', $course) }}"
          class="{{ $errors->hasBag('storeSection') ? '' : 'hidden' }} rounded-2xl border border-blue-100 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-6">
        @csrf
        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <h3 class="text-sm font-black text-slate-950 dark:text-white">Thêm chương mới</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tạo cấu trúc chương trước khi thêm video, tài liệu, quiz hoặc bài tập.</p>
            </div>
            <button type="button" onclick="document.getElementById('add-course-section').classList.add('hidden')" aria-label="Đóng form thêm chương" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:bg-slate-50 hover:text-slate-700 dark:border-slate-700 dark:hover:bg-slate-800">×</button>
        </div>
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)_auto] lg:items-end">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Tên chương</span>
                <input type="text" name="title" maxlength="255" placeholder="Ví dụ: Giới thiệu khóa học"
                       class="w-full rounded-xl border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 dark:bg-slate-950 dark:text-white @error('title', 'storeSection') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-blue-500 focus-visible:ring-blue-500/20 dark:border-slate-700 @enderror">
                @error('title', 'storeSection') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Mô tả chương</span>
                <input type="text" name="description" maxlength="1000" placeholder="Nội dung chính của chương này"
                       class="w-full rounded-xl border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 dark:bg-slate-950 dark:text-white @error('description', 'storeSection') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-blue-500 focus-visible:ring-blue-500/20 dark:border-slate-700 @enderror">
                @error('description', 'storeSection') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>
            <div class="flex flex-col gap-2 sm:flex-row">
                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-colors duration-200 hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer sm:w-auto">
                    + Thêm chương
                </button>
            </div>
        </div>
    </form>

    @include('instructor.courses.partials.lesson-import-modal', ['course' => $course])

    <div class="space-y-4">
        @forelse($curriculumSections as $section)
            @php
                $hasInvalidSectionDescription = filled($section->description)
                    && \App\Models\CourseSection::descriptionContainsMarkup($section->description);
                $safeSectionDescription = $hasInvalidSectionDescription ? null : $section->description;
                $sectionUpdate = $section->draft_update ?? null;
                $sectionReadOnly = $sectionUpdate?->isPending() ?? false;
                $sectionVersionContext = $sectionUpdate
                    ? app(\App\Services\ContentUpdateDiffService::class)->versionContext($sectionUpdate)
                    : ['current' => $section->publishedVersion?->version_number, 'proposed' => null];
            @endphp
            <article class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm transition hover:border-blue-200 dark:border-slate-700 dark:bg-slate-900" data-curriculum-section>
                <div class="border-b border-blue-100 bg-blue-50/60 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/70">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg bg-blue-600 px-2.5 py-1 text-xs font-bold text-white">Chương {{ $loop->iteration }}</span>
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $section->lessons->count() }} bài học</span>
                                @if(isset($section->update_status))
                                    @if($section->update_status === 'draft')
                                        <span class="rounded-full border border-amber-300 bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Chương nháp</span>
                                    @elseif($section->update_status === 'pending')
                                        <span class="rounded-full border border-blue-300 bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">Chương đã gửi duyệt</span>
                                    @elseif($section->update_status === 'rejected')
                                        <span class="rounded-full border border-rose-300 bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">Chương bị từ chối</span>
                                    @endif
                                @endif
                                @if(($sectionVersionContext['current'] ?? null) !== null)
                                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Đang xuất bản: V{{ $sectionVersionContext['current'] }}</span>
                                @endif
                                @if(($sectionVersionContext['proposed'] ?? null) !== null)
                                    <span class="rounded-full border {{ $sectionUpdate?->isRejected() ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-blue-200 bg-blue-50 text-blue-800' }} px-2.5 py-0.5 text-xs font-bold">
                                        {{ $sectionUpdate?->isRejected() ? 'V'.$sectionVersionContext['proposed'].' — Bị từ chối' : 'Đề xuất: V'.$sectionVersionContext['proposed'].' — '.($sectionUpdate?->isPending() ? 'Chờ duyệt' : 'Nháp') }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="mt-2 text-base font-black text-slate-950 dark:text-white">{{ $section->title }}</h3>
                            @if(filled($safeSectionDescription))
                                <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $safeSectionDescription }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            @if($sectionReadOnly)
                                <span class="inline-flex min-h-10 items-center rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-800">
                                    Thay đổi đang chờ Admin duyệt và không thể chỉnh sửa.
                                </span>
                            @else
                            <details class="group">
                                <summary class="inline-flex min-h-9 cursor-pointer list-none items-center justify-center rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-bold text-blue-700 transition-colors duration-200 hover:bg-blue-50 dark:border-slate-600 dark:bg-slate-800 dark:text-blue-300">
                                    Sửa chương
                                </summary>
                                <div class="mt-3 w-full rounded-xl border border-blue-100 bg-white p-4 shadow-lg dark:border-slate-700 dark:bg-slate-800 lg:w-[520px]">
                                    <form method="POST" action="{{ route('instructor.courses.sections.update', [$course, $section]) }}" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Tên chương</span>
                                            <input type="text" name="title" value="{{ $section->title }}" class="w-full rounded-lg border px-3 py-2 text-sm outline-none @error('title', 'updateSection_'.$section->id) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">
                                            @error('title', 'updateSection_'.$section->id) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                                        </label>
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Mô tả</span>
                                            <textarea name="description" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm outline-none @error('description', 'updateSection_'.$section->id) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">{{ $safeSectionDescription }}</textarea>
                                            @if($hasInvalidSectionDescription)
                                                <p class="mt-1 text-xs font-semibold text-amber-700">Mô tả cũ chứa dữ liệu không hợp lệ. Hãy nhập lại mô tả bằng văn bản thuần.</p>
                                            @endif
                                            @error('description', 'updateSection_'.$section->id) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                                        </label>
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700 cursor-pointer">Lưu chương</button>
                                    </form>
                                </div>
                            </details>
                            <form method="POST" action="{{ route('instructor.courses.sections.destroy', [$course, $section]) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa chương học này? (Lưu ý: Chỉ xóa được khi chương không còn bài học)')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-9 items-center justify-center rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-50 cursor-pointer dark:border-rose-500/30 dark:bg-slate-800 dark:text-rose-300">
                                    Xóa chương
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-blue-50 dark:divide-slate-700" id="section-lessons-{{ $section->id }}" data-section-lessons-container="{{ $section->id }}">
                    @forelse($section->lessons as $lesson)
                        @include('instructor.courses.partials.lesson-item', [
                            'course' => $course,
                            'section' => $section,
                            'lesson' => $lesson,
                            'typeStyles' => $typeStyles,
                            'statusStyles' => $statusStyles,
                            'lessonTypes' => $lessonTypes,
                            'lessonStatuses' => $lessonStatuses,
                            'formatDuration' => $formatDuration,
                        ])
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500" data-empty-lessons-notice>Chương này chưa có bài học.</div>
                    @endforelse
                </div>

                <details class="border-t border-blue-100 bg-white dark:border-slate-700 dark:bg-slate-900" {{ $errors->hasBag('storeLesson_'.$section->id) ? 'open' : '' }}>
                    <summary class="cursor-pointer list-none px-5 py-4 text-sm font-bold text-blue-600 transition-colors duration-200 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-slate-800">
                        + Thêm bài học
                    </summary>
                    <div class="border-t border-blue-100 bg-blue-50/40 p-5 dark:border-slate-700 dark:bg-slate-950/40">
                        @include('instructor.courses.partials.lesson-form', [
                            'course' => $course,
                            'action' => route('instructor.courses.sections.lessons.store', [$course, $section]),
                            'method' => 'POST',
                            'lesson' => null,
                            'errorBag' => 'storeLesson_'.$section->id,
                            'nextSortOrder' => $section->lessons->count(),
                            'lessonTypes' => $lessonTypes,
                            'lessonStatuses' => $lessonStatuses,
                            'submitLabel' => 'Thêm bài học',
                        ])
                    </div>
                </details>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-blue-200 bg-white px-6 py-14 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h10"/>
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-950 dark:text-white">Chưa có chương học</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Tạo chương đầu tiên để bắt đầu chia khóa học thành các phần rõ ràng.</p>
            </div>
        @endforelse
    </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.querySelector('[data-curriculum-search]');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var keyword = searchInput.value.trim().toLocaleLowerCase('vi');

                document.querySelectorAll('[data-curriculum-section]').forEach(function (section) {
                    section.classList.toggle('hidden', keyword !== '' && !section.textContent.toLocaleLowerCase('vi').includes(keyword));
                });
            });
        }

        var agreementCheckbox = document.getElementById('curriculumCopyrightAgreement');
        var confirmButton = document.getElementById('curriculumCopyrightConfirm');
        if (agreementCheckbox && confirmButton) {
            agreementCheckbox.addEventListener('change', function () {
                confirmButton.disabled = !this.checked;
            });
        }
    });

    function openCurriculumCopyrightModal() {
        var modal = document.getElementById('curriculumCopyrightModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCurriculumCopyrightModal() {
        var modal = document.getElementById('curriculumCopyrightModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function submitCurriculumCopyrightForm() {
        var agreement = document.getElementById('curriculumCopyrightAgreement');
        var form = document.getElementById('curriculumCopyrightSubmitForm');
        if (agreement && agreement.checked && form) {
            form.submit();
        }
    }

    document.addEventListener('change', function (e) {
        var target = e.target;
        if (!target || target.name !== 'video_file' || !target.files || !target.files[0]) {
            return;
        }

        var file = target.files[0];
        var form = target.closest('form');
        if (!form) return;

        var durationInput = form.querySelector('input[name="duration"]');
        if (!durationInput) return;

        var video = document.createElement('video');
        video.preload = 'metadata';
        var objectUrl = URL.createObjectURL(file);

        video.onloadedmetadata = function () {
            URL.revokeObjectURL(objectUrl);
            if (video.duration && !isNaN(video.duration) && isFinite(video.duration)) {
                var seconds = Math.round(video.duration);
                durationInput.value = seconds;

                // Thêm hiệu ứng highlight thông báo cho giảng viên biết thời lượng đã tự đồng bộ
                durationInput.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-500');
                setTimeout(function () {
                    durationInput.classList.remove('ring-2', 'ring-emerald-500');
                }, 2500);
            }
        };

        video.onerror = function () {
            URL.revokeObjectURL(objectUrl);
        };

        video.src = objectUrl;
    });

    if (window.initCurriculumHlsPolling) {
        window.initCurriculumHlsPolling(@js(route('instructor.courses.hls-status', $course)));
    }
</script>

</x-instructor-layout>
