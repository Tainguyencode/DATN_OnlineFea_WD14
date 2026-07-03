<x-admin-layout title="Người dùng" page-title="Quản lý người dùng">

<form method="GET" class="mb-5 rounded-2xl border border-slate-200/70 bg-white p-4 shadow-[0_14px_34px_rgba(15,23,42,0.05)] sm:p-5">
    <div class="flex flex-col gap-3 md:flex-row md:items-center">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, email..."
               class="h-11 flex-1 rounded-xl border border-slate-200 bg-slate-50/70 px-4 text-sm text-slate-700 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100">
        <select name="role" class="h-11 min-w-44 rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
            <option value="">Tất cả vai trò</option>
            <option value="student" @selected(request('role') == 'student')>Học viên</option>
            <option value="instructor" @selected(request('role') == 'instructor')>Giảng viên</option>
            <option value="admin" @selected(request('role') == 'admin')>Admin</option>
        </select>
        <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 text-sm font-semibold text-white shadow-sm shadow-rose-900/10 transition-colors duration-200 hover:bg-rose-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-rose-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 01.8 1.6L14 13.667V19a1 1 0 01-1.447.894l-4-2A1 1 0 018 17v-3.333L3.2 4.6A1 1 0 013 4z"/></svg>
            Lọc
        </button>
    </div>
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.06)]">
    <div class="overflow-x-auto p-3 sm:p-4">
        <table class="w-full min-w-[820px] text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="rounded-l-xl px-5 py-3.5 text-left font-semibold text-slate-600">Người dùng</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-600">Vai trò</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-600">Trạng thái</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-600">Ngày tạo</th>
                    <th class="rounded-r-xl px-5 py-3.5 text-right font-semibold text-slate-600">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($users as $user)
                    <tr class="h-16 transition-colors duration-150 hover:bg-slate-50/80">
                        <td class="px-5 py-3 align-middle">
                            <div class="max-w-xs truncate font-semibold text-slate-900">{{ $user->name }}</div>
                            <div class="max-w-xs truncate text-xs text-slate-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-5 py-3 align-middle">
                            @php
                                $roleColors = [
                                    'student' => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100',
                                    'instructor' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100',
                                    'admin' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-100',
                                ];
                                $roleLabels = ['student' => 'Học viên', 'instructor' => 'Giảng viên', 'admin' => 'Admin'];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-slate-50 text-slate-600 ring-1 ring-slate-100' }}">
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-5 py-3 align-middle">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-100' }}">
                                {{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 align-middle text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 align-middle text-right">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline-flex">
                                    @csrf @method('PUT')
                                    <select name="role" class="h-8 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-medium text-slate-600 shadow-sm outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100" onchange="this.form.submit()">
                                        <option value="student" @selected($user->role == 'student')>Học viên</option>
                                        <option value="instructor" @selected($user->role == 'instructor')>Giảng viên</option>
                                        <option value="admin" @selected($user->role == 'admin')>Admin</option>
                                    </select>
                                </form>
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline-flex">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="toggle_active" value="1">
                                    <button type="submit" class="inline-flex h-8 items-center rounded-lg border px-3 text-xs font-semibold transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-100 {{ $user->is_active ? 'border-rose-100 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border-emerald-100 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                        {{ $user->is_active ? 'Khóa' : 'Mở khóa' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 bg-slate-50/40 px-5 py-4">{{ $users->links() }}</div>
</div>

</x-admin-layout>
