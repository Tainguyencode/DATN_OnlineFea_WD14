<x-admin-layout title="Thêm mã giảm giá" page-title="Thêm mã giảm giá" breadcrumb="Mã giảm giá">

<div class="mx-auto max-w-5xl">
    @include('admin.coupons._form', [
        'coupon' => $coupon,
        'action' => route('admin.coupons.store'),
        'method' => 'POST',
        'submitLabel' => 'Tạo mã giảm giá',
    ])
</div>

</x-admin-layout>
