@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - Website học online FEA')

@section('content')
<div class="bg-white dark:bg-slate-950">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-12">
        <div x-data="{ showPassword: false, showConfirm: false, loading: false, password: '', get strength() { let s = 0; if (this.password.length >= 8) s++; if (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password)) s++; if (/[0-9]/.test(this.password)) s++; if (/[^A-Za-z0-9]/.test(this.password)) s++; return s; } }" class="ui-card w-full max-w-lg p-8">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-300">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zm0 0v8m-4-4h8"/></svg>
                </div>
                <h1 class="text-3xl font-bold text-slate-950 dark:text-white">Đặt lại mật khẩu</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Tạo mật khẩu mới mạnh hơn để bảo vệ tài khoản.</p>
            </div>

            @if($errors->any())
                <div class="ui-alert-error mb-6">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5" x-on:submit="loading = true">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required class="ui-input">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Mật khẩu mới</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required class="auth-input pr-14">
                        <button type="button" x-on:click="showPassword = !showPassword" class="absolute inset-y-0 right-3 my-auto rounded-md px-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"><span x-text="showPassword ? 'Ẩn' : 'Hiện'"></span></button>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Xác nhận mật khẩu</label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required class="auth-input pr-14">
                        <button type="button" x-on:click="showConfirm = !showConfirm" class="absolute inset-y-0 right-3 my-auto rounded-md px-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"><span x-text="showConfirm ? 'Ẩn' : 'Hiện'"></span></button>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2">
                    <template x-for="i in 4" :key="i">
                        <div class="h-2 rounded-full transition" :class="strength >= i ? 'bg-[#0056D2]' : 'bg-slate-200 dark:bg-slate-800'"></div>
                    </template>
                </div>

                <button type="submit" :disabled="loading" class="ui-button-primary w-full">
                    <span x-show="!loading">Cập nhật mật khẩu</span>
                    <span x-show="loading">Đang cập nhật...</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
