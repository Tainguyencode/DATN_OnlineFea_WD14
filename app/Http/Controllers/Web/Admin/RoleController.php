<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const GROUP_LABELS = [
        'audit' => 'Nhật ký hệ thống',
        'categories' => 'Danh mục khóa học',
        'courses' => 'Khóa học',
        'coupons' => 'Mã giảm giá',
        'dashboard' => 'Bảng điều khiển',
        'instructor_applications' => 'Giảng viên',
        'instructors' => 'Giảng viên',
        'notifications' => 'Thông báo',
        'orders' => 'Đơn hàng',
        'payments' => 'Thanh toán',
        'profiles' => 'Hồ sơ',
        'reports' => 'Báo cáo',
        'reviews' => 'Đánh giá',
        'roles' => 'Vai trò',
        'system' => 'Hệ thống',
        'users' => 'Người dùng',
    ];

    public function index(): View
    {
        Gate::authorize('roles.view');

        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        Gate::authorize('roles.create');

        return view('admin.roles.create', [
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Gate::authorize('roles.create');

        $validated = $request->validated();

        $role = DB::transaction(function () use ($validated): Role {
            $role = Role::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'is_system' => false,
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);

            return $role;
        });

        ActivityLogService::log(auth()->id(), 'create_role', Role::class, $role->id, ['slug' => $role->slug], $request);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Tạo vai trò thành công.');
    }

    public function edit(Role $role): View
    {
        Gate::authorize('roles.update');

        $role->load('permissions');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        Gate::authorize('roles.update');

        $validated = $request->validated();

        DB::transaction(function () use ($role, $validated): void {
            $payload = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ];

            if (! $role->is_system) {
                $payload['slug'] = $validated['slug'];
            }

            $role->update($payload);
            $role->permissions()->sync($validated['permissions'] ?? []);
        });

        ActivityLogService::log(auth()->id(), 'update_role', Role::class, $role->id, ['slug' => $role->slug], $request);

        return redirect()
            ->route('admin.roles.edit', $role)
            ->with('success', 'Cập nhật vai trò thành công.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        Gate::authorize('roles.delete');

        if ($role->is_system) {
            return back()->withErrors(['role' => 'Không thể xóa vai trò hệ thống.']);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Không thể xóa vai trò đang có người dùng.']);
        }

        $role->delete();
        ActivityLogService::log(auth()->id(), 'delete_role', Role::class, $role->id, null, $request);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Đã xóa vai trò.');
    }

    /**
     * @return Collection<int, array{key: string, label: string, permissions: Collection<int, Permission>}>
     */
    private function permissionGroups(): Collection
    {
        return Permission::query()
            ->orderBy('group')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'group'])
            ->groupBy(fn (Permission $permission): string => $this->permissionGroupKey($permission))
            ->map(fn (Collection $permissions, string $group): array => [
                'key' => $group,
                'label' => $this->permissionGroupLabel($group),
                'permissions' => $permissions->values(),
            ])
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    private function permissionGroupKey(Permission $permission): string
    {
        $group = trim((string) $permission->group);

        if ($group !== '') {
            return $group;
        }

        return str($permission->slug)->before('.')->snake()->toString() ?: 'system';
    }

    private function permissionGroupLabel(string $group): string
    {
        return self::GROUP_LABELS[$group] ?? str($group)
            ->replace(['_', '-'], ' ')
            ->title()
            ->toString();
    }
}
