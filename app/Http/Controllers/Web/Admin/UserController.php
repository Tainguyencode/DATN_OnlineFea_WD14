<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->get('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($request->filled('role')) {
            $request->validate(['role' => 'required|in:student,instructor,admin']);
            $user->update(['role' => $request->role]);
        }

        if ($request->has('toggle_active')) {
            $user->update(['is_active' => ! $user->is_active]);
        }

        ActivityLogService::log(auth()->id(), 'update_user', User::class, $user->id, $request->only(['role']), $request);

        return back()->with('success', 'Cập nhật người dùng thành công!');
    }
}
