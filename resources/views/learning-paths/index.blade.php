@extends('layouts.app')

@section('title', 'Lộ trình học tập Chuyên nghiệp - FEA Learning')

@section('content')
    {{-- Hero Section (Unified with site-wide Dark Slate-950 design) --}}
    <section class="bg-slate-950 text-white relative overflow-hidden border-b border-slate-800">
        <div class="pointer-events-none absolute -top-40 right-0 h-96 w-96 rounded-full bg-indigo-600/10 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-0 left-0 h-64 w-64 rounded-full bg-blue-600/10 blur-3xl"></div>

        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[minmax(0,1fr)_380px] lg:px-8 lg:py-16 items-center">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-400/30 bg-indigo-500/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-indigo-200">
                    <svg class="h-3.5 w-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Career Learning Paths
                </span>

                <h1 class="mt-4 max-w-3xl text-3xl font-extrabold tracking-tight sm:text-5xl leading-tight">
                    Lộ Trình Học Tập & Định Hướng Sự Nghiệp
                </h1>
                <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                    Chương trình đào tạo thiết kế theo từng giai đoạn chuẩn doanh nghiệp. Giúp bạn tiết kiệm thời gian, làm chủ kỹ năng cốt lõi và sẵn sàng cho dự án thực tế.
                </p>

                <div class="mt-7 flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-300">
                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">🎯 {{ $stats['total_paths'] }} Lộ trình bài bản</span>
                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">📚 {{ $stats['total_courses'] }} Khóa học tích hợp</span>
                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">👨‍🎓 {{ $stats['total_students'] }}+ Học viên theo học</span>
                </div>
            </div>

            {{-- Stat & Info Box --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-indigo-950/50 backdrop-blur space-y-4">
                <div class="text-xs font-bold uppercase tracking-wider text-indigo-300">Tổng quan đào tạo</div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-white/5 p-4 border border-white/10">
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Khóa học</span>
                        <strong class="mt-1 block text-2xl font-black text-indigo-400">{{ $stats['total_courses'] }}</strong>
                    </div>
                    <div class="rounded-xl bg-white/5 p-4 border border-white/10">
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Học viên</span>
                        <strong class="mt-1 block text-2xl font-black text-emerald-400">{{ $stats['total_students'] }}+</strong>
                    </div>
                </div>
                <div class="rounded-xl bg-slate-900/80 p-4 border border-white/10">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300">
                        <span>Định hướng bài bản</span>
                        <span class="text-emerald-400">100% Thực chiến</span>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-white/10">
                        <div class="h-full w-full rounded-full bg-gradient-to-r from-indigo-500 via-blue-500 to-emerald-400"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content Section --}}
    <section class="bg-slate-50 py-10 dark:bg-[#0a0a0a]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Filter & Search Form (Matching courses/index.blade.php form design) --}}
            <form method="GET" action="{{ route('learning-paths.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-[#161615] sm:p-5 mb-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-between">
                    <div class="relative flex-1 max-w-lg">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                        </svg>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm theo vị trí việc làm hoặc tên lộ trình..."
                            class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="h-11 rounded-xl bg-indigo-600 px-6 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
                            Tìm kiếm
                        </button>
                        @if(request('search') || request('level'))
                            <a href="{{ route('learning-paths.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-800 dark:text-slate-300 dark:hover:bg-slate-900">
                                Đặt lại
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Level Pills Filter --}}
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mr-1">Cấp độ:</span>
                    <a href="{{ route('learning-paths.index', array_filter(['search' => request('search')])) }}"
                        class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ !request('level') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                        Tất cả cấp độ
                    </a>
                    <a href="{{ route('learning-paths.index', array_merge(request()->query(), ['level' => 'beginner'])) }}"
                        class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request('level') === 'beginner' ? 'bg-emerald-600 text-white shadow-md' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                        🌱 Cơ bản (Beginner)
                    </a>
                    <a href="{{ route('learning-paths.index', array_merge(request()->query(), ['level' => 'intermediate'])) }}"
                        class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request('level') === 'intermediate' ? 'bg-amber-600 text-white shadow-md' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                        ⚡ Trung cấp (Intermediate)
                    </a>
                    <a href="{{ route('learning-paths.index', array_merge(request()->query(), ['level' => 'advanced'])) }}"
                        class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request('level') === 'advanced' ? 'bg-purple-600 text-white shadow-md' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                        🚀 Nâng cao (Advanced)
                    </a>
                </div>
            </form>

            @if ($learningPaths->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-800 dark:bg-[#161615]">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-950 dark:text-white">Không tìm thấy lộ trình phù hợp</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Thử thay đổi từ khóa hoặc bộ lọc tìm kiếm.</p>
                    <a href="{{ route('learning-paths.index') }}" class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">
                        Xem tất cả lộ trình
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($learningPaths as $path)
                        @php
                            $totalLessons = $path->courses->sum('lessons_count');
                            $skillsList = is_array($path->skills) ? $path->skills : (json_decode($path->skills ?? '[]', true) ?: []);
                            $levelLabel = match($path->level) {
                                'beginner' => 'Cơ bản',
                                'intermediate' => 'Trung cấp',
                                'advanced' => 'Nâng cao',
                                default => 'Mọi trình độ'
                            };
                            $levelBadge = match($path->level) {
                                'beginner' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                                'intermediate' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                                'advanced' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
                                default => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800'
                            };
                        @endphp

                        <div class="group flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-indigo-300 hover:shadow-xl dark:border-slate-800 dark:bg-[#161615] dark:hover:border-indigo-500/50">
                            <div>
                                {{-- Header Tags --}}
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $levelBadge }}">
                                        {{ $levelLabel }}
                                    </span>

                                    @if($path->target_role)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 text-xs font-bold text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800">
                                            🎯 {{ $path->target_role }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Title --}}
                                <h2 class="mb-2 text-lg font-extrabold text-slate-950 transition duration-200 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-300 leading-snug">
                                    <a href="{{ route('learning-paths.show', $path->slug) }}">
                                        {{ $path->title }}
                                    </a>
                                </h2>

                                {{-- Description --}}
                                <p class="line-clamp-3 text-xs leading-relaxed text-slate-500 dark:text-slate-400 mb-4">
                                    {{ $path->description ?? 'Lộ trình được chuẩn hóa giúp bạn nắm vững kiến thức theo các giai đoạn bài bản.' }}
                                </p>

                                {{-- Meta Info --}}
                                <div class="mb-4 space-y-2 rounded-xl bg-slate-50 p-3.5 border border-slate-100 dark:bg-slate-900/60 dark:border-slate-800 text-xs">
                                    @if($path->salary_range)
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-slate-500 dark:text-slate-400">💼 Mức lương mục tiêu:</span>
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $path->salary_range }}</span>
                                        </div>
                                    @endif
                                    @if($path->estimated_duration)
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-slate-500 dark:text-slate-400">⏱️ Thời lượng ước tính:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $path->estimated_duration }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Skills Badges --}}
                                @if(!empty($skillsList))
                                    <div class="mb-4">
                                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Kỹ năng đạt được:</div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach(array_slice($skillsList, 0, 4) as $skill)
                                                <span class="rounded-lg bg-slate-100 border border-slate-200/60 px-2 py-0.5 text-[11px] font-semibold text-slate-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300">
                                                    {{ $skill }}
                                                </span>
                                            @endforeach
                                            @if(count($skillsList) > 4)
                                                <span class="rounded-lg bg-slate-200/80 px-2 py-0.5 text-[11px] font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                                    +{{ count($skillsList) - 4 }} khác
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer CTA --}}
                            <div class="border-t border-slate-100 pt-4 dark:border-slate-800">
                                <div class="mb-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    <span>📚 {{ $path->courses_count }} môn học</span>
                                    <span>▶ {{ $totalLessons }} bài học</span>
                                </div>

                                <a href="{{ route('learning-paths.show', $path->slug) }}"
                                    class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-4 text-xs font-bold text-white transition shadow-md shadow-indigo-600/10">
                                    <span>Khám phá lộ trình</span>
                                    <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $learningPaths->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
