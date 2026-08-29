<x-student-layout title="Nhóm học tập" page-title="Nhóm học tập" breadcrumb="Những nhóm bạn đang là thành viên.">
    @if($studyGroups->isEmpty())
        <x-student.dashboard.empty-state title="Bạn chưa tham gia nhóm học tập" description="Nhóm đã tham gia sẽ xuất hiện ở đây. Bạn có thể khám phá nhóm từ khu vực cộng đồng hiện có." :action-url="route('study-groups.index')" action-label="Khám phá nhóm" />
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($studyGroups as $group)
                <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-3"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-300"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" d="M16 20a5 5 0 0 0-10 0M11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM22 20a5 5 0 0 0-7-4.6M16 5.2a3 3 0 0 1 0 5.6"/></svg></span><span class="text-xs font-semibold text-slate-500">{{ $group->members_count }} thành viên</span></div>
                    <h2 class="mt-4 line-clamp-2 text-lg font-extrabold" title="{{ $group->name }}">{{ $group->name }}</h2>
                    <p class="mt-1 line-clamp-1 text-sm text-[#0056D2] dark:text-blue-300">{{ $group->course?->title ?? 'Khóa học' }}</p>
                    <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-500">{{ $group->description ?: 'Nhóm trao đổi và hỗ trợ học tập.' }}</p>
                    <a href="{{ route('student.study-groups.show', $group) }}" class="mt-auto pt-5 text-sm font-bold text-[#0056D2] hover:underline dark:text-blue-300">Xem nhóm →</a>
                </article>
            @endforeach
        </div>
        <x-student.dashboard.pagination :paginator="$studyGroups" />
    @endif
</x-student-layout>
