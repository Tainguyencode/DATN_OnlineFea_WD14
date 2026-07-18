@props([
    'sections' => [],
    'courseProgress' => 0,
    'completedLessons' => 0,
    'totalLessons' => 0,
])

<aside
    class="learning-sidebar fixed inset-y-0 right-0 z-40 flex w-full max-w-[400px] flex-col border-l border-[#d1d7dc] bg-white shadow-xl transition-transform duration-300 lg:static lg:z-auto lg:max-w-none lg:translate-x-0 lg:shadow-none"
    data-learning-sidebar
    data-sidebar-open="true"
>
    <div class="flex h-14 shrink-0 items-center justify-between border-b border-[#d1d7dc] px-4">
        <div>
            <h2 class="text-sm font-bold text-[#1c1d1f]">Nội dung khóa học</h2>
            <p class="text-xs text-[#6a6f73]">{{ $completedLessons }}/{{ $totalLessons }} bài · {{ number_format($courseProgress, 0) }}%</p>
        </div>
        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded hover:bg-[#f7f9fa] lg:hidden" data-close-sidebar aria-label="Đóng nội dung">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto">
        @php $sectionIndex = 0; @endphp
        @forelse($sections as $section)
            @php $sectionIndex++; @endphp
            <div class="border-b border-[#d1d7dc]" x-data="{ open: {{ $section['is_open'] ? 'true' : 'false' }} }">
                <button
                    type="button"
                    class="flex w-full items-start justify-between gap-3 px-4 py-3 text-left hover:bg-[#f7f9fa]"
                    x-on:click="open = !open"
                    :aria-expanded="open"
                >
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">Chương {{ $sectionIndex }}</p>
                        <p class="mt-0.5 text-sm font-bold text-[#1c1d1f]">{{ $section['title'] }}</p>
                        <p class="mt-0.5 text-xs text-[#6a6f73]">
                            {{ $section['completed_count'] }}/{{ $section['total_count'] }} bài
                            @if($section['duration_label'])
                                · {{ $section['duration_label'] }}
                            @endif
                        </p>
                    </div>
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#6a6f73] transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open" x-cloak class="pb-1">
                    @foreach($section['lessons'] as $item)
                        <x-learning.lesson-item :item="$item" />
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-sm text-[#6a6f73]">Khóa học chưa có nội dung.</div>
        @endforelse
    </div>
</aside>

<div class="learning-sidebar-backdrop fixed inset-0 z-30 hidden bg-black/50 lg:hidden" data-sidebar-backdrop></div>
