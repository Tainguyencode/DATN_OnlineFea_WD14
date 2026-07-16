<x-instructor-layout :title="$course->title" page-title="Chỉnh sửa khóa học" :breadcrumb="$course->title">

@php
    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
        'pending_review' => 'bg-amber-50 text-amber-700 border-amber-200',
        'approved' => 'bg-sky-50 text-sky-700 border-sky-200',
        'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
        'archived' => 'bg-zinc-100 text-zinc-700 border-zinc-200',
    ];

    $sectionCount = $course->courseSections->count();
    $lessonCount = $course->courseSections->sum(fn ($section) => $section->lessons->count());
@endphp

<div class="space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusStyles[$course->status] ?? $statusStyles['draft'] }}">
                        {{ $statusOptions[$course->status] ?? $course->status }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">Tạo ngày {{ $course->created_at?->format('d/m/Y') }}</span>
                </div>
                <h2 class="mt-3 text-xl font-bold text-slate-950">{{ $course->title }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $course->short_description ?: 'Bổ sung mô tả ngắn để học viên hiểu nhanh giá trị khóa học.' }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('instructor.courses.index') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 cursor-pointer">
                    Quay lại
                </a>
                <a href="{{ route('instructor.courses.curriculum', $course) }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 cursor-pointer">
                    Quản lý nội dung
                </a>
                @if($course->status === 'published')
                    <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
                       class="inline-flex min-h-10 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 cursor-pointer">
                        Xem trước
                    </a>
                @endif
                @if($course->canBeSubmittedForReview() && $submissionCheck->passes())
                    <button type="button" onclick="openCopyrightModal()"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-amber-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 cursor-pointer">
                        {{ in_array($course->status, ['rejected'], true) ? 'Gửi duyệt lại' : 'Gửi duyệt' }}
                    </button>
                @endif
            </div>
        </div>

        @if($course->status === 'rejected' && $course->rejectionReasonText())
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <strong>Lý do từ chối:</strong> {{ $course->rejectionReasonText() }}
            </div>
        @endif

        @if($errors->has('submission'))
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-bold">Không thể gửi duyệt khóa học</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach($errors->get('submission') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @if($course->status === 'rejected')
        @include('instructor.courses.partials.ai-moderation-results', ['course' => $course])
    @endif

    @if($course->canBeSubmittedForReview())
        @include('instructor.courses.partials.submission-readiness', [
            'course' => $course,
            'submissionCheck' => $submissionCheck,
        ])
    @endif

    @include('instructor.courses._form', [
        'course' => $course,
        'categories' => $categories,
        'action' => route('instructor.courses.update', $course),
        'method' => 'PUT',
        'submitLabel' => 'Lưu nháp',
    ])

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Curriculum builder</p>
                <h2 class="mt-1 text-lg font-bold text-slate-950">Nội dung khóa học</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $sectionCount }} chương · {{ $lessonCount }} bài học</p>
            </div>
            <a href="{{ route('instructor.courses.curriculum', $course) }}"
               class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
                Mở trình quản lý nội dung
            </a>
        </div>
    </section>

    {{-- ===== LỊCH SỬ KIỂM DUYỆT ===== --}}
    @if($courseReviews->isNotEmpty())
    @php
        $statusBadge = [
            \App\Enums\CourseReviewStatus::Approved->value => ['bg-emerald-50 text-emerald-700 border-emerald-200', '✓ Đã duyệt'],
            \App\Enums\CourseReviewStatus::Rejected->value => ['bg-rose-50 text-rose-700 border-rose-200', '✗ Từ chối'],
            \App\Enums\CourseReviewStatus::Pending->value => ['bg-amber-50 text-amber-700 border-amber-200', 'Chờ duyệt'],
        ];
        $checklistLabels = config('course.admin_review_checklist', []);
    @endphp

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Lịch sử kiểm duyệt</p>
                <h2 class="mt-1 text-lg font-bold text-slate-950">Phản hồi từ Admin</h2>
            </div>

            {{-- Nút Gửi duyệt lại khi status = need_revision --}}
            @if($course->status === 'rejected' && $submissionCheck->passes())
                <button type="button"
                        onclick="openCopyrightModal()"
                        id="btn-resubmit-history"
                        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-amber-500 px-5 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-amber-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Gửi duyệt lại
                </button>
            @endif
        </div>

        <div class="mt-5 space-y-6">
            @foreach($courseReviews as $review)
            @php
                $statusValue = $review->status instanceof \App\Enums\CourseReviewStatus ? $review->status->value : (string) $review->status;
                [$badgeClass, $badgeLabel] = $statusBadge[$statusValue] ?? ['bg-slate-50 text-slate-700 border-slate-200', $review->statusLabel()];
                $checklist = $review->checklist_json ?? [];
            @endphp
            <article class="overflow-hidden rounded-xl border border-slate-200">
                {{-- Header --}}
                <div class="flex flex-col gap-3 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                        <span class="text-sm font-semibold text-slate-700">
                            {{ $review->reviewer?->name ?? 'Admin' }}
                        </span>
                    </div>
                    <span class="text-xs text-slate-500">
                        Lần {{ $review->submission_number }} · {{ $review->reviewed_at?->format('d/m/Y H:i') ?? $review->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    {{-- Comment --}}
                    @if($review->comment)
                    <div class="px-5 py-4">
                        <p class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-500">Lý do / Ghi chú của Admin</p>
                        <p class="whitespace-pre-line text-sm leading-6 text-slate-700">{{ $review->comment }}</p>
                    </div>
                    @endif

                    @if(! empty($checklist))
                    <div class="px-5 py-4">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Kết quả checklist</p>
                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <table class="min-w-full text-left text-sm">
                                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-4 py-2.5">Mục kiểm tra</th>
                                        <th class="w-24 px-4 py-2.5">Kết quả</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($checklist as $key => $value)
                                    @php
                                        $label = \App\Models\CourseReviewItem::ITEM_LABELS[$key] ?? config("course.admin_review_checklist.{$key}", $key);
                                        $passed = is_array($value) ? (($value['status'] ?? 'fail') === 'pass') : !empty($value);
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-slate-900">{{ $label }}</td>
                                        <td class="px-4 py-3">
                                            @if($passed)
                                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">✓ PASS</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700">✗ FAIL</span>
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
            </article>
            @endforeach
        </div>
    </section>
    @endif
</div>

<!-- Copyright Commitment Modal -->
<div id="copyrightModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeCopyrightModal()"></div>

        <!-- Trick to center the modal contents -->
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="relative inline-block transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white px-6 pt-6 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-base font-bold leading-6 text-slate-900" id="modal-title">CAM KẾT BẢN QUYỀN</h3>
                        
                        <div class="mt-4 text-xs leading-relaxed text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-200/60 max-h-60 overflow-y-auto space-y-3">
                            <p class="font-medium text-slate-700">Tôi xác nhận rằng toàn bộ video, hình ảnh, âm thanh, tài liệu và các nội dung khác trong khóa học là do tôi sở hữu hoặc tôi có đầy đủ quyền sử dụng theo quy định của pháp luật.</p>
                            <p>Tôi chịu hoàn toàn trách nhiệm đối với mọi khiếu nại, tranh chấp hoặc vi phạm liên quan đến quyền sở hữu trí tuệ và bản quyền phát sinh từ khóa học này.</p>
                            <p>Tôi hiểu rằng Fea Laerning chỉ cung cấp nền tảng đăng tải khóa học. Nếu phát hiện hoặc nhận được khiếu nại hợp lệ về bản quyền, nền tảng có quyền từ chối duyệt, tạm khóa hoặc gỡ bỏ khóa học.</p>
                        </div>

                        <form id="copyrightSubmitForm" method="POST" action="{{ route('instructor.courses.submit', $course) }}" class="mt-5">
                            @csrf
                            <div class="relative flex items-start">
                                <div class="flex h-5 items-center">
                                    <input id="agreeCheckbox" name="copyright_agreed" type="checkbox" value="1" onchange="toggleSubmitButton()"
                                           class="h-4.5 w-4.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="agreeCheckbox" class="font-semibold text-slate-700 select-none cursor-pointer">
                                        Tôi đã đọc và đồng ý với cam kết bản quyền.
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button type="button" id="confirmSubmitBtn" onclick="submitCopyrightForm()" disabled
                        class="inline-flex w-full min-h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors duration-200 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto">
                    Xác nhận gửi duyệt
                </button>
                <button type="button" onclick="closeCopyrightModal()"
                        class="mt-3 inline-flex w-full min-h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 sm:mt-0 sm:w-auto cursor-pointer">
                    Hủy
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openCopyrightModal() {
        const modal = document.getElementById('copyrightModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Escape key closes the modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCopyrightModal();
        }
    });

    function closeCopyrightModal() {
        const modal = document.getElementById('copyrightModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Reset form
        document.getElementById('agreeCheckbox').checked = false;
        document.getElementById('confirmSubmitBtn').disabled = true;
    }

    function toggleSubmitButton() {
        const checkbox = document.getElementById('agreeCheckbox');
        const btn = document.getElementById('confirmSubmitBtn');
        btn.disabled = !checkbox.checked;
    }

    function submitCopyrightForm() {
        const checkbox = document.getElementById('agreeCheckbox');
        if (checkbox.checked) {
            document.getElementById('copyrightSubmitForm').submit();
        }
    }
</script>

</x-instructor-layout>
