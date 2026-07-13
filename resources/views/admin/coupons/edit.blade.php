<x-admin-layout title="Chỉnh sửa mã giảm giá: {{ $coupon->code }}" page-title="Chỉnh sửa mã giảm giá" breadcrumb="Cập nhật thông tin mã giảm giá">

<div class="mx-auto max-w-5xl">
    @include('admin.coupons._form', [
        'coupon' => $coupon,
        'action' => route('admin.coupons.update', $coupon),
        'method' => 'PUT',
        'submitLabel' => 'Cập nhật',
    ])
</div>

</x-admin-layout>
