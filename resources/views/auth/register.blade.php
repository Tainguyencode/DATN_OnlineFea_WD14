@extends('layouts.app')

@section('title', 'Đăng ký - EduPlatform')

@section('content')
<div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Đăng ký tài khoản</h1>
            <p class="text-slate-500 mt-2">Tạo tài khoản miễn phí và bắt đầu học ngay hôm nay.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 mb-6">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Họ và tên</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm"
                           placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm"
                           placeholder="email@example.com">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-1.5">Bạn là</label>
                    <select id="role" name="role" required
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm bg-white">
                        <option value="student" @selected(old('role') == 'student')>Học viên</option>
                        <option value="instructor" @selected(old('role') == 'instructor')>Giảng viên</option>
                    </select>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm"
                           placeholder="Tối thiểu 8 ký tự">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Xác nhận mật khẩu</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm"
                           placeholder="Nhập lại mật khẩu">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition">
                    Đăng ký
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-200 text-center text-sm text-slate-500">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">Đăng nhập</a>
            </div>
        </div>
    </div>
</div>
@endsection
