<x-admin-layout title="Quản lý Giảng viên" page-title="Quản lý giảng viên" breadcrumb="Danh sách ứng tuyển và duyệt giảng viên">
    <div class="space-y-6" x-data="{ 
        rejectModal: false, 
        rejectUrl: '', 
        rejectName: '',
        selectedIds: [],
        applicationsCount: {{ $applications->count() }},
        toggleSelectAll() {
            if (this.selectedIds.length === this.applicationsCount) {
                this.selectedIds = [];
            } else {
                this.selectedIds = Array.from(document.querySelectorAll('.row-checkbox')).map(el => parseInt(el.value));
            }
        },
        isAllSelected() {
            return this.selectedIds.length > 0 && this.selectedIds.length === this.applicationsCount;
        },
        toggleSelect(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx > -1) {
                this.selectedIds.splice(idx, 1);
            } else {
                this.selectedIds.push(id);
            }
        },
        clearSelection() {
            this.selectedIds = [];
        }
    }">
        {{-- ========================================================================= --}}
        {{-- HEADER SECTION WITH CONFIG BUTTON                                         --}}
        {{-- ========================================================================= --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Thống kê & Quản lý ứng tuyển</h2>
            <div>
                <a href="{{ route('admin.instructors.requirements.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs sm:text-sm font-bold text-[#0056D2] shadow-sm transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/60 dark:text-blue-300 dark:hover:bg-blue-900/60">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Cấu hình hồ sơ theo ngành</span>
                </a>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- SUMMARY STAT TABLE                                                        --}}
        {{-- ========================================================================= --}}
        <div class="grid gap-5 xl:grid-cols-[minmax(0,1.55fr)_minmax(320px,.75fr)]">
            <section class="rounded-2xl border border-blue-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <h3 class="text-base font-black text-slate-950 dark:text-white">Biểu đồ tăng trưởng giảng viên</h3>
                        <p class="mt-1 text-xs text-slate-400">Dữ liệu đăng ký mới và phê duyệt theo 12 tháng của năm {{ $growthYear }}</p>
                    </div>
                    <div class="flex flex-wrap items-end gap-2">
                        <form method="GET" action="{{ route('admin.instructors.applications.index') }}">
                            @foreach(request()->except(['growth_year', 'page']) as $key => $value)
                                @if(is_scalar($value))
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <label class="block rounded-xl border border-blue-100 bg-white px-3 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                <span class="mb-1 block text-[10px] font-semibold text-slate-400">Xem theo năm</span>
                                <select name="growth_year" onchange="this.form.submit()" class="min-w-24 border-0 bg-transparent p-0 text-sm font-black text-blue-700 focus:ring-0 dark:text-blue-300">
                                    @foreach($growthYears as $year)
                                        <option value="{{ $year }}" @selected($growthYear === $year)>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </form>
                        <div class="rounded-xl bg-blue-50 px-3 py-2 dark:bg-blue-500/10">
                            <p class="text-[10px] font-semibold text-slate-400">Năm {{ $growthYear }}</p>
                            <p class="mt-0.5 text-sm font-black text-slate-900 dark:text-white">+{{ $yearRegistered }} giảng viên</p>
                        </div>
                        <div class="rounded-xl px-3 py-2 {{ $growthRate >= 0 ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-red-50 dark:bg-red-500/10' }}">
                            <p class="text-[10px] font-semibold text-slate-400">So với năm {{ $growthYear - 1 }}</p>
                            <p class="mt-0.5 text-sm font-black {{ $growthRate >= 0 ? 'text-emerald-600 dark:text-emerald-300' : 'text-red-600 dark:text-red-300' }}">{{ $growthRate >= 0 ? '↑' : '↓' }} {{ number_format(abs($growthRate), 1, ',', '.') }}%</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 h-[300px]">
                    <canvas id="instructorGrowthChart" role="img" aria-label="Biểu đồ tăng trưởng giảng viên theo tháng"></canvas>
                </div>
            </section>

            <aside class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                @php
                    $growthCards = [
                        ['all', 'Tổng giảng viên', $counts['all'], 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'],
                        ['pending', 'Đang chờ duyệt', $counts['pending'], 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300'],
                        ['new_updates', 'Có cập nhật mới', $counts['new_updates'], 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300'],
                        ['approved', 'Đã phê duyệt', $counts['approved'], 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'],
                        ['rejected', 'Đã từ chối', $counts['rejected'], 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'],
                    ];
                @endphp
                @foreach($growthCards as $card)
                    <a href="{{ $card[0] === 'all' ? route('admin.instructors.applications.index', request()->except('status')) : route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => $card[0]])) }}" class="group flex items-center justify-between rounded-2xl border border-blue-100 bg-white px-4 py-3.5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $card[3] }} text-sm font-black">{{ $card[2] }}</span>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $card[1] }}</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endforeach
            </aside>
        </div>

        <div class="hidden overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-black w-20 text-center">STT</th>
                            <th class="px-6 py-4 font-black w-56">Trạng thái</th>
                            <th class="px-6 py-4 font-black w-32 text-center">Số lượng</th>
                            <th class="px-6 py-4 font-black">Mô tả</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        {{-- 1. Tất cả giảng viên --}}
                        <tr onclick="window.location.href='{{ route('admin.instructors.applications.index', request()->except('status')) }}'" 
                            class="cursor-pointer transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <td class="px-6 py-4 text-center font-bold text-slate-400 dark:text-slate-500">1</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                    Tất cả giảng viên
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-base text-blue-600 dark:text-blue-400">
                                {{ $counts['all'] }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Tổng số lượng tài khoản giảng viên trong hệ thống.</span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </td>
                        </tr>
                        {{-- 2. Chờ duyệt --}}
                        <tr onclick="window.location.href='{{ route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => 'pending'])) }}'" 
                            class="cursor-pointer transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <td class="px-6 py-4 text-center font-bold text-slate-400 dark:text-slate-500">2</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    Chờ duyệt
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-base text-amber-600 dark:text-amber-400">
                                {{ $counts['pending'] }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Hồ sơ ứng tuyển mới đang chờ ban quản trị xem xét phê duyệt.</span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </td>
                        </tr>
                        {{-- 3. Cập nhật mới --}}
                        <tr onclick="window.location.href='{{ route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => 'new_updates'])) }}'" 
                            class="cursor-pointer transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <td class="px-6 py-4 text-center font-bold text-slate-400 dark:text-slate-500">3</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 border border-rose-200 px-3 py-1 text-xs font-black text-rose-700 dark:bg-rose-950/50 dark:border-rose-800 dark:text-rose-300">
                                    <span class="h-2 w-2 rounded-full bg-rose-500 animate-ping"></span>
                                    Cập nhật mới
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-base text-rose-600 dark:text-rose-400">
                                {{ $counts['new_updates'] }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Hồ sơ giảng viên đã được cập nhật thông tin hoặc chứng chỉ mới cần Admin xem xét.</span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </td>
                        </tr>
                        {{-- 4. Đã phê duyệt --}}
                        <tr onclick="window.location.href='{{ route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => 'approved'])) }}'" 
                            class="cursor-pointer transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <td class="px-6 py-4 text-center font-bold text-slate-400 dark:text-slate-500">4</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Đã phê duyệt
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-base text-emerald-600 dark:text-emerald-400">
                                {{ $counts['approved'] }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Giảng viên đã được phê duyệt và đang hoạt động.</span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </td>
                        </tr>
                        {{-- 5. Từ chối --}}
                        <tr onclick="window.location.href='{{ route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => 'rejected'])) }}'" 
                            class="cursor-pointer transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <td class="px-6 py-4 text-center font-bold text-slate-400 dark:text-slate-500">5</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-800 dark:bg-slate-900/40 dark:text-slate-300">
                                    Từ chối
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-base text-slate-600 dark:text-slate-400">
                                {{ $counts['rejected'] }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Hồ sơ ứng tuyển đã bị từ chối phê duyệt.</span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- FILTER BAR (ADVANCED FILTER)                                              --}}
        {{-- ========================================================================= --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form method="GET" action="{{ route('admin.instructors.applications.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 items-end">
                {{-- Ô tìm kiếm --}}
                <div class="space-y-1">
                    <label for="search" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Tìm theo tên, email, sđt..." class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                {{-- Ô trạng thái --}}
                <div class="space-y-1">
                    <label for="status" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Trạng thái</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="new_updates" {{ $status === 'new_updates' ? 'selected' : '' }}>Cập nhật mới</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Đã phê duyệt</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>

                {{-- Ô chuyên ngành --}}
                <div class="space-y-1">
                    <label for="category_id" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Chuyên ngành</label>
                    <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Tất cả chuyên ngành</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Ô ngày đăng ký --}}
                <div class="space-y-1">
                    <label for="date" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ngày đăng ký</label>
                    <input type="date" id="date" name="date" value="{{ $date }}" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                {{-- Nút hành động --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 rounded-xl bg-[#0056D2] py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">Tìm kiếm</button>
                    <a href="{{ route('admin.instructors.applications.index') }}" class="flex-1 text-center rounded-xl border border-slate-300 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 transition active:scale-95">Làm mới</a>
                </div>
            </form>
        </div>

        {{-- ========================================================================= --}}
        {{-- FILTER TABS                                                               --}}
        {{-- ========================================================================= --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3 dark:border-slate-800">
            <a href="{{ route('admin.instructors.applications.index', request()->except('status')) }}"
               class="rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'all' ? 'bg-[#0056D2] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                Tất cả ({{ $counts['all'] }})
            </a>

            <a href="{{ route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => 'new_updates'])) }}"
               class="relative rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'new_updates' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-rose-700 hover:bg-rose-50 dark:bg-slate-800 dark:text-rose-300 dark:hover:bg-slate-700' }}">
                <span> Cập nhật mới</span>
                @if($counts['new_updates'] > 0)
                    <span class="ml-1.5 rounded-full {{ $status === 'new_updates' ? 'bg-white text-rose-600' : 'bg-rose-600 text-white' }} px-2 py-0.5 text-[10px] font-black">
                        {{ $counts['new_updates'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => 'pending'])) }}"
               class="relative rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                <span> Chờ duyệt</span>
                @if($counts['pending'] > 0)
                    <span class="ml-1.5 rounded-full {{ $status === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-500 text-white' }} px-2 py-0.5 text-[10px] font-black">
                        {{ $counts['pending'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => 'approved'])) }}"
               class="rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                ✔ Đã duyệt ({{ $counts['approved'] }})
            </a>

            <a href="{{ route('admin.instructors.applications.index', array_merge(request()->query(), ['status' => 'rejected'])) }}"
               class="rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                ✖ Từ chối ({{ $counts['rejected'] }})
            </a>
        </div>

        {{-- ========================================================================= --}}
        {{-- BULK ACTIONS BAR (UI ONLY)                                                --}}
        {{-- ========================================================================= --}}
        <div x-show="selectedIds.length > 0" x-cloak class="flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-blue-50 p-4 border border-blue-100 dark:bg-blue-950/40 dark:border-blue-900/60 transition duration-200">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-[#0056D2] dark:text-blue-300">
                    Đã chọn <span x-text="selectedIds.length"></span> giảng viên
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" disabled class="opacity-50 cursor-not-allowed inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition">
                    Duyệt đã chọn
                </button>
                <button type="button" disabled class="opacity-50 cursor-not-allowed inline-flex items-center gap-1 rounded-xl bg-rose-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition">
                    Từ chối đã chọn
                </button>
                <button type="button" @click="clearSelection()" class="inline-flex items-center gap-1 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 px-3.5 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 transition">
                    Bỏ chọn
                </button>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- APPLICATIONS TABLE                                                        --}}
        {{-- ========================================================================= --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-4 w-12 text-center">
                                <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" :checked="isAllSelected()" @change="toggleSelectAll()">
                            </th>
                            <th class="px-4 py-4 w-16 text-center">STT</th>
                            <th class="px-6 py-4 font-black">Giảng viên</th>
                            <th class="px-6 py-4 font-black">Chuyên môn</th>
                            <th class="px-6 py-4 font-black">Minh chứng</th>
                            <th class="px-6 py-4 font-black">Kinh nghiệm giảng dạy</th>
                            <th class="px-6 py-4 font-black">Trạng thái</th>
                            <th class="px-6 py-4 font-black">Ngày đăng ký</th>
                            <th class="px-6 py-4 font-black text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($applications as $app)
                            <tr class="transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 {{ $app->needs_admin_review ? 'bg-rose-50/30 dark:bg-rose-950/10' : '' }}">
                                {{-- Checkbox --}}
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" value="{{ $app->id }}" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" :checked="selectedIds.includes({{ $app->id }})" @change="toggleSelect({{ $app->id }})">
                                </td>

                                {{-- STT --}}
                                <td class="px-4 py-4 text-center font-bold text-slate-400 dark:text-slate-500">
                                    {{ ($applications->currentPage() - 1) * $applications->perPage() + $loop->iteration }}
                                </td>

                                {{-- Giảng viên (Gộp basic info: Avatar, Name, Username, Email, Phone) --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $app->avatarUrl() }}" alt="{{ $app->name }}" class="h-11 w-11 rounded-2xl object-cover border-2 border-slate-200 dark:border-slate-700 shadow-sm">
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                                <span>{{ $app->name }}</span>
                                                @if($app->needs_admin_review)
                                                    <span class="inline-flex items-center rounded-full bg-rose-500 px-1.5 py-0.5 text-[9px] font-black text-white leading-none animate-pulse">NEW</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-400">{{ '@'.$app->username }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">{{ $app->email }}</div>
                                            <div class="text-xs text-slate-400">{{ $app->instructorProfile?->phone ?? $app->phone ?? 'Chưa cập nhật' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Chuyên môn (Teaching Category) --}}
                                <td class="px-6 py-4">
                                    @php
                                        $categoriesList = $app->getTeachingCategories();
                                        $primaryCategory = $categoriesList->firstWhere('pivot.is_primary', true) ?? $categoriesList->first();
                                        $additionalCount = max(0, $categoriesList->count() - 1);
                                    @endphp
                                    <div class="max-w-xs">
                                        @if($primaryCategory)
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-sm text-slate-900 dark:text-white">
                                                    {{ $primaryCategory->name }}
                                                </span>
                                                @if($additionalCount > 0)
                                                    <span class="inline-flex items-center rounded-md bg-blue-50 border border-blue-200 px-1.5 py-0.5 text-xs font-bold text-[#0056D2] dark:bg-blue-950/60 dark:border-blue-800 dark:text-blue-300" title="Có thêm {{ $additionalCount }} ngành phụ khác">
                                                        +{{ $additionalCount }} ngành khác
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">
                                                Chưa chọn ngành
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Minh chứng (Certificate Progress) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $progress = $app->certificate_progress ?? null;
                                        $reqCount = $progress['required_count'] ?? 0;
                                        $completedCount = $progress['completed_count'] ?? 0;
                                        $pct = $progress['percentage'] ?? null;
                                    @endphp
                                    @if($reqCount > 0)
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                                                <span>{{ $completedCount }}/{{ $reqCount }}</span>
                                                <span>{{ $pct !== null ? $pct.'%' : '0%' }}</span>
                                            </div>
                                            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                                <div class="h-full rounded-full transition-all duration-300 {{ ($pct ?? 0) >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $pct ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Không bắt buộc</span>
                                    @endif
                                </td>

                                {{-- Kinh nghiệm giảng dạy --}}
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ $app->instructorProfile?->experience ?? 'Chưa cập nhật' }}
                                </td>

                                {{-- Trạng thái --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($app->needs_admin_review)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 border border-rose-200 px-3 py-1 text-xs font-black text-rose-700 dark:bg-rose-950/50 dark:border-rose-800 dark:text-rose-300">
                                            <span class="h-2 w-2 rounded-full bg-rose-500 animate-ping"></span>
                                            Cập nhật mới
                                        </span>
                                    @elseif($app->isLocked())
                                        <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                            🔒 Bị khóa
                                        </span>
                                    @elseif($app->instructor_status === 'approved')
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                             Đã duyệt
                                        </span>
                                    @elseif($app->instructor_status === 'rejected')
                                        <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                             Từ chối
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                             Chờ duyệt
                                        </span>
                                    @endif
                                </td>

                                {{-- Ngày đăng ký --}}
                                <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $app->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $app->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Thao tác (icon + tooltip) --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Xem chi tiết --}}
                                        <div class="relative group">
                                            <a href="{{ route('admin.instructors.applications.show', $app) }}"
                                               class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block rounded bg-slate-900 px-2 py-1 text-[10px] font-bold text-white whitespace-nowrap shadow-md z-30">
                                                Xem chi tiết
                                            </span>
                                        </div>

                                        {{-- Duyệt --}}
                                        @if($app->instructor_status !== 'approved')
                                            <div class="relative group">
                                                <form method="POST" action="{{ route('admin.instructors.applications.approve', $app) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Bạn chắc chắn muốn duyệt giảng viên này?')" 
                                                            class="cursor-pointer flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 shadow-sm transition hover:bg-emerald-200 active:scale-95 dark:bg-emerald-950/60 dark:text-emerald-300 dark:hover:bg-emerald-900/60">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block rounded bg-slate-900 px-2 py-1 text-[10px] font-bold text-white whitespace-nowrap shadow-md z-30">
                                                    Duyệt
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Từ chối --}}
                                        @if($app->instructor_status !== 'rejected')
                                            <div class="relative group">
                                                <button type="button"
                                                        @click="rejectModal = true; rejectUrl = '{{ route('admin.instructors.applications.reject', $app) }}'; rejectName = '{{ $app->name }}'"
                                                        class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-100 text-rose-800 shadow-sm transition hover:bg-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:hover:bg-rose-900/60">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                                <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block rounded bg-slate-900 px-2 py-1 text-[10px] font-bold text-white whitespace-nowrap shadow-md z-30">
                                                    Từ chối
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    Không có hồ sơ giảng viên nào phù hợp với bộ lọc.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="border-t border-slate-200 p-4 dark:border-slate-800">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>

        {{-- Reject Reason Modal --}}
        <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900" x-data="{ errorMsg: '' }">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Từ chối ứng viên Giảng viên</h3>
                <p class="mt-1 text-xs text-slate-500">Ứng viên: <strong x-text="rejectName"></strong></p>

                <form :action="rejectUrl" method="POST" class="mt-4 space-y-4" @submit="if(!$el.querySelector('textarea').value.trim()){ $event.preventDefault(); errorMsg = 'Vui lòng nhập lý do từ chối.'; }">
                    @csrf
                    <div>
                        <label for="rejected_reason" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Lý do từ chối *
                        </label>
                        <textarea id="rejected_reason" name="rejected_reason" rows="4" placeholder="Nhập chi tiết lý do từ chối để gửi cho ứng viên..."
                                  @input="if($event.target.value.trim()) errorMsg = ''"
                                  :class="errorMsg ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-700'"
                                  class="mt-1 w-full rounded-xl border bg-slate-50 p-3 text-sm dark:bg-slate-800 dark:text-white"></textarea>
                        <p x-show="errorMsg" x-text="errorMsg" class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400"></p>
                        @error('rejected_reason')
                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="rejectModal = false; errorMsg = ''" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300">
                            Hủy
                        </button>
                        <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700">
                            Xác nhận từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.Chart) return;

        const dark = document.documentElement.classList.contains('dark');
        const text = dark ? '#cbd5e1' : '#64748b';
        const grid = dark ? 'rgba(148,163,184,.12)' : 'rgba(148,163,184,.18)';
        const growthData = @json($growthData);

        new Chart(document.getElementById('instructorGrowthChart'), {
            type: 'bar',
            data: {
                labels: growthData.map(item => item.label),
                datasets: [
                    {
                        type: 'bar',
                        label: 'Đăng ký mới',
                        data: growthData.map(item => item.registered),
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    },
                    {
                        type: 'bar',
                        label: 'Được phê duyệt',
                        data: growthData.map(item => item.approved),
                        backgroundColor: '#22c55e',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    },
                    {
                        type: 'line',
                        label: 'Tổng tích lũy',
                        data: growthData.map(item => item.cumulative),
                        borderColor: '#1e3a8a',
                        backgroundColor: '#1e3a8a',
                        borderWidth: 2,
                        pointRadius: 3,
                        tension: .35,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        labels: { color: text, usePointStyle: true, boxWidth: 9, font: { size: 10 } },
                    },
                    tooltip: {
                        callbacks: {
                            title: items => growthData[items[0].dataIndex]?.full_label ?? items[0].label,
                        },
                    },
                },
                scales: {
                    x: { ticks: { color: text }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { color: text, precision: 0 }, grid: { color: grid } },
                    y1: { beginAtZero: true, position: 'right', ticks: { color: text, precision: 0 }, grid: { display: false } },
                },
            },
        });
    });
    </script>
</x-admin-layout>
