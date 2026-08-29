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
        ? 'inline-flex h-11 w-full cursor-pointer items-center justify-center gap-2 rounded-xl px-4 text-sm font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2'
        : 'inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
    $buttonState = $isFavorited
        ? 'border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 focus-visible:ring-rose-400 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20'
        : 'border border-slate-200 bg-white/95 text-slate-600 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 focus-visible:ring-slate-300 dark:border-slate-700 dark:bg-slate-900/95 dark:text-slate-300 dark:hover:border-rose-500/40 dark:hover:bg-rose-500/10 dark:hover:text-rose-300';
    $disabledState = 'border border-slate-200 bg-white/90 text-slate-400 opacity-80 dark:border-slate-700 dark:bg-slate-900/90 dark:text-slate-500';
    $wrapperStyle = trim('pointer-events: auto; z-index: 20; '.(string) $attributes->get('style'));
@endphp

@if($isStudent)
    <div x-data="{
            favorited: @js($isFavorited),
            loading: false,
            courseId: {{ (int) $course->id }},
            storeUrl: @js(route('courses.favorite.store', $course)),
            destroyUrl: @js(route('courses.favorite.destroy', $course)),
            async toggle() {
                if (this.loading) return;
                const prev = this.favorited;
                this.favorited = !this.favorited;
                this.loading = true;
                const url = prev ? this.destroyUrl : this.storeUrl;
                const method = prev ? 'DELETE' : 'POST';

                try {
                    const csrfMeta = document.querySelector('meta[name=\'csrf-token\']');
                    const token = csrfMeta ? csrfMeta.getAttribute('content') : @js(csrf_token());
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            _token: token,
                            _method: method
                        })
                    });

                    if (!res.ok) {
                        throw new Error('Request failed with status ' + res.status);
                    }

                    const data = await res.json();
                    this.favorited = Boolean(data.favorited);

                    window.dispatchEvent(new CustomEvent('favorite-updated', {
                        detail: {
                            courseId: this.courseId,
                            favorited: this.favorited,
                            count: data.count,
                            message: data.message
                        }
                    }));

                    if (window.AppToast && data.message) {
                        window.AppToast.show({
                            message: data.message,
                            type: this.favorited ? 'success' : 'info'
                        });
                    }
                } catch (err) {
                    this.favorited = prev;
                    if (window.AppToast) {
                        window.AppToast.show({
                            message: 'Không thể cập nhật yêu thích. Vui lòng thử lại.',
                            type: 'error'
                        });
                    }
                } finally {
                    this.loading = false;
                }
            }
         }"
         x-on:favorite-updated.window="if ($event.detail.courseId === courseId) favorited = $event.detail.favorited"
         class="pointer-events-auto {{ $wrapperClass }} {{ $block ? 'w-full' : '' }}"
         style="{{ $wrapperStyle }}">
        <button type="button"
                x-on:click.prevent.stop="toggle()"
                :disabled="loading"
                :title="favorited ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích'"
                :aria-label="favorited ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích'"
                :class="favorited
                    ? 'border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 focus-visible:ring-rose-400 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20'
                    : 'border border-slate-200 bg-white/95 text-slate-600 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 focus-visible:ring-slate-300 dark:border-slate-700 dark:bg-slate-900/95 dark:text-slate-300 dark:hover:border-rose-500/40 dark:hover:bg-rose-500/10 dark:hover:text-rose-300'"
                class="{{ $buttonBase }}"
                :style="loading ? 'opacity: 0.7; cursor: wait;' : ''">
            <svg class="h-5 w-5 transition-transform duration-200"
                 :class="{ 'scale-110': favorited, 'animate-pulse': loading }"
                 :fill="favorited ? 'currentColor' : 'none'"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24"
                 aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364Z"/>
            </svg>
            @if($label)
                <span x-text="favorited ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích'">{{ $isFavorited ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' }}</span>
            @endif
        </button>
    </div>
@elseif(auth()->check())
    <span class="pointer-events-auto {{ $wrapperClass }}" title="Chỉ học viên mới có thể yêu thích khóa học" style="{{ $wrapperStyle }}">
        <button type="button"
                class="{{ str_replace('cursor-pointer', 'cursor-not-allowed', $buttonBase) }} {{ $disabledState }}"
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
       class="pointer-events-auto {{ $wrapperClass }} {{ $buttonBase }} {{ $buttonState }}"
       style="{{ $wrapperStyle }}"
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
