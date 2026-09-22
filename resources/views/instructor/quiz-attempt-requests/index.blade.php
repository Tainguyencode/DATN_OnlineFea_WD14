<x-instructor-layout title="Yêu cầu cấp lại lượt Quiz" pageTitle="Yêu cầu cấp lại lượt Quiz" breadcrumb="Giảng viên / Yêu cầu làm lại Quiz">
    <div class="space-y-6" x-data="{
        rejectModalOpen: false,
        activeRequestId: null,
        activeStudentName: '',
        activeRejectUrl: '',
        
        openRejectModal(id, name, url) {
            this.activeRequestId = id;
            this.activeStudentName = name;
            this.activeRejectUrl = url;
            this.rejectModalOpen = true;
        },
        closeRejectModal() {
            this.rejectModalOpen = false;
            this.activeRequestId = null;
            this.activeStudentName = '';
            this.activeRejectUrl = '';
        }
    }">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/30 dark:text-emerald-300 flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800 dark:border-rose-500/30 dark:bg-rose-950/30 dark:text-rose-300 flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Filter Header --}}
        <form method="GET" action="{{ route('instructor.quiz-attempt-requests.index') }}" class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Khóa học</label>
                <select name="course_id" onchange="this.form.submit()" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Tất cả khóa học</option>
                    @foreach($ownedCourses as $course)
                        <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Trạng thái</label>
                <select name="status" onchange="this.form.submit()" class="w-full cursor-pointer rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" @selected(request('status') === 'pending')>Chờ xử lý ({{ $pendingCount }})</option>
                    <option value="approved" @selected(request('status') === 'approved')>Đã duyệt</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Đã từ chối</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Tìm học viên</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email..." class="w-full rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full cursor-pointer rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700">
                    Lọc danh sách
                </button>
                @if(request()->hasAny(['course_id', 'status', 'search']))
                    <a href="{{ route('instructor.quiz-attempt-requests.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>

        {{-- Table Container --}}
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100 dark:bg-slate-950 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Học viên</th>
                            <th class="px-6 py-4">Khóa học / Bài Quiz</th>
                            <th class="px-6 py-4 text-center">Lượt làm</th>
                            <th class="px-6 py-4">Lý do xin cấp lại</th>
                            <th class="px-6 py-4">Thời gian gửi</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($requests as $req)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-700 dark:text-slate-200 shrink-0">
                                            {{ strtoupper(substr($req->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white">{{ $req->user?->name }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $req->user?->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 max-w-xs">
                                    <p class="font-bold text-slate-900 dark:text-white truncate">{{ $req->course?->title }}</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                        📝 {{ $req->lesson?->title ?? $req->quiz?->title ?? 'Bài kiểm tra' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 font-bold text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                        {{ $req->attempts_used }}/{{ $req->max_attempts }} lượt
                                    </span>
                                </td>

                                <td class="px-6 py-4 max-w-xs">
                                    <p class="text-slate-700 dark:text-slate-300 line-clamp-3 leading-relaxed">
                                        {{ $req->reason }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                    {{ $req->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 font-bold text-[11px] {{ $req->getStatusBadgeClass() }}">
                                        {{ $req->getStatusLabel() }}
                                        @if($req->isApproved() && $req->extra_attempts_granted > 0)
                                            (+{{ $req->extra_attempts_granted }} lượt)
                                        @endif
                                    </span>
                                    @if($req->isRejected() && $req->rejection_reason)
                                        <p class="text-[10px] text-rose-500 mt-1 max-w-[150px] truncate" title="{{ $req->rejection_reason }}">
                                            {{ $req->rejection_reason }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if($req->isPending())
                                        <div class="inline-flex items-center gap-2">
                                            <form method="POST" action="{{ route('instructor.quiz-attempt-requests.approve', $req) }}" onsubmit="return confirm('Xác nhận duyệt cấp thêm 1 lượt làm Quiz cho học viên {{ addslashes($req->user?->name) }}?')">
                                                @csrf
                                                <input type="hidden" name="extra_attempts" value="1">
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3 py-1.5 font-bold text-white shadow-sm hover:bg-emerald-700 transition cursor-pointer">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Duyệt (+1 lượt)
                                                </button>
                                            </form>

                                            <button type="button" @click="openRejectModal({{ $req->id }}, '{{ addslashes($req->user?->name) }}', '{{ route('instructor.quiz-attempt-requests.reject', $req) }}')" class="inline-flex items-center gap-1 rounded-xl border border-rose-300 bg-rose-50 px-3 py-1.5 font-bold text-rose-700 hover:bg-rose-100 transition cursor-pointer dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Từ chối
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400">
                                            Đã duyệt lúc {{ $req->reviewed_at?->format('d/m/Y H:i') ?? 'N/A' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Không có yêu cầu cấp lại lượt làm Quiz nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Từ chối yêu cầu --}}
        <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="rejectModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeRejectModal()"></div>

                <div x-show="rejectModalOpen" x-transition class="inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all dark:bg-slate-900 sm:my-8 sm:w-full sm:max-w-md sm:align-middle p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">
                        Từ chối yêu cầu cấp lại lượt Quiz
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Học viên: <strong class="text-slate-800 dark:text-slate-200" x-text="activeStudentName"></strong>
                    </p>

                    <form :action="activeRejectUrl" method="POST" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="rejection_reason" class="block text-xs font-semibold uppercase text-slate-500 mb-1">
                                Lý do từ chối (tùy chọn)
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3" class="w-full rounded-xl border-slate-300 text-sm placeholder-slate-400 focus:border-rose-500 focus:ring-rose-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Nhập lý do không chấp thuận để học viên được biết..."></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="closeRejectModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition cursor-pointer dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                Hủy bỏ
                            </button>
                            <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 transition cursor-pointer">
                                Xác nhận từ chối
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-instructor-layout>
