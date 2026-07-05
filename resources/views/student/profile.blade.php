<x-student-layout title="Hồ sơ" page-title="Hồ sơ cá nhân">

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
        <div class="flex items-center gap-5 mb-8 pb-8 border-b border-slate-100">
            <div class="w-20 h-20 rounded-xl bg-[#0056D2] flex items-center justify-center text-white text-2xl font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-slate-500">{{ $user->email }}</p>
                <span class="inline-block mt-2 text-xs bg-blue-50 text-[#0056D2] px-2 py-1 rounded-full font-medium capitalize">{{ $user->role === 'student' ? 'Học viên' : $user->role }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Họ và tên</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#0056D2] outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#0056D2] outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Giới thiệu</label>
                <textarea name="bio" rows="4" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#0056D2] outline-none resize-none">{{ old('bio', $user->bio) }}</textarea>
            </div>
            <button type="submit" class="bg-[#0056D2] text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-[#0046B8] transition">Lưu thay đổi</button>
        </form>
    </div>
</div>

</x-student-layout>
