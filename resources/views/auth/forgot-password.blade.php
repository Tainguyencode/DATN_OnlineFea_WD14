@extends('layouts.app')

@section('title', 'Quên mật khẩu - Website học online FEA')

@section('content')
<div class="bg-white dark:bg-slate-950">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-12">
        <div x-data="{ loading: false, seconds: {{ session('resend_after', 0) }}, init() { if (this.seconds > 0) setInterval(() => { if (this.seconds > 0) this.seconds-- }, 1000) } }" class="ui-card w-full max-w-lg p-8">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-300">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H3v-4.586l5.257-5.257A6 6 0 1121 9z"/></svg>
                </div>
                <h1 class="text-3xl font-bold text-slate-950 dark:text-white">Khôi phục mật khẩu</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Nhập email để nhận liên kết đặt lại mật khẩu an toàn.</p>
            </div>

            @if(session('success'))
                <div class="ui-alert-success mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="ui-alert-error mb-6">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5" x-on:submit="loading = true">
                @csrf
                <input type="hidden" name="captcha_token" value="{{ $captcha['token'] }}">

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="ui-input" placeholder="email@example.com">
                </div>

                <div class="grid gap-3 sm:grid-cols-[1fr_140px]">
                    <div class="flex h-[50px] items-center rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 text-sm font-semibold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">{{ $captcha['question'] }}</div>
                    <input type="text" name="captcha_answer" required inputmode="numeric" class="ui-input" placeholder="Kết quả">
                </div>

                <button type="submit" :disabled="loading || seconds > 0" class="ui-button-primary w-full">
                    <span x-show="seconds === 0 && !loading">Gửi email khôi phục</span>
                    <span x-show="loading">Đang gửi...</span>
                    <span x-show="seconds > 0">Gửi lại sau <span x-text="seconds"></span>s</span>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
                Nhớ mật khẩu?
                <a href="{{ route('login') }}" class="font-bold text-[#0056D2] dark:text-blue-300">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>
@endsection
