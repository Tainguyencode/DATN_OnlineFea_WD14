@php
    $layout = match ($user->role) {
        'admin' => 'admin-layout',
        'instructor' => 'instructor-layout',
        default => 'student-layout',
    };
    $roleLabels = ['admin' => 'Quản trị viên', 'instructor' => 'Giảng viên', 'student' => 'Học viên'];
    $profileUpdateRoute = match (true) {
        request()->routeIs('admin.*') => route('admin.profile.update'),
        request()->routeIs('instructor.*') => route('instructor.profile.update'),
        request()->routeIs('student.*') => route('student.profile.update'),
        default => route('profile.update'),
    };
@endphp

<x-dynamic-component :component="$layout" title="Hồ sơ" page-title="Hồ sơ cá nhân" breadcrumb="Bảo mật tài khoản và hoạt động đăng nhập">
    <div class="space-y-6">
        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Header card --}}
        <div class="relative overflow-hidden rounded-xl border border-slate-800 bg-slate-900 p-6 text-white shadow-md">
            <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-5">
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-xl border border-white/20 object-cover shadow-md">
                    <div>
                        <div class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-blue-100">{{ $roleLabels[$user->role] ?? $user->role }}</div>
                        <h2 class="mt-3 text-3xl font-extrabold tracking-tight">{{ $user->name }}</h2>
                        <p class="mt-1 text-sm text-slate-300">{{ '@'.$user->username }} · {{ $user->email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center sm:grid-cols-3">
                    <div class="rounded-xl bg-white/10 px-4 py-3">
                        <div class="text-lg font-black">{{ $user->email_verified_at ? 'OK' : '!' }}</div>
                        <div class="text-xs text-slate-300">Email</div>
                    </div>
                    <div class="rounded-xl bg-white/10 px-4 py-3">
                        <div class="text-lg font-black">{{ $user->two_factor_enabled ? 'ON' : 'OFF' }}</div>
                        <div class="text-xs text-slate-300">2FA</div>
                    </div>
                    <div class="rounded-xl bg-white/10 px-4 py-3">
                        <div class="text-lg font-black">{{ $sessions->count() }}</div>
                        <div class="text-xs text-slate-300">Thiết bị</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content: 2 columns on xl --}}
        <div class="grid gap-6 xl:grid-cols-[1.2fr_.8fr]">

            {{-- LEFT COLUMN --}}
            <div class="space-y-6 min-w-0">

                {{-- Thông tin cá nhân --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Thông tin cá nhân</h3>
                    <p class="mt-1 text-sm text-slate-500">Cập nhật avatar, username, số điện thoại và giới thiệu.</p>

                    <form method="POST" action="{{ $profileUpdateRoute }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Họ và tên</label>
                                <input name="name" value="{{ old('name', $user->name) }}" required
                                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Username</label>
                                <input name="username" value="{{ old('username', $user->username) }}" required
                                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Số điện thoại</label>
                                <input name="phone" value="{{ old('phone', $user->phone) }}"
                                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Avatar</label>
                                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp"
                                    class="block w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-bold file:text-[#0056D2]">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">Giới thiệu</label>
                            <textarea name="bio" rows="4"
                                class="block w-full resize-none rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                        <button class="rounded-lg bg-[#0056D2] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#0046B8]">Lưu thay đổi</button>
                    </form>
                </div>

                {{-- Đổi email & Đổi mật khẩu: mỗi cái 1 hàng riêng để không bị hẹp --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Đổi email</h3>
                    <form method="POST" action="{{ route('profile.email.update') }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">Email mới</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" required placeholder="Nhập mật khẩu để xác nhận"
                                class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">
                        </div>
                        <button class="rounded-2xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700">Cập nhật email</button>
                    </form>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Đổi mật khẩu</h3>
                    <form method="POST" action="{{ route('profile.password.update') }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" required placeholder="Nhập mật khẩu hiện tại"
                                class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Mật khẩu mới</label>
                                <input type="password" name="password" required placeholder="Mật khẩu mới"
                                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" required placeholder="Nhập lại mật khẩu mới"
                                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10">
                            </div>
                        </div>
                        <button class="rounded-2xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700">Cập nhật mật khẩu</button>
                    </form>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="space-y-6 min-w-0">

                {{-- Xác thực hai lớp --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Xác thực hai lớp</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $user->two_factor_enabled ? 'Đang bật bảo vệ 2FA.' : 'Nhận mã OTP qua email khi bật 2FA.' }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $user->two_factor_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $user->two_factor_enabled ? 'ON' : 'OFF' }}</span>
                    </div>

                    @if(! $user->two_factor_enabled)
                        <form method="POST" action="{{ route('profile.two-factor.send') }}" class="mt-5">
                            @csrf
                            <button class="w-full rounded-2xl bg-[#0056D2] px-5 py-3 text-sm font-bold text-white">Gửi mã bật 2FA</button>
                        </form>
                        @if(session('two_factor_pending'))
                            <form method="POST" action="{{ route('profile.two-factor.enable') }}" class="mt-4 flex gap-3">
                                @csrf
                                <input name="code" required maxlength="6" class="min-w-0 flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0056D2] focus:ring-4 focus:ring-blue-500/10" placeholder="Nhập mã 6 số">
                                <button class="shrink-0 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white">Xác nhận</button>
                            </form>
                        @endif
                    @else
                        <form method="POST" action="{{ route('profile.two-factor.disable') }}" class="mt-5 space-y-3">
                            @csrf
                            @method('DELETE')
                            <input type="password" name="current_password" required placeholder="Mật khẩu hiện tại"
                                class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-red-400 focus:ring-4 focus:ring-red-500/10">
                            <button class="w-full rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white">Tắt 2FA</button>
                        </form>
                    @endif
                </div>

                {{-- Thiết bị đăng nhập --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900">Thiết bị đăng nhập</h3>
                        <form method="POST" action="{{ route('profile.sessions.destroy-others') }}">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200">Đăng xuất thiết bị khác</button>
                        </form>
                    </div>
                    <div class="space-y-3">
                        @forelse($sessions as $session)
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $session->user_agent ?: 'Thiết bị không xác định' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $session->ip_address ?: 'No IP' }} · {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</p>
                                    </div>
                                    @if($session->id === session()->getId())
                                        <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-1 text-xs font-bold text-emerald-700">Hiện tại</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-slate-50 p-5 text-center text-sm text-slate-500">Chưa có session nào được ghi nhận.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Activity Timeline</h3>
                    <div class="mt-5 space-y-4">
                        @forelse($activityLogs as $log)
                            <div class="flex gap-3">
                                <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#0056D2]"></div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $log->action }}</p>
                                    <p class="text-xs text-slate-500">{{ $log->created_at->diffForHumans() }} · {{ $log->ip_address }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-slate-50 p-5 text-center text-sm text-slate-500">Chưa có hoạt động nào.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-dynamic-component>
