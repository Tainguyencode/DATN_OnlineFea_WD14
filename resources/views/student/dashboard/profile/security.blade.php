<x-student-layout title="Bảo mật" page-title="Bảo mật tài khoản" breadcrumb="Quản lý email đăng nhập và mật khẩu ở một trang riêng.">
    <div class="grid gap-5 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3"><div><h2 class="text-lg font-extrabold">Email đăng nhập</h2><p class="mt-1 text-sm text-slate-500">Đổi email sẽ yêu cầu xác thực lại.</p></div><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $user->hasVerifiedEmail() ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $user->hasVerifiedEmail() ? 'Đã xác thực' : 'Chưa xác thực' }}</span></div>
            <form method="POST" action="{{ route('student.profile.email.update') }}" class="mt-5 space-y-4" x-data="{ submitting:false }" x-on:submit="submitting=true">
                @csrf @method('PUT')
                <div><label for="email" class="mb-1.5 block text-sm font-bold">Email mới</label><input id="email" type="email" name="email" required value="{{ old('email', $user->email) }}" class="min-h-11 w-full rounded-xl border px-3 outline-none focus:ring-2 focus:ring-blue-100 @error('email') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror dark:bg-slate-950">@error('email')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="email-current-password" class="mb-1.5 block text-sm font-bold">Mật khẩu hiện tại</label><input id="email-current-password" type="password" name="current_password" required autocomplete="current-password" class="min-h-11 w-full rounded-xl border px-3 outline-none focus:ring-2 focus:ring-blue-100 @error('current_password') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror dark:bg-slate-950">@error('current_password')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
                <button type="submit" :disabled="submitting" class="min-h-11 rounded-xl border border-blue-200 px-5 text-sm font-bold text-[#0056D2] hover:bg-blue-50 disabled:opacity-60 dark:border-blue-900 dark:text-blue-300">Cập nhật email</button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-extrabold">Đổi mật khẩu</h2><p class="mt-1 text-sm text-slate-500">Dùng ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
            <form method="POST" action="{{ route('student.profile.password.update') }}" class="mt-5 space-y-4" x-data="{ submitting:false }" x-on:submit="submitting=true">
                @csrf @method('PUT')
                <div><label for="current_password" class="mb-1.5 block text-sm font-bold">Mật khẩu hiện tại</label><input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="min-h-11 w-full rounded-xl border px-3 outline-none focus:ring-2 focus:ring-blue-100 @error('current_password') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror dark:bg-slate-950">@error('current_password')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="password" class="mb-1.5 block text-sm font-bold">Mật khẩu mới</label><input id="password" type="password" name="password" required autocomplete="new-password" class="min-h-11 w-full rounded-xl border px-3 outline-none focus:ring-2 focus:ring-blue-100 @error('password') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror dark:bg-slate-950">@error('password')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="password_confirmation" class="mb-1.5 block text-sm font-bold">Xác nhận mật khẩu mới</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="min-h-11 w-full rounded-xl border border-slate-300 px-3 outline-none focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950"></div>
                <button type="submit" :disabled="submitting" class="min-h-11 rounded-xl bg-slate-900 px-5 text-sm font-bold text-white hover:bg-slate-700 disabled:opacity-60 dark:bg-white dark:text-slate-950">Cập nhật mật khẩu</button>
            </form>
        </section>
    </div>
</x-student-layout>
