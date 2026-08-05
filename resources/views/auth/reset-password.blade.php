@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - Website học online FEA')

@section('content')
    <x-auth.layout>
        <x-auth.card x-data="{ showPassword: false, showConfirm: false, loading: false }">
            <x-auth.header title="Đặt lại mật khẩu" subtitle="Tạo mật khẩu mới mạnh hơn để bảo vệ tài khoản." />

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4" x-on:submit="loading = true">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <x-auth.input
                    label="Email"
                    name="email"
                    type="email"
                    :value="old('email', $email)"
                    placeholder="email@example.com"
                    required
                />

                <x-auth.input
                    label="Mật khẩu mới"
                    name="password"
                    type="password"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    placeholder="Nhập mật khẩu mới"
                    required
                    inputClass="pr-14"
                >
                    <x-slot:trailing>
                        <button type="button" x-on:click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-3 my-auto rounded-md px-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                            <span x-text="showPassword ? 'Ẩn' : 'Hiện'"></span>
                        </button>
                    </x-slot:trailing>
                </x-auth.input>

                <x-auth.input
                    label="Xác nhận mật khẩu mới"
                    name="password_confirmation"
                    type="password"
                    x-bind:type="showConfirm ? 'text' : 'password'"
                    placeholder="Xác nhận mật khẩu mới"
                    required
                    inputClass="pr-14"
                >
                    <x-slot:trailing>
                        <button type="button" x-on:click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-3 my-auto rounded-md px-2 text-xs font-semibold text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                            <span x-text="showConfirm ? 'Ẩn' : 'Hiện'"></span>
                        </button>
                    </x-slot:trailing>
                </x-auth.input>

                <x-auth.button x-bind:disabled="loading" loading-text="Đang cập nhật...">
                    Cập nhật mật khẩu
                </x-auth.button>
            </form>

            <x-auth.footer-link text="Nhớ mật khẩu?" link-text="Đăng nhập ngay" :href="route('login')" />
        </x-auth.card>
    </x-auth.layout>
@endsection
