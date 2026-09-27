@php
    $totalVideoLessons = $course->lessons()->where('type', 'video')->count();
    $videoReadinessBlockers = $reviewState['videoReadinessBlockers'];
    $hasVideoReadinessBlockers = $videoReadinessBlockers !== [];
    $videoBlockerTitle = $videoReadinessBlockers[0]['title'] ?? null;
@endphp

<div id="curriculum-review-state-root" class="space-y-4" data-draft-count="{{ $reviewState['draftCount'] }}" data-pending-count="{{ $reviewState['pendingCount'] }}">
    @if($reviewState['hasPendingUpdates'])
        <div id="curriculum-pending-review-state" class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
            <p class="font-bold">{{ $reviewState['pendingCount'] }} thay đổi đang chờ Admin duyệt.</p>
            @if($reviewState['draftCount'] > 0)
                <p class="mt-1 font-semibold">{{ $reviewState['draftCount'] }} thay đổi mới đang lưu nháp cho lượt tiếp theo.</p>
                <p class="mt-1 text-xs leading-5">Bạn có thể tiếp tục lưu thay đổi mới. Sau khi Admin xử lý lượt đang chờ, các bản nháp mới sẽ có thể gửi duyệt.</p>
            @endif
        </div>
    @elseif($reviewState['draftCount'] > 0)
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            <p class="font-bold">{{ $reviewState['draftCount'] }} thay đổi mới đang lưu nháp.</p>
            @if($reviewState['draftVersionLabels'] !== [])
                <p class="mt-1 text-xs">Bản đề xuất: {{ collect($reviewState['draftVersionLabels'])->pluck('label')->filter()->implode(', ') ?: 'đang chuẩn bị phiên bản' }}.</p>
            @endif
        </div>
    @endif

    @if($reviewState['activeUpdates']->isNotEmpty())
        <div id="curriculum-active-review-panel" class="rounded-lg border border-amber-200 bg-amber-50 p-4">
            <h4 class="text-sm font-bold text-amber-900">Thay đổi đang hoạt động — nháp / chờ duyệt ({{ $reviewState['activeUpdates']->count() }}):</h4>
            <ul class="mt-2 space-y-2 text-xs text-amber-900">
                @foreach($reviewState['activeUpdates'] as $pUpdate)
                    @include('instructor.courses.partials.curriculum-review-update-row', ['pUpdate' => $pUpdate])
                @endforeach
            </ul>
        </div>
    @endif

    @if($reviewState['actionableRejectedUpdates']->isNotEmpty())
        <div id="curriculum-actionable-rejections-panel" class="rounded-lg border border-rose-200 bg-rose-50 p-4">
            <h4 class="text-sm font-bold text-rose-900">Thay đổi bị từ chối cần xử lý ({{ $reviewState['actionableRejectedUpdates']->count() }}):</h4>
            <ul class="mt-2 space-y-2 text-xs text-rose-900">
                @foreach($reviewState['actionableRejectedUpdates'] as $pUpdate)
                    @include('instructor.courses.partials.curriculum-review-update-row', ['pUpdate' => $pUpdate])
                @endforeach
            </ul>
        </div>
    @endif

    <div id="common-hls-banner-wrapper"
         class="rounded-xl border p-4 shadow-xs transition-all duration-300 {{ $totalVideoLessons === 0 && ! $reviewState['hasDraftUpdates'] && ! $reviewState['hasPendingUpdates'] ? 'hidden' : ($hasVideoReadinessBlockers ? 'border-amber-200 bg-amber-50/80 text-amber-900' : 'border-emerald-200 bg-emerald-50/80 text-emerald-900') }}">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p id="common-hls-message" class="text-sm font-bold">
                    @if($hasVideoReadinessBlockers)
                        Còn video chưa sẵn sàng: {{ $videoBlockerTitle }}.
                    @elseif($reviewState['hasPendingUpdates'])
                        {{ $reviewState['submissionBlockedReason'] }}
                    @elseif($reviewState['hasDraftUpdates'])
                        {{ $reviewState['draftCount'] }} thay đổi đã sẵn sàng để gửi duyệt.
                    @elseif($totalVideoLessons > 0)
                        Tất cả video đã được xử lý bảo mật thành công. Chưa có thay đổi mới để gửi duyệt.
                    @endif
                </p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if($reviewState['canSubmitCourse'])
                    <form method="POST" action="{{ route('instructor.courses.submit', $course) }}" id="curriculumSubmitForm">
                        @csrf
                        <input type="hidden" name="copyright_agreed" value="1">
                        <button type="submit" id="curriculum-submit-review-btn" class="inline-flex min-h-10 cursor-pointer items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500">
                            {{ in_array($course->status, ['need_revision', 'rejected'], true) ? 'Gửi duyệt lại' : 'Gửi duyệt' }}
                        </button>
                    </form>
                @elseif($reviewState['hasPendingUpdates'])
                    <button type="button" disabled class="inline-flex min-h-10 cursor-not-allowed items-center justify-center rounded-lg bg-slate-300 px-4 py-2 text-sm font-bold text-slate-600">
                        {{ $reviewState['hasDraftUpdates'] ? 'Chờ lượt duyệt hiện tại' : 'Đang chờ duyệt' }}
                    </button>
                @elseif($reviewState['hasDraftUpdates'])
                    <button type="button" disabled title="{{ $reviewState['submissionBlockedReason'] }}" class="inline-flex min-h-10 cursor-not-allowed items-center justify-center rounded-lg bg-slate-300 px-4 py-2 text-sm font-bold text-slate-600">Chưa thể gửi duyệt</button>
                @endif
            </div>
        </div>
    </div>
</div>
