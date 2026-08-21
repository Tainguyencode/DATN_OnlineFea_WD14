<x-instructor-layout title="Hồ sơ & Chứng chỉ" page-title="Hồ sơ & Chứng chỉ Giảng viên" breadcrumb="Quản lý thông tin cá nhân, chuyên môn và tài liệu minh chứng">

<div class="space-y-8" x-data="{ activeTab: 'general', showUploadModal: false }">
    {{-- TRẠNG THÁI KHÓA HOẶC CẢNH BÁO DEADLINE / REVIEW BANNER                    --}}
    @if($user->isLocked())
        <div class="overflow-hidden rounded-2xl border-2 border-rose-500/40 bg-gradient-to-r from-rose-950/80 via-rose-900/60 to-slate-900 p-6 text-white shadow-xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-rose-600/30 text-rose-300 ring-2 ring-rose-500/50">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/20 px-3 py-1 text-xs font-black uppercase tracking-wider text-rose-300">
                            Tài khoản tạm khóa
                        </div>
                        <h2 class="mt-2 text-xl font-black tracking-tight text-white sm:text-2xl">Tài khoản giảng viên đang bị tạm khóa</h2>
                        <p class="mt-1 text-sm text-rose-200">
                            <span class="font-bold">Lý do:</span> {{ $user->locked_reason ?: 'Bạn chưa hoàn thiện hồ sơ chứng chỉ trong thời hạn 7 ngày.' }}
                        </p>
                        <p class="mt-1 text-xs text-rose-300/80">
                            Bạn vẫn có thể bổ sung hồ sơ chứng chỉ và gửi yêu cầu cấp lại quyền giảng viên sau thời gian quy định.
                        </p>
                    </div>
                </div>

                <div class="shrink-0">
                    @if($user->reactivation_status === 'pending')
                        <div class="rounded-xl border border-amber-500/30 bg-amber-500/20 px-5 py-3 text-center text-amber-200">
                            <span class="block text-xs uppercase tracking-wider font-bold">Trạng thái</span>
                            <span class="font-black text-sm">Đang chờ Admin xét duyệt đơn cấp lại</span>
                        </div>
                    @elseif($canRequestReactivation)
                        <button type="button" @click="$refs.reactivationModal.showModal()" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 transition hover:brightness-110">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Gửi yêu cầu cấp lại quyền</span>
                        </button>
                    @else
                        <div class="rounded-xl border border-white/10 bg-white/10 px-5 py-3 text-center">
                            <span class="block text-xs uppercase tracking-wider text-slate-400">Thời gian chờ cấp lại</span>
                            <span class="text-sm font-bold text-rose-200">Có thể gửi yêu cầu sau <span class="text-lg font-black text-white">{{ $cooldownDaysRemaining }}</span> ngày</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif($user->instructor_status === 'pending')
        @if($user->submitted_for_review_at)
            <div class="rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-5 text-blue-900 shadow-sm dark:border-blue-900/40 dark:bg-slate-900 dark:text-blue-100">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white">
                            <svg class="h-6 w-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold">Hồ sơ đang chờ Ban quản trị xét duyệt</h3>
                            <p class="text-xs text-blue-700 dark:text-blue-300">Hồ sơ đã nộp lúc {{ $user->submitted_for_review_at->format('d/m/Y H:i') }}. Bạn vẫn có thể tạo khóa học, chỉnh sửa nội dung và tải video bình thường.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-amber-300 bg-amber-50/90 p-5 text-amber-900 shadow-sm dark:border-amber-900/50 dark:bg-slate-900 dark:text-amber-100">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold">⚠️ Vui lòng bổ sung chứng chỉ và gửi xét duyệt hồ sơ</h3>
                            <p class="text-xs text-amber-800 dark:text-amber-300">
                                Bạn còn <span class="font-black text-amber-600 dark:text-amber-400">{{ $daysRemaining }} ngày</span> để hoàn thiện hồ sơ chứng chỉ trước khi bị khóa tạm thời.
                            </p>
                        </div>
                    </div>
                    @if($certificatesCount > 0)
                        <form method="POST" action="{{ route('instructor.profile.submit-review') }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-amber-700">
                                <span>Gửi hồ sơ xét duyệt ngay</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    @elseif($user->instructor_status === 'rejected')
        <div class="rounded-2xl border border-rose-300 bg-rose-50/90 p-5 text-rose-900 shadow-sm dark:border-rose-900/50 dark:bg-slate-900 dark:text-rose-100">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-bold text-rose-800 dark:text-rose-300">Hồ sơ giảng viên cần bổ sung / chỉnh sửa</h3>
                    <p class="mt-1 text-xs text-rose-700 dark:text-rose-400"><span class="font-bold">Ghi chú từ Admin:</span> {{ $user->rejected_reason ?: 'Vui lòng bổ sung đầy đủ văn bằng chứng chỉ hợp lệ.' }}</p>
                </div>
                <form method="POST" action="{{ route('instructor.profile.submit-review') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-rose-700">
                        <span>Gửi lại hồ sơ xét duyệt</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                </form>
            </div>
        </div>
    @elseif($user->instructor_status === 'approved')
        <div class="rounded-2xl border border-emerald-300 bg-emerald-50/90 p-4 text-emerald-900 shadow-sm dark:border-emerald-900/50 dark:bg-slate-900 dark:text-emerald-100">
            <div class="flex items-center gap-3">
                <svg class="h-6 w-6 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs sm:text-sm font-semibold">
                    ✅ Hồ sơ Giảng viên chính thức đã được phê duyệt! Bạn có thể tiếp tục cập nhật văn bằng chứng chỉ mới bất kỳ lúc nào.
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- PROFILE HEADER CARD                                                       --}}
    {{-- ========================================================================= --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 p-6 text-white shadow-xl sm:p-8">
        <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-5">
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-20 w-20 sm:h-24 sm:w-24 rounded-2xl border-2 border-white/20 object-cover shadow-lg">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-extrabold text-emerald-300 ring-1 ring-emerald-500/30">Giảng viên</span>
                        @if($user->isLocked())
                            <span class="inline-flex rounded-full bg-rose-500/20 px-3 py-1 text-xs font-extrabold text-rose-300">Tạm khóa</span>
                        @elseif($user->instructor_status === 'approved')
                            <span class="inline-flex rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-extrabold text-emerald-300">Đã duyệt</span>
                        @elseif($user->instructor_status === 'rejected')
                            <span class="inline-flex rounded-full bg-rose-500/20 px-3 py-1 text-xs font-extrabold text-rose-300">Cần sửa</span>
                        @else
                            <span class="inline-flex rounded-full bg-amber-500/20 px-3 py-1 text-xs font-extrabold text-amber-300">Chờ duyệt</span>
                        @endif
                    </div>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight">{{ $user->name }}</h1>
                    <p class="mt-1 text-xs sm:text-sm text-slate-400">{{ '@'.$user->username }} · {{ $user->email }} · {{ $profile->organization ?: 'Chưa cập nhật đơn vị' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="rounded-2xl bg-white/5 p-3 sm:p-4 backdrop-blur">
                    <div class="text-xl sm:text-2xl font-black text-emerald-400">{{ $certificatesCount }}</div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400 mt-0.5">Tài liệu</div>
                </div>
                <div class="rounded-2xl bg-white/5 p-3 sm:p-4 backdrop-blur">
                    <div class="text-xl sm:text-2xl font-black text-blue-400">{{ $user->courses()->count() }}</div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400 mt-0.5">Khóa học</div>
                </div>
                <div class="rounded-2xl bg-white/5 p-3 sm:p-4 backdrop-blur">
                    <div class="text-xl sm:text-2xl font-black text-amber-400">{{ $user->two_factor_enabled ? 'ON' : 'OFF' }}</div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400 mt-0.5">2FA</div>
                </div>
            </div>
        </div>
    </div>
    {{-- NAVIGATION TABS                                                           --}}
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3 dark:border-slate-800">
        <button
            type="button"
            @click="activeTab = 'general'"
            :class="activeTab === 'general' ? 'bg-[#0056D2] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'"
            class="rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition duration-150 cursor-pointer"
        >
            Thông tin cá nhân & Nghề nghiệp
        </button>
        <button
            type="button"
            @click="activeTab = 'documents'"
            :class="activeTab === 'documents' ? 'bg-[#0056D2] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'"
            class="relative rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition duration-150 cursor-pointer"
        >
            <span>Hồ sơ minh chứng & Chứng chỉ</span>
            <span class="ml-1.5 rounded-full bg-emerald-500/20 px-2 py-0.5 text-[10px] font-black text-emerald-600 dark:text-emerald-300">{{ $certificatesCount }}</span>
        </button>
        <button
            type="button"
            @click="activeTab = 'security'"
            :class="activeTab === 'security' ? 'bg-[#0056D2] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'"
            class="rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition duration-150 cursor-pointer"
        >
            Bảo mật & Phiên đăng nhập
        </button>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 1: THÔNG TIN CÁ NHÂN & NGHỀ NGHIỆP                                    --}}
    {{-- ========================================================================= --}}
    <div x-show="activeTab === 'general'" x-cloak class="space-y-6">
        <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- SECTION A: THÔNG TIN CÁ NHÂN --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">A. Thông tin cá nhân</h3>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Họ và tên *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('name') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Username *</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('username') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0912345678"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('phone') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Ảnh đại diện</label>
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp"
                            class="w-full rounded-xl border border-slate-300 bg-white p-2 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        @error('avatar') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Giới thiệu ngắn</label>
                    <textarea name="bio" rows="3" placeholder="Đôi nét về bản thân và phong cách giảng dạy..."
                        class="w-full rounded-xl border border-slate-300 bg-white p-3.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>

            {{-- SECTION B: THÔNG TIN NGHỀ NGHIỆP & CHUYÊN MÔN --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">B. Thông tin nghề nghiệp & Chuyên môn</h3>
                <div class="mt-6 grid gap-5 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Trường / Đơn vị công tác</label>
                        <input type="text" name="organization" value="{{ old('organization', $profile->organization) }}" placeholder="Đại học Bách Khoa, FPT Software..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Chức vụ / Vị trí</label>
                        <input type="text" name="position" value="{{ old('position', $profile->position) }}" placeholder="Giảng viên chính, Senior Engineer..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Ngành / Lĩnh vực giảng dạy</label>
                        <input type="text" name="teaching_field" value="{{ old('teaching_field', $profile->teaching_field) }}" placeholder="Công nghệ thông tin, Thiết kế đồ họa..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Lĩnh vực chuyên môn chi tiết</label>
                    <input type="text" name="specialty" value="{{ old('specialty', $profile->specialty) }}" placeholder="Fullstack Web, AI, Data Science, DevOps..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Kinh nghiệm giảng dạy & làm việc</label>
                    <textarea name="experience" rows="3" placeholder="Mô tả các dự án thực tế, số năm kinh nghiệm hoặc các cơ sở đào tạo đã từng tham gia..."
                        class="w-full rounded-xl border border-slate-300 bg-white p-3.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">{{ old('experience', $profile->experience) }}</textarea>
                </div>
            </div>

            {{-- SECTION C: THÔNG TIN TÀI KHOẢN NGÂN HÀNG --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">C. Tài khoản ngân hàng nhận tiền</h3>
                <p class="mt-1 text-xs text-slate-500">Thông tin nhận chuyển khoản đối soát doanh thu khóa học.</p>
                <div class="mt-6 grid gap-5 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Tên ngân hàng</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="MB Bank, Vietcombank..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Số tài khoản</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $user->bank_account_number) }}" placeholder="0123456789"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Chủ tài khoản (Không dấu)</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $user->bank_account_name) }}" placeholder="NGUYEN VAN A"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-[#0056D2] px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition hover:bg-[#0046B8]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Lưu thay đổi hồ sơ</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 2: HỒ SƠ MINH CHỨNG & ĐA CHỨNG CHỈ                                   --}}
    {{-- ========================================================================= --}}
    <div x-show="activeTab === 'documents'" x-cloak class="space-y-6">

        {{-- FORM TẢI LÊN TÀI LIỆU MỚI --}}
        <div class="rounded-3xl border border-violet-200 bg-gradient-to-br from-violet-50/50 via-white to-white p-6 sm:p-8 shadow-sm dark:border-violet-900/40 dark:bg-slate-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-violet-100 pb-5 dark:border-violet-900/30">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="h-6 w-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Tải lên Chứng chỉ & Hồ sơ minh chứng
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Bạn có thể tải lên nhiều tài liệu minh chứng (Chứng chỉ, Bằng cấp, Hợp đồng lao động, Bảng điểm...). Mỗi tài liệu được lưu trữ riêng biệt.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('instructor.profile.documents.upload') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Loại tài liệu minh chứng *</label>
                        <select name="document_type" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Tiêu đề tài liệu (Tùy chọn)</label>
                        <input type="text" name="title" placeholder="Ví dụ: Bằng Cử nhân CNTT, Chứng chỉ AWS Solutions Architect..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Chọn tệp (Hỗ trợ chọn nhiều tệp: PDF, JPG, PNG, WEBP, DOC, DOCX - Tối đa 10MB/tệp) *</label>
                    <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" required
                        class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-violet-600 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-violet-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-violet-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Tải lên tài liệu</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- DANH SÁCH TÀI LIỆU MINH CHỨNG ĐÃ TẢI LÊN --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-base font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Danh sách tài liệu đã nộp ({{ $certificatesCount }})</h3>

            @if($certificates->isEmpty())
                <div class="my-8 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="mt-3 text-sm font-bold text-slate-700 dark:text-slate-300">Chưa có tài liệu minh chứng nào</p>
                    <p class="mt-1 text-xs text-slate-500">Vui lòng tải lên chứng chỉ hoặc bằng cấp của bạn ở form trên.</p>
                </div>
            @else
                <div class="mt-6 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($certificates as $doc)
                        <div class="flex flex-col gap-4 py-4 sm:flex-row sm:items-center sm:justify-between transition hover:bg-slate-50/60 dark:hover:bg-slate-800/40 rounded-xl px-3">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $doc->isPdf() ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400' : 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400' }}">
                                    @if($doc->isPdf())
                                        <span class="text-xs font-black">PDF</span>
                                    @else
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $doc->title ?: $doc->original_name }}</h4>
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $doc->documentTypeLabel() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $doc->formattedFileSize() }} · Tải lên {{ $doc->uploaded_at ? $doc->uploaded_at->format('d/m/Y H:i') : 'N/A' }}
                                    </p>
                                    @if($doc->isRejected() && $doc->rejection_reason)
                                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">
                                            Lý do từ chối: {{ $doc->rejection_reason }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                @if($doc->isApproved())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Đã duyệt
                                    </span>
                                @elseif($doc->isRejected())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Từ chối
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">
                                        Chờ duyệt
                                    </span>
                                @endif

                                <a href="{{ route('instructor.profile.documents.view', $doc) }}" target="_blank" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                    Xem
                                </a>

                                @unless($doc->isApproved())
                                    <form method="POST" action="{{ route('instructor.profile.documents.delete', $doc) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài liệu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/40">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 3: BẢO MẬT & PHIÊN ĐĂNG NHẬP                                          --}}
    {{-- ========================================================================= --}}
    <div x-show="activeTab === 'security'" x-cloak class="space-y-6">

        {{-- Đổi email & Mật khẩu --}}
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Đổi địa chỉ Email</h3>
                <form method="POST" action="{{ route('profile.email.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Email mới *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('email') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Mật khẩu hiện tại để xác nhận *</label>
                        <input type="password" name="current_password" required placeholder="Nhập mật khẩu"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('current_password') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700">
                        Cập nhật Email
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Đổi Mật khẩu</h3>
                <form method="POST" action="{{ route('profile.password.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Mật khẩu hiện tại *</label>
                        <input type="password" name="current_password" required placeholder="Nhập mật khẩu cũ"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('current_password') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Mật khẩu mới *</label>
                        <input type="password" name="password" required placeholder="Tối thiểu 8 ký tự"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('password') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Xác nhận mật khẩu mới *</label>
                        <input type="password" name="password_confirmation" required placeholder="Nhập lại mật khẩu mới"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700">
                        Cập nhật Mật khẩu
                    </button>
                </form>
            </div>
        </div>

        {{-- Phiên đăng nhập & Thiết bị --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Phiên đăng nhập thiết bị</h3>
                    <p class="text-xs text-slate-500 mt-1">Danh sách thiết bị hiện đang đăng nhập vào tài khoản của bạn.</p>
                </div>
                <form method="POST" action="{{ route('profile.sessions.destroy-others') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100 dark:border-rose-900/40 dark:bg-rose-950/30 dark:text-rose-300">
                        Đăng xuất các thiết bị khác
                    </button>
                </form>
            </div>

            <div class="mt-6 space-y-3">
                @foreach($sessions as $s)
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 p-4 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-xs text-slate-900 dark:text-white">{{ $s->ip_address }}</p>
                                <p class="text-[11px] text-slate-500 truncate max-w-xs">{{ $s->user_agent }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-400">{{ \Carbon\Carbon::createFromTimestamp($s->last_activity)->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODAL GỬI YÊU CẦU CẤP LẠI QUYỀN (REACTIVATION MODAL) --}}
    <dialog id="reactivationModal" x-ref="reactivationModal" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xl backdrop:bg-black/60 dark:border-slate-800 dark:bg-slate-900 max-w-lg w-full">
        <form method="POST" action="{{ route('instructor.profile.request-reactivation') }}" class="space-y-4">
            @csrf
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Gửi yêu cầu cấp lại quyền Giảng viên</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Vui lòng cung cấp giải trình và lý do đề nghị mở khóa tài khoản để Ban quản trị xét duyệt.</p>
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Nội dung giải trình *</label>
                <textarea name="reason" rows="4" required placeholder="Giải trình lý do chưa hoàn thành hồ sơ đúng hạn hoặc các tài liệu đã bổ sung..."
                    class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" @click="$refs.reactivationModal.close()" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Hủy</button>
                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">Gửi yêu cầu</button>
            </div>
        </form>
    </dialog>

</div>

</x-instructor-layout>
