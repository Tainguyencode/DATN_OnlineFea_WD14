<x-admin-layout title="Quản lý Giảng viên" page-title="Quản lý giảng viên" breadcrumb="Danh sách ứng tuyển và duyệt giảng viên">
    <div class="space-y-6" x-data="{ rejectModal: false, rejectUrl: '', rejectName: '' }">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-900/20 dark:text-emerald-300 shadow-sm">
                ✔ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800 dark:border-rose-800/40 dark:bg-rose-900/20 dark:text-rose-300 shadow-sm">
                ✖ {{ session('error') }}
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- SUMMARY STAT CARDS                                                        --}}
        {{-- ========================================================================= --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Card 1: Chờ duyệt --}}
            <a href="{{ route('admin.instructors.applications.index', ['status' => 'pending']) }}" class="group relative overflow-hidden rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5 transition duration-200 hover:shadow-lg hover:border-amber-400 dark:border-amber-900/40 dark:from-amber-950/40">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-amber-700 dark:text-amber-300">Chờ duyệt</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500/20 text-amber-600 dark:text-amber-300 ring-1 ring-amber-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black tracking-tight text-amber-900 dark:text-amber-100">{{ $counts['pending'] }}</div>
                    <p class="mt-1 text-xs text-amber-700/80 dark:text-amber-300/70">Hồ sơ cần Admin kiểm tra & xét duyệt</p>
                </div>
            </a>

            {{-- Card 2: Cập nhật mới --}}
            <a href="{{ route('admin.instructors.applications.index', ['status' => 'new_updates']) }}" class="group relative overflow-hidden rounded-3xl border border-rose-200 bg-gradient-to-br from-rose-500/10 via-rose-500/5 to-transparent p-5 transition duration-200 hover:shadow-lg hover:border-rose-400 dark:border-rose-900/40 dark:from-rose-950/40">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-rose-700 dark:text-rose-300 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-rose-500 animate-ping"></span>
                        Cập nhật mới
                    </span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500/20 text-rose-600 dark:text-rose-300 ring-1 ring-rose-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black tracking-tight text-rose-900 dark:text-rose-100">{{ $counts['new_updates'] }}</div>
                    <p class="mt-1 text-xs text-rose-700/80 dark:text-rose-300/70">Hồ sơ vừa có thay đổi chưa xem</p>
                </div>
            </a>

            {{-- Card 3: Đã duyệt --}}
            <a href="{{ route('admin.instructors.applications.index', ['status' => 'approved']) }}" class="group relative overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent p-5 transition duration-200 hover:shadow-lg hover:border-emerald-400 dark:border-emerald-900/40 dark:from-emerald-950/40">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Đã phê duyệt</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-300 ring-1 ring-emerald-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black tracking-tight text-emerald-900 dark:text-emerald-100">{{ $counts['approved'] }}</div>
                    <p class="mt-1 text-xs text-emerald-700/80 dark:text-emerald-300/70">Giảng viên chính thức đang hoạt động</p>
                </div>
            </a>

            {{-- Card 4: Từ chối --}}
            <a href="{{ route('admin.instructors.applications.index', ['status' => 'rejected']) }}" class="group relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-500/10 via-slate-500/5 to-transparent p-5 transition duration-200 hover:shadow-lg hover:border-slate-400 dark:border-slate-800 dark:from-slate-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-400">Từ chối</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-500/20 text-slate-600 dark:text-slate-300 ring-1 ring-slate-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ $counts['rejected'] }}</div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Hồ sơ cần bổ sung / chỉnh sửa</p>
                </div>
            </a>
        </div>

        {{-- ========================================================================= --}}
        {{-- FILTER TABS                                                               --}}
        {{-- ========================================================================= --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3 dark:border-slate-800">
            <a href="{{ route('admin.instructors.applications.index', ['status' => 'all']) }}"
               class="rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'all' ? 'bg-[#0056D2] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                Tất cả ({{ $counts['all'] }})
            </a>

            <a href="{{ route('admin.instructors.applications.index', ['status' => 'new_updates']) }}"
               class="relative rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'new_updates' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-rose-700 hover:bg-rose-50 dark:bg-slate-800 dark:text-rose-300 dark:hover:bg-slate-700' }}">
                <span> Cập nhật mới</span>
                @if($counts['new_updates'] > 0)
                    <span class="ml-1.5 rounded-full {{ $status === 'new_updates' ? 'bg-white text-rose-600' : 'bg-rose-600 text-white' }} px-2 py-0.5 text-[10px] font-black">
                        {{ $counts['new_updates'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.instructors.applications.index', ['status' => 'pending']) }}"
               class="relative rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                <span> Chờ duyệt</span>
                @if($counts['pending'] > 0)
                    <span class="ml-1.5 rounded-full {{ $status === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-500 text-white' }} px-2 py-0.5 text-[10px] font-black">
                        {{ $counts['pending'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.instructors.applications.index', ['status' => 'approved']) }}"
               class="rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                ✔ Đã duyệt ({{ $counts['approved'] }})
            </a>

            <a href="{{ route('admin.instructors.applications.index', ['status' => 'rejected']) }}"
               class="rounded-xl px-4 py-2 text-xs sm:text-sm font-bold transition duration-150 {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                ✖ Từ chối ({{ $counts['rejected'] }})
            </a>

            <div class="ml-auto">
                <a href="{{ route('admin.instructors.requirements.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs sm:text-sm font-bold text-[#0056D2] shadow-sm transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/60 dark:text-blue-300 dark:hover:bg-blue-900/60">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Cấu hình hồ sơ theo ngành</span>
                </a>
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
                            <th class="px-6 py-4 font-black">Giảng viên</th>
                            <th class="px-6 py-4 font-black">Liên hệ</th>
                            <th class="px-6 py-4 font-black">Chuyên môn</th>
                            <th class="px-6 py-4 font-black">Ngày đăng ký</th>
                            <th class="px-6 py-4 font-black">Cập nhật cuối</th>
                            <th class="px-6 py-4 font-black">Trạng thái</th>
                            <th class="px-6 py-4 font-black text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($applications as $app)
                            <tr class="transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 {{ $app->needs_admin_review ? 'bg-rose-50/30 dark:bg-rose-950/10' : '' }}">
                                {{-- Avatar & Name --}}
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
                                        </div>
                                    </div>
                                </td>

                                {{-- Contact --}}
                                <td class="px-6 py-4">
                                    <div class="text-xs space-y-0.5">
                                        <div class="font-medium text-slate-800 dark:text-slate-200">{{ $app->email }}</div>
                                        <div class="text-slate-500">{{ $app->instructorProfile?->phone ?? $app->phone ?? 'Chưa cập nhật' }}</div>
                                    </div>
                                </td>

                                {{-- Teaching Category --}}
                                <td class="px-6 py-4">
                                    @php
                                        $categories = $app->getTeachingCategories();
                                        $primaryCategory = $categories->firstWhere('pivot.is_primary', true) ?? $categories->first();
                                        $additionalCount = max(0, $categories->count() - 1);
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

                                {{-- Reg Date --}}
                                <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $app->created_at->format('d/m/Y H:i') }}
                                </td>

                                {{-- Last Updated --}}
                                <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $app->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $app->updated_at->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Status --}}
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

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.instructors.applications.show', $app) }}"
                                           class="rounded-xl bg-slate-100 px-3.5 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                            Xem
                                        </a>

                                        @if($app->instructor_status !== 'approved')
                                            <form method="POST" action="{{ route('admin.instructors.applications.approve', $app) }}" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Bạn chắc chắn muốn duyệt giảng viên này?')" class="rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700">
                                                    Duyệt
                                                </button>
                                            </form>
                                        @endif

                                        @if($app->instructor_status !== 'rejected')
                                            <button type="button"
                                                    @click="rejectModal = true; rejectUrl = '{{ route('admin.instructors.applications.reject', $app) }}'; rejectName = '{{ $app->name }}'"
                                                    class="rounded-xl bg-rose-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700">
                                                Từ chối
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
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
</x-admin-layout>
