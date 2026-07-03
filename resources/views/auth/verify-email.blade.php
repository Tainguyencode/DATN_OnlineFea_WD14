@extends('layouts.app')

@section('title', 'Xác thực email - Website học online FEA')

@section('content')
<div class="bg-white dark:bg-slate-950">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-12">
        <div x-data="{ seconds: {{ session('resend_after', 0) }}, loading: false, init() { if (this.seconds > 0) setInterval(() => { if (this.seconds > 0) this.seconds-- }, 1000) } }" class="ui-card w-full max-w-2xl p-8 text-center">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </div>

            <div class="mx-auto mb-6 h-2 max-w-sm overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                <div class="h-full w-2/3 rounded-full bg-emerald-500"></div>
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white">Kiểm tra email của bạn</h1>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                Chúng tôi đã gửi liên kết xác thực tới <strong>{{ auth()->user()->email }}</strong>. Sau khi xác thực, bạn sẽ được chuyển vào dashboard tương ứng.
            </p>

            @if(session('success'))
                <div class="ui-alert-success mx-auto mt-6 max-w-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <form method="POST" action="{{ route('verification.send') }}" x-on:submit="loading = true">
                    @csrf
                    <button type="submit" :disabled="loading || seconds > 0" class="ui-button-primary">
                        <span x-show="seconds === 0 && !loading">Gửi lại email</span>
                        <span x-show="loading">Đang gửi...</span>
                        <span x-show="seconds > 0">Gửi lại sau <span x-text="seconds"></span>s</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="ui-button-secondary">Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
