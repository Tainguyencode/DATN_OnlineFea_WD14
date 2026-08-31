@php($versionContext = app(\App\Services\ContentUpdateDiffService::class)->versionContext($pUpdate))

<li data-content-update-id="{{ $pUpdate->id }}" class="flex flex-wrap items-center justify-between gap-2 rounded-md border {{ $pUpdate->isRejected() ? 'border-rose-200 bg-rose-100/60' : 'border-amber-200 bg-amber-100/60' }} p-2">
    <div>
        <span class="rounded bg-indigo-50 px-2 py-0.5 text-[11px] font-bold uppercase text-indigo-700">{{ $pUpdate->type }}</span>
        <span class="font-semibold text-slate-700">({{ $pUpdate->action }})</span>:
        <strong class="font-bold text-slate-900">@if(isset($pUpdate->payload['title'])) "{{ $pUpdate->payload['title'] }}" @else #{{ $pUpdate->entity_id }} @endif</strong>
        — <span class="font-bold {{ $pUpdate->isRejected() ? 'text-rose-700' : ($pUpdate->isPending() ? 'text-blue-800' : 'text-amber-800') }}">{{ $pUpdate->isRejected() ? 'Bị từ chối' : ($pUpdate->isPending() ? 'Chờ duyệt' : 'Nháp') }}</span>
        @if(($versionContext['current'] ?? null) !== null)
            <span class="ml-1 font-semibold text-slate-600">Đang xuất bản: V{{ $versionContext['current'] }}</span>
        @endif
        @if(($versionContext['proposed'] ?? null) !== null)
            <span class="ml-1 font-semibold {{ $pUpdate->isRejected() ? 'text-rose-700' : ($pUpdate->isPending() ? 'text-blue-800' : 'text-amber-800') }}">
                @if($pUpdate->isRejected())
                    Đề xuất V{{ $versionContext['proposed'] }} — Bị từ chối
                @else
                    Đề xuất V{{ $versionContext['proposed'] }} — {{ $pUpdate->isPending() ? 'Chờ duyệt' : 'Nháp' }}
                @endif
            </span>
        @endif
        @if($pUpdate->rejection_reason)
            <span class="mt-0.5 block text-[11px] font-semibold text-rose-600">Lý do từ chối: {{ $pUpdate->rejection_reason }}</span>
        @endif
    </div>
    @if($pUpdate->isRejected())
        <form method="POST" action="{{ route('instructor.courses.content-updates.revise', [$course, $pUpdate]) }}">
            @csrf
            <button type="submit" class="cursor-pointer rounded bg-rose-700 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-rose-800">Tạo bản chỉnh sửa mới</button>
        </form>
    @elseif($pUpdate->isPending())
        <span class="rounded bg-blue-100 px-2.5 py-1 text-[11px] font-bold text-blue-800">Chỉ đọc trong khi chờ duyệt</span>
    @else
        <span class="rounded bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">Bản nháp cho lượt duyệt tiếp theo</span>
    @endif
</li>
