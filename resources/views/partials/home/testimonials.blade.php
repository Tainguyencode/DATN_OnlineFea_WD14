<section id="student-reviews" class="border-y border-slate-100 bg-slate-50 py-10 sm:py-14 lg:py-16 dark:border-slate-800 dark:bg-slate-900/30">
    <div class="ui-container">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-950 sm:text-2xl dark:text-white">Học viên nói gì?</h2>
            <p class="mt-1 max-w-2xl text-xs text-slate-500 sm:text-sm dark:text-slate-400">Những chia sẻ thực tế từ học viên sau khi trải nghiệm các khóa học tại FEA Learning.</p>
        </div>
        @if($testimonials->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                Chưa có chia sẻ công khai từ học viên.
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                @foreach($testimonials as $testimonial)
                    @php
                        $studentAvatar = null;
                        if ($testimonial->user?->avatar) {
                            $studentAvatar = str_starts_with($testimonial->user->avatar, 'http://') || str_starts_with($testimonial->user->avatar, 'https://')
                                ? $testimonial->user->avatar
                                : asset('storage/'.$testimonial->user->avatar);
                        }
                    @endphp
                    <article data-home-testimonial class="flex min-h-52 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center gap-3">
                            @if($studentAvatar)
                                <img src="{{ $studentAvatar }}" alt="{{ $testimonial->user->name }}" width="44" height="44" loading="lazy" decoding="async" class="h-11 w-11 rounded-full object-cover">
                            @else
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-black text-[#0D5BD7] dark:bg-blue-950/60 dark:text-blue-300">{{ mb_strtoupper(mb_substr($testimonial->user?->name ?? 'H', 0, 1)) }}</span>
                            @endif
                            <span class="min-w-0">
                                <strong class="block truncate text-sm text-slate-950 dark:text-white">{{ $testimonial->user?->name ?? 'Học viên FEA' }}</strong>
                                <span class="mt-0.5 flex gap-0.5" aria-label="{{ $testimonial->rating }} trên 5 sao">
                                    @for($star = 1; $star <= 5; $star++)
                                        <svg class="h-3.5 w-3.5 {{ $star <= $testimonial->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                                    @endfor
                                </span>
                            </span>
                        </div>
                        <p class="mt-4 line-clamp-4 text-sm leading-6 text-slate-600 dark:text-slate-300">“{{ $testimonial->comment }}”</p>
                        @if($testimonial->course)
                            <a href="{{ route('courses.show', $testimonial->course->slug) }}" class="mt-auto line-clamp-1 pt-4 text-xs font-bold text-[#0D5BD7] hover:text-blue-700 dark:text-blue-300">{{ $testimonial->course->title }}</a>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
