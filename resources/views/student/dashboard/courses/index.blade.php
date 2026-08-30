<x-student-layout title="Khóa học của tôi" page-title="Khóa học của tôi" breadcrumb="Theo dõi tiến độ và tiếp tục hành trình học tập của bạn.">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap gap-2" role="navigation" aria-label="Lọc khóa học">
            @foreach(['all' => 'Tất cả', 'in_progress' => 'Đang học', 'completed' => 'Hoàn thành'] as $key => $label)
                <a href="{{ route('student.courses', ['status' => $key]) }}" @if($status === $key) aria-current="page" @endif class="inline-flex min-h-10 items-center rounded-xl px-4 text-sm font-bold {{ $status === $key ? 'bg-[#0056D2] text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300' }}">{{ $label }}</a>
            @endforeach
        </div>
        <span class="text-sm font-semibold text-slate-500">{{ $enrollments->total() }} khóa học</span>
    </div>

    @if($enrollments->isEmpty())
        <x-student.dashboard.empty-state title="Chưa có khóa học phù hợp" description="Thử bộ lọc khác hoặc khám phá khóa học mới." :action-url="route('courses.index')" action-label="Khám phá khóa học" />
    @else
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 2xl:grid-cols-3">
            @foreach($enrollments as $enrollment)
                @continue(! $enrollment->course)
                @php $completed = $enrollment->completed_at || $enrollment->status === \App\Models\Enrollment::STATUS_COMPLETED; @endphp
                <x-student.dashboard.course-card :course="$enrollment->course" :progress="$enrollment->progress_percent" :status="$completed ? 'completed' : 'in_progress'" />
            @endforeach
        </div>
        <x-student.dashboard.pagination :paginator="$enrollments" />
    @endif
</x-student-layout>
