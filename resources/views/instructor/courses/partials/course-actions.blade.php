@php
    $variant = $variant ?? 'desktop';
    $menuId = 'course-actions-menu-'.$course->id.'-'.$variant;
    $triggerId = 'course-actions-trigger-'.$course->id.'-'.$variant;
    $isPublished = $course->status === 'published';
    $hasWorkflowActions = ($canSubmit && $isReady) || $isPublished;
@endphp

<div class="course-actions relative {{ $variant === 'mobile' ? 'w-full' : 'ml-auto w-fit' }}" data-course-actions>
    <button
        id="{{ $triggerId }}"
        type="button"
        class="inline-flex min-h-10 {{ $variant === 'mobile' ? 'w-full justify-between' : '' }} items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm transition-colors duration-200 hover:border-slate-400 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 cursor-pointer"
        aria-controls="{{ $menuId }}"
        aria-expanded="false"
        aria-haspopup="menu"
        data-course-actions-trigger
    >
        <span>Thao tác</span>
        <svg class="h-3.5 w-3.5 shrink-0 text-slate-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-course-actions-chevron>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
        </svg>
    </button>

    <div
        id="{{ $menuId }}"
        class="absolute right-0 top-full z-[70] mt-0 w-60 max-w-[calc(100vw-2rem)] origin-top-right rounded-lg border border-slate-200 bg-white p-1.5 text-left shadow-xl ring-1 ring-slate-900/5"
        role="menu"
        aria-labelledby="{{ $triggerId }}"
        hidden
        data-course-actions-menu
    >
        <div class="px-2.5 py-1.5 text-[10px] font-black uppercase tracking-[0.12em] text-slate-400" role="presentation">Quản lý</div>

        <a href="{{ route('instructor.courses.students', $course) }}"
           role="menuitem"
           data-course-actions-item
           class="flex min-h-10 w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-2 text-xs font-bold text-blue-700 transition-colors duration-150 hover:bg-blue-50 focus-visible:bg-blue-50 focus-visible:outline-none">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm9 10v-2a4 4 0 0 0-3-3.87m-1-9.13a4 4 0 0 1 0 7.75" />
            </svg>
            <span>Học viên</span>
        </a>

        <a href="{{ route('instructor.courses.edit', $course) }}"
           role="menuitem"
           data-course-actions-item
           class="flex min-h-10 w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-2 text-xs font-bold text-emerald-700 transition-colors duration-150 hover:bg-emerald-50 focus-visible:bg-emerald-50 focus-visible:outline-none">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5h10M9 12h10M9 19h10M4 5h.01M4 12h.01M4 19h.01" />
            </svg>
            <span>Kiểm duyệt</span>
        </a>

        <a href="{{ route('instructor.courses.curriculum', $course) }}"
           role="menuitem"
           data-course-actions-item
           class="flex min-h-10 w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-2 text-xs font-bold text-indigo-700 transition-colors duration-150 hover:bg-indigo-50 focus-visible:bg-indigo-50 focus-visible:outline-none">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5m0-16v16M4 5.5A2.5 2.5 0 0 1 6.5 3" />
            </svg>
            <span>Nội dung</span>
        </a>

        @if ($isPublished)
            <a href="{{ route('courses.show', $course->slug) }}"
               target="_blank"
               rel="noopener"
               role="menuitem"
               data-course-actions-item
               class="flex min-h-10 w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-2 text-xs font-bold text-slate-700 transition-colors duration-150 hover:bg-slate-100 focus-visible:bg-slate-100 focus-visible:outline-none">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" /><circle cx="12" cy="12" r="2.5" stroke-width="1.8" />
                </svg>
                <span>Xem trước</span>
            </a>
        @endif

        @if ($hasWorkflowActions)
            <div class="my-1 border-t border-slate-100" role="separator"></div>

            @if ($canSubmit && $isReady)
                <a href="{{ route('instructor.courses.edit', $course) }}"
                   role="menuitem"
                   data-course-actions-item
                   class="flex min-h-10 w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-2 text-xs font-bold text-amber-700 transition-colors duration-150 hover:bg-amber-50 focus-visible:bg-amber-50 focus-visible:outline-none">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" />
                    </svg>
                    <span>{{ in_array($course->status, ['need_revision', 'rejected'], true) ? 'Gửi lại' : 'Gửi duyệt' }}</span>
                </a>
            @endif

            @if ($isPublished)
                <form method="POST" action="{{ route('instructor.courses.archive', $course) }}" role="none"
                      onsubmit="return confirm('Ẩn khóa học này khỏi trang học viên?')">
                    @csrf
                    <button type="submit"
                            role="menuitem"
                            data-course-actions-item
                            class="flex min-h-10 w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-2 text-left text-xs font-bold text-zinc-700 transition-colors duration-150 hover:bg-zinc-100 focus-visible:bg-zinc-100 focus-visible:outline-none">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 5 14 14" />
                        </svg>
                        <span>Ẩn</span>
                    </button>
                </form>
            @endif
        @endif

        @if ($course->is_featured)
            <div class="my-1 border-t border-slate-100" role="separator"></div>
            <div class="flex items-center gap-2 px-2.5 py-2 text-xs font-bold text-amber-700" role="status">
                <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m12 3 2.78 5.63 6.22.9-4.5 4.39 1.06 6.2L12 17.2l-5.56 2.92 1.06-6.2L3 9.53l6.22-.9L12 3Z" />
                </svg>
                <span>{{ $variant === 'mobile' ? 'Đang nổi bật (Do Admin chọn)' : 'Nổi bật' }}</span>
            </div>
        @endif

        <div class="my-1 border-t border-slate-100" role="separator"></div>
        <form method="POST" action="{{ route('instructor.courses.destroy', $course) }}" role="none"
              onsubmit="return confirm('Bạn chắc chắn muốn xóa hoặc lưu trữ khóa học này?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    role="menuitem"
                    data-course-actions-item
                    class="flex min-h-10 w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-2 text-left text-xs font-bold text-rose-700 transition-colors duration-150 hover:bg-rose-50 focus-visible:bg-rose-50 focus-visible:outline-none">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16m-10 4v6m4-6v6M9 7V4h6v3m-9 0 1 13h10l1-13" />
                </svg>
                <span>Xóa</span>
            </button>
        </form>
    </div>
</div>
