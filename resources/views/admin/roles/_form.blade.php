@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $role = $role ?? null;
    $selectedPermissionIds = collect(old('permissions', $isEdit ? $role->permissions->pluck('id')->all() : []))
        ->map(fn ($id) => (int) $id)
        ->all();
    $totalPermissions = $permissionGroups->sum(fn ($group) => $group['permissions']->count());
@endphp

@if($errors->any())
    <div class="role-alert role-alert-danger">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form
    method="POST"
    action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
    class="role-form"
    data-role-permission-form
>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <section class="role-card role-info-card">
        <div class="role-card-header">
            <div>
                <h3>Thông tin vai trò</h3>
                <p>{{ $isEdit ? 'Cập nhật tên, slug và mô tả vai trò.' : 'Thiết lập vai trò mới trước khi gán quyền.' }}</p>
            </div>

            @if($isEdit && $role->is_system)
                <span class="role-system-badge">Hệ thống</span>
            @endif
        </div>

        <div class="role-field-grid">
            <label class="role-field">
                <span>Tên vai trò</span>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $role?->name) }}"
                    required
                    data-role-name
                    class="role-input"
                    placeholder="Ví dụ: Quản lý khóa học"
                >
                @error('name')
                    <small class="role-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="role-field">
                <span>Slug</span>
                <input
                    type="text"
                    name="slug"
                    value="{{ old('slug', $role?->slug) }}"
                    {{ $isEdit && $role->is_system ? 'disabled' : '' }}
                    data-role-slug
                    class="role-input"
                    placeholder="quan-ly-khoa-hoc"
                >
                @if($isEdit && $role->is_system)
                    <small class="role-help">Slug của vai trò hệ thống được khóa để tránh ảnh hưởng phân quyền.</small>
                @endif
                @error('slug')
                    <small class="role-error">{{ $message }}</small>
                @enderror
            </label>
        </div>

        <label class="role-field">
            <span>Mô tả</span>
            <textarea name="description" rows="3" class="role-input role-textarea" placeholder="Mô tả phạm vi và trách nhiệm của vai trò">{{ old('description', $role?->description) }}</textarea>
            @error('description')
                <small class="role-error">{{ $message }}</small>
            @enderror
        </label>
    </section>

    <section class="role-card">
        <div class="role-card-header role-permission-header">
            <div>
                <h3>Danh sách quyền</h3>
                <p>Quyền được nhóm theo module từ bảng permissions.</p>
            </div>

            <div class="role-toolbar">
                <span class="role-selected-count"><strong data-selected-count>0</strong> / {{ $totalPermissions }} quyền</span>
                <button type="button" class="role-btn role-btn-secondary" data-select-all-permissions>Chọn tất cả quyền</button>
                <button type="button" class="role-btn role-btn-ghost" data-clear-all-permissions>Bỏ chọn tất cả</button>
            </div>
        </div>

        @error('permissions')
            <div class="role-alert role-alert-danger">{{ $message }}</div>
        @enderror

        @if($permissionGroups->count())
            <div class="role-permission-grid">
                @foreach($permissionGroups as $group)
                    <article class="role-permission-card" data-permission-group="{{ $group['key'] }}">
                        <div class="role-permission-card-head">
                            <div>
                                <h4>{{ $group['label'] }}</h4>
                                <span>{{ $group['permissions']->count() }} quyền</span>
                            </div>

                            <label class="role-group-check">
                                <input type="checkbox" data-group-toggle="{{ $group['key'] }}">
                                <span>Chọn tất cả</span>
                            </label>
                        </div>

                        <div class="role-permission-list">
                            @foreach($group['permissions'] as $permission)
                                <label class="role-permission-option">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        data-permission-checkbox
                                        data-group="{{ $group['key'] }}"
                                        @checked(in_array((int) $permission->id, $selectedPermissionIds, true))
                                    >
                                    <span>
                                        <strong>{{ $permission->name }}</strong>
                                        <small>{{ $permission->slug }}</small>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="role-empty-state">
                <h3>Chưa có quyền</h3>
                <p>Vui lòng seed dữ liệu permission trước khi gán quyền cho vai trò.</p>
            </div>
        @endif
    </section>

    <div class="role-form-actions">
        <button type="submit" class="role-btn role-btn-primary">
            <svg class="w-5 h-5 shrink-0 inline-block" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ $isEdit ? 'Lưu vai trò' : 'Lưu vai trò' }}
        </button>

        <a href="{{ route('admin.roles.index') }}" class="role-btn role-btn-secondary">Hủy</a>
    </div>
</form>

<script>
    (() => {
        const form = document.querySelector('[data-role-permission-form]');

        if (!form) {
            return;
        }

        const permissionCheckboxes = Array.from(form.querySelectorAll('[data-permission-checkbox]'));
        const groupToggles = Array.from(form.querySelectorAll('[data-group-toggle]'));
        const selectedCount = form.querySelector('[data-selected-count]');
        const nameInput = form.querySelector('[data-role-name]');
        const slugInput = form.querySelector('[data-role-slug]');
        let slugTouched = Boolean(slugInput && slugInput.value);

        const slugify = (value) => value
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'D')
            .toLowerCase()
            .replace(/[^a-z0-9_-]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .slice(0, 64);

        const updateGroupState = () => {
            groupToggles.forEach((toggle) => {
                const group = toggle.dataset.groupToggle;
                const groupBoxes = permissionCheckboxes.filter((checkbox) => checkbox.dataset.group === group);
                const checkedBoxes = groupBoxes.filter((checkbox) => checkbox.checked);

                toggle.checked = groupBoxes.length > 0 && checkedBoxes.length === groupBoxes.length;
                toggle.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < groupBoxes.length;
            });

            if (selectedCount) {
                selectedCount.textContent = permissionCheckboxes.filter((checkbox) => checkbox.checked).length;
            }
        };

        groupToggles.forEach((toggle) => {
            toggle.addEventListener('change', () => {
                permissionCheckboxes
                    .filter((checkbox) => checkbox.dataset.group === toggle.dataset.groupToggle)
                    .forEach((checkbox) => {
                        checkbox.checked = toggle.checked;
                    });

                updateGroupState();
            });
        });

        permissionCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateGroupState);
        });

        form.querySelector('[data-select-all-permissions]')?.addEventListener('click', () => {
            permissionCheckboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
            updateGroupState();
        });

        form.querySelector('[data-clear-all-permissions]')?.addEventListener('click', () => {
            permissionCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            updateGroupState();
        });

        slugInput?.addEventListener('input', () => {
            slugTouched = true;
            slugInput.value = slugify(slugInput.value);
        });

        nameInput?.addEventListener('input', () => {
            if (!slugInput || slugInput.disabled || slugTouched) {
                return;
            }

            slugInput.value = slugify(nameInput.value);
        });

        updateGroupState();
    })();
</script>
