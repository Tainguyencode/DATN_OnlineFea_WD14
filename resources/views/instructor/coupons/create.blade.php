<x-instructor-layout title="Tạo mã giảm giá mới" pageTitle="Tạo mã giảm giá mới" breadcrumb="Tạo mã giảm giá">
    <div class="max-w-4xl">
        @include('instructor.coupons._form', [
            'action' => route('instructor.coupons.store'),
            'method' => 'POST',
            'submitLabel' => 'Tạo mã giảm giá',
        ])
    </div>
</x-instructor-layout>
