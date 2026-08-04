<x-admin-layout title="Chi tiết đơn giảng viên" page-title="Chi tiết đơn ứng tuyển giảng viên" breadcrumb="Quản lý giảng viên / Chi tiết">
    <div class="space-y-6" x-data="{ rejectModal: false }">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.instructors.applications.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-violet-600 dark:text-slate-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                Quay lại danh sách
            </a>

            <div class="flex items-center gap-3">
                @if($application->instructor_status !== 'approved')
                    <form method="POST" action="{{ route('admin.instructors.applications.approve', $application) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Bạn chắc chắn muốn duyệt giảng viên này?')" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-emerald-700">
                            ✔ Phê duyệt Giảng viên
                        </button>
                    </form>
                @endif

                @if($application->instructor_status !== 'rejected')
                    <button type="button" @click="rejectModal = true" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-rose-700">
                        ✖ Từ chối
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left column: User info summary --}}
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 text-center">
                    <img src="{{ $application->avatarUrl() }}" alt="{{ $application->name }}" class="mx-auto h-28 w-28 rounded-full object-cover border-4 border-violet-100 shadow-md dark:border-violet-900/40">
                    
                    <h2 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">{{ $application->name }}</h2>
                    <p class="text-xs text-slate-400">@ {{ $application->username }}</p>

                    <div class="mt-4 flex justify-center">
                        @if($application->instructor_status === 'approved')
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-1.5 text-xs font-extrabold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                Đã phê duyệt
                            </span>
                        @elseif($application->instructor_status === 'rejected')
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-4 py-1.5 text-xs font-extrabold text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                Đã từ chối
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-4 py-1.5 text-xs font-extrabold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                Chờ xét duyệt
                            </span>
                        @endif
                    </div>

                    <div class="mt-6 border-t border-slate-100 pt-6 dark:border-slate-800 space-y-3 text-left text-sm">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase">Email</span>
                            <div class="font-medium text-slate-800 dark:text-slate-200 flex items-center justify-between">
                                {{ $application->email }}
                                @if($application->hasVerifiedEmail())
                                    <span class="text-xs font-bold text-emerald-600">✔ Verified</span>
                                @else
                                    <span class="text-xs font-bold text-slate-400">Unverified</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase">Số điện thoại</span>
                            <div class="font-medium text-slate-800 dark:text-slate-200">
                                {{ $profile->phone ?? $application->phone ?? 'Chưa cung cấp' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase">Ngày đăng ký</span>
                            <div class="font-medium text-slate-800 dark:text-slate-200">
                                {{ $application->created_at->format('d/m/Y H:i:s') }}
                            </div>
                        </div>

                        @if($application->approved_at)
                            <div>
                                <span class="text-xs font-semibold text-slate-400 uppercase">Người duyệt / Ngày duyệt</span>
                                <div class="font-medium text-slate-800 dark:text-slate-200">
                                    {{ $application->approver->name ?? 'Admin' }} ({{ $application->approved_at->format('d/m/Y H:i') }})
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right column: Full Application Details --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 pb-3 dark:border-slate-800">
                        Hồ sơ ứng tuyển chi tiết
                    </h3>

                    @if($application->rejected_reason)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-300">
                            <strong>Lý do từ chối trước đó:</strong> {{ $application->rejected_reason }}
                        </div>
                    @endif

                    {{-- Specialty --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Lĩnh vực chuyên môn</h4>
                        <p class="mt-1 text-base font-semibold text-slate-900 dark:text-slate-100">
                            {{ $profile->specialty ?? 'Chưa có thông tin' }}
                        </p>
                    </div>

                    {{-- Experience --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Kinh nghiệm làm việc & giảng dạy</h4>
                        <div class="mt-1 whitespace-pre-line rounded-xl bg-slate-50 p-4 text-sm text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                            {{ $profile->experience ?? 'Chưa có thông tin' }}
                        </div>
                    </div>

                    {{-- Bio --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Giới thiệu bản thân</h4>
                        <div class="mt-1 whitespace-pre-line rounded-xl bg-slate-50 p-4 text-sm text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                            {{ $profile->bio ?? $application->bio ?? 'Chưa có thông tin' }}
                        </div>
                    </div>

                    {{-- Links --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Liên kết cá nhân & Mạng xã hội</h4>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-800">
                                <span class="text-xs text-slate-400">LinkedIn</span>
                                @if($profile && $profile->linkedin_url)
                                    <a href="{{ $profile->linkedin_url }}" target="_blank" class="block truncate text-sm font-semibold text-violet-600 hover:underline">
                                        {{ $profile->linkedin_url }}
                                    </a>
                                @else
                                    <p class="text-xs text-slate-500">Chưa cung cấp</p>
                                @endif
                            </div>

                            <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-800">
                                <span class="text-xs text-slate-400">GitHub</span>
                                @if($profile && $profile->github_url)
                                    <a href="{{ $profile->github_url }}" target="_blank" class="block truncate text-sm font-semibold text-violet-600 hover:underline">
                                        {{ $profile->github_url }}
                                    </a>
                                @else
                                    <p class="text-xs text-slate-500">Chưa cung cấp</p>
                                @endif
                            </div>

                            <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-800">
                                <span class="text-xs text-slate-400">Website</span>
                                @if($profile && $profile->website_url)
                                    <a href="{{ $profile->website_url }}" target="_blank" class="block truncate text-sm font-semibold text-violet-600 hover:underline">
                                        {{ $profile->website_url }}
                                    </a>
                                @else
                                    <p class="text-xs text-slate-500">Chưa cung cấp</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- CV File --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tài liệu CV</h4>
                        @if($profile && $profile->cv)
                            <div class="flex items-center justify-between rounded-xl border border-violet-200 bg-violet-50/50 p-4 dark:border-violet-900/40 dark:bg-violet-950/20">
                                <div class="flex items-center gap-3">
                                    <svg class="h-8 w-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 1H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <div>
                                        <h5 class="font-bold text-slate-900 dark:text-white">CV_Instructor_{{ $application->username }}.pdf</h5>
                                        <p class="text-xs text-slate-500">Định dạng PDF</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($profile->cv) }}" target="_blank" class="rounded-lg bg-violet-600 px-4 py-2 text-xs font-semibold text-white hover:bg-violet-700">
                                    Mở / Tải về CV
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-slate-500 italic">Ứng viên không tải lên tệp CV.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-900" x-data="{ errorMsg: '' }">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Từ chối ứng viên Giảng viên</h3>
                <p class="mt-1 text-xs text-slate-500">Ứng viên: <strong>{{ $application->name }}</strong></p>

                <form action="{{ route('admin.instructors.applications.reject', $application) }}" method="POST" class="mt-4 space-y-4" @submit="if(!$el.querySelector('textarea').value.trim()){ $event.preventDefault(); errorMsg = 'Vui lòng nhập lý do từ chối.'; }">
                    @csrf
                    <div>
                        <label for="rejected_reason_show" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Lý do từ chối *
                        </label>
                        <textarea id="rejected_reason_show" name="rejected_reason" rows="4" placeholder="Nhập chi tiết lý do từ chối để gửi cho ứng viên..."
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
