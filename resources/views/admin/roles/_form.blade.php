@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $role = $role ?? null;
    $selectedPermissionIds = collect(old('permissions', $isEdit ? $role->permissions->pluck('id')->all() : []))
        ->map(fn ($id) => (int) $id)
        ->all();
    $totalPermissions = $permissionGroups->sum(fn ($group) => $group['permissions']->count());
@endphp

<form
    method="POST"
    action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
    class="space-y-6"
    data-role-permission-form
>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Thông tin vai trò</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $isEdit ? 'Cập nhật tên, slug và mô tả vai trò.' : 'Thiết lập vai trò mới trước khi gán quyền.' }}</p>
            </div>

            @if($isEdit && $role->is_system)
                <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">Hệ thống</span>
            @endif
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex flex-col gap-1.5">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Tên vai trò <span class="text-rose-500">*</span></span>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $role?->name) }}"
                    required
                    data-role-name
                    class="h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100"
                    placeholder="Ví dụ: Quản lý khóa học"
                >
                @error('name')
                    <small class="text-xs font-semibold text-rose-600">{{ $message }}</small>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Slug</span>
                <input
                    type="text"
                    name="slug"
                    value="{{ old('slug', $role?->slug) }}"
                    {{ $isEdit && $role->is_system ? 'disabled' : '' }}
                    data-role-slug
                    class="h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100 disabled:bg-slate-100 disabled:cursor-not-allowed"
                    placeholder="quan-ly-khoa-hoc"
                >
                @if($isEdit && $role->is_system)
                    <small class="text-xs text-slate-500">Slug của vai trò hệ thống được khóa để tránh ảnh hưởng phân quyền.</small>
                @endif
                @error('slug')
                    <small class="text-xs font-semibold text-rose-600">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="flex flex-col gap-1.5">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Mô tả</span>
            <textarea
                name="description"
                rows="3"
                class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100"
                placeholder="Mô tả phạm vi và trách nhiệm của vai trò"
            >{{ old('description', $role?->description) }}</textarea>
            @error('description')
                <small class="text-xs font-semibold text-rose-600">{{ $message }}</small>
            @enderror
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Danh sách quyền</h3>
                <p class="text-xs text-slate-500 mt-0.5">Quyền được nhóm theo module từ bảng permissions.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">
                    Đã chọn: <span data-selected-count class="mx-1 text-rose-600 font-extrabold">0</span> / {{ $totalPermissions }} quyền
                </span>
                <button type="button" class="inline-flex h-8 items-center rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 cursor-pointer" data-select-all-permissions>Chọn tất cả</button>
                <button type="button" class="inline-flex h-8 items-center rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 cursor-pointer" data-clear-all-permissions>Bỏ chọn tất cả</button>
            </div>
        </div>

        @error('permissions')
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ $message }}</div>
        @enderror

        @if($permissionGroups->count())
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($permissionGroups as $group)
                    <article class="rounded-lg border border-slate-100 bg-white shadow-sm overflow-hidden flex flex-col" data-permission-group="{{ $group['key'] }}">
                        <div class="bg-slate-50 border-b border-slate-100 px-4 py-3 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $group['label'] }}</h4>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $group['permissions']->count() }} quyền</span>
                            </div>

                            <label class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 cursor-pointer select-none">
                                <input type="checkbox" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500" data-group-toggle="{{ $group['key'] }}">
                                <span>Tất cả</span>
                            </label>
                        </div>

                        <div class="p-3 space-y-2 max-h-[250px] overflow-y-auto">
                            @foreach($group['permissions'] as $permission)
                                <label class="flex items-start gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-2.5 hover:border-rose-200 hover:bg-white transition-colors duration-150 cursor-pointer select-none">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        data-permission-checkbox
                                        data-group="{{ $group['key'] }}"
                                        @checked(in_array((int) $permission->id, $selectedPermissionIds, true))
                                        class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
                                    >
                                    <div class="min-w-0">
                                        <strong class="block text-xs font-bold text-slate-800 leading-normal">{{ $permission->name }}</strong>
                                        <code class="block text-[10px] text-slate-400 mt-0.5 font-mono truncate">{{ $permission->slug }}</code>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 border-2 border-dashed border-slate-200 rounded-lg">
                <h3 class="text-base font-bold text-slate-950">Chưa có quyền</h3>
                <p class="mt-1 text-sm text-slate-500">Vui lòng seed dữ liệu permission trước khi gán quyền cho vai trò.</p>
            </div>
        @endif
    </section>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-rose-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700 cursor-pointer shadow-md shadow-rose-600/10">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ $isEdit ? 'Lưu vai trò' : 'Tạo vai trò' }}
        </button>

        <a href="{{ route('admin.roles.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition-colors duration-200 hover:bg-slate-50">Hủy</a>
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
