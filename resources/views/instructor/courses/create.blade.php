<x-instructor-layout title="Tạo khóa học" page-title="Tạo khóa học" page-title-class="text-lg sm:text-xl font-bold leading-tight text-slate-900 truncate" breadcrumb="Tạo nội dung chất lượng và chia sẻ kiến thức của bạn">

<div class="w-full min-w-0 xl:mx-auto xl:max-w-[1700px]">
    <div class="mb-5 flex justify-end">
        <a href="{{ route('instructor.courses.full-import.create') }}" class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 transition-colors hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Nhập toàn bộ khóa học từ Excel
        </a>
    </div>
    @include('instructor.courses._form', [
        'course' => null,
        'categories' => $categories,
        'action' => route('instructor.courses.store'),
        'method' => 'POST',
        'submitLabel' => 'Lưu nháp',
        'wideLayout' => true,
    ])
</div>

</x-instructor-layout>
