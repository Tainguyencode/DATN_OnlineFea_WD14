<x-student-layout title="Đã xem gần đây" page-title="Đã xem gần đây" breadcrumb="Quay lại những khóa học bạn đã quan tâm, không tải trùng lịch sử.">
    @if($histories->isEmpty())
        <x-student.dashboard.empty-state title="Chưa có lịch sử xem" description="Các khóa học bạn đã mở sẽ xuất hiện tại đây." :action-url="route('courses.index')" action-label="Khám phá khóa học" />
    @else
        <div class="mb-5 flex items-center justify-between gap-3">
            <span class="text-sm font-semibold text-slate-500">{{ $histories->total() }} khóa học đã xem</span>
            <form method="POST" action="{{ route('student.recently-viewed.clear') }}" x-data="{ submitting: false }" x-on:submit="submitting = true" onsubmit="return confirm('Xóa toàn bộ lịch sử xem gần đây?')">
                @csrf @method('DELETE')
                <button type="submit" :disabled="submitting" class="rounded-xl px-3.5 py-2 text-sm font-bold text-rose-600 transition-all duration-200 hover:bg-rose-50 hover:text-rose-700 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60 dark:text-rose-400 dark:hover:bg-rose-950/40">Xóa lịch sử</button>
            </form>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 2xl:grid-cols-3">
            @foreach($histories as $history)
                @php $enrollment = $enrollmentMap->get($history->course_id); @endphp
                <x-student.dashboard.course-card :course="$history->course" :progress="$enrollment?->progress_percent" :viewed-at="$history->last_viewed_at">
                    <x-slot:actions>
                        <div class="grid grid-cols-[1fr_auto] gap-2">
                            <a href="{{ route('courses.show', $history->course->slug) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0056D2] px-3 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0046B8] hover:shadow-md active:translate-y-0 active:scale-95">Xem chi tiết</a>
                            <form method="POST" action="{{ route('student.recently-viewed.destroy', $history->id) }}" onsubmit="return confirm('Xóa khóa học này khỏi lịch sử?')">@csrf @method('DELETE')<button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-rose-300 hover:bg-rose-50 hover:text-rose-600 active:scale-95 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-rose-950/30" aria-label="Xóa {{ $history->course->title }} khỏi lịch sử"><span aria-hidden="true" class="text-base font-bold">×</span></button></form>
                        </div>
                    </x-slot:actions>
                </x-student.dashboard.course-card>
            @endforeach
        </div>
        <x-student.dashboard.pagination :paginator="$histories" />
    @endif
</x-student-layout>
