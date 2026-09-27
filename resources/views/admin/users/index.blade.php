<x-admin-layout title="Người dùng" page-title="Quản lý người dùng" breadcrumb="Dashboard SaaS cho người dùng, bảo mật và phân quyền">
@php
    $roleColors = ['student' => 'bg-blue-50 text-[#0056D2]', 'instructor' => 'bg-emerald-100 text-emerald-700', 'admin' => 'bg-rose-100 text-rose-700'];
    $roleLabels = ['student' => 'Học viên', 'instructor' => 'Giảng viên', 'admin' => 'Admin'];
@endphp

<div x-data="{ createOpen: false, importOpen: false, selected: [], allVisible: false }" class="space-y-6">
    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- HÀNG 1: TỔNG QUAN VAI TRÒ NGƯỜI DÙNG --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            [
                'label' => 'Tổng người dùng',
                'value' => $stats['total'],
                'sub' => 'Bao gồm tất cả tài khoản',
                'iconClass' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300',
                'icon' => '<svg class="h-5 w-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m8-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-3a4 4 0 0 1 0 7.75M22 21v-2a4 4 0 0 0-3-3.87"/></svg>',
            ],
            [
                'label' => 'Quản trị viên (Admin)',
                'value' => $stats['admins'],
                'sub' => 'Toàn quyền kiểm duyệt & hệ thống',
                'iconClass' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
                'icon' => '<svg class="h-5 w-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3 4 6v5c0 5 3.4 8.8 8 10 4.6-1.2 8-5 8-10V6l-8-3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4"/></svg>',
            ],
            [
                'label' => 'Giảng viên (Instructor)',
                'value' => $stats['instructors'],
                'sub' => 'Đã xác minh hồ sơ & chứng chỉ',
                'iconClass' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                'icon' => '<svg class="h-5 w-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 3 9 5-9 5-9-5 9-5Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10v5c2.2 2 4.5 3 7 3s4.8-1 7-3v-5M21 8v6"/></svg>',
            ],
            [
                'label' => 'Học viên (Student)',
                'value' => $stats['students'],
                'sub' => 'Người học đang tham gia các khóa',
                'iconClass' => 'bg-violet-50 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300',
                'icon' => '<svg class="h-5 w-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 9a7 7 0 0 0-14 0"/></svg>',
            ],
        ] as $card)
            <div class="h-full rounded-2xl border border-blue-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $card['label'] }}</span>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $card['iconClass'] }}">
                        {!! $card['icon'] !!}
                    </span>
                </div>
                <div class="mt-3 text-3xl font-black text-slate-950 dark:text-white">{{ number_format($card['value']) }}</div>
                <p class="mt-1 text-[11px] text-slate-400">{{ $card['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- HÀNG 2: THỐNG KÊ PHÂN LOẠI TRẠNG THÁI HỌC TẬP (MỚI, CŨ, ĐANG HỌC, HOÀN THÀNH, CHƯA HOÀN THÀNH) --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 shadow-sm dark:border-emerald-900/50 dark:bg-emerald-950/20">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 font-black text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">✓</span>
                <div>
                    <p class="text-xs font-bold text-emerald-800 dark:text-emerald-200">Hoàn thành khóa học</p>
                    <p class="text-2xl font-black text-emerald-900 dark:text-white">{{ number_format($stats['completed_students']) }}</p>
                    <p class="text-[10px] text-emerald-700/70 dark:text-emerald-300/70">Tiến độ 100% & Đã cấp chứng chỉ</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-cyan-200 bg-cyan-50/50 p-4 shadow-sm dark:border-cyan-900/50 dark:bg-cyan-950/20">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-100 font-black text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300">▶</span>
                <div>
                    <p class="text-xs font-bold text-cyan-800 dark:text-cyan-200">Đang học tích cực</p>
                    <p class="text-2xl font-black text-cyan-900 dark:text-white">{{ number_format($stats['in_progress_students']) }}</p>
                    <p class="text-[10px] text-cyan-700/70 dark:text-cyan-300/70">Tiến độ từ 15% đến 99%</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 shadow-sm dark:border-amber-900/50 dark:bg-amber-950/20">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 font-black text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">★</span>
                <div>
                    <p class="text-xs font-bold text-amber-800 dark:text-amber-200">Người dùng mới</p>
                    <p class="text-2xl font-black text-amber-900 dark:text-white">{{ number_format($stats['new_students']) }}</p>
                    <p class="text-[10px] text-amber-700/70 dark:text-amber-300/70">Mới tham gia / Bắt đầu bài học</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/60">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-200 font-black text-slate-700 dark:bg-slate-800 dark:text-slate-300">⏸</span>
                <div>
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Chưa hoàn thành / Dở dang</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['incomplete_students']) }}</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Tiến độ &lt; 15% hoặc tạm dừng</p>
                </div>
            </div>
        </div>
    </div>

    {{-- BIỂU ĐỒ PHÂN TÍCH: TĂNG TRƯỞNG & TRẠNG THÁI HỌC TẬP (2025 - 2026) --}}
    <div class="grid gap-4 xl:grid-cols-[minmax(0,1.7fr)_minmax(340px,.8fr)]">
        {{-- BIỂU ĐỒ CHÍNH --}}
        <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-950 dark:text-white">Phân tích Người dùng & Tiến độ học tập (T1/2025 - T8/2026)</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Theo dõi số người dùng mới, đang học, hoàn thành, dở dang và tổng tích lũy</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">20 tháng</span>
                </div>
            </div>
            <div class="mt-4 h-[320px]">
                <canvas id="registrationChart" role="img" aria-label="Biểu đồ phân tích người dùng theo tháng"></canvas>
            </div>
        </div>

        {{-- BIỂU ĐỒ CƠ CẤU & TRẠNG THÁI ONLINE --}}
        <div class="flex flex-col gap-4">
            <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-sm font-black text-slate-950 dark:text-white">Cơ cấu trạng thái học tập</h3>
                <p class="mt-0.5 text-xs text-slate-400">Tỷ lệ phân bổ học viên theo tiến độ</p>
                <div class="mt-3 h-[180px]">
                    <canvas id="learningDistributionChart" role="img" aria-label="Cơ cấu trạng thái học tập"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Trạng thái kết nối</h3>
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-emerald-50 p-3 text-center dark:bg-emerald-950/20">
                        <div class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ $stats['online'] }}</div>
                        <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Đang Online</div>
                    </div>
                    <div class="rounded-xl bg-slate-100 p-3 text-center dark:bg-slate-800">
                        <div class="text-2xl font-black text-slate-700 dark:text-slate-300">{{ $stats['offline'] }}</div>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Offline</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BỘ LỌC VÀ TÌM KIẾM --}}
    <div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <form method="GET" action="{{ route('admin.users') }}" class="flex flex-1 flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, username, email, phone..."
                       class="min-w-[240px] flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <select name="role" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <option value="">Tất cả vai trò</option>
                    @foreach($roleLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected(request('status') == 'active')>Hoạt động</option>
                    <option value="blocked" @selected(request('status') == 'blocked')>Đã khóa</option>
                    <option value="deleted" @selected(request('status') == 'deleted')>Đã xóa</option>
                </select>
                <select name="sort" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <option value="created_at" @selected(request('sort') == 'created_at')>Ngày tạo</option>
                    <option value="name" @selected(request('sort') == 'name')>Tên</option>
                    <option value="email" @selected(request('sort') == 'email')>Email</option>
                    <option value="last_login_at" @selected(request('sort') == 'last_login_at')>Lần đăng nhập</option>
                </select>
                <button class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-500/20 hover:bg-blue-700 transition">Lọc</button>
            </form>

            <div class="flex flex-wrap gap-2">
                <button type="button" x-on:click="createOpen = true" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white dark:bg-blue-600 hover:bg-slate-800 transition">Thêm user</button>
                <button type="button" x-on:click="importOpen = true" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">Import</button>
                <a href="{{ route('admin.users.export.csv') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">Export Excel</a>
                <a href="{{ route('admin.users.export.pdf') }}" target="_blank" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">Export PDF</a>
            </div>
        </div>
    </div>

    {{-- BẢNG DANH SÁCH NGƯỜI DÙNG --}}
    <form id="users-bulk-form" method="POST" action="{{ route('admin.users.bulk') }}">@csrf</form>
    <div class="rounded-2xl border border-blue-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <select name="action" form="users-bulk-form" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <option value="activate">Bulk Active</option>
                    <option value="block">Bulk Block</option>
                    <option value="delete">Bulk Delete</option>
                    <option value="restore">Bulk Restore</option>
                </select>
                <button type="submit" form="users-bulk-form" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white dark:bg-blue-600" x-bind:disabled="selected.length === 0">Áp dụng</button>
            </div>
            <div class="text-sm font-semibold text-slate-500"><span x-text="selected.length"></span> đã chọn</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="bg-blue-600 text-white dark:bg-slate-800">
                    <tr>
                        <th class="px-5 py-3.5 text-left"><input type="checkbox" x-on:change="selected = $event.target.checked ? [...document.querySelectorAll('.user-checkbox')].map(i => i.value) : []"></th>
                        <th class="px-5 py-3.5 text-left font-bold">Người dùng</th>
                        <th class="px-5 py-3.5 text-left font-bold">Vai trò</th>
                        <th class="px-5 py-3.5 text-center font-bold whitespace-nowrap">Trạng thái</th>
                        <th class="px-5 py-3.5 text-left font-bold">Đăng nhập gần nhất</th>
                        <th class="px-5 py-3.5 text-right font-bold">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr class="transition hover:bg-blue-50/40 dark:hover:bg-slate-800/60">
                            <td class="px-5 py-4">
                                <input type="checkbox" name="users[]" form="users-bulk-form" value="{{ $user->id }}" class="user-checkbox rounded border-slate-300 text-blue-600" x-model="selected">
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatarUrl() }}" class="h-10 w-10 rounded-xl object-cover" alt="{{ $user->name }}">
                                    <div>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold text-slate-900 transition hover:text-blue-600 dark:text-white">{{ $user->name }}</a>
                                        <div class="text-xs text-slate-400">{{ '@'.$user->username }} · {{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4"><span class="rounded-lg px-2.5 py-1 text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">{{ $roleLabels[$user->role] ?? $user->role }}</span></td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @if($user->trashed())
                                    <span class="status-badge status-danger">Đã xóa</span>
                                @else
                                    <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-danger' }}">{{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">{{ $user->last_login_at?->diffForHumans() ?? 'Chưa có' }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}" title="Xem chi tiết" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-700 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-300">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <button type="button" x-on:click="open = !open" title="Thao tác" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-on:click.outside="open = false" class="absolute right-0 z-20 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-2 text-left shadow-md dark:border-slate-700 dark:bg-slate-900" x-cloak>
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="block rounded-lg px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800">Xem chi tiết</a>
                                            @if(! $user->trashed())
                                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-1">
                                                    @csrf @method('PUT')
                                                    <select name="role" class="mb-1.5 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                                        @foreach($roleLabels as $value => $label)
                                                            <option value="{{ $value }}" @selected($user->role == $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="w-full rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white dark:bg-blue-600">Đổi role</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-1">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="toggle_active" value="1">
                                                    <button class="w-full rounded-lg px-3 py-1.5 text-xs font-bold {{ $user->is_active ? 'bg-red-50 text-red-700 dark:bg-red-500/10' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10' }}">{{ $user->is_active ? 'Khóa user' : 'Mở khóa' }}</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="p-1" onsubmit="return confirm('Xóa người dùng này?')">
                                                    @csrf @method('DELETE')
                                                    <button class="w-full rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white">Xóa</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="p-1">
                                                    @csrf
                                                    <button class="w-full rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white">Restore</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.force-delete', $user->id) }}" class="p-1" onsubmit="return confirm('Xóa vĩnh viễn người dùng này?')">
                                                    @csrf @method('DELETE')
                                                    <button class="w-full rounded-lg bg-red-700 px-3 py-1.5 text-xs font-bold text-white">Force Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">Không có người dùng phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4 dark:border-slate-800">{{ $users->links() }}</div>
    </div>

    {{-- MODAL TẠO USER --}}
    <div x-show="createOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" x-cloak>
        <div x-on:click.outside="createOpen = false" class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-900">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Tạo người dùng mới</h3>
                <button type="button" x-on:click="createOpen = false" class="rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Đóng</button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-4 md:grid-cols-2">
                @csrf
                <input name="name" required placeholder="Họ tên" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <input name="username" required placeholder="Username" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <input name="email" type="email" required placeholder="Email" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <input name="phone" placeholder="Số điện thoại" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <select name="role" required class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    @foreach($roleLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold dark:border-slate-700 dark:text-slate-300"><input type="checkbox" name="is_active" value="1" checked> Hoạt động</label>
                <input name="password" type="password" required placeholder="Mật khẩu" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <input name="password_confirmation" type="password" required placeholder="Xác nhận mật khẩu" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <button class="md:col-span-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-700 transition">Tạo người dùng</button>
            </form>
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    <div x-show="importOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" x-cloak>
        <div x-on:click.outside="importOpen = false" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-900">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Import Danh sách User</h3>
                <button type="button" x-on:click="importOpen = false" class="rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Đóng</button>
            </div>
            <p class="mb-4 text-xs text-slate-500">File CSV có header: name, username, email, phone, role, password, status.</p>
            <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="file" name="file" required accept=".csv,.txt" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950">
                <button class="w-full rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-700 transition">Bắt đầu Import</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Chart) return;

    const isDark = document.documentElement.classList.contains('dark');
    const text = isDark ? '#cbd5e1' : '#64748b';
    const grid = isDark ? 'rgba(148,163,184,.12)' : 'rgba(148,163,184,.18)';

    const registrationData = @json($registrationGrowth);

    // 1. Biểu đồ Phân tích Người dùng & Tiến độ học tập theo tháng (T1/2025 - T8/2026)
    new Chart(document.getElementById('registrationChart'), {
        type: 'bar',
        data: {
            labels: registrationData.map(d => d.label),
            datasets: [
                {
                    type: 'bar',
                    label: 'Người dùng mới',
                    data: registrationData.map(d => d.new_users),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    stack: 'learning',
                },
                {
                    type: 'bar',
                    label: 'Đang học',
                    data: registrationData.map(d => d.in_progress),
                    backgroundColor: '#06b6d4',
                    borderRadius: 4,
                    stack: 'learning',
                },
                {
                    type: 'bar',
                    label: 'Hoàn thành',
                    data: registrationData.map(d => d.completed),
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                    stack: 'learning',
                },
                {
                    type: 'bar',
                    label: 'Dở dang / Chưa xong',
                    data: registrationData.map(d => d.incomplete),
                    backgroundColor: '#94a3b8',
                    borderRadius: 4,
                    stack: 'learning',
                },
                {
                    type: 'line',
                    label: 'Tổng tích lũy',
                    data: registrationData.map(d => d.cumulative),
                    borderColor: '#6366f1',
                    backgroundColor: '#6366f1',
                    borderWidth: 2.5,
                    pointRadius: 2.5,
                    pointHoverRadius: 5,
                    tension: .35,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: text, boxWidth: 10, usePointStyle: true, font: { size: 11, weight: '600' } }
                },
                tooltip: {
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true,
                    callbacks: {
                        title: (items) => registrationData[items[0].dataIndex]?.full_label ?? items[0].label,
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' user';
                            }
                            return label;
                        },
                        afterBody: function(items) {
                            const d = registrationData[items[0].dataIndex];
                            if (!d) return '';
                            return [
                                '------------------------------',
                                '📊 Tổng trong tháng: ' + new Intl.NumberFormat('vi-VN').format(d.total) + ' user',
                                '📈 Tích lũy hệ thống: ' + new Intl.NumberFormat('vi-VN').format(d.cumulative) + ' user'
                            ];
                        }
                    }
                }
            },
            scales: {
                x: { ticks: { color: text, font: { size: 10 } }, grid: { display: false } },
                y: {
                    beginAtZero: true,
                    stacked: true,
                    ticks: {
                        color: text,
                        font: { size: 10 },
                        callback: (v) => new Intl.NumberFormat('vi-VN').format(v)
                    },
                    grid: { color: grid },
                    title: { display: true, text: 'Học viên theo đợt', color: text, font: { size: 10, weight: '600' } }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    ticks: {
                        color: '#6366f1',
                        font: { size: 10 },
                        callback: (v) => new Intl.NumberFormat('vi-VN').format(v)
                    },
                    grid: { display: false },
                    title: { display: true, text: 'Tổng tích lũy', color: '#6366f1', font: { size: 10, weight: '600' } }
                }
            }
        }
    });

    // 2. Biểu đồ Cơ cấu trạng thái học tập (Doughnut Chart)
    new Chart(document.getElementById('learningDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Hoàn thành', 'Đang học', 'Người dùng mới', 'Chưa hoàn thành'],
            datasets: [{
                data: [
                    {{ $stats['completed_students'] }},
                    {{ $stats['in_progress_students'] }},
                    {{ $stats['new_students'] }},
                    {{ $stats['incomplete_students'] }}
                ],
                backgroundColor: ['#22c55e', '#06b6d4', '#f59e0b', '#94a3b8'],
                borderWidth: 2,
                borderColor: isDark ? '#0f172a' : '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: { color: text, boxWidth: 8, usePointStyle: true, font: { size: 10 } }
                }
            }
        }
    });
});
</script>
</x-admin-layout>
