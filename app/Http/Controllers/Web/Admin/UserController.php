<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('users.view');

        $query = User::query()->withTrashed();

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->get('status')) {
            match ($status) {
                'active' => $query->where('is_active', true)->whereNull('deleted_at'),
                'blocked' => $query->where('is_active', false)->whereNull('deleted_at'),
                'deleted' => $query->onlyTrashed(),
                default => null,
            };
        }

        if ($search = $request->get('search')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';
        $sort = in_array($sort, ['name', 'email', 'role', 'created_at', 'last_login_at'], true) ? $sort : 'created_at';

        $users = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        $onlineUserIds = DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        $stats = [
            'total' => User::withTrashed()->count(),
            'admins' => User::where('role', 'admin')->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'students' => User::where('role', 'student')->count(),
            'online' => $onlineUserIds->count(),
            'offline' => max(User::count() - $onlineUserIds->count(), 0),
            'blocked' => User::where('is_active', false)->count(),
            'deleted' => User::onlyTrashed()->count(),
        ];

        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";
        $dayExpr = $driver === 'sqlite' ? "strftime('%Y-%m-%d', last_login_at)" : "DATE_FORMAT(last_login_at, '%Y-%m-%d')";

        $registrationGrowth = User::withTrashed()
            ->selectRaw("{$monthExpr} as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderBy('label')
            ->limit(12)
            ->get();

        $loginGrowth = User::whereNotNull('last_login_at')
            ->selectRaw("{$dayExpr} as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderBy('label')
            ->limit(14)
            ->get();

        return view('admin.users.index', compact('users', 'stats', 'registrationGrowth', 'loginGrowth'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('users.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash:ascii', 'min:3', 'max:32', 'unique:users,username'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:student,instructor,admin'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'is_active' => ['nullable', 'boolean'],
        ]);
        unset($validated['password_confirmation']);

        $user = User::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
            'password_changed_at' => now(),
        ]);

        ActivityLogService::log(auth()->id(), 'create_user', User::class, $user->id, ['role' => $user->role], $request);

        return back()->with('success', 'Tạo người dùng thành công.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('users.update');

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'alpha_dash:ascii', 'min:3', 'max:32', 'unique:users,username,'.$user->id],
            'email' => ['nullable', 'email:rfc', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['nullable', 'in:student,instructor,admin'],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'is_active' => ['nullable', 'boolean'],
            'toggle_active' => ['nullable', 'boolean'],
        ]);

        if ($user->id === auth()->id() && (($request->filled('role') && $request->role !== 'admin') || $request->has('toggle_active'))) {
            return back()->withErrors(['user' => 'Bạn không thể tự hạ quyền hoặc khóa chính tài khoản admin đang dùng.']);
        }

        if ($request->has('toggle_active')) {
            $validated['is_active'] = ! $user->is_active;
        }

        if (! empty($validated['password'])) {
            $validated['password_changed_at'] = now();
        } else {
            unset($validated['password']);
        }

        unset($validated['toggle_active']);

        $user->update(array_filter($validated, fn ($value) => $value !== null));
        ActivityLogService::log(auth()->id(), 'update_user', User::class, $user->id, $request->only(['role', 'is_active']), $request);

        return back()->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('users.delete');

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Bạn không thể xóa tài khoản đang đăng nhập.']);
        }

        $user->delete();
        ActivityLogService::log(auth()->id(), 'delete_user', User::class, $user->id, null, $request);

        return back()->with('success', 'Đã xóa người dùng. Bạn có thể khôi phục trong danh sách đã xóa.');
    }

    public function restore(Request $request, int $user): RedirectResponse
    {
        Gate::authorize('users.restore');

        $restoredUser = User::onlyTrashed()->findOrFail($user);
        $restoredUser->restore();
        ActivityLogService::log(auth()->id(), 'restore_user', User::class, $restoredUser->id, null, $request);

        return back()->with('success', 'Đã khôi phục người dùng.');
    }

    public function forceDelete(Request $request, int $user): RedirectResponse
    {
        Gate::authorize('users.force_delete');

        $deletedUser = User::onlyTrashed()->findOrFail($user);
        $deletedUser->forceDelete();
        ActivityLogService::log(auth()->id(), 'force_delete_user', User::class, $user, null, $request);

        return back()->with('success', 'Đã xóa vĩnh viễn người dùng.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,block,delete,restore'],
            'users' => ['required', 'array'],
            'users.*' => ['integer'],
        ]);

        Gate::authorize(match ($validated['action']) {
            'activate', 'block' => 'users.update',
            'restore' => 'users.restore',
            default => 'users.delete',
        });

        $ids = collect($validated['users'])->reject(fn ($id) => (int) $id === auth()->id())->values();

        match ($validated['action']) {
            'activate' => User::whereIn('id', $ids)->update(['is_active' => true]),
            'block' => User::whereIn('id', $ids)->update(['is_active' => false]),
            'delete' => User::whereIn('id', $ids)->delete(),
            'restore' => User::onlyTrashed()->whereIn('id', $ids)->restore(),
        };

        ActivityLogService::log(auth()->id(), 'bulk_'.$validated['action'].'_users', User::class, null, ['ids' => $ids->all()], $request);

        return back()->with('success', 'Đã xử lý thao tác hàng loạt.');
    }

    public function exportCsv(): StreamedResponse
    {
        Gate::authorize('users.export');

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'name', 'username', 'email', 'phone', 'role', 'status', 'created_at', 'last_login_at']);

            User::withTrashed()->orderBy('id')->chunk(200, function ($users) use ($handle): void {
                foreach ($users as $user) {
                    fputcsv($handle, [
                        $user->id,
                        $user->name,
                        $user->username,
                        $user->email,
                        $user->phone,
                        $user->role,
                        $user->deleted_at ? 'deleted' : ($user->is_active ? 'active' : 'blocked'),
                        $user->created_at,
                        $user->last_login_at,
                    ]);
                }
            });

            fclose($handle);
        }, 'users-export.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf(): Response
    {
        Gate::authorize('users.export');

        $users = User::withTrashed()->orderByDesc('created_at')->limit(500)->get();

        return response()
            ->view('admin.users.export-pdf', compact('users'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('users.import');

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:4096'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            if (! $data || empty($data['email'])) {
                continue;
            }

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'] ?? Str::headline(Str::before($data['email'], '@')),
                    'username' => $data['username'] ?? $this->uniqueUsername(Str::before($data['email'], '@')),
                    'phone' => $data['phone'] ?? null,
                    'role' => in_array($data['role'] ?? 'student', ['admin', 'instructor', 'student'], true) ? $data['role'] : 'student',
                    'password' => $data['password'] ?? Str::password(16),
                    'is_active' => ($data['status'] ?? 'active') !== 'blocked',
                    'email_verified_at' => now(),
                ]
            );

            $count++;
        }

        fclose($handle);
        ActivityLogService::log(auth()->id(), 'import_users', User::class, null, ['count' => $count], $request);

        return back()->with('success', "Đã import {$count} người dùng.");
    }

    private function uniqueUsername(string $base): string
    {
        $base = Str::of($base)->ascii()->lower()->replaceMatches('/[^a-z0-9_]+/', '_')->trim('_')->limit(24, '')->toString() ?: 'user';
        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.$suffix++;
        }

        return $username;
    }
}
