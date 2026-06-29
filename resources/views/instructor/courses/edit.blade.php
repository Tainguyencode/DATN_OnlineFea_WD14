<x-instructor-layout :title="$course->title" page-title="Chỉnh sửa khóa học" :breadcrumb="$course->title">

@php
    $statusMap = ['draft' => 'Nháp', 'pending' => 'Chờ duyệt', 'published' => 'Đã xuất bản', 'rejected' => 'Từ chối'];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        {{-- Edit form --}}
        <form method="POST" action="{{ route('instructor.courses.update', $course) }}" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
            @csrf @method('PUT')
            <h2 class="font-bold text-slate-900 text-lg">Thông tin khóa học</h2>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tên khóa học</label>
                <input type="text" name="title" value="{{ old('title', $course->title) }}" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Danh mục</label>
                    <select name="category_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($course->category_id == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Trình độ</label>
                    <select name="level" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-white">
                        <option value="beginner" @selected($course->level == 'beginner')>Cơ bản</option>
                        <option value="intermediate" @selected($course->level == 'intermediate')>Trung cấp</option>
                        <option value="advanced" @selected($course->level == 'advanced')>Nâng cao</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mô tả</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl resize-none">{{ old('description', $course->description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Giá</label>
                    <input type="number" name="price" value="{{ $course->price }}" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Giá KM</label>
                    <input type="number" name="sale_price" value="{{ $course->sale_price }}" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl">
                </div>
            </div>
            <button type="submit" class="bg-emerald-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-emerald-700 transition">Lưu thay đổi</button>
        </form>

        {{-- Chapters & Lessons --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 text-lg mb-4">Nội dung khóa học</h2>
            @foreach($course->chapters as $chapter)
                <div class="mb-4 border border-slate-200 rounded-xl overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 font-semibold text-slate-800">{{ $chapter->title }}</div>
                    <ul class="divide-y divide-slate-100">
                        @foreach($chapter->lessons as $lesson)
                            <li class="px-4 py-2 text-sm text-slate-600 flex justify-between">
                                <span>{{ $lesson->title }}</span>
                                <span class="text-xs text-slate-400">{{ $lesson->type }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <form method="POST" action="{{ route('instructor.chapters.lessons.store', $chapter) }}" class="p-4 border-t border-slate-100 flex gap-2 flex-wrap">
                        @csrf
                        <input type="text" name="title" placeholder="Tên bài giảng" required class="flex-1 min-w-[150px] px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        <select name="type" class="px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white">
                            <option value="video">Video</option>
                            <option value="document">Tài liệu</option>
                            <option value="quiz">Quiz</option>
                        </select>
                        <label class="flex items-center gap-1 text-sm text-slate-600"><input type="checkbox" name="is_preview" value="1"> Học thử</label>
                        <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm">+ Bài</button>
                    </form>
                </div>
            @endforeach

            <form method="POST" action="{{ route('instructor.courses.chapters.store', $course) }}" class="flex gap-2 mt-4">
                @csrf
                <input type="text" name="title" placeholder="Tên chương mới" required class="flex-1 px-4 py-2.5 border border-slate-300 rounded-xl text-sm">
                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium">+ Thêm chương</button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-slate-900 mb-3">Trạng thái</h3>
            <p class="text-2xl font-bold text-emerald-600 mb-4">{{ $statusMap[$course->status] ?? $course->status }}</p>
            @if($course->status === 'rejected' && $course->rejection_reason)
                <p class="text-sm text-red-600 bg-red-50 p-3 rounded-lg mb-4">{{ $course->rejection_reason }}</p>
            @endif
            @if(in_array($course->status, ['draft', 'rejected']))
                <form method="POST" action="{{ route('instructor.courses.submit', $course) }}">
                    @csrf
                    <button type="submit" class="w-full bg-amber-500 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-amber-600 transition">Gửi duyệt</button>
                </form>
            @endif
        </div>
        <a href="{{ route('instructor.courses.students', $course) }}" class="block bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-emerald-300 transition text-center">
            <div class="text-2xl font-bold text-slate-900">{{ $course->enrollments()->count() }}</div>
            <div class="text-sm text-slate-500">Học viên đăng ký</div>
        </a>
    </div>
</div>

</x-instructor-layout>
