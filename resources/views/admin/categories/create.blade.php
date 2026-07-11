<x-admin-layout title="Thêm danh mục khóa học" page-title="Thêm danh mục khóa học" breadcrumb="Quản lý danh mục">

<div class="mx-auto max-w-5xl">
    @include('admin.categories._form', [
        'category' => $category,
        'parents' => $parents,
        'action' => route('admin.categories.store'),
        'method' => 'POST',
        'submitLabel' => 'Tạo danh mục',
    ])
</div>

</x-admin-layout>
