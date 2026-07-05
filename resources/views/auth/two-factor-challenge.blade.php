@extends('layouts.app')

@section('title', 'Xác thực 2FA - Website học online FEA')

@section('content')
<div class="bg-white dark:bg-slate-950">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-12">
        <div x-data="{ loading: false, seconds: {{ session('resend_after', 0) }}, init() { if (this.seconds > 0) setInterval(() => { if (this.seconds > 0) this.seconds-- }, 1000) } }" class="ui-card w-full max-w-lg p-8 text-center">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-300">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zm0 0v8m-4-4h8"/></svg>
            </div>
            <h1 class="text-3xl font-bold text-slate-950 dark:text-white">Nhập mã xác thực</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Mã 6 số đã được gửi tới {{ auth()->user()->email }}.</p>

            @if(session('success'))
                <div class="ui-alert-success mt-6">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="ui-alert-error mt-6">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-6 space-y-5" x-on:submit="loading = true">
                @csrf
                <input name="code" maxlength="6" inputmode="numeric" required autofocus class="ui-input text-center text-2xl font-bold tracking-[0.4em]" placeholder="000000">
                <button :disabled="loading" class="ui-button-primary w-full disabled:opacity-70">
                    <span x-show="!loading">Xác thực</span>
                    <span x-show="loading">Đang kiểm tra...</span>
                </button>
            </form>

            <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-4">
                @csrf
                <button :disabled="seconds > 0" class="text-sm font-bold text-[#0056D2] disabled:text-slate-400 dark:text-blue-300">
                    <span x-show="seconds === 0">Gửi lại mã</span>
                    <span x-show="seconds > 0">Gửi lại sau <span x-text="seconds"></span>s</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
