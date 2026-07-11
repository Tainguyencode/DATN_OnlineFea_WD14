@extends('layouts.app')

@section('title', 'Đăng nhập - Website học online FEA')

@section('content')
    <x-auth.layout>
        <x-auth.card x-data="{ showPassword: false, loading: false }">
            <x-auth.header title="Đăng nhập" subtitle="Đăng nhập để tiếp tục hành trình học tập của bạn." />

            <x-auth.errors />

            @if(\App\Enums\SocialProvider::anyConfigured())
                <x-auth.social-buttons />

                <x-auth.divider>Hoặc tiếp tục bằng email</x-auth.divider>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4" x-on:submit="loading = true">
                @csrf
                <input type="hidden" name="captcha_token" value="{{ $captcha['token'] }}">

                <x-auth.input
                    label="Email hoặc tên đăng nhập"
                    name="identifier"
                    :value="old('identifier')"
                    placeholder="email@example.com hoặc username"
                    autofocus
                />

                <x-auth.input label="Mật khẩu" name="password" type="password" x-bind:type="showPassword ? 'text' : 'password'"
                    placeholder="Nhập mật khẩu" inputClass="pr-14">
                    <x-slot:labelAction>
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-semibold text-[#0056D2] transition duration-200 hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200">
                            Quên mật khẩu?
                        </a>
                    </x-slot:labelAction>
                    <x-slot:trailing>
                        <x-auth.password-toggle />
                    </x-slot:trailing>
                </x-auth.input>

                <x-auth.captcha :question="$captcha['question']" />

                <label class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                    <input type="checkbox" name="remember" value="1"
                        class="rounded border-slate-300 text-[#0056D2] transition duration-200 focus:ring-[#0056D2] dark:border-slate-700">
                    Ghi nhớ đăng nhập
                </label>

                <x-auth.button x-bind:disabled="loading" loading-text="Đang xác thực...">
                    Đăng nhập
                </x-auth.button>
            </form>
            <x-auth.footer-link text="Chưa có tài khoản?" link-text="Đăng ký ngay" :href="route('register')" />
        </x-auth.card>
    </x-auth.layout>
@endsection
