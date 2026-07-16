<x-student-layout title="Đã xem gần đây" page-title="Khóa học đã xem gần đây" breadcrumb="Các khóa học bạn vừa truy cập">

@if(session('success'))
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200">
        {{ session('error') }}
    </div>
@endif

<div class="mb-6 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">Khóa học đã xem gần đây</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Danh sách được sắp xếp theo lần xem mới nhất của riêng bạn.</p>
    </div>

    @if($histories->count() > 0)
        <form method="POST" action="{{ route('student.recently-viewed.clear') }}" onsubmit="return confirm('Xóa toàn bộ lịch sử xem gần đây của bạn?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl border border-rose-200 bg-white px-4 text-sm font-bold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-300 dark:hover:bg-rose-950/30">
                Xóa toàn bộ lịch sử
            </button>
        </form>
    @endif
</div>

@if($histories->count() === 0)
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-200">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="mt-5 text-xl font-extrabold text-slate-950 dark:text-white">Bạn chưa xem khóa học nào gần đây</h3>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Khám phá catalog và quay lại đây để tiếp tục nhanh các khóa học bạn quan tâm.</p>
        <a href="{{ route('courses.index') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl bg-[#0056D2] px-5 text-sm font-bold text-white transition hover:bg-[#0046B8]">
            Khám phá khóa học
        </a>
    </div>
@else
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach($histories as $history)
            <x-recently-viewed-course-card
                :history="$history"
                :enrollment="$enrollmentMap->get($history->course_id)"
                :show-delete="true"
            />
        @endforeach
    </div>

    <div class="mt-8">
        {{ $histories->links() }}
    </div>
@endif

</x-student-layout>
