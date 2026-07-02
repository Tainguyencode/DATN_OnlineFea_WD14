<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['password_confirmation']);

        if (! array_key_exists('is_active', $data)) {
            $data['is_active'] = true;
        }

        $user = User::create($data);

        ActivityLogService::log(
            auth()->id(),
            'create_user',
            User::class,
            $user->id,
            $request->safe()->except(['password', 'password_confirmation']),
            $request
        );

        return redirect()
            ->route('admin.users')
            ->with('success', 'Thêm người dùng thành công!');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($request->has('toggle_active')) {
            $user->update(['is_active' => ! $user->is_active]);

            ActivityLogService::log(
                auth()->id(),
                'toggle_user_active',
                User::class,
                $user->id,
                ['is_active' => $user->is_active],
                $request
            );

            return back()->with('success', 'Cập nhật trạng thái người dùng thành công!');
        }

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        unset($data['password_confirmation']);

        $user->update($data);

        ActivityLogService::log(
            auth()->id(),
            'update_user',
            User::class,
            $user->id,
            collect($data)->except(['password'])->all(),
            $request
        );

        return back()->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Không thể xóa tài khoản của chính bạn.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['error' => 'Không thể xóa admin cuối cùng của hệ thống.']);
        }

        $userId = $user->id;
        $userName = $user->name;

        try {
            $user->delete();
        } catch (QueryException) {
            return back()->withErrors(['error' => 'Không thể xóa người dùng do còn dữ liệu liên quan.']);
        }

        ActivityLogService::log(
            auth()->id(),
            'delete_user',
            User::class,
            $userId,
            ['name' => $userName],
            $request
        );

        return redirect()
            ->route('admin.users')
            ->with('success', 'Xóa người dùng thành công!');
    }
}
