<x-student-layout title="Đơn hàng" page-title="Lịch sử đơn hàng" breadcrumb="Tra cứu duy nhất các giao dịch khóa học của bạn.">
    <div data-refresh-on-history>
    @include('student.dashboard.orders.partials.filter')

    @if($orders->isEmpty())
        <x-student.dashboard.empty-state class="mt-5" title="Không tìm thấy đơn hàng" description="Thử thay đổi từ khóa hoặc bộ lọc trạng thái." />
    @else
        <div class="mt-5 grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
            @foreach($orders as $order)
                @include('student.dashboard.orders.partials.order-card', ['order' => $order])
            @endforeach
        </div>
        <x-student.dashboard.pagination :paginator="$orders" />
    @endif
    </div>
</x-student-layout>
