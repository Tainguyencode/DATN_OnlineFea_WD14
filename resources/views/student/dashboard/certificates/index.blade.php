<x-student-layout title="Chứng chỉ" page-title="Chứng chỉ của tôi" breadcrumb="Xem hoặc tải những chứng chỉ hợp lệ bạn đã nhận.">
    @if($certificates->isEmpty())
        <x-student.dashboard.empty-state title="Chưa có chứng chỉ" description="Hoàn thành khóa học đủ điều kiện để nhận chứng chỉ." :action-url="route('student.courses')" action-label="Xem khóa học của tôi" />
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($certificates as $certificate)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-3"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="9" r="6"/><path stroke-linecap="round" d="m8.5 14-1 7 4.5-2 4.5 2-1-7"/></svg></span><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Hợp lệ</span></div>
                    <h2 class="mt-4 line-clamp-2 text-lg font-extrabold" title="{{ $certificate->course->title }}">{{ $certificate->course->title }}</h2>
                    <dl class="mt-3 space-y-1 text-sm text-slate-500"><div class="flex justify-between gap-3"><dt>Mã chứng chỉ</dt><dd class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ $certificate->certificate_code }}</dd></div><div class="flex justify-between gap-3"><dt>Ngày cấp</dt><dd class="font-semibold text-slate-700 dark:text-slate-200">{{ $certificate->issued_at->format('d/m/Y') }}</dd></div></dl>
                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <a target="_blank" rel="noopener" href="{{ route('student.certificates.pdf', $certificate) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0056D2] px-3 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0046B8] hover:shadow-md active:translate-y-0 active:scale-95">Xem</a>
                        <a href="{{ route('student.certificates.pdf', [$certificate, 'download' => 1]) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50 hover:shadow active:translate-y-0 active:scale-95 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Tải xuống</a>
                    </div>
                </article>
            @endforeach
        </div>
        <x-student.dashboard.pagination :paginator="$certificates" />
    @endif
</x-student-layout>
