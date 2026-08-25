@extends('layouts.app')

@section('title', 'Khóa học đang chờ duyệt giảng viên - FEA LMS')

@section('content')
<div class="flex min-h-[70vh] flex-col items-center justify-center px-4 py-12 text-center">
    <div class="rounded-3xl border border-amber-200 bg-white p-8 shadow-xl dark:border-amber-900/40 dark:bg-slate-900 max-w-lg w-full">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <div class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
            <span>⏳ Đang hoàn thiện xét duyệt</span>
        </div>

        <h2 class="mt-4 text-2xl font-black text-slate-900 dark:text-white">Khóa học chưa sẵn sàng</h2>
        
        <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
            Khóa học đang chờ hoàn tất xét duyệt hồ sơ giảng viên.
        </p>

        <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">
            Nội dung khóa học đã được kiểm duyệt. Hệ thống sẽ tự động mở công khai ngay khi hồ sơ giảng viên được phê duyệt.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3">
            <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center rounded-xl bg-[#0056D2] px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-[#00419e] transition">
                Khám phá khóa học khác
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-6 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 transition">
                Về Trang chủ
            </a>
        </div>
    </div>
</div>
@endsection
