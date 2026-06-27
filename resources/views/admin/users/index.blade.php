<x-admin-layout title="Người dùng" page-title="Quản lý người dùng">

<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, email..."
           class="flex-1 min-w-[200px] px-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 outline-none">
    <select name="role" class="px-4 py-2 border border-slate-300 rounded-xl text-sm bg-white">
        <option value="">Tất cả vai trò</option>
        <option value="student" @selected(request('role') == 'student')>Học viên</option>
        <option value="instructor" @selected(request('role') == 'instructor')>Giảng viên</option>
        <option value="admin" @selected(request('role') == 'admin')>Admin</option>
    </select>
    <button type="submit" class="bg-rose-600 text-white px-5 py-2 rounded-xl text-sm font-medium hover:bg-rose-700">Lọc</button>
</form>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Người dùng</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Vai trò</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Trạng thái</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Ngày tạo</th>
                <th class="text-right px-6 py-4 font-semibold text-slate-600">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($users as $user)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-900">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $roleColors = ['student' => 'bg-indigo-100 text-indigo-700', 'instructor' => 'bg-emerald-100 text-emerald-700', 'admin' => 'bg-rose-100 text-rose-700'];
                            $roleLabels = ['student' => 'Học viên', 'instructor' => 'Giảng viên', 'admin' => 'Admin'];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? '' }}">{{ $roleLabels[$user->role] ?? $user->role }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="inline-flex gap-2 items-center">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                @csrf @method('PUT')
                                <select name="role" class="text-xs border border-slate-300 rounded-lg px-2 py-1 bg-white" onchange="this.form.submit()">
                                    <option value="student" @selected($user->role == 'student')>Học viên</option>
                                    <option value="instructor" @selected($user->role == 'instructor')>Giảng viên</option>
                                    <option value="admin" @selected($user->role == 'admin')>Admin</option>
                                </select>
                            </form>
                            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="toggle_active" value="1">
                                <button type="submit" class="text-xs px-3 py-1 rounded-lg font-medium {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                    {{ $user->is_active ? 'Khóa' : 'Mở khóa' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $users->links() }}</div>
</div>

</x-admin-layout>
