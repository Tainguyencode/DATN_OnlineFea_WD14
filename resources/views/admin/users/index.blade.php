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

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Tổng user', 'value' => $stats['total'], 'tone' => 'from-slate-900 to-slate-700'],
            ['label' => 'Admin', 'value' => $stats['admins'], 'tone' => 'from-rose-600 to-orange-500'],
            ['label' => 'Instructor', 'value' => $stats['instructors'], 'tone' => 'from-emerald-600 to-teal-500'],
            ['label' => 'Student', 'value' => $stats['students'], 'tone' => 'from-blue-700 to-blue-500'],
        ] as $card)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</span>
                    <span class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800"></span>
                </div>
                <div class="mt-4 text-3xl font-black text-slate-950">{{ number_format($card['value']) }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_360px]">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-bold text-slate-900">Tăng trưởng đăng ký</h2>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#0056D2]">12 tháng</span>
            </div>
            <canvas id="registrationChart" height="110"></canvas>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="font-bold text-slate-900">Trạng thái online</h2>
            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-emerald-50 p-4 text-center">
                    <div class="text-3xl font-black text-emerald-700">{{ $stats['online'] }}</div>
                    <div class="text-xs font-bold text-emerald-600">Online</div>
                </div>
                <div class="rounded-2xl bg-slate-100 p-4 text-center">
                    <div class="text-3xl font-black text-slate-700">{{ $stats['offline'] }}</div>
                    <div class="text-xs font-bold text-slate-500">Offline</div>
                </div>
            </div>
            <div class="mt-5">
                <canvas id="loginChart" height="130"></canvas>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <form method="GET" class="flex flex-1 flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, username, email, phone..."
                       class="min-w-[240px] flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-rose-400 focus:ring-4 focus:ring-rose-500/10">
                <select name="role" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    <option value="">Tất cả vai trò</option>
                    @foreach($roleLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected(request('status') == 'active')>Hoạt động</option>
                    <option value="blocked" @selected(request('status') == 'blocked')>Đã khóa</option>
                    <option value="deleted" @selected(request('status') == 'deleted')>Đã xóa</option>
                </select>
                <select name="sort" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    <option value="created_at" @selected(request('sort') == 'created_at')>Ngày tạo</option>
                    <option value="name" @selected(request('sort') == 'name')>Tên</option>
                    <option value="email" @selected(request('sort') == 'email')>Email</option>
                    <option value="last_login_at" @selected(request('sort') == 'last_login_at')>Lần đăng nhập</option>
                </select>
                <button class="rounded-2xl bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-rose-500/20">Lọc</button>
            </form>

            <div class="flex flex-wrap gap-2">
                <button type="button" x-on:click="createOpen = true" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white">Thêm user</button>
                <button type="button" x-on:click="importOpen = true" class="rounded-2xl bg-white px-4 py-3 text-sm font-bold text-slate-700 ring-1 ring-slate-200">Import</button>
                <a href="{{ route('admin.users.export.csv') }}" class="rounded-2xl bg-white px-4 py-3 text-sm font-bold text-slate-700 ring-1 ring-slate-200">Export Excel</a>
                <a href="{{ route('admin.users.export.pdf') }}" target="_blank" class="rounded-2xl bg-white px-4 py-3 text-sm font-bold text-slate-700 ring-1 ring-slate-200">Export PDF</a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.bulk') }}" class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        @csrf
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <select name="action" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                    <option value="activate">Bulk Active</option>
                    <option value="block">Bulk Block</option>
                    <option value="delete">Bulk Delete</option>
                    <option value="restore">Bulk Restore</option>
                </select>
                <button class="rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white" x-bind:disabled="selected.length === 0">Áp dụng</button>
            </div>
            <div class="text-sm font-semibold text-slate-500"><span x-text="selected.length"></span> đã chọn</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left"><input type="checkbox" x-on:change="selected = $event.target.checked ? [...document.querySelectorAll('.user-checkbox')].map(i => i.value) : []"></th>
                        <th class="px-5 py-4 text-left font-bold text-slate-600">Người dùng</th>
                        <th class="px-5 py-4 text-left font-bold text-slate-600">Vai trò</th>
                        <th class="px-5 py-4 text-left font-bold text-slate-600">Trạng thái</th>
                        <th class="px-5 py-4 text-left font-bold text-slate-600">Đăng nhập</th>
                        <th class="px-5 py-4 text-right font-bold text-slate-600">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <input type="checkbox" name="users[]" value="{{ $user->id }}" class="user-checkbox rounded border-slate-300 text-rose-600" x-model="selected">
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatarUrl() }}" class="h-11 w-11 rounded-2xl object-cover" alt="{{ $user->name }}">
                                    <div>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold text-slate-900 transition hover:text-rose-600">{{ $user->name }}</a>
                                        <div class="text-xs text-slate-500">{{ '@'.$user->username }} · {{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">{{ $roleLabels[$user->role] ?? $user->role }}</span></td>
                            <td class="px-5 py-4">
                                @if($user->trashed())
                                    <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-600">Đã xóa</span>
                                @else
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-500">{{ $user->last_login_at?->diffForHumans() ?? 'Chưa có' }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="inline-flex h-9 items-center justify-center rounded-xl bg-blue-50 px-3 text-xs font-bold text-[#0056D2] ring-1 ring-blue-100 transition hover:bg-blue-100">Xem</a>
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button type="button" x-on:click="open = !open" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200">Actions</button>
                                    <div x-show="open" x-on:click.outside="open = false" class="absolute right-0 z-20 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-2 text-left shadow-md dark:border-slate-700 dark:bg-slate-900" x-cloak>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="block rounded-xl px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Xem chi tiết</a>
                                        @if(! $user->trashed())
                                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-2">
                                                @csrf @method('PUT')
                                                <select name="role" class="mb-2 w-full rounded-xl border border-slate-200 px-2 py-2 text-xs">
                                                    @foreach($roleLabels as $value => $label)
                                                        <option value="{{ $value }}" @selected($user->role == $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="w-full rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">Đổi role</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-2">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="toggle_active" value="1">
                                                <button class="w-full rounded-xl px-3 py-2 text-xs font-bold {{ $user->is_active ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $user->is_active ? 'Khóa user' : 'Mở khóa' }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="p-2" onsubmit="return confirm('Xóa người dùng này?')">
                                                @csrf @method('DELETE')
                                                <button class="w-full rounded-xl bg-red-600 px-3 py-2 text-xs font-bold text-white">Xóa</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="p-2">
                                                @csrf
                                                <button class="w-full rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white">Restore</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.force-delete', $user->id) }}" class="p-2" onsubmit="return confirm('Xóa vĩnh viễn người dùng này?')">
                                                @csrf @method('DELETE')
                                                <button class="w-full rounded-xl bg-red-700 px-3 py-2 text-xs font-bold text-white">Force Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto max-w-sm">
                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">0</div>
                                    <h3 class="font-bold text-slate-900">Không có người dùng</h3>
                                    <p class="mt-1 text-sm text-slate-500">Thử thay đổi bộ lọc hoặc import danh sách mới.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">{{ $users->links() }}</div>
    </form>

    <div x-show="createOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" x-cloak>
        <div x-on:click.outside="createOpen = false" class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-md dark:bg-slate-900">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900">Tạo người dùng</h3>
                <button type="button" x-on:click="createOpen = false" class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-bold">Đóng</button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-4 md:grid-cols-2">
                @csrf
                <input name="name" required placeholder="Họ tên" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <input name="username" required placeholder="Username" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <input name="email" type="email" required placeholder="Email" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <input name="phone" placeholder="Phone" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <select name="role" required class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    @foreach($roleLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold"><input type="checkbox" name="is_active" value="1" checked> Hoạt động</label>
                <input name="password" type="password" required placeholder="Mật khẩu" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <input name="password_confirmation" type="password" required placeholder="Xác nhận mật khẩu" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <button class="md:col-span-2 rounded-2xl bg-rose-600 px-5 py-3 text-sm font-bold text-white">Tạo user</button>
            </form>
        </div>
    </div>

    <div x-show="importOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" x-cloak>
        <div x-on:click.outside="importOpen = false" class="w-full max-w-lg rounded-xl bg-white p-6 shadow-md dark:bg-slate-900">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900">Import Excel/CSV</h3>
                <button type="button" x-on:click="importOpen = false" class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-bold">Đóng</button>
            </div>
            <p class="mb-4 text-sm text-slate-500">File CSV có header: name, username, email, phone, role, password, status.</p>
            <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="file" name="file" required accept=".csv,.txt" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <button class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white">Import</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Chart) return;
    new Chart(document.getElementById('registrationChart'), {
        type: 'line',
        data: {
            labels: @json($registrationGrowth->pluck('label')),
            datasets: [{ label: 'Đăng ký', data: @json($registrationGrowth->pluck('total')), borderColor: 'rgb(79,70,229)', backgroundColor: 'rgba(79,70,229,.12)', tension: .4, fill: true }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    new Chart(document.getElementById('loginChart'), {
        type: 'bar',
        data: {
            labels: @json($loginGrowth->pluck('label')),
            datasets: [{ label: 'Login', data: @json($loginGrowth->pluck('total')), backgroundColor: 'rgb(79,70,229)', borderRadius: 10 }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
});
</script>
</x-admin-layout>
