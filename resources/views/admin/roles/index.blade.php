<x-admin-layout title="Vai trò" page-title="Vai trò" breadcrumb="Hệ thống / Vai trò">
    <div class="space-y-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Vai trò</h2>
                <p class="text-xs text-slate-500 mt-1">Hệ thống / Vai trò</p>
            </div>

            @can('roles.create')
                <a href="{{ route('admin.roles.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Thêm vai trò
                </a>
            @endcan
        </div>

        @if($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto p-3 sm:p-4">
                <table class="w-full min-w-[800px] text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="rounded-l-lg px-4 py-3 text-left font-semibold text-slate-600">Vai trò</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Slug</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Mô tả</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-600">Người dùng</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-600">Quyền</th>
                            <th class="rounded-r-lg px-4 py-3 text-right font-semibold text-slate-600">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($roles as $role)
                            <tr class="transition-colors duration-150 hover:bg-slate-50/80">
                                <td class="px-4 py-3.5 align-middle font-bold text-slate-950">
                                    <div class="flex items-center gap-2">
                                        {{ $role->name }}
                                        @if($role->is_system)
                                            <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-xs font-bold text-amber-700 ring-1 ring-amber-200">Hệ thống</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 align-middle text-slate-600">
                                    <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-700 font-semibold">{{ $role->slug }}</code>
                                </td>
                                <td class="px-4 py-3.5 align-middle text-slate-500 max-w-xs truncate">
                                    {{ $role->description ?? 'Chưa có mô tả' }}
                                </td>
                                <td class="px-4 py-3.5 text-center align-middle font-semibold text-slate-900">
                                    {{ number_format($role->users_count) }}
                                </td>
                                <td class="px-4 py-3.5 text-center align-middle">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                        {{ $role->permissions_count }} quyền
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @can('roles.update')
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex h-8 items-center rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">Sửa</a>
                                        @endcan

                                        @if(! $role->is_system)
                                            @can('roles.delete')
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline-flex" data-role-delete data-role-name="{{ $role->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex h-8 items-center rounded-lg border border-rose-100 bg-rose-50 px-3 text-xs font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-100">Xóa</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-14 text-center">
                                    <h3 class="text-base font-bold text-slate-950">Chưa có vai trò</h3>
                                    <p class="mt-1 text-sm text-slate-500">Hãy tạo vai trò đầu tiên để bắt đầu cấu hình quyền cho hệ thống.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 bg-slate-50/40 px-5 py-4">
                {{ $roles->links() }}
            </div>
        </section>
    </div>

    <script>
        document.querySelectorAll('[data-role-delete]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                const roleName = form.dataset.roleName || 'vai trò này';

                if (!window.confirm(`Bạn chắc chắn muốn xóa ${roleName}?`)) {
                    event.preventDefault();
                }
            });
        });
    </script>
</x-admin-layout>
