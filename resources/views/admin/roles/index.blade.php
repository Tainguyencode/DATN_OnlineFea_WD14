<x-admin-layout title="Vai trò" page-title="Vai trò" breadcrumb="Hệ thống / Vai trò">
    <div class="role-management-page">
        <div class="role-page-heading">
            <div>
                <h2 class="role-page-title">Vai trò</h2>
                <p class="role-page-breadcrumb">Hệ thống / Vai trò</p>
            </div>

            @can('roles.create')
                <a href="{{ route('admin.roles.create') }}" class="role-btn role-btn-primary">
                    <svg class="w-5 h-5 shrink-0 inline-block" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Thêm vai trò
                </a>
            @endcan
        </div>

        @if($errors->any())
            <div class="role-alert role-alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="role-card">
            <div class="role-card-header">
                <div>
                    <h3>Danh sách vai trò</h3>
                    <p>Vai trò, số người dùng và quyền được lấy trực tiếp từ database.</p>
                </div>
                <span class="role-card-total">{{ $roles->total() }} vai trò</span>
            </div>

            @if($roles->count())
                <div class="role-table-wrap">
                    <table class="role-table">
                        <thead>
                            <tr>
                                <th>Vai trò</th>
                                <th>Slug</th>
                                <th>Người dùng</th>
                                <th>Quyền</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td data-label="Vai trò">
                                        <div class="role-name-cell">
                                            <span class="role-name">{{ $role->name }}</span>
                                            @if($role->is_system)
                                                <span class="role-system-badge">Bảo vệ</span>
                                            @endif
                                            @if($role->description)
                                                <span class="role-description">{{ $role->description }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Slug">
                                        <span class="role-code">{{ $role->slug }}</span>
                                    </td>
                                    <td data-label="Người dùng">{{ $role->users_count }}</td>
                                    <td data-label="Quyền">
                                        <span class="role-count-badge">{{ $role->permissions_count }} quyền</span>
                                    </td>
                                    <td data-label="Hành động">
                                        <div class="role-actions">
                                            @can('roles.update')
                                                <a href="{{ route('admin.roles.edit', $role) }}" class="role-link role-link-edit">Sửa</a>
                                            @endcan

                                            @if(! $role->is_system)
                                                @can('roles.delete')
                                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" data-role-delete data-role-name="{{ $role->name }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="role-link role-link-delete">Xóa</button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="role-pagination">
                    {{ $roles->links() }}
                </div>
            @else
                <div class="role-empty-state">
                    <h3>Chưa có vai trò</h3>
                    <p>Hãy tạo vai trò đầu tiên để bắt đầu cấu hình quyền cho hệ thống.</p>
                    @can('roles.create')
                        <a href="{{ route('admin.roles.create') }}" class="role-btn role-btn-primary">Thêm vai trò</a>
                    @endcan
                </div>
            @endif
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
