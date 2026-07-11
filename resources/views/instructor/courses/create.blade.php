<x-instructor-layout title="Tạo khóa học" page-title="Tạo khóa học mới" breadcrumb="Điền thông tin cơ bản">

<div class="max-w-3xl">
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <p class="font-semibold">Không thể tạo khóa học:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('instructor.courses.store') }}" class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm space-y-6">
        @csrf
        <input type="hidden" name="language" value="vi">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tên khóa học *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Danh mục *</label>
                <select name="category_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Trình độ *</label>
                <select name="level" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="beginner">Cơ bản</option>
                    <option value="intermediate">Trung cấp</option>
                    <option value="advanced">Nâng cao</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Mô tả *</label>
            <textarea name="description" rows="5" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none resize-none">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Mục tiêu khóa học</label>
            <textarea name="objectives" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none resize-none">{{ old('objectives') }}</textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Giá (VNĐ) *</label>
                <input type="number" name="price" value="{{ old('price', 0) }}" min="0" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Giá khuyến mãi</label>
                <input type="number" name="sale_price" value="{{ old('sale_price') }}" min="0" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
        </div>
        <button type="submit" class="bg-emerald-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-emerald-700 transition">Tạo khóa học</button>
    </form>
</div>

</x-instructor-layout>
