<x-student-layout title="Bài tập của tôi" page-title="Bài tập của tôi" breadcrumb="Theo dõi và nộp bài tập trong các khóa học">
    <div class="space-y-5">
        <form method="GET" class="flex flex-wrap gap-2 rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                <option value="">Tất cả trạng thái</option>
                <option value="not_submitted" @selected(request('status') === 'not_submitted')>Chưa nộp</option>
                <option value="submitted" @selected(request('status') === 'submitted')>Chờ chấm</option>
                <option value="graded" @selected(request('status') === 'graded')>Đã chấm</option>
                <option value="resubmit_required" @selected(request('status') === 'resubmit_required')>Cần làm lại</option>
            </select>
            <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Lọc</button>
        </form>

        <div class="grid gap-4">
            @forelse($assignments as $assignment)
                @php($submission = $assignment->submissions->first())
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase text-indigo-600">{{ $assignment->course?->title }}</p>
                            <h2 class="mt-1 text-lg font-black text-slate-950 dark:text-white">{{ $assignment->title }}</h2>
                            <p class="mt-2 text-sm text-slate-500">Hạn nộp: {{ $assignment->due_date?->format('H:i d/m/Y') ?? 'Không giới hạn' }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ !$submission ? 'bg-slate-100 text-slate-700' : ($submission->status === 'graded' ? 'bg-emerald-100 text-emerald-700' : (in_array($submission->status, ['resubmit_required', 'returned']) ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')) }}">
                            {{ !$submission ? 'Chưa nộp' : match($submission->status) {'graded' => 'Đã chấm', 'resubmit_required', 'returned' => 'Cần làm lại', default => 'Chờ chấm'} }}
                        </span>
                    </div>
                    <a href="{{ route('courses.lessons.show', [$assignment->course, $assignment->lesson]) }}" class="mt-4 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">{{ $submission ? 'Xem bài làm' : 'Làm bài ngay' }}</a>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 p-10 text-center text-slate-500">Không có bài tập phù hợp.</div>
            @endforelse
        </div>
        {{ $assignments->links() }}
    </div>
</x-student-layout>
