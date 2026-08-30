<x-student-layout title="Tổng quan">
    <section class="mb-5 flex flex-col gap-4 rounded-2xl border border-blue-100 bg-gradient-to-r from-white to-blue-50 p-4 shadow-sm sm:flex-row sm:items-center sm:p-5 dark:border-blue-950 dark:from-slate-900 dark:to-blue-950/30">
        <img src="{{ $user->avatarUrl() }}" alt="Ảnh đại diện của {{ $user->name }}" class="h-16 w-16 rounded-xl object-cover ring-4 ring-white shadow-sm dark:ring-slate-800">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-[#0056D2] dark:text-blue-300">Chào mừng bạn trở lại</p>
            <h1 class="mt-0.5 truncate text-2xl font-extrabold text-slate-950 dark:text-white">Xin chào, {{ $user->name }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <span class="break-all">{{ $user->email }}</span>
                <span aria-hidden="true">·</span>
                <span class="inline-flex items-center gap-1 font-semibold {{ $user->hasVerifiedEmail() ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">
                    <span aria-hidden="true">{{ $user->hasVerifiedEmail() ? '✓' : '!' }}</span>
                    {{ $user->hasVerifiedEmail() ? 'Email đã xác thực' : 'Email chưa xác thực' }}
                </span>
            </div>
        </div>
        <a href="{{ route('student.profile') }}" class="inline-flex min-h-10 shrink-0 items-center justify-center rounded-lg border border-blue-200 bg-white px-4 text-sm font-bold text-[#0056D2] hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:border-blue-900 dark:bg-slate-900 dark:text-blue-300">Chỉnh sửa hồ sơ</a>
    </section>

    <section aria-labelledby="student-stats-title" class="mb-6">
        <h2 id="student-stats-title" class="sr-only">Thống kê học tập</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-student.dashboard.stat-card label="Khóa học đã đăng ký" :value="$stats['enrolled']" :href="route('student.courses')" />
            <x-student.dashboard.stat-card label="Khóa học đang học" :value="$stats['in_progress']" :href="route('student.courses', ['status' => 'in_progress'])" tone="amber" icon="play" />
            <x-student.dashboard.stat-card label="Khóa học hoàn thành" :value="$stats['completed']" :href="route('student.courses', ['status' => 'completed'])" tone="emerald" icon="check" />
            <x-student.dashboard.stat-card label="Chứng chỉ đã nhận" :value="$stats['certificates']" :href="route('student.certificates')" tone="violet" icon="award" />
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(18rem,0.75fr)]">
        <div class="min-w-0 space-y-6">
            @include('student.dashboard.overview.partials.progress')

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900" aria-labelledby="continue-learning-title">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <div><h2 id="continue-learning-title" class="text-lg font-extrabold text-slate-950 dark:text-white">Tiếp tục học</h2><p class="mt-0.5 text-sm text-slate-500">Các khóa bạn truy cập gần đây nhất</p></div>
                    @if($continueLearning->isNotEmpty())<a href="{{ route('student.courses', ['status' => 'in_progress']) }}" class="shrink-0 text-sm font-bold text-[#0056D2] hover:underline dark:text-blue-300">Xem tất cả</a>@endif
                </div>
                <div class="p-5">
                    @if($continueLearning->isEmpty())
                        <x-student.dashboard.empty-state title="Chưa có khóa học đang học" description="Khám phá khóa học phù hợp và bắt đầu hành trình của bạn." :action-url="route('courses.index')" action-label="Khám phá khóa học" />
                    @else
                        <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                            @foreach($continueLearning as $enrollment)
                                <x-student.dashboard.course-card :course="$enrollment->course" :progress="$enrollment->progress_percent" status="in_progress" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <aside class="min-w-0 space-y-6">
            @include('student.dashboard.overview.partials.recent-activity')
            <section class="rounded-2xl bg-gradient-to-br from-[#0056D2] to-blue-700 p-5 text-white shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2M5.6 5.6 7 7m10 10 1.4 1.4M3 12h2m14 0h2M5.6 18.4 7 17m10-10 1.4-1.4M9 16h6M10 19h4M9 12a3 3 0 1 1 6 0c0 1.2-.7 2-1.5 2.8-.4.4-.5.7-.5 1.2h-2c0-.5-.1-.8-.5-1.2C9.7 14 9 13.2 9 12Z"/></svg></div>
                <h2 class="mt-4 text-lg font-extrabold">Gợi ý cho bạn</h2>
                <p class="mt-1 text-sm leading-6 text-blue-100">Dành vài phút chọn khóa học đúng mục tiêu để duy trì nhịp học đều đặn.</p>
                <a href="{{ route('courses.index') }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-xl bg-white px-4 text-sm font-bold text-[#0056D2] hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">Khám phá khóa học</a>
            </section>
        </aside>
    </div>
</x-student-layout>
