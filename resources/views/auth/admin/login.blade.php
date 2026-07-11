<x-auth.layout title="Admin Login - FEA Learning" subtitle="Đăng nhập hệ thống quản trị riêng biệt.">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-[#0F172A]">Admin Portal</h1>
        <p class="mt-2 text-xs text-[#6B7280]">Chỉ dành cho Admin / Super Admin</p>
    </div>

    <x-auth.errors :errors="$errors" />

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
        @csrf
        <x-auth.input label="Email hoặc số điện thoại" name="identifier" placeholder="admin@example.com" :value="old('identifier')" required autofocus />
        <x-auth.input label="Mật khẩu" name="password" type="password" required />

        <label class="inline-flex items-center gap-2 text-sm text-[#6B7280]">
            <input type="checkbox" name="remember" class="rounded border-[#E5E7EB] text-[#2563EB] focus:ring-[#2563EB]/20">
            Ghi nhớ đăng nhập
        </label>

        <button type="submit" class="auth-btn !bg-[#0F172A] hover:!bg-[#1E293B]">Đăng nhập quản trị</button>
    </form>

    <x-auth.footer-link text="Quay về trang người dùng?" :href="route('login')" link-text="User Login" />
</x-auth.layout>
