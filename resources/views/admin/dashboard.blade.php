<x-admin-layout title="Analytics" page-title="Analytics" breadcrumb="This is an example dashboard created using built-in elements and components.">

<style>
    .admin-dashboard .admin-dashboard-scrollbar {
        scrollbar-color: #cbd5e1 transparent;
        scrollbar-width: thin;
    }

    .admin-dashboard .admin-dashboard-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .admin-dashboard .admin-dashboard-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .admin-dashboard .admin-dashboard-scrollbar::-webkit-scrollbar-thumb {
        border-radius: 9999px;
        background: #cbd5e1;
    }

    .admin-dashboard .admin-dashboard-progress {
        appearance: none;
        height: 0.5rem;
        overflow: hidden;
        width: 100%;
        border-radius: 9999px;
        background: #f1f5f9;
    }

    .admin-dashboard .admin-dashboard-progress::-webkit-progress-bar {
        border-radius: 9999px;
        background: #f1f5f9;
    }

    .admin-dashboard .admin-dashboard-progress::-webkit-progress-value {
        border-radius: 9999px;
        background: #f43f5e;
    }

    .admin-dashboard .admin-dashboard-progress::-moz-progress-bar {
        border-radius: 9999px;
        background: #f43f5e;
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-dashboard a,
        .admin-dashboard button {
            transition: none !important;
        }
    }
</style>

