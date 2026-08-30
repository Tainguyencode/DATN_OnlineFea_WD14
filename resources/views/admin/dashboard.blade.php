<x-admin-layout title="Tổng quan hệ thống" page-title="Tổng quan quản trị" breadcrumb="Thống kê hiệu suất và chỉ số hoạt động của nền tảng OnlineFEA">

<div class="admin-dashboard min-w-0 space-y-6">

    {{-- KHU VỰC THẺ CHỈ SỐ KPI TỔNG QUAN (ADM-FE-03) --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Chỉ số hoạt động chính (KPIs)</h2>
            <span class="text-xs font-medium text-slate-400">Dữ liệu cập nhật thời gian thực</span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            {{-- Thẻ 1: Doanh thu --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tên chỉ số</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Tổng doanh thu</h3>
                    </div>
                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                        Toàn thời gian
                    </span>
                </div>

                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-950 dark:text-white">
                        {{ number_format($stats['revenue_total'], 0, ',', '.') }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">VNĐ</span>
                </div>

                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-3 flex items-center justify-between text-xs">
                    <span class="text-slate-500" title="Tính trên tất cả đơn hàng đã thanh toán thành công qua PayOS">
                        ℹ️ Tiêu chí: Đơn đã thanh toán
                    </span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                        Tháng này: {{ number_format($stats['revenue_month'], 0, ',', '.') }}đ
                    </span>
                </div>
            </div>

            {{-- Thẻ 2: Người dùng hệ thống --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tên chỉ số</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Người dùng hệ thống</h3>
                    </div>
                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-0.5 text-[11px] font-bold text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
                        Toàn thời gian
                    </span>
                </div>

                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-950 dark:text-white">
                        {{ number_format($stats['users_total']) }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">Tài khoản</span>
                </div>

                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Học viên: <strong class="text-slate-800 dark:text-slate-200">{{ number_format($stats['students_count']) }}</strong></span>
                    <span>Giảng viên: <strong class="text-slate-800 dark:text-slate-200">{{ number_format($stats['instructors_count']) }}</strong></span>
                </div>
            </div>

            {{-- Thẻ 3: Khóa học đã xuất bản --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tên chỉ số</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Khóa học xuất bản</h3>
                    </div>
                    <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">
                        Đang hoạt động
                    </span>
                </div>

                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-950 dark:text-white">
                        {{ number_format($stats['courses_published']) }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">Khóa học</span>
                </div>

                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Tiêu chí: Xuất bản công khai</span>
                    <a href="{{ route('admin.courses.index') }}" class="font-bold text-indigo-600 hover:underline dark:text-indigo-400">Xem danh sách →</a>
                </div>
            </div>

            {{-- Thẻ 4: Khóa học chờ duyệt --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900 {{ $stats['courses_pending'] > 0 ? 'border-amber-300 bg-amber-50/20 dark:border-amber-700/50' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tên chỉ số</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Chờ kiểm duyệt</h3>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full {{ $stats['courses_pending'] > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-slate-100 text-slate-600' }} px-2.5 py-0.5 text-[11px] font-bold">
                        @if($stats['courses_pending'] > 0)
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                        @endif
                        Cần xử lý
                    </span>
                </div>

                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold {{ $stats['courses_pending'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-950 dark:text-white' }}">
                        {{ number_format($stats['courses_pending']) }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">Yêu cầu</span>
                </div>

                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Tiêu chí: Khóa học mới/cập nhật</span>
                    <a href="{{ route('admin.courses.pending') }}" class="font-bold text-amber-600 hover:underline dark:text-amber-400">Kiểm duyệt ngay →</a>
                </div>
            </div>

            {{-- Thẻ 5: Lượt ghi danh học tập --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tên chỉ số</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Lượt ghi danh</h3>
                    </div>
                    <span class="inline-flex rounded-full bg-purple-50 px-2.5 py-0.5 text-[11px] font-bold text-purple-700 dark:bg-purple-950/40 dark:text-purple-300">
                        Toàn thời gian
                    </span>
                </div>

                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-950 dark:text-white">
                        {{ number_format($stats['enrollments_total']) }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">Lượt học</span>
                </div>

                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Tiêu chí: Học viên vào khóa học</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400">Tháng này: +{{ number_format($stats['enrollments_month']) }}</span>
                </div>
            </div>

            {{-- Thẻ 6: Đơn hàng thành công --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tên chỉ số</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Đơn hàng hoàn tất</h3>
                    </div>
                    <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-0.5 text-[11px] font-bold text-teal-700 dark:bg-teal-950/40 dark:text-teal-300">
                        Toàn thời gian
                    </span>
                </div>

                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-950 dark:text-white">
                        {{ number_format($stats['orders_paid_count']) }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">Đơn hàng</span>
                </div>

                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Tiêu chí: Giao dịch thanh toán PayOS</span>
                    <a href="{{ route('admin.revenue') }}" class="font-bold text-teal-600 hover:underline dark:text-teal-400">Báo cáo tài chính →</a>
                </div>
            </div>
        </div>
    </div>

    {{-- KHU VỰC CHI TIẾT: KHÓA HỌC CHỜ DUYỆT & HOẠT ĐỘNG GẦN ĐÂY --}}
    <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(340px,1fr)]">
        {{-- Khóa học chờ duyệt --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-extrabold text-slate-950 dark:text-white">Khóa học chờ duyệt gần đây</h3>
                    @if($stats['courses_pending'] > 0)
                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                            {{ $stats['courses_pending'] }}
                        </span>
                    @endif
                </div>
                <a href="{{ route('admin.courses.pending') }}" class="text-xs font-bold text-indigo-600 hover:underline dark:text-indigo-400">
                    Xem tất cả →
                </a>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($pendingCourses as $course)
                    <div class="flex flex-col gap-3 p-4 transition hover:bg-slate-50 dark:hover:bg-slate-800/40 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="min-w-0">
                            <h4 class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $course->title }}</h4>
                            <p class="mt-0.5 text-xs text-slate-500">
                                Giảng viên: <strong class="text-slate-700 dark:text-slate-300">{{ $course->instructor?->name ?? 'Chưa gán' }}</strong> · {{ $course->category?->name ?? 'Chưa chọn danh mục' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('admin.courses.review', $course) }}" class="inline-flex h-8 items-center rounded-xl bg-indigo-600 px-3 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700">
                                Kiểm duyệt
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        ✨ Hiện không có khóa học nào đang chờ duyệt.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Hoạt động hệ thống gần đây --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-base font-extrabold text-slate-950 dark:text-white">Nhật ký hoạt động</h3>
                <a href="{{ route('admin.activity-logs') }}" class="text-xs font-bold text-indigo-600 hover:underline dark:text-indigo-400">
                    Xem tất cả →
                </a>
            </div>

            <div class="max-h-[360px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 px-6 py-2">
                @forelse($recentLogs as $log)
                    <div class="flex gap-3 py-3 text-xs">
                        <div class="mt-1 h-2 w-2 shrink-0 rounded-full bg-indigo-500"></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-slate-800 dark:text-slate-200 line-clamp-1">
                                    {{ str_replace(['login', 'logout'], ['Đăng nhập', 'Đăng xuất'], $log->action) }}
                                </p>
                                @if($loop->first)
                                    <span class="rounded bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white">MỚI</span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-slate-400">{{ $log->user?->name ?? 'Hệ thống' }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400">
                        Chưa có nhật ký hoạt động nào.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-admin-layout>
