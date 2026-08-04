<x-admin-layout title="Quản lý Giảng viên" page-title="Quản lý giảng viên" breadcrumb="Danh sách ứng tuyển và duyệt giảng viên">
    <div class="space-y-6" x-data="{ rejectModal: false, rejectUrl: '', rejectName: '' }">
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-900/20 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800/40 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter tabs --}}
        <div class="flex items-center justify-between border-b border-slate-200 pb-3 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.instructors.applications.index', ['status' => 'all']) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $status === 'all' ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}">
                    Tất cả ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.instructors.applications.index', ['status' => 'pending']) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $status === 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}">
                    ⏳ Chờ duyệt ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('admin.instructors.applications.index', ['status' => 'approved']) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}">
                    ✔ Đã duyệt ({{ $counts['approved'] }})
                </a>
                <a href="{{ route('admin.instructors.applications.index', ['status' => 'rejected']) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $status === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}">
                    ✖ Từ chối ({{ $counts['rejected'] }})
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-bold">Giảng viên</th>
                            <th class="px-6 py-4 font-bold">Liên hệ</th>
                            <th class="px-6 py-4 font-bold">Chuyên môn & Kinh nghiệm</th>
                            <th class="px-6 py-4 font-bold">Ngày đăng ký</th>
                            <th class="px-6 py-4 font-bold">Email Verify</th>
                            <th class="px-6 py-4 font-bold">Trạng thái</th>
                            <th class="px-6 py-4 font-bold text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($applications as $app)
                            <tr class="transition hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                {{-- Avatar & Name --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $app->avatarUrl() }}" alt="{{ $app->name }}" class="h-10 w-10 rounded-full object-cover border border-slate-200">
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $app->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $app->username }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Contact --}}
                                <td class="px-6 py-4">
                                    <div class="text-xs space-y-0.5">
                                        <div class="font-medium text-slate-800 dark:text-slate-200">{{ $app->email }}</div>
                                        <div class="text-slate-500">{{ $app->instructorProfile->phone ?? $app->phone ?? 'Chưa cập nhật' }}</div>
                                    </div>
                                </td>

                                {{-- Specialty & Exp --}}
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <div class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $app->instructorProfile->specialty ?? 'N/A' }}</div>
                                        <div class="text-xs text-slate-500 line-clamp-1">{{ $app->instructorProfile->experience ?? 'Chưa khai báo' }}</div>
                                    </div>
                                </td>

                                {{-- Reg Date --}}
                                <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $app->created_at->format('d/m/Y H:i') }}
                                </td>

                                {{-- Email verified --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($app->hasVerifiedEmail())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                            ✔ Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                            Unverified
                                        </span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($app->instructor_status === 'approved')
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
                                           class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                            Xem
                                        </a>

                                        @if($app->instructor_status !== 'approved')
                                            <form method="POST" action="{{ route('admin.instructors.applications.approve', $app) }}" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Bạn chắc chắn muốn duyệt giảng viên này?')" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                                    Duyệt
                                                </button>
                                            </form>
                                        @endif

                                        @if($app->instructor_status !== 'rejected')
                                            <button type="button"
                                                    @click="rejectModal = true; rejectUrl = '{{ route('admin.instructors.applications.reject', $app) }}'; rejectName = '{{ $app->name }}'"
                                                    class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">
                                                Từ chối
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    Không có đơn đăng ký giảng viên nào.
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
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-900" x-data="{ errorMsg: '' }">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Từ chối ứng viên Giảng viên</h3>
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
                        <button type="button" @click="rejectModal = false; errorMsg = ''" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300">
                            Hủy
                        </button>
                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                            Xác nhận từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
