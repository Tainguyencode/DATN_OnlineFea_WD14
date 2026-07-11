@props(['item'])

@php
    $isLocked = $item['state'] === 'locked';
    $isCurrent = $item['is_current'];
    $isCompleted = $item['state'] === 'completed';
@endphp

@if($isLocked)
    <div class="flex items-start gap-3 px-4 py-2.5 text-[#6a6f73]">
        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center" aria-hidden="true">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm">{{ $item['title'] }}</p>
            <p class="text-xs">{{ $item['type_label'] }}@if($item['duration']) · {{ $item['duration'] }}@endif</p>
        </div>
    </div>
@else
    <a
        href="{{ $item['url'] }}"
        class="flex items-start gap-3 px-4 py-2.5 transition hover:bg-[#f7f9fa] {{ $isCurrent ? 'border-l-4 border-[#0056D2] bg-[#f0f7ff] pl-3' : '' }}"
        aria-current="{{ $isCurrent ? 'page' : 'false' }}"
    >
        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center" aria-hidden="true">
            @if($isCompleted)
                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            @elseif($item['type'] === 'quiz')
                <svg class="h-4 w-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            @elseif($item['type'] === 'document')
                <svg class="h-4 w-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            @else
                <svg class="h-4 w-4 text-[#0056D2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-[#1c1d1f]">{{ $item['title'] }}</p>
            <p class="text-xs text-[#6a6f73]">
                {{ $item['type_label'] }}
                @if($item['duration']) · {{ $item['duration'] }} @endif
                @if($item['is_preview']) · Xem thử @endif
            </p>
        </div>
    </a>
@endif
