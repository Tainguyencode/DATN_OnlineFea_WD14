<?php

use App\Enums\SocialProvider;

$googleReady = SocialProvider::Google->isConfigured();
$facebookReady = SocialProvider::Facebook->isConfigured();
$anyReady = $googleReady || $facebookReady;
?>

@if ($anyReady)
<div
    x-data="{ socialLoading: null }"
    class="grid grid-cols-1 gap-3 sm:grid-cols-2"
>
    @if ($googleReady)
        <a
            href="{{ route('social.redirect', 'google') }}"
            @click="socialLoading = 'google'"
            :aria-busy="socialLoading === 'google'"
            aria-label="Tiếp tục với Google"
            :class="socialLoading ? 'pointer-events-none opacity-60' : ''"
            class="auth-social-btn group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] focus-visible:ring-offset-2"
        >
            <svg class="mr-2 h-5 w-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span class="text-sm font-medium" x-show="socialLoading !== 'google'">Tiếp tục với Google</span>
            <span class="text-sm font-medium" x-show="socialLoading === 'google'" x-cloak>Đang chuyển hướng...</span>
        </a>
    @else
        <span
            aria-disabled="true"
            class="auth-social-btn pointer-events-none cursor-not-allowed opacity-50"
            title="Đăng nhập Google chưa được quản trị viên cấu hình."
        >
            <svg class="mr-2 h-5 w-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span class="text-sm font-medium">Tiếp tục với Google</span>
        </span>
    @endif

    @if ($facebookReady)
        <a
            href="{{ route('social.redirect', 'facebook') }}"
            @click="socialLoading = 'facebook'"
            :aria-busy="socialLoading === 'facebook'"
            aria-label="Tiếp tục với Facebook"
            :class="socialLoading ? 'pointer-events-none opacity-60' : ''"
            class="auth-social-btn group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2] focus-visible:ring-offset-2"
        >
            <svg class="mr-2 h-5 w-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z" fill="#1877F2"/>
                <path d="M16.671 15.49 17.203 12h-3.328V9.75c0-.949.465-1.874 1.956-1.874h1.513V5.923S17.437 5.688 16.125 5.688c-2.741 0-4.533 1.662-4.533 4.669v2.643H8.545V12h3.047v8.437H12a12.07 12.07 0 0 0 3.671-4.947z" fill="#fff"/>
            </svg>
            <span class="text-sm font-medium" x-show="socialLoading !== 'facebook'">Tiếp tục với Facebook</span>
            <span class="text-sm font-medium" x-show="socialLoading === 'facebook'" x-cloak>Đang chuyển hướng...</span>
        </a>
    @else
        <span
            aria-disabled="true"
            class="auth-social-btn pointer-events-none cursor-not-allowed opacity-50"
            title="Đăng nhập Facebook chưa được quản trị viên cấu hình."
        >
            <svg class="mr-2 h-5 w-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z" fill="#1877F2"/>
                <path d="M16.671 15.49 17.203 12h-3.328V9.75c0-.949.465-1.874 1.956-1.874h1.513V5.923S17.437 5.688 16.125 5.688c-2.741 0-4.533 1.662-4.533 4.669v2.643H8.545V12h3.047v8.437H12a12.07 12.07 0 0 0 3.671-4.947z" fill="#fff"/>
            </svg>
            <span class="text-sm font-medium">Tiếp tục với Facebook</span>
        </span>
    @endif
</div>
@endif
