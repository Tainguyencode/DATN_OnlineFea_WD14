<x-instructor-layout title="Chỉnh sửa mã giảm giá" pageTitle="Chỉnh sửa mã giảm giá" breadcrumb="Chỉnh sửa mã">
    <div class="max-w-4xl">
        @include('instructor.coupons._form', [
            'action' => route('instructor.coupons.update', $coupon),
            'method' => 'PUT',
            'submitLabel' => 'Cập nhật mã giảm giá',
        ])
    </div>
</x-instructor-layout>
