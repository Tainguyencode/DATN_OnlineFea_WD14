<x-admin-layout :title="'Người dùng - '.$user->name" page-title="Chi tiết người dùng" :breadcrumb="$user->email">
@php
    $roleLabels = ['student' => 'Học viên', 'instructor' => 'Giảng viên', 'admin' => 'Admin'];
    $roleColors = ['student' => 'bg-blue-50 text-[#0056D2]', 'instructor' => 'bg-emerald-100 text-emerald-700', 'admin' => 'bg-rose-100 text-rose-700'];
    $courseStatusLabels = ['draft' => 'Nháp', 'pending' => 'Chờ duyệt', 'published' => 'Đã xuất bản', 'rejected' => 'Bị từ chối', 'archived' => 'Đã ẩn'];
    $enrollmentStatusLabels = ['active' => 'Đang học', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy', 'expired' => 'Hết hạn'];
    $orderStatusLabels = ['pending' => 'Chờ xử lý', 'paid' => 'Đã thanh toán', 'failed' => 'Thất bại', 'cancelled' => 'Đã hủy', 'refunded' => 'Đã hoàn tiền'];
    $statusLabel = $user->trashed() ? 'Đã xóa' : ($user->is_active ? 'Hoạt động' : 'Đã khóa');
    $statusClass = $user->trashed()
        ? 'bg-slate-200 text-slate-600'
        : ($user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700');
    $formatDate = fn ($value) => $value ? $value->format('d/m/Y H:i') : 'Chưa có';
    $formatMoney = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';
    $formatPrice = fn ($course) => (float) ($course->discount_price ?? $course->sale_price ?? $course->price ?? 0) <= 0
        ? 'Miễn phí'
        : number_format((float) ($course->discount_price ?? $course->sale_price ?? $course->price), 0, ',', '.').'đ';
@endphp

<div class="space-y-6">
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-6 p-5 xl:grid-cols-[minmax(0,1fr)_280px]">
            <div class="flex min-w-0 flex-col gap-5 sm:flex-row sm:items-start">
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-24 w-24 shrink-0 rounded-2xl border border-slate-200 object-cover">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">{{ $roleLabels[$user->role] ?? $user->role }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $isOnline ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-slate-100 text-slate-600' }}">{{ $isOnline ? 'Online' : 'Offline' }}</span>
                    </div>
                    <h2 class="mt-3 truncate text-2xl font-black text-slate-950">{{ $user->name }}</h2>
                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm font-semibold text-slate-500">
                        <span>{{ '@'.$user->username }}</span>
                        <span>{{ $user->email }}</span>
                        <span>{{ $user->phone ?: 'Chưa có số điện thoại' }}</span>
                    </div>
                    @if($user->bio)
                        <p class="mt-4 max-w-3xl whitespace-pre-line text-sm leading-6 text-slate-600">{{ $user->bio }}</p>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.users') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Quay lại danh sách</a>
                @if(! $user->trashed())
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                        @csrf
                        @method('PUT')
                        <label for="role" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Vai trò</label>
                        <select id="role" name="role" class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                            @foreach($roleLabels as $value => $label)
                                <option value="{{ $value }}" @selected($user->role === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="mt-2 inline-flex h-9 w-full items-center justify-center rounded-lg bg-slate-900 px-3 text-xs font-bold text-white">Cập nhật vai trò</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" onsubmit="return confirm('{{ $user->is_active ? 'Khóa tài khoản này?' : 'Mở khóa tài khoản này?' }}')">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="toggle_active" value="1">
                        <button class="inline-flex min-h-10 w-full items-center justify-center rounded-lg px-4 py-2 text-sm font-bold {{ $user->is_active ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Xóa người dùng này?')">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-700">Xóa người dùng</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">
                        @csrf
                        <button class="inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-700">Khôi phục tài khoản</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.force-delete', $user->id) }}" onsubmit="return confirm('Xóa vĩnh viễn người dùng này?')">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-red-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-800">Xóa vĩnh viễn</button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Khóa đang học', 'value' => $stats['active_enrollments'], 'hint' => $stats['enrollments'].' lượt ghi danh'],
            ['label' => 'Khóa đang dạy', 'value' => $stats['teaching_courses'], 'hint' => 'vai trò giảng viên'],
            ['label' => 'Đơn hàng', 'value' => $stats['orders'], 'hint' => $formatMoney($stats['paid_revenue'])],
            ['label' => 'Chứng chỉ', 'value' => $stats['certificates'], 'hint' => $stats['quiz_attempts'].' lượt làm quiz'],
        ] as $card)
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</span>
                <strong class="mt-2 block text-2xl font-black text-slate-950">{{ number_format((float) $card['value']) }}</strong>
                <p class="mt-1 text-xs font-semibold text-slate-500">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,.95fr)_minmax(0,1.05fr)]">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Thông tin tài khoản</h3>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-lg bg-slate-50 p-3">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">ID</dt>
                    <dd class="mt-1 font-semibold text-slate-900">#{{ $user->id }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Email xác thực</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $formatDate($user->email_verified_at) }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Ngày tạo</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $formatDate($user->created_at) }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Cập nhật</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $formatDate($user->updated_at) }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Đăng nhập cuối</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $formatDate($user->last_login_at) }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">IP cuối</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $user->last_login_ip ?: 'Chưa có' }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Đổi mật khẩu</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $formatDate($user->password_changed_at) }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">2FA</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $user->two_factor_enabled ? 'Đã bật' : 'Chưa bật' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Vai trò & quyền</h3>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">{{ $roleLabels[$user->role] ?? $user->role }}</span>
                @foreach($user->roles as $role)
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $role->name }}</span>
                @endforeach
            </div>
            <div class="mt-5 max-h-72 overflow-y-auto rounded-lg border border-slate-200">
                @php
                    $permissions = $user->roles->flatMap->permissions->unique('id')->sortBy('name')->values();
                @endphp
                @forelse($permissions as $permission)
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 last:border-b-0">
                        <span class="text-sm font-semibold text-slate-700">{{ $permission->name }}</span>
                        <span class="rounded-full bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-500">{{ $permission->slug }}</span>
                    </div>
                @empty
                    <div class="p-5 text-sm text-slate-500">Người dùng chưa có quyền chi tiết qua bảng vai trò.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-2">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-bold text-slate-950">Khóa đang học</h3>
                <span class="text-xs font-bold text-slate-500">{{ number_format($stats['enrollments']) }} ghi danh</span>
            </div>
            <div class="mt-4 divide-y divide-slate-100">
                @forelse($recentEnrollments as $enrollment)
                    @php $progress = (float) $enrollment->progress_percent; @endphp
                    <article class="py-4 first:pt-0 last:pb-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                @if($enrollment->course)
                                    <a href="{{ route('admin.courses.show', $enrollment->course) }}" class="font-bold text-slate-950 transition hover:text-rose-600">{{ $enrollment->course->title }}</a>
                                @else
                                    <div class="font-bold text-slate-950">Khóa học không còn tồn tại</div>
                                @endif
                                <p class="mt-1 text-xs font-semibold text-slate-500">Ghi danh: {{ $formatDate($enrollment->enrolled_at ?? $enrollment->created_at) }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#0056D2]">{{ $enrollmentStatusLabels[$enrollment->status] ?? $enrollment->status }}</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-[#0056D2]" style="width: {{ min($progress, 100) }}%"></div>
                        </div>
                        <p class="mt-1 text-xs font-bold text-slate-500">{{ number_format($progress, 1) }}% hoàn thành</p>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">Người dùng chưa ghi danh khóa học nào.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-bold text-slate-950">Khóa đang dạy</h3>
                <span class="text-xs font-bold text-slate-500">{{ number_format($stats['teaching_courses']) }} khóa</span>
            </div>
            <div class="mt-4 divide-y divide-slate-100">
                @forelse($recentTeachingCourses as $course)
                    <article class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div class="min-w-0">
                            <a href="{{ route('admin.courses.show', $course) }}" class="font-bold text-slate-950 transition hover:text-rose-600">{{ $course->title }}</a>
                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $formatPrice($course) }} · {{ number_format((int) $course->active_enrollments_count) }} học viên</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $courseStatusLabels[$course->status] ?? $course->status }}</span>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">Người dùng chưa sở hữu khóa học nào.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,.85fr)]">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Đơn hàng gần đây</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-slate-600">Mã đơn</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-600">Thanh toán</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-600">Trạng thái</th>
                            <th class="px-4 py-3 text-right font-bold text-slate-600">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $order->order_code ?: '#'.$order->id }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $order->payment_method ?: 'Chưa có' }}</td>
                                <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $orderStatusLabels[$order->status] ?? $order->status }}</span></td>
                                <td class="px-4 py-3 text-right font-bold text-slate-950">{{ $formatMoney($order->total_amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Chưa có đơn hàng.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Chứng chỉ</h3>
            <div class="mt-4 divide-y divide-slate-100">
                @forelse($recentCertificates as $certificate)
                    <article class="py-4 first:pt-0 last:pb-0">
                        <div class="font-bold text-slate-950">{{ $certificate->certificate_code }}</div>
                        <p class="mt-1 text-sm text-slate-500">{{ $certificate->course?->title ?? 'Khóa học không còn tồn tại' }}</p>
                        <p class="mt-1 text-xs font-semibold text-slate-500">Cấp ngày {{ $formatDate($certificate->issued_at) }}</p>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">Chưa có chứng chỉ.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-lg font-bold text-slate-950">Hoạt động gần đây</h3>
        <div class="mt-4 divide-y divide-slate-100">
            @forelse($recentActivityLogs as $log)
                <article class="flex flex-col gap-1 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="font-bold text-slate-950">{{ $log->action }}</div>
                        <p class="mt-1 text-sm text-slate-500">{{ $log->description ?: 'Không có mô tả chi tiết.' }}</p>
                        <p class="mt-1 text-xs font-semibold text-slate-500">IP: {{ $log->ip_address ?: 'Chưa có' }}</p>
                    </div>
                    <time class="shrink-0 text-xs font-bold text-slate-500">{{ $formatDate($log->created_at) }}</time>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">Chưa có log hoạt động.</div>
            @endforelse
        </div>
    </section>
</div>
</x-admin-layout>
