@php
    $mobile = $mobile ?? false;
    $groups = [
        'Tổng quan' => [
            ['label' => 'Tổng quan', 'route' => 'student.dashboard', 'active' => ['student.dashboard'], 'icon' => 'home'],
        ],
        'Học tập' => [
            ['label' => 'Khóa học của tôi', 'route' => 'student.courses', 'active' => ['student.courses'], 'icon' => 'book'],
            ['label' => 'Đã xem gần đây', 'route' => 'student.recently-viewed', 'active' => ['student.recently-viewed*'], 'icon' => 'clock'],
            ['label' => 'Chứng chỉ', 'route' => 'student.certificates', 'active' => ['student.certificates*'], 'icon' => 'award'],
        ],
        'Cá nhân' => [
            ['label' => 'Yêu thích', 'route' => 'student.wishlist', 'active' => ['student.wishlist*', 'favorites.*'], 'icon' => 'heart'],
            ['label' => 'Hồ sơ cá nhân', 'route' => 'student.profile', 'active' => ['student.profile'], 'icon' => 'user'],
            ['label' => 'Bảo mật', 'route' => 'student.profile.security', 'active' => ['student.profile.security'], 'icon' => 'shield'],
        ],
        'Giao dịch' => [
            ['label' => 'Đơn hàng', 'route' => 'student.orders', 'active' => ['student.orders*'], 'icon' => 'receipt'],
            ['label' => 'Voucher của tôi', 'route' => 'student.vouchers.index', 'active' => ['student.vouchers.*'], 'icon' => 'ticket'],
        ],
        'Cộng đồng' => [
            ['label' => 'Nhóm học tập', 'route' => 'student.study-groups.index', 'active' => ['student.study-groups.*'], 'icon' => 'users'],
        ],
    ];
@endphp

<div class="flex h-full flex-col overflow-y-auto px-4 py-5">
    <nav aria-label="Điều hướng học viên" class="space-y-5">
        @foreach($groups as $groupLabel => $items)
            <section aria-labelledby="student-nav-{{ $loop->index }}">
                <h2 id="student-nav-{{ $loop->index }}" class="mb-1.5 px-3 text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-400">{{ $groupLabel }}</h2>
                <div class="space-y-1">
                    @foreach($items as $item)
                        @php $active = request()->routeIs(...$item['active']); @endphp
                        <a href="{{ route($item['route']) }}" @if($mobile) x-on:click="$dispatch('close-student-sidebar')" @endif
                           @if($active) aria-current="page" @endif
                           class="group flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-[#0056D2] text-white shadow-sm shadow-blue-600/20' : 'text-slate-600 hover:bg-blue-50 hover:text-[#0056D2] dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                                @switch($item['icon'])
                                    @case('home') <path stroke-linecap="round" stroke-linejoin="round" d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-9Z"/> @break
                                    @case('book') <path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21.5v-16ZM20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5a2.5 2.5 0 0 1 2.5 2.5v-16Z"/> @break
                                    @case('clock') <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/> @break
                                    @case('award') <circle cx="12" cy="9" r="6"/><path stroke-linecap="round" stroke-linejoin="round" d="m8.5 14-1 7 4.5-2 4.5 2-1-7"/> @break
                                    @case('heart') <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/> @break
                                    @case('user') <circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21a8 8 0 0 1 16 0"/> @break
                                    @case('shield') <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4.5 6v5c0 4.8 3.1 8.1 7.5 10 4.4-1.9 7.5-5.2 7.5-10V6L12 3Z"/><path stroke-linecap="round" d="m9 12 2 2 4-4"/> @break
                                    @case('receipt') <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path stroke-linecap="round" d="M9 8h6M9 12h6"/> @break
                                    @case('ticket') <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4V6Z"/><path stroke-linecap="round" d="M13 8v8"/> @break
                                    @case('users') <path stroke-linecap="round" d="M16 20a5 5 0 0 0-10 0M11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM22 20a5 5 0 0 0-7-4.6M16 5.2a3 3 0 0 1 0 5.6"/> @break
                                @endswitch
                            </svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="mt-auto border-t border-slate-200 pt-4 dark:border-slate-800">
        @csrf
        <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-600 hover:bg-rose-50 hover:text-rose-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 dark:text-slate-300 dark:hover:bg-rose-950/30 dark:hover:text-rose-300">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17l5-5-5-5M15 12H3M14 4h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5"/></svg>
            Đăng xuất
        </button>
    </form>
</div>
