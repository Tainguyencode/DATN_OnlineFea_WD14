<x-student-layout title="Đã xem gần đây" page-title="Đã xem gần đây" breadcrumb="Quay lại những khóa học bạn đã quan tâm, không tải trùng lịch sử.">
    @if($histories->isEmpty())
        <x-student.dashboard.empty-state title="Chưa có lịch sử xem" description="Các khóa học bạn đã mở sẽ xuất hiện tại đây." :action-url="route('courses.index')" action-label="Khám phá khóa học" />
    @else
        <div class="mb-5 flex items-center justify-between gap-3">
            <span class="text-sm font-semibold text-slate-500">{{ $histories->total() }} khóa học đã xem</span>
            <form method="POST" action="{{ route('student.recently-viewed.clear') }}" x-data="{ submitting: false }" x-on:submit="submitting = true" onsubmit="return confirm('Xóa toàn bộ lịch sử xem gần đây?')">
                @csrf @method('DELETE')
                <button type="submit" :disabled="submitting" class="rounded-xl px-3 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-60">Xóa lịch sử</button>
            </form>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 2xl:grid-cols-3">
            @foreach($histories as $history)
                @php $enrollment = $enrollmentMap->get($history->course_id); @endphp
                <x-student.dashboard.course-card :course="$history->course" :progress="$enrollment?->progress_percent" :viewed-at="$history->last_viewed_at">
                    <x-slot:actions>
                        <div class="grid grid-cols-[1fr_auto] gap-2">
                            <a href="{{ route('courses.show', $history->course->slug) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0056D2] px-3 text-sm font-bold text-white hover:bg-[#0046B8]">Xem chi tiết</a>
                            <form method="POST" action="{{ route('student.recently-viewed.destroy', $history->id) }}" onsubmit="return confirm('Xóa khóa học này khỏi lịch sử?')">@csrf @method('DELETE')<button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:border-rose-200 hover:text-rose-600" aria-label="Xóa {{ $history->course->title }} khỏi lịch sử"><span aria-hidden="true">×</span></button></form>
                        </div>
                    </x-slot:actions>
                </x-student.dashboard.course-card>
            @endforeach
        </div>
        <x-student.dashboard.pagination :paginator="$histories" />
    @endif
</x-student-layout>