<div class="admin-dashboard min-w-0 space-y-5 sm:space-y-6">
    <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
        <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-rose-500">Overview</p>
                <h2 class="mt-1 text-lg font-bold tracking-tight text-slate-900 sm:text-xl">Portfolio Performance</h2>
            </div>
            <div class="flex flex-wrap gap-1.5 text-xs font-semibold">
                <a href="{{ route('admin.users') }}" class="cursor-pointer rounded-lg px-3 py-2 text-slate-500 transition-colors duration-200 hover:bg-slate-50 hover:text-rose-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/30">Audiences</a>
                <a href="{{ route('admin.revenue') }}" class="cursor-pointer rounded-lg px-3 py-2 text-slate-500 transition-colors duration-200 hover:bg-slate-50 hover:text-rose-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/30">Demographics</a>
                <a href="{{ route('admin.activity-logs') }}" class="cursor-pointer rounded-lg px-3 py-2 text-slate-500 transition-colors duration-200 hover:bg-slate-50 hover:text-rose-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/30">More</a>
            </div>
        </div>

        <div class="grid md:grid-cols-3">
            <div class="flex items-center gap-3.5 border-b border-slate-100 p-5 sm:p-6 md:border-b-0 md:border-r">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v-1"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-500">Cash Deposits</p>
                    <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</p>
                    <p class="mt-1 text-xs font-semibold text-rose-500">-54.1% less earnings</p>
                </div>
            </div>
            <div class="flex items-center gap-3.5 border-b border-slate-100 p-5 sm:p-6 md:border-b-0 md:border-r">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-500">Invested Dividends</p>
                    <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['users']) }}</p>
                    <p class="mt-1 text-xs font-semibold text-emerald-500">Grow Rate +14.1%</p>
                </div>
            </div>
            <div class="flex items-center gap-3.5 p-5 sm:p-6">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-500">Capital Gains</p>
                    <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['courses']) }}</p>
                    <p class="mt-1 text-xs font-semibold text-amber-500">Increased by +7.35%</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end border-t border-slate-100 px-5 py-4 sm:px-6">
            <a href="{{ route('admin.revenue') }}" class="inline-flex cursor-pointer items-center rounded-lg border border-rose-500 bg-rose-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition-colors duration-200 hover:border-rose-600 hover:bg-rose-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/30">View Complete Report</a>
        </div>
    </div>

    <div class="grid items-start gap-5 xl:grid-cols-[minmax(0,1.65fr)_minmax(320px,1fr)]">
        <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-500">Technical Support</p>
                    <h3 class="mt-1 truncate text-base font-bold tracking-tight text-slate-900 sm:text-lg">New Accounts Since 2018</h3>
                </div>
                <button class="cursor-pointer rounded-lg p-2 text-slate-400 transition-colors duration-200 hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/30" aria-label="More">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4ZM10 12a2 2 0 110-4 2 2 0 010 4ZM10 18a2 2 0 110-4 2 2 0 010 4Z"/></svg>
                </button>
            </div>
            <div class="p-5 sm:p-6">
                <div class="mb-4 flex items-end gap-3">
                    <div class="text-4xl font-extrabold text-slate-900">{{ number_format($stats['users']) }}</div>
                    <div class="pb-1 text-sm font-bold text-emerald-500">+14</div>
                </div>
                <div class="flex h-[220px] items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-4 sm:h-[280px]" role="img" aria-label="Không có dữ liệu lịch sử cho biểu đồ New Accounts Since 2018">
                    <div class="flex flex-col items-center gap-2 text-center">
                        <svg class="h-9 w-9 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 19.5V5.75A1.75 1.75 0 015.75 4H18.25A1.75 1.75 0 0120 5.75V18.25A1.75 1.75 0 0118.25 20H4.5a.5.5 0 01-.5-.5Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16l3-3 2 2 4-5"/>
                        </svg>
                        <p class="text-sm font-semibold text-slate-500">Không có dữ liệu</p>
                    </div>
                </div>
                <div class="mt-5">
                    <div class="mb-2 flex justify-between gap-4 text-xs font-semibold text-slate-500">
                        <span>Total Orders</span>
                        <span class="text-emerald-600">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</span>
                    </div>
                    <progress class="admin-dashboard-progress" max="100" value="72"></progress>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-rose-500">Timeline Example</p>
                    <h3 class="mt-1 truncate text-base font-bold tracking-tight text-slate-900 sm:text-lg">Latest Admin Activity</h3>
                </div>
                <button class="cursor-pointer rounded-lg p-2 text-slate-400 transition-colors duration-200 hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/30" aria-label="More">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4ZM10 12a2 2 0 110-4 2 2 0 010 4ZM10 18a2 2 0 110-4 2 2 0 010 4Z"/></svg>
                </button>
            </div>
            <div class="admin-dashboard-scrollbar max-h-[360px] overflow-y-auto px-5 py-2 sm:px-6">
                @forelse($recentLogs as $log)
                    <div class="flex gap-3 border-b border-slate-100 py-3.5 last:border-b-0">
                        <div class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full ring-4 ring-slate-50 {{ $loop->iteration % 4 === 0 ? 'bg-sky-400' : ($loop->iteration % 3 === 0 ? 'bg-amber-400' : ($loop->iteration % 2 === 0 ? 'bg-emerald-400' : 'bg-rose-500')) }}"></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $log->action }}</p>
                                @if($loop->first)
                                    <span class="rounded bg-rose-500 px-1.5 py-0.5 text-[10px] font-bold text-white">NEW</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $log->user?->name ?? 'Hệ thống' }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="ui-empty">Chưa có hoạt động gần đây.</div>
                @endforelse
                <a href="{{ route('admin.activity-logs') }}" class="mx-auto my-3 flex w-max cursor-pointer rounded-lg border border-slate-200 bg-slate-800 px-4 py-2 text-xs font-bold text-white transition-colors duration-200 hover:border-slate-900 hover:bg-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-500/30">View All Messages</a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Users this month', 'value' => $stats['users'], 'tone' => 'border-emerald-200 text-emerald-600'],
            ['label' => 'Active courses', 'value' => $stats['courses'], 'tone' => 'border-red-200 text-red-600'],
            ['label' => 'Pending approvals', 'value' => $stats['pending'], 'tone' => 'border-amber-200 text-amber-600'],
            ['label' => 'Revenue today', 'value' => number_format($stats['revenue'], 0, ',', '.') . 'đ', 'tone' => 'border-rose-200 text-rose-600'],
        ] as $card)
            <div class="flex min-h-[168px] flex-col rounded-xl border bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)] {{ $card['tone'] }}">
                <p class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $card['value'] }}</p>
                <div class="mt-auto pt-4" aria-hidden="true">
                    <div class="flex h-8 items-center gap-2 rounded-lg border border-dashed border-slate-200 bg-slate-50 px-3">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                        <span class="h-px w-10 bg-slate-200"></span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <h3 class="text-base font-bold tracking-tight text-slate-900 sm:text-lg">Khóa học chờ duyệt</h3>
            <a href="{{ route('admin.courses.pending') }}" class="cursor-pointer text-xs font-bold text-rose-500 transition-colors duration-200 hover:text-rose-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/30">View all</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($pendingCourses as $course)
                <div class="flex flex-col gap-3 p-4 transition-colors duration-200 hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="min-w-0">
                        <h4 class="truncate text-sm font-semibold text-slate-900">{{ $course->title }}</h4>
                        <p class="mt-1 text-xs text-slate-500">{{ $course->instructor?->name }} · {{ $course->category?->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.courses.approve', $course) }}" class="shrink-0">
                        @csrf
                        <button class="cursor-pointer rounded-lg bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 transition-colors duration-200 hover:bg-emerald-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/30">Approve</button>
                    </form>
                </div>
            @empty
                <div class="ui-empty m-5">Không có khóa học chờ duyệt.</div>
            @endforelse
        </div>
    </div>
</div>

</x-admin-layout>
