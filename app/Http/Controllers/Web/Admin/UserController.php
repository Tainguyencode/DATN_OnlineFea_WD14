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
        $query = User::query()
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');

                $q->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

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

    public function show(int $user): View
    {
        Gate::authorize('users.view');

        $user = User::withTrashed()
            ->with('roles.permissions')
            ->findOrFail($user);

        $isOnline = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->exists();

        $stats = [
            'teaching_courses' => $user->courses()->count(),
            'enrollments' => $user->enrollments()->count(),
            'active_enrollments' => $user->enrollments()->where('status', 'active')->count(),
            'orders' => $user->orders()->count(),
            'paid_revenue' => (float) $user->orders()->where('status', 'paid')->sum('total_amount'),
            'certificates' => $user->certificates()->count(),
            'reviews' => $user->reviews()->count(),
            'quiz_attempts' => $user->quizAttempts()->count(),
        ];

        $recentEnrollments = $user->enrollments()
            ->with('course:id,title,slug,status')
            ->orderByDesc('enrolled_at')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $recentTeachingCourses = $user->courses()
            ->select(['id', 'instructor_id', 'title', 'slug', 'status', 'price', 'sale_price', 'discount_price', 'created_at'])
            ->withCount([
                'enrollments as active_enrollments_count' => fn ($query) => $query->where('status', 'active'),
            ])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $recentOrders = $user->orders()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'order_code', 'status', 'total_amount', 'payment_method', 'created_at']);

        $recentCertificates = $user->certificates()
            ->with('course:id,title,slug')
            ->orderByDesc('issued_at')
            ->limit(5)
            ->get(['id', 'user_id', 'course_id', 'certificate_code', 'issued_at']);

        $recentActivityLogs = $user->activityLogs()
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['id', 'action', 'description', 'ip_address', 'created_at']);

        return view('admin.users.show', compact(
            'user',
            'isOnline',
            'stats',
            'recentEnrollments',
            'recentTeachingCourses',
            'recentOrders',
            'recentCertificates',
            'recentActivityLogs'
        ));
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
