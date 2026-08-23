@props([
    'sections' => [],
    'courseProgress' => 0,
    'completedLessons' => 0,
    'totalLessons' => 0,
    'course' => null,
    'lesson' => null,
    'isEnrolled' => false,
    'courseDiscussion' => null,
])

<div x-data="{ chatOpen: {{ request()->has('open_chat') || request()->has('discussion_id') || request()->query('tab') === 'qa' ? 'true' : 'false' }} }"
     @keydown.escape.window="chatOpen = false">
    <aside
        class="learning-sidebar fixed inset-y-0 right-0 z-40 flex w-full max-w-[400px] flex-col border-l border-[#d1d7dc] bg-white shadow-xl transition-transform duration-300 lg:static lg:z-auto lg:max-w-none lg:translate-x-0 lg:shadow-none"
        data-learning-sidebar
        data-sidebar-open="true"
    >
        <!-- HEADER CHAT MINI Ở SIDEBAR PHÍA TRÊN DANH SÁCH BÀI HỌC -->
        @if($course)
            @php
                $instructor = $course->instructor;
            @endphp
            <div class="border-b border-[#d1d7dc] p-3.5 bg-slate-50/80">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="relative shrink-0">
                            @if($instructor?->avatar)
                                <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-2xs">
                            @else
                                <div class="w-10 h-10 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center text-sm border border-slate-200 shadow-2xs">
                                    {{ strtoupper(mb_substr($instructor?->name ?? 'G', 0, 1)) }}
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate leading-snug">
                                {{ $instructor?->name ?? 'Giảng viên' }}
                            </h4>
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Giảng viên
                                </span>
                            </div>
                        </div>
                    </div>

                    <button type="button" 
                            @click="chatOpen = true; $nextTick(() => { const el = document.getElementById('student-chat-body'); if(el) el.scrollTop = el.scrollHeight; })"
                            class="cursor-pointer inline-flex items-center gap-1.5 rounded-xl bg-[#0056D2] hover:bg-[#0046B8] text-white px-3 py-2 text-xs font-bold transition shadow-xs shrink-0"
                            title="Mở trao đổi với giảng viên">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Chat</span>
                    </button>
                </div>
            </div>
        @endif

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

    <!-- CHAT DRAWER PANEL -->
    @if($course && $lesson)
        <x-learning.course-chat-drawer
            :course="$course"
            :lesson="$lesson"
            :is-enrolled="$isEnrolled"
            :course-discussion="$courseDiscussion"
        />
    @endif
</div>
