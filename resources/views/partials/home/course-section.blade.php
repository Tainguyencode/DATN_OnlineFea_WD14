<section id="{{ $sectionId }}" class="py-10 sm:py-14 lg:py-16 {{ $alternate ? 'border-y border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/30' : 'bg-white dark:bg-slate-950' }}">
    <div class="ui-container">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-950 sm:text-2xl dark:text-white">{{ $title }}</h2>
                <p class="mt-1 text-xs text-slate-500 sm:text-sm dark:text-slate-400">{{ $subtitle }}</p>
            </div>
            <a href="{{ route('courses.index', $variant === 'free' ? ['pricing' => 'free'] : []) }}" class="shrink-0 text-xs font-bold text-[#0D5BD7] hover:text-blue-700 dark:text-blue-300">Xem tất cả →</a>
        </div>

        @if($courses->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                Hiện chưa có {{ mb_strtolower($title) }} được xuất bản.
            </div>
        @else
            <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 sm:gap-x-5 sm:gap-y-5 lg:grid-cols-4 lg:gap-x-5 lg:gap-y-5">
                @foreach($courses as $course)
                    <x-home.course-card
                        :course="$course"
                        :variant="$variant"
                        :favorited="in_array((int) $course->id, $favoriteCourseIds, true)"
                        :enrolled="in_array((int) $course->id, $enrolledCourseIds, true)" />
                @endforeach
            </div>
        @endif
    </div>
</section>
