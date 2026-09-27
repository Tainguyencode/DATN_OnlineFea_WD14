<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function bulk(Request $request): RedirectResponse
    {
        Gate::authorize('users.manage');
        $data = $request->validate([
            'action' => ['required', Rule::in(['activate', 'block', 'delete', 'restore'])],
            'users' => ['required', 'array', 'min:1', 'max:200'],
            'users.*' => ['required', 'integer', 'distinct', 'exists:users,id'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $users = User::withTrashed()->whereIn('id', $data['users'])->orderBy('id')->lockForUpdate()->get();
            foreach ($users as $user) {
                if (in_array($data['action'], ['block', 'delete'], true)
                    && ($user->id === auth()->id() || $user->role === 'admin')) {
                    throw ValidationException::withMessages(['users' => 'Không thể khóa hoặc xóa tài khoản của bạn hay quản trị viên bằng thao tác hàng loạt.']);
                }
                if ($data['action'] === 'restore') {
                    $user->restore();
                } elseif (! $user->trashed()) {
                    if ($data['action'] === 'delete') {
                        $user->delete();
                    } else {
                        $user->update(['is_active' => $data['action'] === 'activate']);
                    }
                }
            }
            ActivityLogService::log(auth()->id(), 'bulk_users', User::class, null, $data, $request);
        });

        return back()->with('success', 'Đã xử lý danh sách người dùng.');
    }

    public function restore(Request $request, int $user): RedirectResponse
    {
        Gate::authorize('users.manage');
        DB::transaction(function () use ($user, $request) {
            $record = User::onlyTrashed()->lockForUpdate()->findOrFail($user);
            $record->restore();
            ActivityLogService::log(auth()->id(), 'restore_user', User::class, $user, null, $request);
        });

        return back()->with('success', 'Khôi phục người dùng thành công!');
    }

    public function forceDelete(Request $request, int $user): RedirectResponse
    {
        Gate::authorize('users.manage');
        try {
            DB::transaction(function () use ($user, $request) {
                $record = User::onlyTrashed()->lockForUpdate()->findOrFail($user);
                if ($record->id === auth()->id() || $record->role === 'admin') {
                    throw ValidationException::withMessages(['user' => 'Không thể xóa vĩnh viễn tài khoản quản trị viên.']);
                }
                // Giữ lịch sử học tập và tài chính, không để khóa ngoại cascade xóa theo.
                if ($record->orders()->exists() || $record->enrollments()->exists()
                    || $record->courses()->exists() || $record->withdrawals()->exists()) {
                    throw ValidationException::withMessages(['user' => 'Người dùng còn dữ liệu học tập hoặc tài chính. Hãy giữ tài khoản trong thùng rác.']);
                }
                $record->forceDelete();
                ActivityLogService::log(auth()->id(), 'force_delete_user', User::class, $user, null, $request);
            });
        } catch (QueryException) {
            return back()->withErrors(['user' => 'Không thể xóa vĩnh viễn do còn dữ liệu liên quan.']);
        }

        return redirect()->route('admin.users')->with('success', 'Xóa vĩnh viễn người dùng thành công!');
    }

    public function exportCsv()
    {
        Gate::authorize('users.view');

        return response()->streamDownload(function () {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['name', 'username', 'email', 'phone', 'role', 'status'], ',', '"', '');
            foreach (User::withTrashed()->select(['id', 'name', 'username', 'email', 'phone', 'role', 'is_active', 'deleted_at'])->lazyById() as $user) {
                $row = [$user->name, $user->username, $user->email, $user->phone, $user->role,
                    $user->trashed() ? 'deleted' : ($user->is_active ? 'active' : 'blocked')];
                $row = array_map(static function ($value) {
                    $value = (string) $value;

                    return preg_match('/^[\s]*[=+@-]|^[\t\r\n]/u', $value) ? "'".$value : $value;
                }, $row);
                fputcsv($stream, $row, ',', '"', '');
            }
            fclose($stream);
        }, 'users.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf(): View
    {
        Gate::authorize('users.view');

        // Trang in hiện có hỗ trợ chọn Save as PDF trong trình duyệt.
        return view('admin.users.export-pdf', ['users' => User::withTrashed()->orderBy('id')->get()]);
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('users.manage');
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);
        $stream = fopen($request->file('file')->getRealPath(), 'r');
        $rows = [];
        try {
            $headers = fgetcsv($stream, 0, ',', '"', '');
            $expected = ['name', 'username', 'email', 'phone', 'role', 'password', 'status'];
            if ($headers) {
                $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
                $headers = array_map('trim', $headers);
            }
            if (! $headers || count($headers) !== count(array_unique($headers)) || array_diff($expected, $headers)) {
                throw ValidationException::withMessages(['file' => 'CSV phải có đủ các cột: '.implode(', ', $expected)]);
            }
            $line = 1;
            while (($values = fgetcsv($stream, 0, ',', '"', '')) !== false) {
                $line++;
                if ($values === [null]) {
                    continue;
                }
                if (count($rows) >= 1000 || count($values) !== count($headers)) {
                    throw ValidationException::withMessages(['file' => "Dòng {$line} không hợp lệ hoặc file vượt quá 1.000 người dùng."]);
                }
                $row = array_intersect_key(array_combine($headers, $values), array_flip($expected));
                foreach (['name', 'username', 'email', 'phone', 'role', 'status'] as $field) {
                    $row[$field] = trim($row[$field] ?? '');
                }
                $rows[] = $row;
            }
        } finally {
            fclose($stream);
        }

        // Không flash input/mật khẩu CSV khi dữ liệu không hợp lệ.
        $validator = Validator::make(['rows' => $rows], [
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.name' => ['required', 'string', 'max:255'],
            'rows.*.username' => ['nullable', 'string', 'max:255', 'distinct:ignore_case', 'unique:users,username'],
            'rows.*.email' => ['required', 'email', 'max:255', 'distinct:ignore_case', 'unique:users,email'],
            'rows.*.phone' => ['nullable', 'regex:/^[0-9+\-\s().]{8,20}$/'],
            'rows.*.role' => ['required', Rule::in(['student', 'instructor', 'admin'])],
            'rows.*.password' => ['required', 'string', Password::defaults()],
            'rows.*.status' => ['required', Rule::in(['active', 'blocked'])],
        ]);
        if ($validator->fails()) {
            return back()->withErrors(['file' => 'Không nhập dữ liệu: '.$validator->errors()->first()]);
        }
        try {
            DB::transaction(function () use ($rows, $request) {
                foreach ($rows as $row) {
                    $row['is_active'] = $row['status'] === 'active';
                    unset($row['status']);
                    $row['username'] = $row['username'] ?: null;
                    if ($row['role'] === 'instructor') {
                        $row['instructor_status'] = 'pending';
                        $row['needs_admin_review'] = false;
                    }
                    User::create($row);
                }
                ActivityLogService::log(auth()->id(), 'import_users', User::class, null, ['count' => count($rows)], $request);
            });
        } catch (QueryException) {
            return back()->withErrors(['file' => 'Không nhập dữ liệu do trùng tài khoản hoặc lỗi cơ sở dữ liệu. Không có dòng nào được lưu.']);
        }

        return back()->with('success', 'Đã nhập '.count($rows).' người dùng.');
    }

    public function index(Request $request): View
    {
        $query = User::query()
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->string('status');
                if ($status == 'active') {
                    $q->where('is_active', true)->whereNull('deleted_at');
                } elseif ($status == 'blocked') {
                    $q->where('is_active', false);
                } elseif ($status == 'deleted') {
                    $q->onlyTrashed();
                }
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');

                $q->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
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

        // Thống kê phân nhóm người dùng theo tiến độ học tập (Hoàn thành, Đang học, Mới, Chưa hoàn thành)
        $completedStudentsCount = Enrollment::where('status', 'completed')
            ->orWhere('progress_percent', '>=', 100)
            ->count();

        $inProgressStudentsCount = Enrollment::where('status', 'active')
            ->whereBetween('progress_percent', [15, 99.99])
            ->count();

        $incompleteStudentsCount = Enrollment::where('status', 'active')
            ->whereBetween('progress_percent', [0.01, 14.99])
            ->count();

        $newStudentsCount = Enrollment::where('status', 'active')
            ->where('progress_percent', '<=', 0)
            ->count();

        $totalStudents = User::where('role', 'student')->count();
        if ($totalStudents > ($completedStudentsCount + $inProgressStudentsCount + $incompleteStudentsCount + $newStudentsCount)) {
            $newStudentsCount += $totalStudents - ($completedStudentsCount + $inProgressStudentsCount + $incompleteStudentsCount + $newStudentsCount);
        }

        $stats = [
            'total' => User::withTrashed()->count(),
            'admins' => User::where('role', 'admin')->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'students' => $totalStudents,
            'online' => $onlineUserIds->count(),
            'offline' => max(User::count() - $onlineUserIds->count(), 0),
            'blocked' => User::where('is_active', false)->count(),
            'deleted' => User::onlyTrashed()->count(),
            'completed_students' => $completedStudentsCount,
            'in_progress_students' => $inProgressStudentsCount,
            'new_students' => $newStudentsCount,
            'incomplete_students' => $incompleteStudentsCount,
        ];

        // Tạo chuỗi 20 tháng từ Tháng 01/2025 đến Tháng 08/2026
        $monthTimeline = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthTimeline[] = Carbon::create(2025, $m, 1, 0, 0, 0);
        }
        for ($m = 1; $m <= 8; $m++) {
            $monthTimeline[] = Carbon::create(2026, $m, 1, 0, 0, 0);
        }

        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        // Lấy số lượng người dùng theo tháng trực tiếp từ DB
        $userCountsByMonth = User::selectRaw("{$monthExpr} as m_label, COUNT(*) as count")
            ->groupBy('m_label')
            ->pluck('count', 'm_label')
            ->all();

        // Lấy số lượng học tập theo tháng trực tiếp từ DB
        $enrollMonthExpr = $driver === 'sqlite' ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";
        $completedByMonth = Enrollment::where(function ($q) {
            $q->where('status', 'completed')->orWhere('progress_percent', '>=', 100);
        })
            ->selectRaw("{$enrollMonthExpr} as m_label, COUNT(*) as count")
            ->groupBy('m_label')
            ->pluck('count', 'm_label')
            ->all();

        $inProgressByMonth = Enrollment::where('status', 'active')
            ->whereBetween('progress_percent', [15, 99.99])
            ->selectRaw("{$enrollMonthExpr} as m_label, COUNT(*) as count")
            ->groupBy('m_label')
            ->pluck('count', 'm_label')
            ->all();

        $incompleteByMonth = Enrollment::where('status', 'active')
            ->whereBetween('progress_percent', [0.01, 14.99])
            ->selectRaw("{$enrollMonthExpr} as m_label, COUNT(*) as count")
            ->groupBy('m_label')
            ->pluck('count', 'm_label')
            ->all();

        $newEnrolledByMonth = Enrollment::where('status', 'active')
            ->where('progress_percent', '<=', 0)
            ->selectRaw("{$enrollMonthExpr} as m_label, COUNT(*) as count")
            ->groupBy('m_label')
            ->pluck('count', 'm_label')
            ->all();

        $runningTotal = 0;
        $registrationGrowth = collect($monthTimeline)->map(function (Carbon $start) use (&$runningTotal, $userCountsByMonth, $completedByMonth, $inProgressByMonth, $incompleteByMonth, $newEnrolledByMonth): array {
            $key = $start->format('Y-m');
            $totalMonthUsers = $userCountsByMonth[$key] ?? 0;
            $completedCount = $completedByMonth[$key] ?? 0;
            $inProgressCount = $inProgressByMonth[$key] ?? 0;
            $incompleteCount = $incompleteByMonth[$key] ?? 0;
            $newCount = $newEnrolledByMonth[$key] ?? 0;

            if ($totalMonthUsers > ($completedCount + $inProgressCount + $incompleteCount + $newCount)) {
                $newCount += $totalMonthUsers - ($completedCount + $inProgressCount + $incompleteCount + $newCount);
            }

            $runningTotal += $totalMonthUsers;

            return [
                'label' => $start->format('m/y'),
                'full_label' => 'Tháng '.$start->format('m/Y'),
                'total' => $totalMonthUsers,
                'new_users' => $newCount,
                'completed' => $completedCount,
                'in_progress' => $inProgressCount,
                'incomplete' => $incompleteCount,
                'cumulative' => $runningTotal,
            ];
        })->values();

        $driver = DB::connection()->getDriverName();
        $dayExpr = $driver === 'sqlite' ? "strftime('%Y-%m-%d', last_login_at)" : "DATE_FORMAT(last_login_at, '%Y-%m-%d')";

        $loginGrowth = User::whereNotNull('last_login_at')
            ->selectRaw("{$dayExpr} as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderBy('label', 'desc')
            ->limit(14)
            ->get()
            ->reverse()
            ->values();

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

        if (($data['role'] ?? '') === 'instructor') {
            $data['instructor_status'] = 'pending';
            $data['needs_admin_review'] = false;
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
