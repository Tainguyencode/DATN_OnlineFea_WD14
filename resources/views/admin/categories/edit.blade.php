<x-admin-layout :title="$category->name" page-title="Chỉnh sửa danh mục khóa học" :breadcrumb="$category->full_name">

<div class="mx-auto max-w-5xl">
    @include('admin.categories._form', [
        'category' => $category,
        'parents' => $parents,
        'action' => route('admin.categories.update', $category),
        'method' => 'PUT',
        'submitLabel' => 'Lưu thay đổi',
    ])
</div>

</x-admin-layout>
