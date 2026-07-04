<x-admin-layout title="Analytics" page-title="Analytics" breadcrumb="This is an example dashboard created using built-in elements and components.">

<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-red-500">Overview</p>
                <h2 class="mt-1 text-lg font-extrabold text-slate-900">Portfolio Performance</h2>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-bold">
                <a href="{{ route('admin.users') }}" class="rounded-lg px-3 py-2 text-slate-500 transition hover:bg-slate-50 hover:text-red-600">Audiences</a>
                <a href="{{ route('admin.revenue') }}" class="rounded-lg px-3 py-2 text-slate-500 transition hover:bg-slate-50 hover:text-red-600">Demographics</a>
                <a href="{{ route('admin.activity-logs') }}" class="rounded-lg px-3 py-2 text-slate-500 transition hover:bg-slate-50 hover:text-red-600">More</a>
            </div>
        </div>

        <div class="grid gap-0 divide-y divide-slate-100 md:grid-cols-3 md:divide-x md:divide-y-0">
            <div class="flex items-center gap-4 p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v-1"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400">Cash Deposits</p>
                    <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</p>
                    <p class="mt-1 text-xs font-bold text-red-500">-54.1% less earnings</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400">Invested Dividends</p>
                    <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['users']) }}</p>
                    <p class="mt-1 text-xs font-bold text-emerald-500">Grow Rate +14.1%</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400">Capital Gains</p>
                    <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['courses']) }}</p>
                    <p class="mt-1 text-xs font-bold text-amber-500">Increased by +7.35%</p>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 px-5 py-4 text-center">
            <a href="{{ route('admin.revenue') }}" class="inline-flex rounded-full bg-red-500 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-600">View Complete Report</a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-500">Technical Support</p>
                    <h3 class="mt-1 font-extrabold text-slate-900">New Accounts Since 2018</h3>
                </div>
                <button class="rounded-lg p-2 text-slate-400 hover:bg-slate-50" aria-label="More">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4ZM10 12a2 2 0 110-4 2 2 0 010 4ZM10 18a2 2 0 110-4 2 2 0 010 4Z"/></svg>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4 flex items-end gap-3">
                    <div class="text-4xl font-extrabold text-slate-900">{{ number_format($stats['users']) }}</div>
                    <div class="pb-1 text-sm font-bold text-emerald-500">+14</div>
                </div>
                <div class="h-48 rounded-2xl bg-emerald-50 p-4">
                    <svg viewBox="0 0 520 180" class="h-full w-full">
                        <path d="M0 135 C30 80, 42 120, 60 92 S95 150, 122 104 S162 115, 184 70 S226 122, 256 86 S298 90, 318 48 S370 98, 410 82 S470 96, 520 76" fill="none" stroke="#10b981" stroke-width="7" stroke-linecap="round"/>
                        <path d="M0 150 H520" stroke="#d1fae5" stroke-width="2"/>
                    </svg>
                </div>
                <div class="mt-5">
                    <div class="mb-2 flex justify-between text-xs font-bold text-slate-500">
                        <span>Total Orders</span>
                        <span class="text-emerald-600">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</span>
                    </div>
                    <progress class="h-2 w-full overflow-hidden rounded-full [&::-moz-progress-bar]:bg-red-500 [&::-webkit-progress-bar]:bg-slate-100 [&::-webkit-progress-value]:bg-red-500" max="100" value="72"></progress>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-500">Timeline Example</p>
                    <h3 class="mt-1 font-extrabold text-slate-900">Latest Admin Activity</h3>
                </div>
                <button class="rounded-lg p-2 text-slate-400 hover:bg-slate-50" aria-label="More">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4ZM10 12a2 2 0 110-4 2 2 0 010 4ZM10 18a2 2 0 110-4 2 2 0 010 4Z"/></svg>
                </button>
            </div>
            <div class="max-h-[360px] space-y-4 overflow-y-auto p-5">
                @forelse($recentLogs as $log)
                    <div class="flex gap-3">
                        <div class="mt-1.5 h-3 w-3 rounded-full {{ $loop->iteration % 4 === 0 ? 'bg-sky-400' : ($loop->iteration % 3 === 0 ? 'bg-amber-400' : ($loop->iteration % 2 === 0 ? 'bg-emerald-400' : 'bg-rose-500')) }}"></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $log->action }}</p>
                                @if($loop->first)
                                    <span class="rounded bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white">NEW</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $log->user?->name ?? 'Hệ thống' }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="ui-empty">Chưa có hoạt động gần đây.</div>
                @endforelse
                <a href="{{ route('admin.activity-logs') }}" class="mx-auto mt-2 flex w-max rounded-full bg-slate-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-900">View All Messages</a>
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
            <div class="rounded-2xl border bg-white p-5 shadow-sm {{ $card['tone'] }}">
                <p class="text-xs font-semibold text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $card['value'] }}</p>
                <div class="mt-4 h-16 rounded-xl bg-slate-50">
                    <svg viewBox="0 0 180 60" class="h-full w-full">
                        <path d="M0 46 C28 12, 42 48, 65 28 S104 42, 123 18 S150 35, 180 20" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="font-extrabold text-slate-900">Khóa học chờ duyệt</h3>
            <a href="{{ route('admin.courses.pending') }}" class="text-xs font-bold text-red-500 hover:text-red-600">View all</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($pendingCourses as $course)
                <div class="flex flex-col gap-3 p-4 transition duration-200 hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $course->title }}</h4>
                        <p class="mt-1 text-xs text-slate-500">{{ $course->instructor?->name }} · {{ $course->category?->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                        @csrf
                        <button class="rounded-full bg-emerald-100 px-4 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-200">Approve</button>
                    </form>
                </div>
            @empty
                <div class="ui-empty m-5">Không có khóa học chờ duyệt.</div>
            @endforelse
        </div>
    </div>
</div>

</x-admin-layout>
