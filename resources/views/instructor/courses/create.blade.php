<x-instructor-layout title="Tạo khóa học" page-title="Tạo khóa học" breadcrumb="Lưu bản nháp đầu tiên trước khi xây dựng nội dung">

<div class="mx-auto max-w-5xl">
    @include('instructor.courses._form', [
        'course' => null,
        'categories' => $categories,
        'action' => route('instructor.courses.store'),
        'method' => 'POST',
        'submitLabel' => 'Lưu nháp',
    ])
</div>

</x-instructor-layout>
