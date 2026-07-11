@props([
    'lesson',
    'course',
    'sectionTitle' => null,
    'navigation' => ['previous' => null, 'next' => null],
    'lessonState' => 'available',
    'isEnrolled' => false,
    'canAccessLesson' => false,
])

<div class="learning-tabs border-t border-[#d1d7dc] bg-white">
    <div class="border-b border-[#d1d7dc] px-4 sm:px-6" x-data="{ tab: 'overview' }">
        <div class="flex gap-1 overflow-x-auto" role="tablist">
            @foreach(['overview' => 'Tổng quan', 'notes' => 'Ghi chú', 'qa' => 'Hỏi đáp', 'resources' => 'Tài liệu', 'announcements' => 'Thông báo'] as $key => $label)
                <button
                    type="button"
                    role="tab"
                    class="shrink-0 border-b-2 px-4 py-3 text-sm font-semibold transition"
                    :class="tab === '{{ $key }}' ? 'border-[#1c1d1f] text-[#1c1d1f]' : 'border-transparent text-[#6a6f73] hover:text-[#1c1d1f]'"
                    x-on:click="tab = '{{ $key }}'"
                    :aria-selected="tab === '{{ $key }}'"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="max-w-4xl px-4 py-6 sm:px-6">
            <div x-show="tab === 'overview'" x-cloak>
                <h1 class="text-xl font-bold text-[#1c1d1f] sm:text-2xl">{{ $lesson->title }}</h1>
                @if($sectionTitle)
                    <p class="mt-1 text-sm text-[#6a6f73]">Chương: {{ $sectionTitle }}</p>
                @endif

                @if($lesson->content)
                    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-[#1c1d1f]">{{ $lesson->content }}</div>
                @else
                    <p class="mt-4 text-sm text-[#6a6f73]">Bài học chưa có mô tả chi tiết.</p>
                @endif

                @if($lesson->document_file)
                    <div class="mt-6">
                        <h3 class="text-sm font-bold text-[#1c1d1f]">Tài nguyên đính kèm</h3>
                        <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-[#0056D2] hover:underline">
                            Tải tài liệu bài học
                        </a>
                    </div>
                @endif

                <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-[#6a6f73]">Giảng viên</dt>
                        <dd class="mt-1 text-[#1c1d1f]">{{ $course->instructor?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-[#6a6f73]">Cập nhật</dt>
                        <dd class="mt-1 text-[#1c1d1f]">{{ $lesson->updated_at?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div x-show="tab === 'notes'" x-cloak>
                <p class="text-sm text-[#6a6f73]">Tính năng ghi chú sẽ được bổ sung trong phiên bản tiếp theo.</p>
            </div>
            <div x-show="tab === 'qa'" x-cloak>
                <p class="text-sm text-[#6a6f73]">Khu vực hỏi đáp sẽ được bổ sung trong phiên bản tiếp theo.</p>
            </div>
            <div x-show="tab === 'resources'" x-cloak>
                @if($lesson->document_file)
                    <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="inline-flex h-10 items-center rounded border border-[#d1d7dc] px-4 text-sm font-semibold text-[#1c1d1f] hover:bg-[#f7f9fa]">Tải tài liệu</a>
                @else
                    <p class="text-sm text-[#6a6f73]">Không có tài liệu đính kèm.</p>
                @endif
            </div>
            <div x-show="tab === 'announcements'" x-cloak>
                <p class="text-sm text-[#6a6f73]">Chưa có thông báo cho bài học này.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#d1d7dc] px-4 py-4 sm:px-6">
        <div>
            @if($navigation['previous'])
                <a href="{{ $navigation['previous']['url'] }}" class="inline-flex h-10 items-center rounded border border-[#d1d7dc] px-4 text-sm font-semibold text-[#1c1d1f] hover:bg-[#f7f9fa]">
                    ← Bài trước
                </a>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($isEnrolled && $lesson->type !== 'video' && $canAccessLesson && $lessonState !== 'completed')
                <button type="button" data-mark-lesson-complete class="inline-flex h-10 items-center rounded bg-[#0056D2] px-4 text-sm font-bold text-white hover:bg-[#0046B8]">
                    Đánh dấu hoàn thành
                </button>
            @endif

            @if($navigation['next'])
                <a href="{{ $navigation['next']['url'] }}" class="inline-flex h-10 items-center rounded bg-[#1c1d1f] px-4 text-sm font-bold text-white hover:bg-black">
                    Bài tiếp theo →
                </a>
            @endif
        </div>
    </div>
</div>
