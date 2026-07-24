@props([
    'title' => 'Instructor Dashboard',
    'pageTitle' => 'Instructor Dashboard',
    'breadcrumb' => null,
])

@php
    $menu = [
        [
            'route' => 'instructor.dashboard',
            'label' => 'Tổng quan',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'
        ],
        [
            'route' => 'instructor.courses.index',
            'label' => 'Khóa học',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'
        ],
        [
            'route' => 'instructor.courses.create',
            'label' => 'Tạo khóa học',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>'
        ],
        [
            'route' => 'notifications.index',
            'label' => 'Thông báo',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>'
        ],
        [
            'route' => 'support.tickets.index',
            'active' => ['support.tickets.*'],
            'label' => 'Ticket hỗ trợ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>'
        ],
        [
            'route' => 'instructor.reviews.index',
            'active' => ['instructor.reviews.*'],
            'label' => 'Đánh giá học viên',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-7 8l-4-4V6a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2H8l-2 2z"/></svg>'
        ],
        [
            'route' => 'instructor.revenue',
            'label' => 'Doanh thu',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2"/></svg>'
        ],
        [
            'route' => 'study-groups.index',
            'label' => 'Nhóm học tập',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
        ],
        [
            'route' => 'instructor.profile',
            'label' => 'Hồ sơ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
        ],
    ];
@endphp

<x-layouts.dashboard
    role="instructor"
    roleLabel="Giảng viên"
    accent="emerald"
    :menu="$menu"
    :title="$title"
    :pageTitle="$pageTitle"
    :breadcrumb="$breadcrumb"
>
    @if(config('auth.email_verification_enabled', true) && auth()->check() && ! auth()->user()->hasVerifiedEmail())
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-5 text-amber-900 shadow-sm dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-100">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-bold">Tài khoản giảng viên chưa xác thực email.</p>
                    <p class="mt-1 text-sm font-medium text-amber-800 dark:text-amber-200">Bạn vẫn có thể xem khu vực giảng viên, nhưng cần xác thực email trước khi lưu thay đổi hoặc gửi khóa học.</p>
                </div>
                <form method="POST" action="{{ route('verification.send') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-amber-600 px-4 text-sm font-bold text-white transition hover:bg-amber-700">
                        Gửi lại email
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{ $slot }}
</x-layouts.dashboard>
