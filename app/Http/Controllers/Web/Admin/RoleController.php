<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('roles.view');

        $roles = Role::with('permissions')->withCount('users')->orderBy('name')->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('roles.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'alpha_dash:ascii', 'max:64', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);
        ActivityLogService::log(auth()->id(), 'create_role', Role::class, $role->id, ['slug' => $role->slug], $request);

        return back()->with('success', 'Tạo vai trò thành công.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        Gate::authorize('roles.update');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);
        ActivityLogService::log(auth()->id(), 'update_role', Role::class, $role->id, ['slug' => $role->slug], $request);

        return back()->with('success', 'Cập nhật vai trò thành công.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        Gate::authorize('roles.delete');

        if ($role->is_system || $role->users()->exists()) {
            return back()->withErrors(['role' => 'Không thể xóa role hệ thống hoặc role đang có người dùng.']);
        }

        $role->delete();
        ActivityLogService::log(auth()->id(), 'delete_role', Role::class, $role->id, null, $request);

        return back()->with('success', 'Đã xóa vai trò.');
    }
}
