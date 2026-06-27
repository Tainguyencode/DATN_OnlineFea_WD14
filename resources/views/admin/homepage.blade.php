<x-admin-layout title="Trang chủ" page-title="Cấu hình trang chủ">

@php
    $banner = $settings['banner'] ?? ['title' => '', 'subtitle' => ''];
@endphp

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.homepage.update') }}" class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm space-y-6">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tiêu đề banner</label>
            <input type="text" name="banner_title" value="{{ old('banner_title', $banner['title'] ?? '') }}" required
                   class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Phụ đề banner</label>
            <input type="text" name="banner_subtitle" value="{{ old('banner_subtitle', $banner['subtitle'] ?? '') }}" required
                   class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Thông báo (tùy chọn)</label>
            <input type="text" name="announcement" value="{{ old('announcement', $settings['announcement'] ?? '') }}"
                   class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none">
        </div>
        <button type="submit" class="bg-rose-600 text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-700 transition">Lưu cấu hình</button>
    </form>
</div>

</x-admin-layout>
