@php
    $discountPrice = $course->discount_price ?? $course->sale_price;
    $price = $discountPrice ?? $course->price;
    $originalPrice = $discountPrice ? $course->price : null;
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $gradients = [
        'from-indigo-500 to-purple-600',
        'from-emerald-500 to-teal-600',
        'from-orange-500 to-red-500',
        'from-blue-500 to-cyan-500',
        'from-pink-500 to-rose-500',
    ];
    $gradient = $gradients[$course->id % count($gradients)];
@endphp

<a href="{{ route('courses.show', $course) }}" class="group bg-white dark:bg-[#161615] rounded-2xl border border-slate-200/60 dark:border-slate-800/80 overflow-hidden hover:shadow-xl hover:shadow-indigo-500/5 dark:hover:shadow-none hover:border-indigo-300 dark:hover:border-indigo-500/50 transition-all duration-300 flex flex-col">
    <!-- Thumbnail / Gradient Placeholder -->
    <div class="aspect-video bg-gradient-to-br {{ $gradient }} relative overflow-hidden">
        @if($course->thumbnail)
            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-16 h-16 text-white/30" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824 2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
        @endif
        @if($course->is_featured)
            <span class="absolute top-3 left-3 bg-amber-400 text-amber-950 text-[10px] font-bold tracking-wider uppercase px-2.5 py-1 rounded-full shadow-sm">Nổi bật</span>
        @endif
        <span class="absolute top-3 right-3 bg-black/45 text-white text-xs font-semibold px-2.5 py-1 rounded-full backdrop-blur-md">
            {{ $levelLabels[$course->level] ?? $course->level }}
        </span>
    </div>

    <!-- Details -->
    <div class="p-5 flex flex-col flex-1">
        @if($course->category)
            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-1.5 uppercase tracking-wider">{{ $course->category->name }}</span>
        @endif
        
        <h3 class="font-bold text-slate-900 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200 line-clamp-2 mb-2 leading-snug">
            {{ $course->title }}
        </h3>
        
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-3.5 flex items-center gap-1">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            {{ $course->instructor?->name ?? 'Giảng viên Fea' }}
        </p>

        <!-- Star Rating -->
        <div class="flex items-center gap-1 mb-4">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-4 h-4 {{ $i <= round($course->rating_avg) ? 'text-amber-400 fill-amber-400' : 'text-slate-200 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
            <span class="text-xs text-slate-500 dark:text-slate-400 ml-1 font-semibold">({{ $course->rating_count }})</span>
        </div>

        <!-- Price and Enrollments -->
        <div class="mt-auto flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/80">
            <div>
                @if($price == 0)
                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">Miễn phí</span>
                @else
                    <span class="text-base font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($price, 0, ',', '.') }}đ</span>
                    @if($originalPrice)
                        <span class="text-xs text-slate-400 line-through ml-1">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                    @endif
                @endif
            </div>
            <span class="text-xs text-slate-400 dark:text-slate-400 flex items-center gap-1 font-medium">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                {{ $course->enrollment_count }}
            </span>
        </div>
    </div>
</a >
