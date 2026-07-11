<x-student-layout title="Hồ sơ" page-title="Hồ sơ cá nhân" breadcrumb="Quản lý thông tin và bảo mật tài khoản">

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-6 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:p-8">
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-2xl object-cover ring-4 ring-slate-100">
            <div>
                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#0056D2]">Học viên</span>
                <h2 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $user->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ '@'.$user->username }} · {{ $user->email }}</p>
                <p class="mt-2 text-xs text-slate-400">
                    Email {{ $user->hasVerifiedEmail() ? 'đã xác thực' : 'chưa xác thực' }}
                    @if($user->phone)
                        · {{ $user->phone }}
                    @endif
                </p>
            </div>
        </div>

        <div class="grid gap-6 p-6 lg:grid-cols-2 lg:p-8">
            <div class="rounded-2xl border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900">Thông tin cá nhân</h3>
                <p class="mt-1 text-sm text-slate-500">Cập nhật họ tên, tên đăng nhập, số điện thoại và ảnh đại diện.</p>

                <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Họ và tên</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2]">
                    </div>

                    <div>
                        <label for="username" class="mb-1.5 block text-sm font-semibold text-slate-700">Tên đăng nhập</label>
                        <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2]">
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-semibold text-slate-700">Số điện thoại</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2]">
                    </div>

                    <div>
                        <label for="avatar" class="mb-1.5 block text-sm font-semibold text-slate-700">Ảnh đại diện</label>
                        <input id="avatar" type="file" name="avatar" accept="image/png,image/jpeg,image/webp"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#0056D2] file:px-3 file:py-1.5 file:text-sm file:font-bold file:text-white">
                    </div>

                    <div>
                        <label for="bio" class="mb-1.5 block text-sm font-semibold text-slate-700">Giới thiệu</label>
                        <textarea id="bio" name="bio" rows="4"
                                  class="w-full resize-none rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2]">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <button type="submit" class="rounded-xl bg-[#0056D2] px-6 py-2.5 text-sm font-bold text-white transition hover:bg-[#0046B8]">
                        Lưu thay đổi
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900">Đổi mật khẩu</h3>
                <p class="mt-1 text-sm text-slate-500">Sử dụng mật khẩu mạnh gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>

                <form method="POST" action="{{ route('student.profile.password.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="mb-1.5 block text-sm font-semibold text-slate-700">Mật khẩu hiện tại</label>
                        <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2]">
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Mật khẩu mới</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2]">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Xác nhận mật khẩu mới</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2]">
                    </div>

                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700">
                        Cập nhật mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</x-student-layout>
