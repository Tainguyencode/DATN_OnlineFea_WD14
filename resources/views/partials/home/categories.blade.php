@if($categories->isNotEmpty())
    <section id="categories" class="bg-white py-10 sm:py-14 lg:py-16 dark:bg-slate-950">
        <div class="ui-container" x-data="{ expanded: false }">
            <div class="mb-6 flex flex-col items-stretch gap-3 sm:flex-row sm:items-end sm:justify-between sm:gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-950 sm:text-2xl dark:text-white">Danh mục môn học</h2>
                    <p class="mt-1 text-xs text-slate-500 sm:text-sm dark:text-slate-400">Khám phá các môn học giúp bạn phát triển kỹ năng chuyên môn.</p>
                </div>
                @if($categories->count() > 4)
                    <button
                        data-home-category-toggle
                        type="button"
                        class="inline-flex shrink-0 self-end items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs font-bold text-[#0D5BD7] transition hover:bg-blue-50 hover:text-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0D5BD7] focus-visible:ring-offset-2 dark:text-blue-300 dark:hover:bg-blue-950/50 dark:hover:text-blue-200 dark:focus-visible:ring-offset-slate-950 sm:self-auto"
                        @click="expanded = ! expanded"
                        :aria-expanded="expanded.toString()"
                        aria-controls="home-category-grid"
                    >
                        <span x-text="expanded ? 'Thu gọn danh mục' : 'Xem tất cả danh mục'">Xem tất cả danh mục</span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                @endif
            </div>

            @php
                $categoryStyles = [
                    ['icon' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300', 'badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300'],
                    ['icon' => 'bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-300', 'badge' => 'bg-violet-50 text-violet-700 dark:bg-violet-950/50 dark:text-violet-300'],
                    ['icon' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-300', 'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'],
                    ['icon' => 'bg-orange-50 text-orange-600 dark:bg-orange-950/50 dark:text-orange-300', 'badge' => 'bg-orange-50 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300'],
                ];
            @endphp

            <div id="home-category-grid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($categories as $index => $category)
                    @php($categoryStyle = $categoryStyles[$index % count($categoryStyles)])
                    <div
                        data-home-category-item
                        @if($index >= 4)
                            data-home-category-extra
                            x-show="expanded"
                            x-cloak
                            x-transition.opacity.duration.200ms
                        @endif
                    >
                        <a data-home-category-card href="{{ route('courses.category', $category->slug) }}" class="group flex h-full min-h-44 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-800">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $categoryStyle['icon'] }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            </span>
                            <h3 class="mt-4 line-clamp-1 text-sm font-extrabold text-slate-950 transition group-hover:text-[#0D5BD7] dark:text-white">{{ $category->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $category->description ?: 'Khóa học chất lượng do đội ngũ giảng viên FEA biên soạn.' }}</p>
                            <span class="mt-auto flex items-center justify-between border-t border-slate-100 pt-3 text-[11px] dark:border-slate-800">
                                <span class="rounded-full px-2 py-1 font-bold {{ $categoryStyle['badge'] }}">{{ (int) $category->courses_count }} khóa học</span>
                                <span class="font-bold text-[#0D5BD7] dark:text-blue-300">Khám phá →</span>
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
