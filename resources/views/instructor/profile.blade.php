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
                            <p class="mt-0.5 text-xs text-amber-700 dark:text-amber-400">
                                Bạn hãy hoàn thành cập nhật chứng chỉ bắt buộc ngay để Admin trả lời bạn sớm nhất trong vòng 72h tới. Trong khi chờ Admin duyệt bạn vẫn có thể xử dụng các chức năng quản trị bình thường.
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

            {{-- SECTION B: THÔNG TIN NGHỀ NGHIỆP & CHUYÊN MÔN THEO TỪNG NGÀNH --}}
            <div x-data="{
                fields: @js(old('teaching_fields', !empty($teachingFields) ? $teachingFields : [[
                    'category_id' => $selectedCategoryIds[0] ?? ($categories->first()?->children->first()?->id ?? $categories->first()?->id ?? 1),
                    'organization' => $profile->organization ?? '',
                    'position' => $profile->position ?? '',
                    'specialty' => $profile->specialty ?? '',
                    'experience' => $profile->experience ?? '',
                ]])),
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
                        organization: '',
                        position: '',
                        specialty: '',
                        experience: ''
                    });
                },
                removeField(index) {
                    if (this.fields.length > 1) {
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

                    <button type="button" @click="addField()"
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
                    <template x-for="(field, index) in fields" :key="index">
                        <div class="relative rounded-2xl border-2 border-slate-200/80 bg-slate-50/60 p-5 sm:p-6 transition hover:border-blue-300 dark:border-slate-800 dark:bg-slate-800/40 space-y-5">
                            
                            {{-- Header của từng Khối Ngành --}}
                            <div class="flex items-center justify-between border-b border-slate-200/70 pb-3 dark:border-slate-700/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-[#0056D2] text-xs font-black text-white shadow-sm" x-text="index + 1"></span>
                                    <span class="text-sm font-black text-slate-900 dark:text-white" x-text="getCategoryName(field.category_id)"></span>
                                </div>

                                <template x-if="fields.length > 1">
                                    <button type="button" @click="removeField(index)"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold text-rose-600 transition hover:bg-rose-100 dark:text-rose-400 dark:hover:bg-rose-950/50">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Xóa ngành này</span>
                                    </button>
                                </template>
                            </div>

                            {{-- Hàng 1: Ngành + Đơn vị + Chức vụ --}}
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                        Ngành / Lĩnh vực giảng dạy *
                                    </label>
                                    <select :name="'teaching_fields[' + index + '][category_id]'"
                                            x-model="field.category_id"
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
                                          rows="3"
                                          placeholder="Mô tả các dự án thực tế, số năm kinh nghiệm hoặc các cơ sở đào tạo đã từng tham gia..."
                                          class="w-full rounded-xl border border-slate-300 bg-white p-3.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Nút Thêm Ngành ở cuối --}}
                <div class="pt-2">
                    <button type="button" @click="addField()"
                            class="w-full rounded-2xl border-2 border-dashed border-slate-300 p-4 text-center text-xs font-bold text-slate-600 transition hover:border-[#0056D2] hover:bg-blue-50/50 hover:text-[#0056D2] dark:border-slate-700 dark:text-slate-400 dark:hover:border-blue-500 dark:hover:bg-slate-800/60 dark:hover:text-blue-300 flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Thêm một ngành / lĩnh vực giảng dạy khác</span>
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

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-[#0056D2] px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition hover:bg-[#0046B8]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Lưu thay đổi hồ sơ</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 2: HỒ SƠ MINH CHỨNG & CHỨNG CHỈ THEO NGÀNH                           --}}
    {{-- ========================================================================= --}}
    <div x-show="activeTab === 'documents'" x-cloak class="space-y-6" x-data="{ uploadModal: false, activeRequirementId: null, activeRequirementTitle: '', activeDocType: 'certificate' }">

        {{-- BANNER TÓM TẮT TIẾN ĐỘ HỒ SƠ THEO NGÀNH --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-5 dark:border-slate-800">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if(!empty($requirementData['categories']) && $requirementData['categories']->isNotEmpty())
                            @foreach($requirementData['categories'] as $cat)
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-[#0056D2] dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                     {{ $cat->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                Chưa chọn ngành giảng dạy
                            </span>
                        @endif

                        @if($requirementData['summary']['total_requirements'] === 0)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                ℹ Chưa có cấu hình yêu cầu hồ sơ
                            </span>
                        @elseif($requirementData['summary']['required_count'] === 0)
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">
                                ℹ Không có hồ sơ bắt buộc
                            </span>
                        @elseif($requirementData['summary']['can_approve'])
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                ✔ Đã đủ hồ sơ bắt buộc
                            </span>
                        @else
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                 Còn thiếu {{ $requirementData['summary']['required_missing_count'] + $requirementData['summary']['required_rejected_count'] }} tài liệu bắt buộc
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
            @if(empty($requirementData['categories_requirements']))
                <div class="my-8 rounded-2xl bg-amber-50 p-6 text-center text-amber-900 dark:bg-amber-950/30 dark:text-amber-200">
                    <p class="text-sm font-bold">Chưa có danh sách yêu cầu tài liệu cụ thể hoặc bạn chưa chọn ngành giảng dạy.</p>
                    <p class="mt-1 text-xs">Vui lòng chọn ngành ở Tab "Thông tin cá nhân & nghề nghiệp" để hệ thống tải bộ yêu cầu tương ứng.</p>
                </div>
            @else
                <div class="mt-6 space-y-8">
                    @foreach($requirementData['categories_requirements'] as $catGroup)
                        @php
                            $groupCat = $catGroup['category'];
                            $groupReqs = $catGroup['requirements'];
                            $groupSummary = $catGroup['summary'];
                        @endphp
                        <div class="rounded-3xl border border-slate-200 bg-slate-50/50 p-5 sm:p-6 dark:border-slate-800 dark:bg-slate-900/50 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-3 dark:border-slate-800">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#0056D2] text-white text-xs font-black">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900 dark:text-white">
                                            Ngành: {{ $groupCat->name }}
                                        </h4>
                                        <p class="text-xs text-slate-500">
                                            {{ $groupSummary['required_count'] }} yêu cầu bắt buộc · {{ $groupSummary['optional_count'] }} tùy chọn
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    @if($groupSummary['total_requirements'] === 0)
                                        <span class="rounded-full bg-slate-200 px-3 py-1 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            ℹ Chưa cấu hình hồ sơ
                                        </span>
                                    @elseif($groupSummary['required_count'] === 0)
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-[11px] font-bold text-blue-800 dark:bg-blue-950/60 dark:text-blue-300">
                                            ℹ Không có hồ sơ bắt buộc
                                        </span>
                                    @elseif($groupSummary['has_all_required_submitted'])
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            ✔ Đủ hồ sơ ngành này
                                        </span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-[11px] font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                             Thiếu {{ $groupSummary['required_missing_count'] + $groupSummary['required_rejected_count'] }} tài liệu bắt buộc
                                        </span>
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
                                        <div class="rounded-2xl border {{ $req->is_required ? ($item['is_fulfilled'] ? 'border-emerald-200 bg-white dark:bg-slate-800/90' : 'border-amber-200 bg-white dark:bg-slate-800/90') : 'border-slate-200 bg-white dark:bg-slate-800/90' }} p-4.5 transition">
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
                                                                ✔ Đã duyệt ({{ $item['approved_count'] }})
                                                            </span>
                                                        @elseif($status === 'pending')
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-black text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                                 Đã nộp - Chờ duyệt ({{ $item['pending_count'] }})
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

                                                <button type="button"
                                                        @click="activeRequirementId = {{ $req->id }}; activeRequirementTitle = '{{ addslashes($req->document_title) }} ({{ addslashes($groupCat->name) }})'; activeDocType = '{{ $req->document_type }}'; uploadModal = true"
                                                        class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#0046B8]">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    <span>Tải lên</span>
                                                </button>
                                            </div>

                                            {{-- Danh sách file đã nộp cho requirement này --}}
                                            @if($docs->isNotEmpty())
                                                <div class="mt-3 border-t border-slate-100 pt-2.5 dark:border-slate-700/60 space-y-2">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tệp đã nộp ({{ $docs->count() }}):</span>
                                                    @foreach($docs as $doc)
                                                        <div class="flex flex-col gap-2 rounded-xl bg-slate-50/80 p-3 dark:bg-slate-900/60 sm:flex-row sm:items-center sm:justify-between border border-slate-100 dark:border-slate-700/40">
                                                            <div class="flex items-center gap-3 min-w-0">
                                                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $doc->isPdf() ? 'bg-rose-50 text-rose-600 font-bold text-[10px]' : 'bg-blue-50 text-blue-600 text-xs' }}">
                                                                    {{ $doc->isPdf() ? 'PDF' : 'IMG' }}
                                                                </span>
                                                                <div class="min-w-0">
                                                                    <div class="flex flex-wrap items-center gap-2">
                                                                        <h6 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $doc->title ?: $doc->original_name }}</h6>
                                                                        @if($doc->isApproved())
                                                                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">✔ Đã duyệt</span>
                                                                        @elseif($doc->isRejected())
                                                                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">✖ Bị từ chối</span>
                                                                        @else
                                                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">⏳ Chờ duyệt</span>
                                                                        @endif
                                                                    </div>
                                                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $doc->original_name }} · {{ $doc->formattedFileSize() }} · Tải lên: {{ $doc->uploaded_at ? $doc->uploaded_at->format('d/m/Y H:i') : '' }}</p>
                                                                    @if($doc->isRejected() && $doc->rejection_reason)
                                                                        <p class="text-[11px] font-semibold text-rose-600 mt-1">Lý do từ chối: {{ $doc->rejection_reason }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                                                <a href="{{ route('instructor.profile.documents.view', $doc) }}" target="_blank" class="rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200">
                                                                    Xem tệp
                                                                </a>
                                                                @unless($doc->isApproved())
                                                                    <form method="POST" action="{{ route('instructor.profile.documents.delete', $doc) }}" onsubmit="return confirm('Xác nhận xóa tài liệu này?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="rounded-lg p-1 text-slate-400 hover:bg-rose-50 hover:text-rose-600">
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
                        @click="activeRequirementId = null; activeRequirementTitle = 'Tài liệu minh chứng bổ sung khác (Tự do)'; activeDocType = 'other'; uploadModal = true"
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
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $doc->isPdf() ? 'bg-rose-100 text-rose-600 font-black text-xs' : 'bg-blue-100 text-blue-600 text-xs' }}">
                                    {{ $doc->isPdf() ? 'PDF' : 'IMG' }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h5 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $doc->title ?: $doc->original_name }}</h5>
                                        <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            {{ $doc->documentTypeLabel() }}
                                        </span>
                                        @if($doc->isApproved())
                                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">✔ Đã duyệt</span>
                                        @elseif($doc->isRejected())
                                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">✖ Bị từ chối</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">⏳ Chờ duyệt</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $doc->original_name }} · {{ $doc->formattedFileSize() }} · Tải lên: {{ $doc->uploaded_at ? $doc->uploaded_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                    @if($doc->isRejected() && $doc->rejection_reason)
                                        <p class="text-[11px] font-semibold text-rose-600 mt-1">Lý do từ chối: {{ $doc->rejection_reason }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                <a href="{{ route('instructor.profile.documents.view', $doc) }}" target="_blank" class="rounded-xl bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-100 border border-slate-200 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200">
                                    Xem tệp
                                </a>
                                @unless($doc->isApproved())
                                    <form method="POST" action="{{ route('instructor.profile.documents.delete', $doc) }}" onsubmit="return confirm('Xác nhận xóa tài liệu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl bg-rose-50 p-1.5 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300" title="Xóa tài liệu">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endunless
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
            <div class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800" @click.away="uploadModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Tải lên tài liệu minh chứng</h3>
                        <p class="text-xs text-blue-600 dark:text-blue-400 font-bold mt-0.5" x-text="activeRequirementTitle"></p>
                    </div>
                    <button type="button" @click="uploadModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" action="{{ route('instructor.profile.documents.upload') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="requirement_id" :value="activeRequirementId">

                    {{-- Chọn loại tài liệu khi upload tự do --}}
                    <div x-show="!activeRequirementId">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Loại tài liệu bổ sung *
                        </label>
                        <select name="document_type" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="transcript">Bảng điểm (Transcript)</option>
                            <option value="certificate">Chứng chỉ / Bằng khen chuyên môn khác</option>
                            <option value="employment_confirmation">Giấy xác nhận công tác / Giấy khen</option>
                            <option value="portfolio">Hồ sơ năng lực / Dự án thực tế (Portfolio)</option>
                            <option value="work_contract">Hợp đồng lao động</option>
                            <option value="degree">Văn bằng phụ</option>
                            <option value="other" selected>Tài liệu minh chứng khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Tiêu đề tài liệu</label>
                        <input type="text" name="title" placeholder="Ví dụ: Bảng điểm tốt nghiệp ĐH, Bằng khen xuất sắc 2025..."
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Chọn tệp (PDF, JPG, PNG, WEBP, DOCX - Tối đa 10MB) *</label>
                        <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" required
                               class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#0056D2] file:px-3.5 file:py-1.5 file:text-xs file:font-bold file:text-white hover:file:bg-[#00419e] dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="uploadModal = false" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300">
                            Hủy
                        </button>
                        <button type="submit" class="rounded-xl bg-[#0056D2] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-[#00419e]">
                            Tải lên
                        </button>
                    </div>
                </form>
            </div>
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
