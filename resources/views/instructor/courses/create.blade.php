<x-instructor-layout title="Tạo khóa học" page-title="Tạo khóa học" page-title-class="text-lg sm:text-xl font-bold leading-tight text-slate-900 truncate" breadcrumb="Tạo nội dung chất lượng và chia sẻ kiến thức của bạn">

<div class="w-full min-w-0 xl:mx-auto xl:max-w-[1700px]">
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
