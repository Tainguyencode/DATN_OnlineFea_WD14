<x-admin-layout title="Thêm vai trò" page-title="Thêm vai trò" breadcrumb="Hệ thống / Vai trò / Thêm mới">
    <div class="space-y-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Thêm vai trò</h2>
                <p class="text-xs text-slate-500 mt-1">Hệ thống / Vai trò / Thêm mới</p>
            </div>

            <a href="{{ route('admin.roles.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition-colors duration-200 hover:bg-slate-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Quay lại
            </a>
        </div>

        @include('admin.roles._form', [
            'mode' => 'create',
            'permissionGroups' => $permissionGroups,
        ])
    </div>
</x-admin-layout>
