<x-student-layout title="Khóa học" page-title="Khóa học của tôi" breadcrumb="Danh sách khóa học đã đăng ký">

@if($enrollments->isEmpty())
    <div class="ui-empty p-16">
        <p class="text-lg text-slate-500 dark:text-slate-400">Bạn chưa có khóa học nào.</p>
        <a href="{{ route('home') }}#courses" class="ui-button-primary mt-4">Tìm khóa học</a>
    </div>
@else
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($enrollments as $enrollment)
            @php $course = $enrollment->course; @endphp
            <div class="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                <div class="flex h-36 items-center justify-center border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-4xl font-bold text-[#0056D2] dark:text-blue-300">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                    @endif
                </div>
                <div class="p-5">
                    <span class="text-xs font-semibold uppercase tracking-wide text-[#0056D2] dark:text-blue-300">{{ $course->category?->name }}</span>
                    <h3 class="mt-1 line-clamp-2 font-bold text-slate-900 dark:text-white">{{ $course->title }}</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $course->instructor?->name }}</p>
                    <div class="mt-4">
                        <div class="mb-1 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>Tiến độ</span>
                            <span class="font-semibold">{{ number_format($enrollment->progress_percent, 0) }}%</span>
                        </div>
                        <progress class="h-2.5 w-full overflow-hidden rounded-full [&::-moz-progress-bar]:bg-[#0056D2] [&::-webkit-progress-bar]:bg-slate-200 [&::-webkit-progress-value]:bg-[#0056D2] dark:[&::-webkit-progress-bar]:bg-slate-800" max="100" value="{{ $enrollment->progress_percent }}"></progress>
                    </div>
                    @if($enrollment->completed_at)
                        <span class="ui-badge-success mt-3">Hoàn thành</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-8">{{ $enrollments->links() }}</div>
@endif

</x-student-layout>
