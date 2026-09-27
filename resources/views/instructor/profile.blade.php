<x-instructor-layout title="Hồ sơ & Chứng chỉ" page-title="Hồ sơ & Chứng chỉ Giảng viên" breadcrumb="Quản lý thông tin cá nhân, chuyên môn và tài liệu minh chứng">

<div class="space-y-8" x-data="{
    activeTab: '{{ session('active_tab', request()->query('tab')) }}' || (['general', 'documents', 'security'].includes(window.location.hash.replace('#', '')) ? window.location.hash.replace('#', '') : 'general'),
    showUploadModal: false,
    setTab(tab) {
        this.activeTab = tab;
        if (window.history.replaceState) {
            window.history.replaceState(null, null, '#' + tab);
        } else {
            window.location.hash = '#' + tab;
        }
    }
}">
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
                            <p class="text-xs text-blue-700 dark:text-blue-300">Hồ sơ đã nộp lúc {{ $user->submitted_for_review_at->format('d/m/Y H:i') }}. Hồ sơ, CV, ngành và tài liệu đang được khóa để bảo toàn gói xét duyệt. Bạn chưa thể tạo hoặc quản lý khóa học cho đến khi được phê duyệt.</p>
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
                            <p class="mt-0.5 text-xs text-amber-700 dark:text-amber-400">
                                Bạn hãy hoàn thành cập nhật chứng chỉ bắt buộc ngay để Admin trả lời bạn sớm nhất trong vòng 72h tới. Trong khi chờ Admin duyệt bạn vẫn có thể xử dụng các chức năng quản trị bình thường.
                            </p>
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        @if($submitEligibility['can_submit'])
                            <p class="mb-2 text-xs font-bold text-emerald-700 dark:text-emerald-300">Đã đủ hồ sơ để gửi xét duyệt</p>
                        @elseif($submitEligibility['missing_count'] > 0)
                            <p class="mb-2 text-xs font-bold text-amber-800 dark:text-amber-300">Còn thiếu {{ $submitEligibility['missing_count'] }} tài liệu bắt buộc</p>
                        @else
                            <p class="mb-2 text-xs font-bold text-amber-800 dark:text-amber-300">{{ $submitEligibility['reason'] }}</p>
                        @endif
                        <form method="POST" action="{{ route('instructor.profile.submit-review') }}" class="space-y-2">
                            @csrf
                            <label class="flex items-start justify-end gap-2 text-left cursor-pointer max-w-sm ml-auto">
                                <input type="checkbox" name="commitment_agreed" value="1" required class="mt-0.5 rounded border-amber-400 text-amber-600 focus:ring-amber-500">
                                <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                                    Tôi cam đoan mọi văn bằng, chứng chỉ và giấy tờ định danh cung cấp là thật và chính chủ, hoàn toàn chịu trách nhiệm trước pháp luật nếu có gian lận.
                                </span>
                            </label>
                            <button type="{{ $submitEligibility['can_submit'] ? 'submit' : 'button' }}" @disabled(! $submitEligibility['can_submit']) class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold shadow-md transition {{ $submitEligibility['can_submit'] ? 'bg-amber-600 text-white hover:bg-amber-700' : 'cursor-not-allowed bg-slate-300 text-slate-500 opacity-70 dark:bg-slate-700 dark:text-slate-400' }}">
                                <span>Gửi hồ sơ xét duyệt ngay</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    </div>
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
                <div class="shrink-0 text-right">
                    @if($submitEligibility['can_submit'])
                        <p class="mb-2 text-xs font-bold text-emerald-700 dark:text-emerald-300">Đã đủ hồ sơ để gửi xét duyệt</p>
                    @elseif($submitEligibility['missing_count'] > 0)
                        <p class="mb-2 text-xs font-bold text-rose-700 dark:text-rose-300">Còn thiếu {{ $submitEligibility['missing_count'] }} tài liệu bắt buộc</p>
                    @else
                        <p class="mb-2 text-xs font-bold text-rose-700 dark:text-rose-300">{{ $submitEligibility['reason'] }}</p>
                    @endif
                    <form method="POST" action="{{ route('instructor.profile.submit-review') }}" class="space-y-2">
                        @csrf
                        <label class="flex items-start justify-end gap-2 text-left cursor-pointer max-w-sm ml-auto">
                            <input type="checkbox" name="commitment_agreed" value="1" required class="mt-0.5 rounded border-rose-400 text-rose-600 focus:ring-rose-500">
                            <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                                Tôi cam đoan mọi văn bằng, chứng chỉ và giấy tờ định danh cung cấp là thật và chính chủ, hoàn toàn chịu trách nhiệm trước pháp luật nếu có gian lận.
                            </span>
                        </label>
                        <button type="{{ $submitEligibility['can_submit'] ? 'submit' : 'button' }}" @disabled(! $submitEligibility['can_submit']) class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold shadow-md transition {{ $submitEligibility['can_submit'] ? 'bg-rose-600 text-white hover:bg-rose-700' : 'cursor-not-allowed bg-slate-300 text-slate-500 opacity-70 dark:bg-slate-700 dark:text-slate-400' }}">
                            <span>Gửi lại hồ sơ xét duyệt</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </form>
                </div>
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
            @click="setTab('general')"
            :class="activeTab === 'general' ? 'bg-[#0056D2] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'"
            class="rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition duration-150 cursor-pointer"
        >
            Thông tin cá nhân & Nghề nghiệp
        </button>
        <button
            type="button"
            @click="setTab('documents')"
            :class="activeTab === 'documents' ? 'bg-[#0056D2] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'"
            class="relative rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition duration-150 cursor-pointer"
        >
            <span>Hồ sơ minh chứng & Chứng chỉ</span>
            <span class="ml-1.5 rounded-full bg-emerald-500/20 px-2 py-0.5 text-[10px] font-black text-emerald-600 dark:text-emerald-300">{{ $certificatesCount }}</span>
        </button>
        <button
            type="button"
            @click="setTab('security')"
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
        @php
            $profileValidationFailed = collect($errors->keys())->contains(fn ($key) => in_array($key, [
                'name', 'username', 'phone', 'avatar', 'cv', 'bio', 'bank_name', 'bank_account_number', 'bank_account_name',
                'category_ids', 'teaching_fields',
            ], true) || str_starts_with($key, 'category_ids.') || str_starts_with($key, 'teaching_fields.'));
        @endphp
        <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data" class="space-y-6"
            x-data="{
                baseline: '',
                isDirty: false,
                hasProfileValidationError: @js($profileValidationFailed),
                snapshot() {
                    return JSON.stringify(Array.from(new FormData(this.$el).entries())
                        .filter(([name]) => !['_token', '_method'].includes(name))
                        .map(([name, value]) => [name, value instanceof File ? [value.name, value.size, value.lastModified] : value]));
                },
                checkDirty() {
                    this.$nextTick(() => {
                        this.isDirty = this.hasProfileValidationError || this.snapshot() !== this.baseline;
                    });
                }
            }"
            x-init="$nextTick(() => { baseline = snapshot(); isDirty = hasProfileValidationError; })"
            @input="checkDirty()"
            @change="checkDirty()"
            @profile-form-fields-changed="checkDirty()">
            @csrf
            @method('PUT')

            @if($user->isGlobalReviewPending())
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm font-semibold text-blue-800 dark:border-blue-900 dark:bg-blue-950/30 dark:text-blue-200">
                    Hồ sơ đang được xét duyệt. Mọi thay đổi đối với thông tin hồ sơ, CV, ngành và tài liệu sẽ được mở lại sau khi Admin phản hồi.
                </div>
            @endif

            <fieldset @disabled($user->isGlobalReviewPending()) class="space-y-6 disabled:opacity-70">

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

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">CV xét duyệt (PDF) *</label>
                        <input type="file" name="cv" accept="application/pdf"
                            class="w-full rounded-xl border border-slate-300 bg-white p-2 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        @if($profile->cv)
                            <p class="mt-1 text-xs text-slate-500">Đã có CV: {{ basename($profile->cv) }}. Chọn tệp mới để thay thế.</p>
                        @else
                            <p class="mt-1 text-xs font-semibold text-amber-700">Bạn cần tải lên CV trước khi gửi hồ sơ.</p>
                        @endif
                        @error('cv') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Giới thiệu ngắn</label>
                    <textarea name="bio" rows="3" placeholder="Đôi nét về bản thân và phong cách giảng dạy..."
                        class="w-full rounded-xl border border-slate-300 bg-white p-3.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>

            {{-- SECTION B: THÔNG TIN NGHỀ NGHIỆP & CHUYÊN MÔN THEO TỪNG NGÀNH --}}
            <div x-data="{
                fields: @js(old('teaching_fields', !empty($teachingFields) ? $teachingFields : [[
                    'category_id' => $selectedCategoryIds[0] ?? ($categories->first()?->children->first()?->id ?? $categories->first()?->id ?? 1),
                    'organization' => $profile->organization ?? '',
                    'position' => $profile->position ?? '',
                    'specialty' => $profile->specialty ?? '',
                    'experience' => $profile->experience ?? '',
                ]])),
                pendingReplacementId: null,
                categories: @js($categories->map(function($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'children' => $c->children->map(fn($ch) => ['id' => $ch->id, 'name' => $ch->name])->values(),
                    ];
                })),
                getCategoryName(id) {
                    id = parseInt(id);
                    for (let cat of this.categories) {
                        if (cat.id === id) return cat.name;
                        if (cat.children) {
                            for (let ch of cat.children) {
                                if (ch.id === id) return ch.name;
                            }
                        }
                    }
                    return 'Chọn ngành giảng dạy';
                },
                getAvailableCategories() {
                    const selectedIds = this.fields.map(f => parseInt(f.category_id)).filter(id => !isNaN(id));
                    let list = [];
                    for (let cat of this.categories) {
                        if (cat.children && cat.children.length > 0) {
                            for (let ch of cat.children) {
                                list.push({ id: ch.id, name: ch.name + ' (' + cat.name + ')' });
                            }
                        } else {
                            list.push({ id: cat.id, name: cat.name });
                        }
                    }
                    return list;
                },
                addField() {
                    const selectedIds = this.fields.map(f => parseInt(f.category_id)).filter(id => !isNaN(id));
                    const all = this.getAvailableCategories();
                    const available = all.find(c => !selectedIds.includes(c.id));
                    this.fields.push({
                        category_id: available ? available.id : (all[0]?.id || ''),
                        replace_of_teaching_field_id: this.pendingReplacementId,
                        organization: '',
                        position: '',
                        specialty: '',
                        experience: ''
                    });
                    this.pendingReplacementId = null;
                },
                removeField(index) {
                    if (this.fields.length > 1) {
                        const removed = this.fields[index];
                        if (removed?.teaching_field_id && ['approved', 'draft', 'rejected'].includes(removed?.approval_status || 'draft')) {
                            this.pendingReplacementId = removed.teaching_field_id;
                        }
                        this.fields.splice(index, 1);
                    }
                }
            }" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#0056D2] text-[11px] font-black text-white">B</span>
                            <span>Thông tin nghề nghiệp & Chuyên môn theo từng ngành</span>
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">Mỗi ngành giảng dạy có thể khai báo đơn vị công tác, chức vụ và kinh nghiệm riêng biệt.</p>
                    </div>

                    <button type="button" @click="addField(); $dispatch('profile-form-fields-changed')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-4 py-2 text-xs font-bold text-[#0056D2] transition hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 dark:hover:bg-blue-900/60 border border-blue-200 dark:border-blue-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Thêm ngành giảng dạy</span>
                    </button>
                </div>

                @error('category_ids')
                    <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-600 dark:border-rose-900/40 dark:bg-rose-950/30 dark:text-rose-400">
                        {{ $message }}
                    </div>
                @enderror

                {{-- DANH SÁCH KHỐI THÔNG TIN TỪNG NGÀNH --}}
                <div class="space-y-6">
                    <template x-for="(field, index) in fields" :key="field.teaching_field_id || index">
                        <div class="relative rounded-2xl border-2 border-slate-200/80 bg-slate-50/60 p-5 sm:p-6 transition hover:border-blue-300 dark:border-slate-800 dark:bg-slate-800/40 space-y-5">

                            {{-- Header của từng Khối Ngành --}}
                            <div class="flex items-center justify-between border-b border-slate-200/70 pb-3 dark:border-slate-700/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-[#0056D2] text-xs font-black text-white shadow-sm" x-text="index + 1"></span>
                                    <span class="text-sm font-black text-slate-900 dark:text-white" x-text="getCategoryName(field.category_id)"></span>
                                    <template x-if="field.approval_status === 'approved'"><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">✅ Đã duyệt</span></template>
                                    <template x-if="!field.approval_status || field.approval_status === 'draft'"><span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-black text-slate-700">📝 Chưa gửi</span></template>
                                    <template x-if="field.approval_status === 'pending'"><span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">⏳ Chờ duyệt</span></template>
                                    <template x-if="field.approval_status === 'rejected'"><span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">❌ Bị từ chối</span></template>
                                    <template x-if="field.approval_status === 'superseded'"><span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-black text-slate-700">⛔ Đã thay thế</span></template>
                                </div>

                                <template x-if="fields.length > 1">
                                    <button type="button" x-show="!['pending', 'superseded'].includes(field.approval_status)" @click="removeField(index); $dispatch('profile-form-fields-changed')"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold text-rose-600 transition hover:bg-rose-100 dark:text-rose-400 dark:hover:bg-rose-950/50">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Xóa ngành này</span>
                                    </button>
                                </template>
                            </div>

                            {{-- Hàng 1: Ngành + Đơn vị + Chức vụ --}}
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <input type="hidden" :name="'teaching_fields[' + index + '][teaching_field_id]'" x-model="field.teaching_field_id">
                                    <input type="hidden" :name="'teaching_fields[' + index + '][replace_of_teaching_field_id]'" x-model="field.replace_of_teaching_field_id">
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                        Ngành / Lĩnh vực giảng dạy *
                                    </label>
                                    <select :name="'teaching_fields[' + index + '][category_id]'"
                                            x-model="field.category_id"
                                            :disabled="['pending', 'superseded'].includes(field.approval_status)"
                                            required
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                        <option value="" disabled>-- Chọn ngành giảng dạy --</option>
                                        <template x-for="cat in categories" :key="'grp-' + cat.id">
                                            <template x-if="cat.children && cat.children.length > 0">
                                                <optgroup :label="cat.name">
                                                    <template x-for="child in cat.children" :key="'opt-' + child.id">
                                                        <option :value="child.id" x-text="child.name" :selected="child.id == field.category_id"></option>
                                                    </template>
                                                </optgroup>
                                            </template>
                                            <template x-if="!cat.children || cat.children.length === 0">
                                                <option :value="cat.id" x-text="cat.name" :selected="cat.id == field.category_id"></option>
                                            </template>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                        Trường / Đơn vị công tác
                                    </label>
                                    <input type="text"
                                           :name="'teaching_fields[' + index + '][organization]'"
                                           x-model="field.organization"
                                           :disabled="['pending', 'superseded'].includes(field.approval_status)"
                                           placeholder="VD: Đại học Bách Khoa, FPT Software..."
                                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                        Chức vụ / Vị trí
                                    </label>
                                    <input type="text"
                                           :name="'teaching_fields[' + index + '][position]'"
                                           x-model="field.position"
                                           :disabled="['pending', 'superseded'].includes(field.approval_status)"
                                           placeholder="VD: Giảng viên chính, Senior Engineer..."
                                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                </div>
                            </div>

                            {{-- Hàng 2: Lĩnh vực chuyên môn chi tiết --}}
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    Lĩnh vực chuyên môn chi tiết của ngành này
                                </label>
                                <input type="text"
                                       :name="'teaching_fields[' + index + '][specialty]'"
                                       x-model="field.specialty"
                                       :disabled="['pending', 'superseded'].includes(field.approval_status)"
                                       placeholder="VD: Fullstack Web, React, Laravel, Node.js, Cloud..."
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            </div>

                            {{-- Hàng 3: Kinh nghiệm giảng dạy & làm việc --}}
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    Kinh nghiệm giảng dạy & làm việc trong ngành này
                                </label>
                                <textarea :name="'teaching_fields[' + index + '][experience]'"
                                          x-model="field.experience"
                                          :disabled="['pending', 'superseded'].includes(field.approval_status)"
                                          rows="3"
                                          placeholder="Mô tả các dự án thực tế, số năm kinh nghiệm hoặc các cơ sở đào tạo đã từng tham gia..."
                                          class="w-full rounded-xl border border-slate-300 bg-white p-3.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Nút Thêm Ngành ở cuối --}}
                <div class="pt-2">
                    <button type="button" @click="addField(); $dispatch('profile-form-fields-changed')"
                            class="w-full rounded-2xl border-2 border-dashed border-slate-300 p-4 text-center text-xs font-bold text-slate-600 transition hover:border-[#0056D2] hover:bg-blue-50/50 hover:text-[#0056D2] dark:border-slate-700 dark:text-slate-400 dark:hover:border-blue-500 dark:hover:bg-slate-800/60 dark:hover:text-blue-300 flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Thêm một ngành / lĩnh vực giảng dạy khác</span>
                    </button>
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

            <div class="flex justify-end" x-show="isDirty" x-cloak>
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-[#0056D2] px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition hover:bg-[#0046B8]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Lưu thay đổi hồ sơ</span>
                </button>
            </div>
            </fieldset>
        </form>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 2: HỒ SƠ MINH CHỨNG & CHỨNG CHỈ THEO NGÀNH                           --}}
    {{-- ========================================================================= --}}
    <script>
    function instructorDocumentsManager() {
        return {
            uploadModal: false,
            editUrlModal: false,
            activeRequirementId: null,
            activeTeachingFieldId: null,
            activeRequirementTitle: '',
            activeDocType: 'certificate',
            submissionSource: 'file',
            degreeOption: 'lookup',
            certOption: 'international',
            domesticOption: 'lookup',
            domesticProofType: 'capstone_project',
            experienceProofType: 'contract',
            supplementaryProofType: 'notarized',
            editingDocumentId: null,
            editingDocumentUrl: '',
            uploadError: '',
            urlError: '',
            formError: '',
            uploading: false,
            isDegreeType() {
                if (this.activeDocType === 'degree') return true;
                if (this.activeDocType === 'certificate') return false;
                const title = (this.activeRequirementTitle || '').toLowerCase();
                return title.includes('bằng') || title.includes('đại học') || title.includes('cao đẳng');
            },
            isCertType() {
                if (this.activeDocType === 'certificate') return true;
                if (this.activeDocType === 'degree') return false;
                const title = (this.activeRequirementTitle || '').toLowerCase();
                return title.includes('chứng chỉ');
            },
            isExpType() {
                const title = (this.activeRequirementTitle || '').toLowerCase();
                return this.activeDocType === 'employment_confirmation' || this.activeDocType === 'employment_contract' || title.includes('kinh nghiệm') || title.includes('công tác');
            },
            validateEvidenceFiles(event) {
                this.uploadError = '';
                const files = event.target.files;
                if (!files || files.length === 0) {
                    this.uploadError = 'Vui lòng chọn ít nhất một tệp tài liệu chính (bản scan / ảnh gốc).';
                    return;
                }
                const oversizedFile = Array.from(files).find(file => file.size > 50 * 1024 * 1024);

                if (oversizedFile) {
                    this.uploadError = 'Tệp "' + oversizedFile.name + '" vượt quá giới hạn 50MB.';
                    event.target.value = '';
                }
            },
            handleUploadSubmit(event) {
                this.uploadError = '';
                this.urlError = '';
                this.formError = '';
                const form = event.target;

                // 1. Kiểm tra tài liệu chính
                if (this.submissionSource === 'file') {
                    const fileInput = form.querySelector('input[name="files[]"]');
                    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                        this.uploadError = 'Vui lòng chọn tệp tài liệu chính (bản scan / ảnh gốc).';
                        fileInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                    const oversizedFile = Array.from(fileInput.files).find(file => file.size > 50 * 1024 * 1024);
                    if (oversizedFile) {
                        this.uploadError = 'Tệp "' + oversizedFile.name + '" vượt quá giới hạn 50MB.';
                        fileInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                } else if (this.submissionSource === 'url') {
                    const urlInput = form.querySelector('input[name="document_url"]');
                    const val = urlInput ? urlInput.value.trim() : '';
                    if (!val) {
                        this.urlError = 'Vui lòng nhập đường link tài liệu chính.';
                        urlInput?.focus();
                        return;
                    }
                    if (!/^https?:\/\//i.test(val)) {
                        this.urlError = 'Đường link tài liệu phải bắt đầu bằng http:// hoặc https://';
                        urlInput?.focus();
                        return;
                    }
                }

                // 2. Kiểm tra xác thực chống làm giả cho Bằng ĐH/CĐ
                if (this.isDegreeType()) {
                    if (this.degreeOption === 'lookup') {
                        const diplomaNum = form.querySelector('input[name="diploma_number"]')?.value.trim();
                        const bookRegNum = form.querySelector('input[name="book_reg_number"]')?.value.trim();
                        if (!diplomaNum || !bookRegNum) {
                            this.formError = 'Ở Cách 1, bạn chỉ cần nhập "Số hiệu văn bằng" và "Số vào sổ cấp bằng" (Nhập đường link tra cứu của trường nếu có). Nếu không muốn nhập 2 số này, bạn có thể bấm chuyển sang "Cách 2: Đính kèm giấy tờ" ở nút bên cạnh.';
                            return;
                        }
                    } else if (this.degreeOption === 'supplementary') {
                        const suppFileInput = form.querySelector('div[x-show="degreeOption === \'supplementary\'"] input[name="supplementary_file"]') || form.querySelector('input[name="supplementary_file"]');
                        if (!suppFileInput || !suppFileInput.files || suppFileInput.files.length === 0) {
                            this.formError = 'Bạn đang ở "Cách 2: Đính kèm giấy tờ": Vui lòng bấm chọn tệp minh chứng phụ (Bản công chứng / Bảng điểm / App trường). Hoặc bấm chuyển về "Cách 1: Tra cứu trực tuyến" nếu bạn muốn nhập số hiệu tra cứu.';
                            return;
                        }
                    }
                }

                // 3. Kiểm tra xác thực chống làm giả cho Chứng chỉ chuyên môn
                if (this.isCertType()) {
                    if (this.certOption === 'international') {
                        const credUrl = form.querySelector('input[name="credential_url"]')?.value.trim();
                        if (!credUrl) {
                            this.formError = 'Bạn đang ở "Chứng chỉ Quốc tế": Vui lòng nhập Đường link xác thực công khai (Credential URL từ Credly, Coursera, AWS...). Nếu là chứng chỉ trung tâm đào tạo trong nước, hãy bấm chọn nút "Chứng chỉ Trong nước" ở ngay bên cạnh.';
                            return;
                        }
                    } else if (this.certOption === 'domestic') {
                        if (this.domesticOption === 'lookup') {
                            const certCode = form.querySelector('input[name="center_cert_code"]')?.value.trim();
                            const centerUrl = form.querySelector('input[name="center_lookup_url"]')?.value.trim();
                            if (!certCode && !centerUrl) {
                                this.formError = 'Chứng chỉ trong nước (Cách 1): Vui lòng nhập Mã số chứng chỉ hoặc Link tra cứu web trung tâm, HOẶC bấm chuyển sang "Cách 2: Sản phẩm / Minh chứng" để cung cấp link Github / Bảng điểm tốt nghiệp.';
                                return;
                            }
                        } else if (this.domesticOption === 'proof') {
                            if (this.domesticProofType === 'capstone_project') {
                                const capstoneUrl = form.querySelector('input[name="capstone_project_url"]')?.value.trim();
                                if (!capstoneUrl) {
                                    this.formError = 'Chứng chỉ trong nước (Cách 2): Vui lòng nhập Đường link Github / Website demo sản phẩm thực tế.';
                                    return;
                                }
                            } else {
                                const suppFileInput = form.querySelector('div[x-show="domesticProofType !== \'capstone_project\'"] input[name="supplementary_file"]') || form.querySelector('input[name="supplementary_file"]');
                                if (!suppFileInput || !suppFileInput.files || suppFileInput.files.length === 0) {
                                    this.formError = 'Chứng chỉ trong nước (Cách 2): Vui lòng đính kèm tệp minh chứng phụ (Bảng điểm / Email chúc mừng / Hóa đơn học phí).';
                                    return;
                                }
                            }
                        }
                    }
                }

                this.uploading = true;
                form.submit();
            }
        };
    }
    </script>

    <div x-show="activeTab === 'documents'" x-cloak class="space-y-6" x-data="instructorDocumentsManager()">

        @if(session('error'))
            <div class="rounded-2xl border border-rose-300 bg-rose-50 p-4 text-xs font-bold text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-200 flex items-center gap-2">
                <svg class="h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->has('anti_forgery'))
            <div class="rounded-2xl border border-rose-300 bg-rose-50 p-4 text-xs font-bold text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-200 flex items-center gap-2">
                <svg class="h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first('anti_forgery') }}</span>
            </div>
        @endif

        @if($user->isGlobalReviewPending())
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm font-semibold text-blue-800 dark:border-blue-900 dark:bg-blue-950/30 dark:text-blue-200">
                Hồ sơ đang được xét duyệt. Bạn có thể xem tài liệu đã gửi nhưng không thể tải lên, thay thế, sửa liên kết hoặc xóa tài liệu lúc này.
            </div>
        @endif

        <fieldset @disabled($user->isGlobalReviewPending()) class="contents">

        {{-- 1. XÁC MINH DANH TÍNH CÁ NHÂN (CCCD & ẢNH CHÂN DUNG SELFIE) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5 dark:border-slate-800">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#0056D2] text-xs font-black text-white">ID</span>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Xác minh danh tính cá nhân (CCCD & Ảnh chân dung)</h3>
                        @if($profile?->hasUploadedIdentity())
                            <span class="rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                ✔ Đã nộp định danh
                            </span>
                        @else
                            <span class="rounded-full bg-amber-100 px-3 py-0.5 text-xs font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                ⚠️ Bắt buộc hoàn thành
                            </span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Hệ thống đối chiếu trực tiếp họ tên trên CCCD với các bằng cấp/chứng chỉ tải lên nhằm phòng chống giả mạo bằng AI/Photoshop.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('instructor.profile.identity.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Số CCCD / CMND gắn chip *
                        </label>
                        <input type="text" name="id_card_number" value="{{ old('id_card_number', $profile?->id_card_number) }}" required
                               placeholder="Ví dụ: 001202012345 (12 chữ số)"
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Họ và tên trên CCCD * (Phải khớp với tên trên bằng cấp)
                        </label>
                        <input type="text" name="id_card_name" value="{{ old('id_card_name', $profile?->id_card_name ?: $user->name) }}" required
                               placeholder="Ví dụ: NGUYỄN VĂN A"
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-900 uppercase focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    {{-- Mặt trước CCCD --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/60 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase text-slate-700 dark:text-slate-200">Mặt trước CCCD *</span>
                            @if($profile?->id_card_front_path)
                                <a href="{{ route('instructor.profile.identity.view', 'front') }}" target="_blank" class="text-[11px] font-bold text-[#0056D2] hover:underline">Xem tệp hiện tại ↗</a>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400">Chụp rõ nét số CCCD, họ tên, quốc huy.</p>
                        <input type="file" name="id_card_front" accept=".jpg,.jpeg,.png,.webp,.pdf" {{ $profile?->id_card_front_path ? '' : 'required' }}
                               class="w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-[#0056D2] hover:file:bg-blue-100 dark:text-slate-300">
                    </div>

                    {{-- Mặt sau CCCD --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/60 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase text-slate-700 dark:text-slate-200">Mặt sau CCCD *</span>
                            @if($profile?->id_card_back_path)
                                <a href="{{ route('instructor.profile.identity.view', 'back') }}" target="_blank" class="text-[11px] font-bold text-[#0056D2] hover:underline">Xem tệp hiện tại ↗</a>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400">Chụp rõ chip điện tử, nơi cấp, ngày cấp.</p>
                        <input type="file" name="id_card_back" accept=".jpg,.jpeg,.png,.webp,.pdf" {{ $profile?->id_card_back_path ? '' : 'required' }}
                               class="w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-[#0056D2] hover:file:bg-blue-100 dark:text-slate-300">
                    </div>

                    {{-- Ảnh chân dung Selfie --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/60 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase text-slate-700 dark:text-slate-200">Ảnh chân dung (Selfie) *</span>
                            @if($profile?->portrait_image_path)
                                <a href="{{ route('instructor.profile.identity.view', 'portrait') }}" target="_blank" class="text-[11px] font-bold text-[#0056D2] hover:underline">Xem tệp hiện tại ↗</a>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400">Ảnh chụp khuôn mặt chính diện, rõ nét.</p>
                        <input type="file" name="portrait_image" accept=".jpg,.jpeg,.png,.webp" {{ $profile?->portrait_image_path ? '' : 'required' }}
                               class="w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-[#0056D2] hover:file:bg-blue-100 dark:text-slate-300">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0056D2] px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#00419e]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Lưu thông tin định danh CCCD</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- BANNER TÓM TẮT TIẾN ĐỘ HỒ SƠ THEO NGÀNH --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-5 dark:border-slate-800">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($teachingFieldRecords->isNotEmpty())
                            @foreach($teachingFieldRecords as $teachingField)
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-[#0056D2] dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                     {{ $teachingField->category->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                Chưa chọn ngành giảng dạy
                            </span>
                        @endif

                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mt-2">Hồ sơ minh chứng theo ngành giảng dạy</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Để đảm bảo chất lượng đào tạo, hệ thống yêu cầu Giảng viên cung cấp đúng các văn bằng, chứng chỉ phù hợp với từng ngành đã đăng ký.
                    </p>
                </div>
            </div>

            {{-- Checklist các yêu cầu theo từng ngành --}}
            @if($teachingFieldRecords->isEmpty())
                @if(!empty($requirementData['categories_requirements']))
                    <div class="mt-6 space-y-6">
                        @foreach($requirementData['categories_requirements'] as $legacyGroup)
                            <div class="rounded-3xl border border-slate-200 bg-slate-50/50 p-5 dark:border-slate-800 dark:bg-slate-900/50">
                                <h4 class="border-b border-slate-200 pb-3 text-base font-black text-slate-900 dark:border-slate-800 dark:text-white">
                                    Ngành: {{ $legacyGroup['category']->name }}
                                </h4>
                                <div class="mt-4 space-y-4">
                                    @forelse($legacyGroup['requirements'] as $item)
                                        @php
                                            $req = $item['requirement'];
                                            $docs = $item['documents'];
                                            $status = $item['status'];
                                        @endphp
                                        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800/90">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <h5 class="text-sm font-black text-slate-900 dark:text-white">{{ $req->document_title }}</h5>
                                                        <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $req->is_required ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600' }}">
                                                            {{ $req->is_required ? 'Bắt buộc' : 'Tùy chọn' }}
                                                        </span>
                                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-700">
                                                            {{ ['approved' => 'Đã duyệt', 'pending' => 'Chờ duyệt', 'draft' => 'Chưa gửi', 'rejected' => 'Bị từ chối'][$status] ?? 'Chưa nộp' }}
                                                        </span>
                                                    </div>
                                                    @if($req->description)
                                                        <p class="mt-1 text-xs text-slate-500">{{ $req->description }}</p>
                                                    @endif
                                                </div>
                                                <button type="button"
                                                        @click="activeTeachingFieldId = null; activeRequirementId = {{ $req->id }}; activeRequirementTitle = @js($req->document_title.' ('.$legacyGroup['category']->name.')'); activeDocType = '{{ $req->document_type }}'; submissionSource = 'file'; uploadError = ''; uploading = false; uploadModal = true"
                                                        class="shrink-0 rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white">
                                                    Tải lên
                                                </button>
                                            </div>

                                            @if($docs->isNotEmpty())
                                                <div class="mt-3 space-y-2 border-t border-slate-100 pt-3 dark:border-slate-700">
                                                    @foreach($docs as $doc)
                                                        <div class="flex flex-col gap-2 rounded-xl bg-slate-50 p-3 dark:bg-slate-900/60 sm:flex-row sm:items-center sm:justify-between">
                                                            <div class="min-w-0">
                                                                <p class="truncate text-xs font-bold text-slate-800 dark:text-white">{{ $doc->title ?: $doc->original_name }}</p>
                                                                <p class="text-[11px] text-slate-400">{{ $doc->isUrlSource() ? 'Liên kết tài liệu' : $doc->original_name.' · '.$doc->formattedFileSize() }}</p>
                                                            </div>
                                                            <div class="flex shrink-0 items-center gap-2">
                                                                <a href="{{ route('instructor.profile.documents.view', $doc) }}" target="_blank" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $doc->isUrlSource() ? 'Xem tài liệu' : ($doc->isVideo() ? 'Xem video' : 'Xem tệp') }}</a>
                                                                @if($doc->isUrlSource() && $doc->isDraft())
                                                                    <button type="button" @click="editingDocumentId = {{ $doc->id }}; editingDocumentUrl = @js($doc->document_url); editUrlModal = true" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700">Sửa link</button>
                                                                @endif
                                                                @if($doc->isDraft() && ! $doc->isUrlSource())
                                                                    <form method="POST" action="{{ route('instructor.profile.documents.replace', $doc) }}" enctype="multipart/form-data" class="flex items-center gap-1">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="file" name="file" class="max-w-28 text-[10px]">
                                                                        <button type="submit" class="rounded-lg bg-blue-50 px-2 py-1 text-xs font-semibold text-[#0056D2]">Thay file</button>
                                                                    </form>
                                                                @endif
                                                                @if($doc->isDraft())
                                                                    <form method="POST" action="{{ route('instructor.profile.documents.delete', $doc) }}" onsubmit="return confirm('Xác nhận xóa tài liệu này?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="rounded-lg bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-600">Xóa</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-center text-xs text-slate-400">Ngành này chưa có cấu hình yêu cầu tài liệu.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="my-8 rounded-2xl bg-amber-50 p-6 text-center text-amber-900 dark:bg-amber-950/30 dark:text-amber-200">
                        <p class="text-sm font-bold">Chưa có danh sách yêu cầu tài liệu cụ thể hoặc bạn chưa chọn ngành giảng dạy.</p>
                        <p class="mt-1 text-xs">Vui lòng chọn ngành ở Tab "Thông tin cá nhân & nghề nghiệp" để hệ thống tải bộ yêu cầu tương ứng.</p>
                    </div>
                @endif
            @else
                <div class="mt-6 space-y-8">
                    @foreach($teachingFieldRecords as $teachingField)
                        @php
                            $fieldData = $teachingFieldRequirementData[$teachingField->id];
                            $groupReqs = $fieldData['requirements'];
                            $groupSummary = $fieldData['summary'];
                            $groupOptionalCount = collect($groupReqs)
                                ->reject(fn ($item) => $item['requirement']->is_required)
                                ->count();
                            $groupDraftDocumentCount = collect($groupReqs)
                                ->sum(fn ($item) => $item['documents']->where('status', 'draft')->count());
                        @endphp
                        <div class="rounded-3xl border border-slate-200 bg-slate-50/50 p-5 sm:p-6 dark:border-slate-800 dark:bg-slate-900/50 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-3 dark:border-slate-800">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#0056D2] text-white text-xs font-black">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900 dark:text-white">
                                            Ngành: {{ $teachingField->category->name }}
                                        </h4>
                                        <p class="text-xs text-slate-500">
                                            {{ $groupSummary['required_count'] }} yêu cầu bắt buộc · {{ $groupOptionalCount }} tùy chọn
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col items-start gap-2 sm:items-end">
                                    @if(empty($groupReqs))
                                        <span class="rounded-full bg-slate-200 px-3 py-1 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            ℹ Chưa cấu hình hồ sơ
                                        </span>
                                    @elseif($groupSummary['required_count'] === 0)
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-[11px] font-bold text-blue-800 dark:bg-blue-950/60 dark:text-blue-300">
                                            ℹ Không có hồ sơ bắt buộc
                                        </span>
                                    @elseif($groupSummary['can_submit'])
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            ✔ Đủ hồ sơ ngành này
                                        </span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-[11px] font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                             Thiếu {{ $groupSummary['missing_count'] }} tài liệu bắt buộc
                                        </span>
                                    @endif
                                    @if($teachingField->approval_status === 'approved')
                                        <span class="text-xs font-bold text-emerald-700">✅ Đã duyệt — có thể tạo và quản lý khóa học</span>
                                    @elseif($teachingField->approval_status === 'pending')
                                        <span class="text-xs font-bold text-amber-700">⏳ Chờ admin duyệt</span>
                                    @elseif($teachingField->approval_status === 'rejected')
                                        <span class="text-xs font-bold text-rose-700">❌ Bị từ chối: {{ $teachingField->rejection_reason }}</span>
                                    @elseif($teachingField->approval_status === 'superseded')
                                        <span class="text-xs font-bold text-slate-600">⛔ Đã thay thế — vẫn giữ lịch sử và quản lý khóa học cũ</span>
                                    @else
                                        <span class="text-xs font-bold text-slate-600">📝 Chưa gửi xét duyệt</span>
                                    @endif
                                    @if($teachingField->isApproved() && $groupDraftDocumentCount > 0)
                                        <form method="POST" action="{{ route('instructor.profile.teaching-fields.submit-supplement', $teachingField) }}">
                                            @csrf
                                            <button class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white">Gửi bổ sung hồ sơ ngành này</button>
                                        </form>
                                    @elseif($teachingField->isEditable())
                                        <form method="POST" action="{{ route('instructor.profile.teaching-fields.submit-review', $teachingField) }}">
                                            @csrf
                                            <button {{ $groupSummary['can_submit'] ? '' : 'disabled' }} class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">Gửi xét duyệt ngành này</button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            @if(empty($groupReqs))
                                <div class="rounded-2xl bg-white p-4 text-center text-xs text-slate-400 dark:bg-slate-800">
                                    Ngành này chưa có cấu hình yêu cầu tài liệu cụ thể.
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($groupReqs as $item)
                                        @php
                                            $req = $item['requirement'];
                                            $docs = $item['documents'];
                                            $status = $item['status'];
                                        @endphp
                                        <div class="rounded-2xl border {{ $req->is_required ? (in_array($status, ['draft', 'pending', 'approved'], true) ? 'border-emerald-200 bg-white dark:bg-slate-800/90' : 'border-amber-200 bg-white dark:bg-slate-800/90') : 'border-slate-200 bg-white dark:bg-slate-800/90' }} p-4.5 transition">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="space-y-1 min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <h5 class="text-sm font-black text-slate-900 dark:text-white">
                                                            {{ $loop->iteration }}. {{ $req->document_title }}
                                                        </h5>
                                                        @if($req->is_required)
                                                            <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-black text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                                                Bắt buộc
                                                            </span>
                                                        @else
                                                            <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                                Tùy chọn
                                                            </span>
                                                        @endif

                                                        {{-- Badge trạng thái --}}
                                                        @if($status === 'approved')
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-black text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                                ✔ Đã duyệt ({{ $docs->where('status', 'approved')->count() }})
                                                            </span>
                                                         @elseif($status === 'pending')
                                                             <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-black text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                                  Đã nộp - Chờ duyệt ({{ $docs->where('status', 'pending')->count() }})
                                                             </span>
                                                        @elseif($status === 'draft')
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                                Chưa gửi ({{ $docs->where('status', 'draft')->count() }})
                                                            </span>
                                                        @elseif($status === 'rejected')
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-black text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                                                ✖ Bị từ chối
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                                Chưa nộp
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($req->description)
                                                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                                            {{ $req->description }}
                                                        </p>
                                                    @endif
                                                </div>

                                                @if($status === 'missing' && $teachingField->acceptsDocumentUploads())
                                                    <button type="button"
                                                            @click="activeTeachingFieldId = {{ $teachingField->id }}; activeRequirementId = {{ $req->id }}; activeRequirementTitle = @js($req->document_title.' ('.$teachingField->category->name.')'); activeDocType = '{{ $req->document_type }}'; submissionSource = 'file'; uploadError = ''; uploading = false; uploadModal = true"
                                                            class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#0046B8]">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        <span>Tải lên</span>
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Danh sách file đã nộp cho requirement này --}}
                                            @if($docs->isNotEmpty())
                                                <div class="mt-3 border-t border-slate-100 pt-2.5 dark:border-slate-700/60 space-y-2">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tệp đã nộp ({{ $docs->count() }}):</span>
                                                    @foreach($docs as $doc)
                                                        <div class="flex flex-col gap-2 rounded-xl bg-slate-50/80 p-3 dark:bg-slate-900/60 sm:flex-row sm:items-center sm:justify-between border border-slate-100 dark:border-slate-700/40">
                                                            <div class="flex items-center gap-3 min-w-0">
                                                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $doc->isUrlSource() ? 'bg-violet-50 text-violet-600 text-xs' : ($doc->isPdf() ? 'bg-rose-50 text-rose-600 font-bold text-[10px]' : 'bg-blue-50 text-blue-600 text-xs') }}">
                                                                    {{ $doc->isUrlSource() ? 'URL' : ($doc->isVideo() ? 'VIDEO' : ($doc->isPdf() ? 'PDF' : 'IMG')) }}
                                                                </span>
                                                                <div class="min-w-0">
                                                                    <div class="flex flex-wrap items-center gap-2">
                                                                        <h6 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $doc->title ?: $doc->original_name }}</h6>
                                                                         @if($doc->isApproved())
                                                                             <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">✔ Đã duyệt</span>
                                                                         @elseif($doc->isDraft())
                                                                            @if(! $doc->hasAntiForgeryVerification())
                                                                                <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-700">⚠️ Chưa có xác thực chống giả</span>
                                                                            @else
                                                                                <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-700">Chưa gửi</span>
                                                                            @endif
                                                                         @elseif($doc->isRejected())
                                                                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">✖ Bị từ chối</span>
                                                                        @else
                                                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">⏳ Chờ duyệt</span>
                                                                        @endif
                                                                    </div>
                                                                    @if($doc->isDraft() && ! $doc->hasAntiForgeryVerification())
                                                                        <p class="text-[11px] font-bold text-rose-600 mt-1">⚠️ Tài liệu này chưa có dữ liệu chống làm giả. Vui lòng bấm biểu tượng thùng rác để xóa và tải lại có thông tin xác thực để gửi xét duyệt.</p>
                                                                    @endif
                                                                    @if($doc->isUrlSource())
                                                                        <p class="text-[11px] text-slate-400 mt-0.5">Nguồn tài liệu: Liên kết · Nộp: {{ $doc->uploaded_at ? $doc->uploaded_at->format('d/m/Y H:i') : '' }}</p>
                                                                    @else
                                                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $doc->original_name }} · {{ $doc->formattedFileSize() }} · Tải lên: {{ $doc->uploaded_at ? $doc->uploaded_at->format('d/m/Y H:i') : '' }}</p>
                                                                    @endif
                                                                    @if($doc->isRejected() && $doc->rejection_reason)
                                                                        <p class="text-[11px] font-semibold text-rose-600 mt-1">Lý do từ chối: {{ $doc->rejection_reason }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                                                <a href="{{ route('instructor.profile.documents.view', $doc) }}" target="_blank" class="rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200">
                                                                    {{ $doc->isUrlSource() ? 'Xem tài liệu' : ($doc->isVideo() ? 'Xem video' : 'Xem tệp') }}
                                                                </a>
                                                                @if($doc->isUrlSource() && $doc->isDraft())
                                                                    <button type="button" @click="editingDocumentId = {{ $doc->id }}; editingDocumentUrl = @js($doc->document_url); editUrlModal = true" class="rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200">Sửa link</button>
                                                                @endif
                                                                @if($doc->isDraft() && ! $doc->isUrlSource())
                                                                    <form method="POST" action="{{ route('instructor.profile.documents.replace', $doc) }}" enctype="multipart/form-data" class="flex items-center gap-1">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="file" name="file" class="max-w-28 text-[10px]">
                                                                        <input type="text" name="title" value="{{ $doc->title }}" class="max-w-28 rounded border border-slate-200 px-1.5 py-1 text-[10px]" aria-label="Tiêu đề tài liệu">
                                                                        <button type="submit" class="rounded-lg bg-blue-50 px-2 py-1 text-xs font-semibold text-[#0056D2] hover:bg-blue-100">Cập nhật</button>
                                                                    </form>
                                                                @endif
                                                                @if($doc->isDraft())
                                                                    <form method="POST" action="{{ route('instructor.profile.documents.delete', $doc) }}" onsubmit="return confirm('Xác nhận xóa tài liệu này?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="rounded-lg p-1 text-slate-400 hover:bg-rose-50 hover:text-rose-600">
                                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                        </button>
                                                                    </form>
                                                                @elseif($doc->isRejected() && $teachingField->acceptsDocumentUploads())
                                                                    <button type="button" @click="activeTeachingFieldId = {{ $teachingField->id }}; activeRequirementId = {{ $doc->requirement_id }}; activeRequirementTitle = @js($req->document_title.' ('.$teachingField->category->name.')'); activeDocType = '{{ $doc->document_type }}'; submissionSource = 'file'; uploadError = ''; uploading = false; uploadModal = true" class="rounded-lg bg-blue-50 px-2 py-1 text-xs font-semibold text-[#0056D2] hover:bg-blue-100">Tải file thay thế</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- TÀI LIỆU MINH CHỨNG BỔ SUNG KHÁC (TỰ DO) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-wider">
                        Tài liệu minh chứng bổ sung khác (Tự do)
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-500">
                        Tải lên các tài liệu bổ trợ như Bảng điểm, Bằng khen, Thư giới thiệu, Hợp đồng hoặc minh chứng năng lực khác.
                    </p>
                </div>
                <button type="button"
                        @click="activeTeachingFieldId = null; activeRequirementId = null; activeRequirementTitle = 'Tài liệu minh chứng bổ sung khác (Tự do)'; activeDocType = 'other'; submissionSource = 'file'; uploadError = ''; uploading = false; uploadModal = true"
                        class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-slate-800 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tải lên tài liệu bổ sung</span>
                </button>
            </div>

            @if(!empty($requirementData['unassigned_certificates']) && $requirementData['unassigned_certificates']->isNotEmpty())
                <div class="mt-5 space-y-3">
                    @foreach($requirementData['unassigned_certificates'] as $doc)
                        <div class="flex flex-col gap-2 rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70 sm:flex-row sm:items-center sm:justify-between border border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $doc->isUrlSource() ? 'bg-violet-100 text-violet-600 text-xs' : ($doc->isPdf() ? 'bg-rose-100 text-rose-600 font-black text-xs' : 'bg-blue-100 text-blue-600 text-xs') }}">
                                    {{ $doc->isUrlSource() ? 'URL' : ($doc->isVideo() ? 'VIDEO' : ($doc->isPdf() ? 'PDF' : 'IMG')) }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h5 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $doc->title ?: $doc->original_name }}</h5>
                                        <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            {{ $doc->documentTypeLabel() }}
                                        </span>
                                         @if($doc->isApproved())
                                             <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">✔ Đã duyệt</span>
                                         @elseif($doc->isDraft())
                                            <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-700">Chưa gửi</span>
                                         @elseif($doc->isRejected())
                                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">✖ Bị từ chối</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">⏳ Chờ duyệt</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $doc->isUrlSource() ? 'Nguồn tài liệu: Liên kết' : $doc->original_name.' · '.$doc->formattedFileSize() }} · {{ $doc->uploaded_at ? $doc->uploaded_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                    @if($doc->isRejected() && $doc->rejection_reason)
                                        <p class="text-[11px] font-semibold text-rose-600 mt-1">Lý do từ chối: {{ $doc->rejection_reason }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                <a href="{{ route('instructor.profile.documents.view', $doc) }}" target="_blank" class="rounded-xl bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-100 border border-slate-200 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200">
                                    {{ $doc->isUrlSource() ? 'Xem tài liệu' : ($doc->isVideo() ? 'Xem video' : 'Xem tệp') }}
                                </a>
                                @if($doc->isUrlSource() && $doc->isDraft())
                                    <button type="button" @click="editingDocumentId = {{ $doc->id }}; editingDocumentUrl = @js($doc->document_url); editUrlModal = true" class="rounded-xl bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-100 border border-slate-200 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200">Sửa link</button>
                                @endif
                                @if($doc->isDraft() && ! $doc->isUrlSource())
                                    <form method="POST" action="{{ route('instructor.profile.documents.replace', $doc) }}" enctype="multipart/form-data" class="flex items-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="file" name="file" class="max-w-28 text-[10px]">
                                        <input type="text" name="title" value="{{ $doc->title }}" class="max-w-28 rounded border border-slate-200 px-1.5 py-1 text-[10px]" aria-label="Tiêu đề tài liệu">
                                        <button type="submit" class="rounded-xl bg-blue-50 px-2 py-1.5 text-xs font-bold text-[#0056D2] hover:bg-blue-100">Cập nhật</button>
                                    </form>
                                @endif
                                @if($doc->isDraft())
                                    <form method="POST" action="{{ route('instructor.profile.documents.delete', $doc) }}" onsubmit="return confirm('Xác nhận xóa tài liệu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl bg-rose-50 p-1.5 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300" title="Xóa tài liệu">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-2xl bg-slate-50 p-6 text-center text-slate-500 dark:bg-slate-800/40 dark:text-slate-400">
                    <p class="text-xs">Chưa có tài liệu bổ sung nào được tải lên.</p>
                </div>
            @endif
        </div>

        {{-- MODAL TẢI LÊN TÀI LIỆU --}}
        <div x-show="uploadModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-xl max-h-[92vh] overflow-y-auto rounded-3xl bg-white p-6 sm:p-8 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800" @click.away="uploadModal = false; uploadError = ''; urlError = ''; formError = ''">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Tải lên tài liệu minh chứng</h3>
                        <p class="text-xs text-blue-600 dark:text-blue-400 font-bold mt-0.5" x-text="activeRequirementTitle"></p>
                    </div>
                    <button type="button" @click="uploadModal = false; uploadError = ''; urlError = ''; formError = ''" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" action="{{ route('instructor.profile.documents.upload') }}" enctype="multipart/form-data" class="mt-5 space-y-4" @submit.prevent="handleUploadSubmit($event)">
                    @csrf
                    <input type="hidden" name="requirement_id" :value="activeRequirementId">
                    <input type="hidden" name="instructor_teaching_field_id" :value="activeTeachingFieldId">

                    <div>
                        <span class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Phương thức nộp tài liệu chính *</span>
                        <div class="grid grid-cols-2 gap-2 rounded-xl bg-slate-100 p-1 dark:bg-slate-800">
                            <label class="cursor-pointer rounded-lg px-3 py-2 text-center text-xs font-bold transition" :class="submissionSource === 'file' ? 'bg-white text-[#0056D2] shadow-sm dark:bg-slate-700 dark:text-blue-300' : 'text-slate-500 dark:text-slate-400'">
                                <input type="radio" name="source_type" value="file" x-model="submissionSource" @change="uploadError = ''; urlError = ''" class="sr-only"> Tải file lên
                            </label>
                            <label class="cursor-pointer rounded-lg px-3 py-2 text-center text-xs font-bold transition" :class="submissionSource === 'url' ? 'bg-white text-[#0056D2] shadow-sm dark:bg-slate-700 dark:text-blue-300' : 'text-slate-500 dark:text-slate-400'">
                                <input type="radio" name="source_type" value="url" x-model="submissionSource" @change="uploadError = ''; urlError = ''" class="sr-only"> Nhập link tài liệu
                            </label>
                        </div>
                    </div>

                    {{-- Chọn loại tài liệu khi upload tự do --}}
                    <div x-show="!activeRequirementId">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Loại tài liệu bổ sung *
                        </label>
                        <select name="document_type" x-model="activeDocType" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="transcript">Bảng điểm (Transcript)</option>
                            <option value="certificate">Chứng chỉ / Bằng khen chuyên môn khác</option>
                            <option value="employment_confirmation">Giấy xác nhận công tác / Giấy khen</option>
                            <option value="portfolio">Hồ sơ năng lực / Dự án thực tế (Portfolio)</option>
                            <option value="employment_contract">Hợp đồng lao động</option>
                            <option value="degree">Văn bằng phụ</option>
                            <option value="other" selected>Tài liệu minh chứng khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Tiêu đề tài liệu</label>
                        <input type="text" name="title" placeholder="Ví dụ: Bằng tốt nghiệp ĐH, Chứng chỉ AWS Developer..."
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>

                    <div x-show="submissionSource === 'file'">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Chọn bản scan / ảnh gốc (PDF, JPG, PNG, WEBP - Tối đa 50MB) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx,.mp4,.mov,.webm" :disabled="submissionSource !== 'file'"
                               @change="validateEvidenceFiles($event)"
                               :class="uploadError ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20 dark:bg-rose-950/20' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800'"
                               class="w-full rounded-xl border p-2.5 text-sm text-slate-700 transition file:mr-3 file:rounded-lg file:border-0 file:bg-[#0056D2] file:px-3.5 file:py-1.5 file:text-xs file:font-bold file:text-white hover:file:bg-[#00419e] dark:text-slate-300">
                        <div x-show="uploadError" x-cloak class="mt-1.5 flex items-center gap-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400">
                            <svg class="h-4 w-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="uploadError"></span>
                        </div>
                    </div>

                    <div x-show="submissionSource === 'url'" x-cloak>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            URL tài liệu <span class="text-rose-500">*</span>
                        </label>
                        <input type="url" name="document_url" placeholder="https://example.com/certificate.pdf" :disabled="submissionSource !== 'url'"
                               @input="urlError = ''"
                               :class="urlError ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20 dark:bg-rose-950/20' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800'"
                               class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 transition focus:border-[#0056D2] focus:ring-[#0056D2] dark:text-white">
                        <div x-show="urlError" x-cloak class="mt-1.5 flex items-center gap-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400">
                            <svg class="h-4 w-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="urlError"></span>
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Chỉ chấp nhận liên kết HTTP hoặc HTTPS; hệ thống không tải tài liệu về máy chủ.</p>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- 🛡️ PHẦN XÁC THỰC BỔ SUNG PHÒNG CHỐNG PHOTOSHOP / AI     --}}
                    {{-- ======================================================== --}}

                    {{-- 1. ĐỐI VỚI BẰNG ĐẠI HỌC / CAO ĐẲNG --}}
                    <div x-show="isDegreeType()" class="rounded-2xl border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-900/50 dark:bg-blue-950/20 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-black text-[#0056D2] dark:text-blue-300 uppercase tracking-wider">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Xác thực văn bằng ĐH / CĐ (Chống làm giả)</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 rounded-xl bg-white p-1 border border-blue-100 dark:bg-slate-800 dark:border-slate-700">
                            <label @click="formError = ''" class="cursor-pointer rounded-lg px-2.5 py-1.5 text-center text-xs font-bold transition" :class="degreeOption === 'lookup' ? 'bg-blue-100 text-[#0056D2] dark:bg-blue-900/60 dark:text-blue-200' : 'text-slate-500'">
                                <input type="radio" value="lookup" x-model="degreeOption" class="sr-only"> Cách 1: Tra cứu trực tuyến
                            </label>
                            <label @click="formError = ''" class="cursor-pointer rounded-lg px-2.5 py-1.5 text-center text-xs font-bold transition" :class="degreeOption === 'supplementary' ? 'bg-blue-100 text-[#0056D2] dark:bg-blue-900/60 dark:text-blue-200' : 'text-slate-500'">
                                <input type="radio" value="supplementary" x-model="degreeOption" class="sr-only"> Cách 2: Đính kèm giấy tờ
                            </label>
                        </div>

                        {{-- Cách 1: Tra cứu số --}}
                        <div x-show="degreeOption === 'lookup'" class="space-y-2.5">
                            <input type="hidden" name="verification_method" value="lookup" :disabled="!isDegreeType() || degreeOption !== 'lookup'">
                            <div class="grid gap-2 sm:grid-cols-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Số hiệu văn bằng <span class="text-rose-500">*</span></label>
                                    <input type="text" name="diploma_number" placeholder="Ví dụ: B1234567"
                                           class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Số vào sổ cấp bằng <span class="text-rose-500">*</span></label>
                                    <input type="text" name="book_reg_number" placeholder="Ví dụ: 105/2022/KTPM"
                                           class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Đường link cổng tra cứu của trường (Không bắt buộc)</label>
                                <input type="url" name="lookup_url" placeholder="https://tracuuvb.hust.edu.vn..."
                                       class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <p class="mt-0.5 text-[10px] text-slate-500">Nếu trường có web tra cứu văn bằng tốt nghiệp, bạn có thể dán link vào đây để Admin đối soát nhanh.</p>
                            </div>
                        </div>

                        {{-- Cách 2: Giấy tờ hỗ trợ --}}
                        <div x-show="degreeOption === 'supplementary'" class="space-y-2.5">
                            <input type="hidden" name="verification_method" value="supplementary" :disabled="!isDegreeType() || degreeOption !== 'supplementary'">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Loại minh chứng hỗ trợ</label>
                                <select name="supplementary_proof_type" :disabled="!isDegreeType() || degreeOption !== 'supplementary'"
                                        class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                    <option value="notarized">Bản sao công chứng</option>
                                    <option value="transcript">Bảng điểm tốt nghiệp toàn khóa</option>
                                    <option value="student_portal">Ảnh chụp cổng thông tin sinh viên / App trường</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Chọn tệp minh chứng phụ (Ảnh hoặc PDF)</label>
                                <input type="file" name="supplementary_file" accept=".pdf,.jpg,.jpeg,.png,.webp" :disabled="!isDegreeType() || degreeOption !== 'supplementary'"
                                       class="mt-0.5 w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-100 file:px-2.5 file:py-1 file:text-xs file:font-bold file:text-[#0056D2]">
                            </div>
                        </div>
                    </div>

                    {{-- 2. ĐỐI VỚI CHỨNG CHỈ NGHỀ / CHUYÊN MÔN --}}
                    <div x-show="isCertType()" class="rounded-2xl border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-900/50 dark:bg-purple-950/20 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-black text-purple-700 dark:text-purple-300 uppercase tracking-wider">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span>Xác thực chứng chỉ chuyên môn (Chống làm giả)</span>
                        </div>

                        {{-- Chọn loại nguồn cấp chứng chỉ --}}
                        <div class="grid grid-cols-2 gap-2 rounded-xl bg-white p-1 border border-purple-100 dark:bg-slate-800 dark:border-slate-700">
                            <label @click="formError = ''" class="cursor-pointer rounded-lg px-2.5 py-1.5 text-center text-xs font-bold transition" :class="certOption === 'international' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/60 dark:text-purple-200' : 'text-slate-500'">
                                <input type="radio" value="international" x-model="certOption" class="sr-only"> 🌐 Chứng chỉ Quốc tế
                            </label>
                            <label @click="formError = ''" class="cursor-pointer rounded-lg px-2.5 py-1.5 text-center text-xs font-bold transition" :class="certOption === 'domestic' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/60 dark:text-purple-200' : 'text-slate-500'">
                                <input type="radio" value="domestic" x-model="certOption" class="sr-only"> 🏫 Chứng chỉ Trong nước
                            </label>
                        </div>

                        {{-- TH1: Chứng chỉ Quốc tế --}}
                        <div x-show="certOption === 'international'" class="space-y-2">
                            <input type="hidden" name="verification_method" value="credential_url" :disabled="!isCertType() || certOption !== 'international'">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Đường link xác thực công khai (Credential URL)</label>
                                <input type="url" name="credential_url" placeholder="https://www.credly.com/badges/... hoặc https://coursera.org/verify/..."
                                       :disabled="!isCertType() || certOption !== 'international'"
                                       class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-purple-500 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <p class="mt-1 text-[10px] text-slate-500">Áp dụng cho AWS, Cisco, Meta, Google, Coursera... Admin chỉ mất 15 giây truy cập link để kiểm tra đối soát trực tiếp.</p>
                            </div>
                        </div>

                        {{-- TH2: Chứng chỉ Trung tâm Trong nước (FPT Aptech, Techmaster, MindX, Cybersoft...) --}}
                        <div x-show="certOption === 'domestic'" class="space-y-3">
                            <div class="grid grid-cols-2 gap-2 rounded-lg bg-purple-100/60 p-1 text-[11px] font-bold">
                                <label @click="formError = ''" class="cursor-pointer rounded-md px-2 py-1 text-center transition" :class="domesticOption === 'lookup' ? 'bg-white text-purple-800 shadow-xs' : 'text-purple-600'">
                                    <input type="radio" value="lookup" x-model="domesticOption" class="sr-only"> Cách 1: Mã / Link tra cứu
                                </label>
                                <label @click="formError = ''" class="cursor-pointer rounded-md px-2 py-1 text-center transition" :class="domesticOption === 'proof' ? 'bg-white text-purple-800 shadow-xs' : 'text-purple-600'">
                                    <input type="radio" value="proof" x-model="domesticOption" class="sr-only"> Cách 2: Sản phẩm / Minh chứng
                                </label>
                            </div>

                            {{-- Cách 1 của trong nước: Tra cứu web trung tâm --}}
                            <div x-show="domesticOption === 'lookup'" class="space-y-2">
                                <input type="hidden" name="verification_method" value="domestic_center_lookup" :disabled="!isCertType() || certOption !== 'domestic' || domesticOption !== 'lookup'">
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Mã / Số hiệu chứng chỉ</label>
                                        <input type="text" name="center_cert_code" placeholder="Ví dụ: TM-2023-890, CS-FE-12..."
                                               :disabled="!isCertType() || certOption !== 'domestic' || domesticOption !== 'lookup'"
                                               class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Đường link tra cứu web trung tâm (nếu có)</label>
                                        <input type="url" name="center_lookup_url" placeholder="https://techmaster.vn/certificate/..."
                                               :disabled="!isCertType() || certOption !== 'domestic' || domesticOption !== 'lookup'"
                                               class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-500">Dành cho trung tâm có cổng tra cứu trực tuyến học viên (Cybersoft, Techmaster, MindX...).</p>
                            </div>

                            {{-- Cách 2 của trong nước: Minh chứng sản phẩm / học tập thực tế --}}
                            <div x-show="domesticOption === 'proof'" class="space-y-2.5">
                                <input type="hidden" name="verification_method" value="domestic_center_proof" :disabled="!isCertType() || certOption !== 'domestic' || domesticOption !== 'proof'">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Hình thức minh chứng bổ sung</label>
                                    <select name="domestic_proof_type" x-model="domesticProofType"
                                            :disabled="!isCertType() || certOption !== 'domestic' || domesticOption !== 'proof'"
                                            class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                        <option value="capstone_project">Link Github / Demo sản phẩm tốt nghiệp (Capstone Project)</option>
                                        <option value="center_transcript">Bảng điểm đánh giá xếp loại cuối khóa của trung tâm</option>
                                        <option value="completion_email_or_receipt">Email chúc mừng tốt nghiệp / Hóa đơn học phí</option>
                                    </select>
                                </div>

                                <div x-show="domesticProofType === 'capstone_project'">
                                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Đường link Github / Website sản phẩm thực tế</label>
                                    <input type="url" name="capstone_project_url" placeholder="https://github.com/username/project hoặc https://myproject.demo..."
                                           :disabled="!isCertType() || certOption !== 'domestic' || domesticOption !== 'proof' || domesticProofType !== 'capstone_project'"
                                           class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                    <p class="mt-0.5 text-[10px] text-slate-500">Minh chứng sản phẩm thực tế của bạn chứng minh năng lực thực tế, không thể làm giả bằng ảnh.</p>
                                </div>

                                <div x-show="domesticProofType !== 'capstone_project'">
                                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Chọn tệp minh chứng phụ (Bảng điểm / Ảnh Email / Hóa đơn)</label>
                                    <input type="file" name="supplementary_file" accept=".pdf,.jpg,.jpeg,.png,.webp"
                                           :disabled="!isCertType() || certOption !== 'domestic' || domesticOption !== 'proof' || domesticProofType === 'capstone_project'"
                                           class="mt-0.5 w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-purple-100 file:px-2.5 file:py-1 file:text-xs file:font-bold file:text-purple-700">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. ĐỐI VỚI GIẤY XÁC NHẬN KINH NGHIỆM --}}
                    <div x-show="isExpType()" class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-900/50 dark:bg-amber-950/20 space-y-2.5">
                        <div class="flex items-center gap-2 text-xs font-black text-amber-700 dark:text-amber-300 uppercase tracking-wider">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Loại giấy xác nhận kinh nghiệm</span>
                        </div>
                        <input type="hidden" name="verification_method" value="experience_proof" :disabled="!isExpType()">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Loại hồ sơ minh chứng</label>
                            <select name="supplementary_proof_type" :disabled="!isExpType()"
                                    class="mt-0.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <option value="contract">Hợp đồng lao động</option>
                                <option value="appointment">Quyết định bổ nhiệm / tuyển dụng</option>
                                <option value="vssid">Mã số / Ảnh chụp quá trình đóng BHXH (VssID)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Báo lỗi validation chống làm giả trên Modal --}}
                    <div x-show="formError" x-cloak class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-bold text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300 flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="formError"></span>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="uploadModal = false; uploadError = ''; urlError = ''; formError = ''" :disabled="uploading" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50 dark:text-slate-300">
                            Hủy
                        </button>
                        <button type="submit" :disabled="uploading" class="rounded-xl bg-[#0056D2] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-[#00419e] disabled:cursor-not-allowed disabled:opacity-60">
                            <span x-show="!uploading">Lưu tài liệu</span>
                            <span x-show="uploading" x-cloak>Đang tải lên...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="editUrlModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900" @click.away="editUrlModal = false">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Sửa liên kết tài liệu</h3>
                <form method="POST" :action="'{{ url('/instructor/profile/documents') }}/' + editingDocumentId + '/url'" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="url" name="document_url" x-model="editingDocumentUrl" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <p class="text-[11px] text-slate-500">Liên kết cập nhật sẽ được đưa về trạng thái chờ duyệt.</p>
                    <div class="flex justify-end gap-3"><button type="button" @click="editUrlModal = false" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600">Hủy</button><button type="submit" class="rounded-xl bg-[#0056D2] px-5 py-2 text-xs font-bold text-white">Lưu liên kết</button></div>
                </form>
            </div>
        </div>
        </fieldset>
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
