<x-student-layout title="Khóa học" page-title="Khóa học của tôi" breadcrumb="Danh sách khóa học đã đăng ký">

@if($enrollments->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <p class="text-slate-600 text-lg">Bạn chưa có khóa học nào.</p>
        <a href="{{ route('home') }}#courses" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-indigo-700 transition">Tìm khóa học</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($enrollments as $enrollment)
            @php $course = $enrollment->course; @endphp
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition group">
                <div class="h-32 bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white/30">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-medium text-indigo-600">{{ $course->category?->name }}</span>
                    <h3 class="font-bold text-slate-900 mt-1 line-clamp-2">{{ $course->title }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ $course->instructor?->name }}</p>
                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Tiến độ</span>
                            <span class="font-semibold">{{ number_format($enrollment->progress_percent, 0) }}%</span>
                        </div>
                        <div class="bg-slate-100 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full h-2.5 transition-all" style="width: {{ $enrollment->progress_percent }}%"></div>
                        </div>
                    </div>
                    @if($enrollment->completed_at)
                        <span class="inline-block mt-3 text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full font-medium">✓ Hoàn thành</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-8">{{ $enrollments->links() }}</div>
@endif

</x-student-layout>
