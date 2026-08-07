@extends('layouts.app')

@section('title', '429 - Thao tác quá nhanh - FEA LMS')

@section('content')
<div class="flex min-h-[70vh] flex-col items-center justify-center px-4 text-center">
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-8 shadow-sm dark:border-amber-900/40 dark:bg-amber-950/20 max-w-md w-full">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/60 dark:text-amber-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h2 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">Thao tác quá nhiều lần</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Bạn đã gửi yêu cầu quá nhanh. Vui lòng chờ 1 phút sau đó thử lại.</p>
        <div class="mt-6 flex justify-center gap-3">
            <a href="{{ route('password.request') }}" class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
                Thử lại Quên mật khẩu
            </a>
            <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300">
                Về Đăng nhập
            </a>
        </div>
    </div>
</div>
@endsection
