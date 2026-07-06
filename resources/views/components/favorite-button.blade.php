@props([
    'course',
    'favorited' => false,
    'label' => false,
    'block' => false,
])

@php
    $user = auth()->user();
    $isStudent = $user?->isStudent();
    $isFavorited = (bool) $favorited;
    $tooltip = $isFavorited ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích';
    $wrapperClass = trim((string) $attributes->get('class'));
    $buttonBase = $block
        ? 'inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl px-4 text-sm font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2'
        : 'inline-flex h-10 w-10 items-center justify-center rounded-full shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
    $buttonState = $isFavorited
        ? 'border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 focus-visible:ring-rose-400 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20'
        : 'border border-slate-200 bg-white/95 text-slate-600 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 focus-visible:ring-slate-300 dark:border-slate-700 dark:bg-slate-900/95 dark:text-slate-300 dark:hover:border-rose-500/40 dark:hover:bg-rose-500/10 dark:hover:text-rose-300';
    $disabledState = 'border border-slate-200 bg-white/90 text-slate-400 opacity-80 dark:border-slate-700 dark:bg-slate-900/90 dark:text-slate-500';
@endphp

@if($isStudent)
    <form method="POST"
          action="{{ $isFavorited ? route('courses.favorite.destroy', $course) : route('courses.favorite.store', $course) }}"
          class="{{ $wrapperClass }} {{ $block ? 'w-full' : '' }}">
        @csrf
        @if($isFavorited)
            @method('DELETE')
        @endif
        <button type="submit"
                class="{{ $buttonBase }} {{ $buttonState }}"
                title="{{ $tooltip }}"
                aria-label="{{ $tooltip }}">
            <svg class="h-5 w-5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364Z"/>
            </svg>
            @if($label)
                <span>{{ $tooltip }}</span>
            @endif
        </button>
    </form>
@elseif(auth()->check())
    <span class="{{ $wrapperClass }}" title="Chỉ học viên mới có thể yêu thích khóa học">
        <button type="button"
                class="{{ $buttonBase }} {{ $disabledState }}"
                aria-label="Chỉ học viên mới có thể yêu thích khóa học"
                disabled>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364Z"/>
            </svg>
            @if($label)
                <span>Yêu thích</span>
            @endif
        </button>
    </span>
@else
    <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}"
       class="{{ $wrapperClass }} {{ $buttonBase }} {{ $buttonState }}"
       title="Thêm vào yêu thích"
       aria-label="Đăng nhập để thêm vào yêu thích">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364Z"/>
        </svg>
        @if($label)
            <span>Thêm vào yêu thích</span>
        @endif
    </a>
@endif
