@extends('layouts.app')

@php
    $isStudent = $role === 'student';
    $pageTitle = $isStudent ? 'Đăng ký học viên' : 'Đăng ký giảng viên';
    $pageSubtitle = $isStudent
        ? 'Tạo tài khoản miễn phí và bắt đầu học ngay hôm nay.'
        : 'Tạo tài khoản giảng viên để xây dựng và quản lý khóa học của bạn.';
    $submitLabel = $isStudent ? 'Tạo tài khoản học viên' : 'Tạo tài khoản giảng viên';
@endphp

@section('title', $pageTitle.' - Website học online FEA')

@section('content')
<x-auth.layout>
    <x-auth.card
        x-data="{
            showPassword: false,
            showConfirm: false,
            loading: false,
            avatarPreview: null,
            password: '',
            emailMessage: '',
            emailOk: null,
            availabilityUrl: @js(route('auth.availability')),
            get strength() {
                let score = 0;
                if (this.password.length >= 8) score++;
                if (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password)) score++;
                if (/[0-9]/.test(this.password)) score++;
                if (/[^A-Za-z0-9]/.test(this.password)) score++;
                return score;
            },
            async check(field, value) {
                if (!value || value.length < 3) return;
                const response = await fetch(`${this.availabilityUrl}?field=${field}&value=${encodeURIComponent(value)}`, { headers: { 'Accept': 'application/json' }});
                const data = await response.json();
                this[`${field}Ok`] = data.available;
                this[`${field}Message`] = data.message;
            }
        }"
    >
        <div class="mb-6">
            <a href="{{ route('register') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 transition hover:text-[#0056D2] dark:text-slate-400 dark:hover:text-blue-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                Chọn loại tài khoản khác
            </a>
        </div>

        <x-auth.header :title="$pageTitle" :subtitle="$pageSubtitle" />

        @if($isStudent)
            <div class="mb-5 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
                Bạn sẽ có thể đăng ký khóa học, theo dõi tiến độ học tập và nhận chứng chỉ hoàn thành.
            </div>
        @else
            <div class="mb-5 rounded-lg border border-violet-100 bg-violet-50 px-4 py-3 text-sm text-violet-800 dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-200">
                Sau khi đăng ký, bạn có thể tạo khóa học, quản lý nội dung và theo dõi học viên.
            </div>
        @endif

        <x-auth.errors />

        @if($isStudent && \App\Enums\SocialProvider::anyConfigured())
            <x-auth.social-buttons />

            <x-auth.divider>Hoặc tiếp tục bằng email</x-auth.divider>
        @endif

        @unless($isStudent)
            <p class="mb-4 text-center text-xs text-slate-500 dark:text-slate-400">
                Đăng ký bằng Google hoặc Facebook chỉ tạo tài khoản học viên. Để đăng ký giảng viên, vui lòng dùng form bên dưới.
            </p>
        @endunless

        <form method="POST" action="{{ route('register.role', $role) }}" enctype="multipart/form-data" class="space-y-4" x-on:submit="loading = true">
            @csrf
            <input type="hidden" name="captcha_token" value="{{ $captcha['token'] }}">

            <div class="flex flex-col items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/60 sm:flex-row sm:items-center">
                <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" alt="Avatar preview" class="h-full w-full object-cover">
                    </template>
                    <img x-show="!avatarPreview" src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="h-full w-full object-contain p-2">
                </div>
                <div class="text-center sm:text-left">
                    <label class="inline-flex cursor-pointer items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        Chọn avatar
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="sr-only" x-on:change="avatarPreview = URL.createObjectURL($event.target.files[0])">
                    </label>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">PNG, JPG hoặc WebP tối đa 2MB.</p>
                </div>
            </div>

            <x-auth.input
                label="Họ và tên"
                name="name"
                :value="old('name')"
                placeholder="Nguyễn Văn A"
                autofocus
            />

            <x-auth.input
                label="Email"
                name="email"
                type="email"
                :value="old('email')"
                placeholder="email@example.com"
                x-on:input.debounce.500ms="check('email', $event.target.value)"
            >
                <x-slot:hint>
                    <p x-show="emailMessage" x-text="emailMessage" class="text-xs font-semibold" :class="emailOk ? 'text-emerald-600' : 'text-red-600'"></p>
                </x-slot:hint>
            </x-auth.input>

            <x-auth.input
                label="Số điện thoại"
                name="phone"
                :value="old('phone')"
                placeholder="0912345678"
            />

            <x-auth.input
                label="Mật khẩu"
                name="password"
                x-bind:type="showPassword ? 'text' : 'password'"
                x-model="password"
                placeholder="Tối thiểu 8 ký tự"
                inputClass="pr-14"
            >
                <x-slot:trailing>
                    <x-auth.password-toggle />
                </x-slot:trailing>
            </x-auth.input>

            <x-auth.input
                label="Xác nhận mật khẩu"
                name="password_confirmation"
                x-bind:type="showConfirm ? 'text' : 'password'"
                placeholder="Nhập lại mật khẩu"
                inputClass="pr-14"
            >
                <x-slot:trailing>
                    <x-auth.password-toggle toggle="showConfirm = !showConfirm" visible="showConfirm" />
                </x-slot:trailing>
            </x-auth.input>

            <div>
                <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>Độ mạnh mật khẩu</span>
                    <span x-text="['Yếu','Trung bình','Khá','Mạnh','Rất mạnh'][strength]"></span>
                </div>
                <div class="grid grid-cols-4 gap-2">
                    <template x-for="i in 4" :key="i">
                        <div class="h-2 rounded-full transition duration-200" :class="strength >= i ? 'bg-[#0056D2]' : 'bg-slate-200 dark:bg-slate-800'"></div>
                    </template>
                </div>
            </div>

            <x-auth.captcha :question="$captcha['question']" />

            <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-500 transition duration-200 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-400">
                <input type="checkbox" name="terms" value="1" class="mt-1 rounded border-slate-300 text-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700">
                <span>Tôi đồng ý với điều khoản sử dụng, chính sách bảo mật và quy định cộng đồng của Website học online FEA.</span>
            </label>

            <x-auth.button x-bind:disabled="loading" loading-text="Đang tạo tài khoản...">
                {{ $submitLabel }}
            </x-auth.button>
        </form>

        <x-auth.footer-link
            text="Đã có tài khoản?"
            link-text="Đăng nhập"
            :href="route('login')"
        />
    </x-auth.card>
</x-auth.layout>
@endsection
