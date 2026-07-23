<x-admin-layout title="Thêm vai trò" page-title="Thêm vai trò" breadcrumb="Hệ thống / Vai trò">
    <div class="role-management-page">
        <div class="role-page-heading">
            <div>
                <h2 class="role-page-title">Thêm vai trò</h2>
                <p class="role-page-breadcrumb">Hệ thống / Vai trò</p>
            </div>

            <a href="{{ route('admin.roles.index') }}" class="role-btn role-btn-secondary">
                <svg class="w-5 h-5 shrink-0 inline-block" aria-hidden="true" viewBox="0 0 24 24" fill="none">
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
