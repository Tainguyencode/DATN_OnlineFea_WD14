<x-admin-layout title="Chi tiết hồ sơ Giảng viên" page-title="Chi tiết hồ sơ ứng tuyển giảng viên" breadcrumb="Quản lý giảng viên / Chi tiết">
    <div class="space-y-6" x-data="{ approveModal: false, rejectModal: false, rejectDocModal: false, selectedDocId: null, selectedDocTitle: '', rejectReactivationModal: false }">
        <div class="flex items-center justify-between relative z-10">
            <a href="{{ route('admin.instructors.applications.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-violet-600 dark:text-slate-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                Quay lại danh sách
            </a>

            <div class="flex items-center gap-3">
                @if($application->instructor_status !== 'approved')
                    @if($requirementData['summary']['can_approve'])
                        <form method="POST" action="{{ route('admin.instructors.applications.approve', $application) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Bạn chắc chắn muốn duyệt hồ sơ giảng viên này?')" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-emerald-700">
                                ✔ Phê duyệt Giảng viên
                            </button>
                        </form>
                    @else
                        @php
                            $missingStr = implode(', ', $requirementData['summary']['missing_titles'] ?? []);
                        @endphp
                        <div class="max-w-sm text-right">
                            <button type="button" disabled aria-describedby="missing-instructor-requirements" class="rounded-xl bg-slate-300 px-5 py-2.5 text-sm font-bold text-slate-600 shadow-none cursor-not-allowed dark:bg-slate-800 dark:text-slate-500">
                                ⚠️ Chưa đủ điều kiện duyệt
                            </button>
                            <p id="missing-instructor-requirements" class="mt-2 text-xs font-semibold leading-5 text-amber-700 dark:text-amber-300">
                                Thiếu tài liệu bắt buộc: {{ $missingStr !== '' ? $missingStr : 'chưa xác định' }}.
                            </p>
                        </div>
                    @endif
                @endif

                @if($application->instructor_status !== 'rejected')
                    <button type="button" @click="rejectModal = true" class="cursor-pointer rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-rose-700 active:scale-95">
                        ✖ Từ chối hồ sơ
                    </button>
                @endif
            </div>
        </div>

        {{-- BANNER HỒ SƠ VỪA CÓ CẬP NHẬT MỚI --}}
        @if(!empty($hadNewUpdates))
            <div class="rounded-2xl border border-rose-300 bg-gradient-to-r from-rose-50 to-amber-50 p-5 text-rose-900 shadow-sm dark:border-rose-900/50 dark:bg-slate-900 dark:text-rose-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-rose-600 text-white shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-rose-600 px-2.5 py-0.5 text-[10px] font-black text-white uppercase tracking-wider">Cập nhật mới</span>
                            <span class="text-xs text-rose-700 dark:text-rose-300 font-bold">Thời gian cập nhật: {{ $application->updated_at->format('d/m/Y H:i:s') }} ({{ $application->updated_at->diffForHumans() }})</span>
                        </div>
                        <p class="mt-1 text-xs text-rose-800 dark:text-rose-200">
                            Giảng viên vừa bổ sung thông tin hoặc tải lên tài liệu minh chứng mới. Hệ thống đã tự động đánh dấu để Admin kiểm tra.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- CẢNH BÁO TÀI KHOẢN BỊ KHÓA & YÊU CẦU CẤP LẠI (REACTIVATION BANNER) --}}
        @if($application->isLocked())
            <div class="rounded-2xl border-2 border-rose-300 bg-rose-50 p-6 dark:border-rose-900/50 dark:bg-rose-950/40">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-rose-600 px-3 py-0.5 text-xs font-black text-white uppercase">Tài khoản bị khóa</span>
                            <span class="text-xs text-rose-700 dark:text-rose-300 font-bold">Khóa lúc: {{ $application->locked_at ? $application->locked_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <p class="mt-2 text-sm text-rose-900 dark:text-rose-200">
                            <strong>Lý do khóa:</strong> {{ $application->locked_reason ?: 'Chưa hoàn thiện hồ sơ chứng chỉ trong thời hạn 7 ngày.' }}
                        </p>
                        @if($application->reactivation_status === 'pending')
                            <div class="mt-3 rounded-xl border border-amber-300 bg-amber-50 p-3.5 text-xs text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-200">
                                <strong>📝 Đơn yêu cầu cấp lại quyền giảng viên:</strong>
                                <p class="mt-1 italic">"{{ $application->reactivation_reason }}"</p>
                                <p class="mt-1 text-[11px] text-amber-700 dark:text-amber-400">Gửi lúc: {{ $application->reactivation_requested_at ? $application->reactivation_requested_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
                            </div>
                        @endif
                    </div>

                    @if($application->reactivation_status === 'pending')
                        <div class="flex items-center gap-3 shrink-0">
                            <form method="POST" action="{{ route('admin.instructors.applications.reactivation.approve', $application) }}">
                                @csrf
                                <button type="submit" onclick="return confirm('Xác nhận mở khóa và cấp lại quyền giảng viên cho tài khoản này?')" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-emerald-700">
                                    ✔ Duyệt cấp lại quyền
                                </button>
                            </form>
                            <button type="button" @click="rejectReactivationModal = true" class="rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-rose-700">
                                ✖ Từ chối cấp lại
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- CỘT TRÁI: THÔNG TIN TỔNG QUAN USER --}}
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 text-center">
                    <img src="{{ $application->avatarUrl() }}" alt="{{ $application->name }}" class="mx-auto h-28 w-28 rounded-2xl object-cover border-4 border-violet-100 shadow-md dark:border-violet-900/40">
                    
                    <h2 class="mt-4 text-xl font-black text-slate-900 dark:text-white">{{ $application->name }}</h2>
                    <p class="text-xs text-slate-400">@ {{ $application->username }}</p>

                    <div class="mt-4 flex justify-center gap-2">
                        @if($application->isLocked())
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-extrabold text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                Bị khóa
                            </span>
                        @elseif($application->instructor_status === 'approved')
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                Đã phê duyệt
                            </span>
                        @elseif($application->instructor_status === 'rejected')
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-extrabold text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                Đã từ chối
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                Chờ xét duyệt
                            </span>
                        @endif
                    </div>

                    <div class="mt-6 border-t border-slate-100 pt-6 dark:border-slate-800 space-y-3 text-left text-sm">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase">Ngành / Lĩnh vực đăng ký</span>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                @if(!empty($requirementData['categories']) && $requirementData['categories']->isNotEmpty())
                                    @foreach($requirementData['categories'] as $cat)
                                        <span class="rounded-lg bg-blue-50 px-2 py-1 text-xs font-bold text-[#0056D2] border border-blue-100 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-900">
                                            {{ $cat->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="font-bold text-sm text-slate-500">
                                        {{ $profile?->teaching_field ?: 'Chưa chọn ngành' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase">Email</span>
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
                            <span class="text-xs font-bold text-slate-400 uppercase">Số điện thoại</span>
                            <div class="font-medium text-slate-800 dark:text-slate-200">
                                {{ $profile?->phone ?? $application->phone ?? 'Chưa cung cấp' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase">Ngày đăng ký</span>
                            <div class="font-medium text-slate-800 dark:text-slate-200">
                                {{ $application->created_at->format('d/m/Y H:i:s') }}
                            </div>
                        </div>

                        @if($application->submitted_for_review_at)
                            <div>
                                <span class="text-xs font-bold text-slate-400 uppercase">Ngày nộp hồ sơ</span>
                                <div class="font-medium text-slate-800 dark:text-slate-200">
                                    {{ $application->submitted_for_review_at->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        @endif

                        @if($application->approved_at)
                            <div>
                                <span class="text-xs font-bold text-slate-400 uppercase">Người duyệt / Ngày duyệt</span>
                                <div class="font-medium text-slate-800 dark:text-slate-200">
                                    {{ $application->approver->name ?? 'Admin' }} ({{ $application->approved_at->format('d/m/Y H:i') }})
                                </div>
                            </div>
                        @endif

                        @if($application->bank_account_number)
                            <div class="border-t border-slate-100 pt-3 dark:border-slate-800">
                                <span class="text-xs font-bold text-slate-400 uppercase">Tài khoản ngân hàng</span>
                                <div class="font-bold text-xs text-slate-800 dark:text-slate-200">
                                    {{ $application->bank_name }} - {{ $application->bank_account_number }} ({{ $application->bank_account_name }})
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: CHI TIẾT HỒ SƠ NGHỀ NGHIỆP & TÀI LIỆU MINH CHỨNG --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">
                                Hồ sơ & Tài liệu theo Ngành Giảng dạy
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Ngành: <strong class="text-blue-600 dark:text-blue-400">{{ $requirementData['category']?->name ?? 'Chưa xác định' }}</strong>
                            </p>
                        </div>
                        <div>
                            @if($requirementData['summary']['can_approve'])
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    ✔ Đủ tài liệu bắt buộc
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-black text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                    ⚠️ Thiếu {{ $requirementData['summary']['required_missing_count'] + $requirementData['summary']['required_rejected_count'] }} mục bắt buộc
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($application->rejected_reason)
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-300">
                            <strong>Lý do từ chối trước đó:</strong> {{ $application->rejected_reason }}
                        </div>
                    @endif

                    {{-- Thông tin chuyên môn theo từng ngành --}}
                    @php
                        $teachingFields = $profile ? $profile->teachingCategories()->get() : collect();
                    @endphp

                    @if($teachingFields->isNotEmpty())
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Khối chuyên môn theo từng ngành</h4>
                            <div class="space-y-3">
                                @foreach($teachingFields as $tField)
                                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-800/40 space-y-3">
                                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2 dark:border-slate-700">
                                            <span class="font-black text-sm text-[#0056D2] dark:text-blue-400">
                                                Ngành: {{ $tField->name }}
                                            </span>
                                            @if($tField->pivot->is_primary)
                                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-black text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">Ngành chính</span>
                                            @endif
                                        </div>
                                        <div class="grid gap-3 sm:grid-cols-3 text-xs">
                                            <div>
                                                <span class="text-slate-400 font-bold uppercase">Đơn vị:</span>
                                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ $tField->pivot->organization ?: 'Chưa cập nhật' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-slate-400 font-bold uppercase">Chức vụ:</span>
                                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ $tField->pivot->position ?: 'Chưa cập nhật' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-slate-400 font-bold uppercase">Chuyên môn:</span>
                                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ $tField->pivot->specialty ?: 'Chưa cập nhật' }}</p>
                                            </div>
                                        </div>
                                        @if($tField->pivot->experience)
                                            <div class="text-xs pt-2 border-t border-slate-200/40 dark:border-slate-700/40">
                                                <span class="text-slate-400 font-bold uppercase">Kinh nghiệm:</span>
                                                <p class="mt-0.5 text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $tField->pivot->experience }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- Fallback nếu chưa có pivot --}}
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Trường / Đơn vị</span>
                                <p class="mt-1 font-bold text-sm text-slate-900 dark:text-white">{{ $profile?->organization ?: 'Chưa cập nhật' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Chức vụ</span>
                                <p class="mt-1 font-bold text-sm text-slate-900 dark:text-white">{{ $profile?->position ?: 'Chưa cập nhật' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Chuyên môn chi tiết</span>
                                <p class="mt-1 font-bold text-sm text-slate-900 dark:text-white">{{ $profile?->specialty ?: ($profile?->teaching_field ?: 'Chưa cập nhật') }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Kinh nghiệm làm việc & giảng dạy</h4>
                            <div class="mt-1 whitespace-pre-line rounded-2xl bg-slate-50 p-4 text-sm text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                {{ $profile?->experience ?: ($application->instructorApplication?->experience ?? 'Chưa có thông tin') }}
                            </div>
                        </div>
                    @endif

                    {{-- Bio --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Giới thiệu bản thân</h4>
                        <div class="mt-1 whitespace-pre-line rounded-2xl bg-slate-50 p-4 text-sm text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                            {{ $profile?->bio ?: ($application->bio ?: 'Chưa có thông tin') }}
                        </div>
                    </div>

                    {{-- CV File --}}
                    @if($profile && $profile->cv)
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tài liệu CV</h4>
                            <div class="flex items-center justify-between rounded-2xl border border-violet-200 bg-violet-50/50 p-4 dark:border-violet-900/40 dark:bg-violet-950/20">
                                <div class="flex items-center gap-3">
                                    <svg class="h-8 w-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 1H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <div>
                                        <h5 class="font-bold text-slate-900 dark:text-white">CV_Instructor_{{ $application->username }}.pdf</h5>
                                        <p class="text-xs text-slate-500">Định dạng PDF</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($profile->cv) }}" target="_blank" class="rounded-xl bg-violet-600 px-4 py-2 text-xs font-bold text-white hover:bg-violet-700">
                                    Mở / Tải về CV
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- BỘ YÊU CẦU HỒ SƠ THEO TỪNG NGÀNH --}}
                    <div class="space-y-6 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                                Đối chiếu Hồ sơ theo Ngành ({{ count($requirementData['requirements']) }} mục)
                            </h4>
                        </div>

                        @if(!empty($requirementData['categories_requirements']))
                            <div class="space-y-6">
                                @foreach($requirementData['categories_requirements'] as $catGroup)
                                    @php
                                        $groupCat = $catGroup['category'];
                                        $groupReqs = $catGroup['requirements'];
                                        $groupSummary = $catGroup['summary'];
                                    @endphp
                                    <div class="rounded-3xl border border-slate-200 bg-slate-50/50 p-5 dark:border-slate-800 dark:bg-slate-900/50 space-y-4">
                                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3 dark:border-slate-800">
                                            <div class="flex items-center gap-2">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#0056D2] text-white text-xs font-black">
                                                    {{ $loop->iteration }}
                                                </span>
                                                <h5 class="font-black text-sm text-slate-900 dark:text-white">
                                                    Ngành: {{ $groupCat->name }}
                                                </h5>
                                            </div>
                                            <div>
                                                @if($groupSummary['has_all_required_submitted'])
                                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-black text-emerald-800">
                                                        ✔ Đủ hồ sơ ngành này
                                                    </span>
                                                @else
                                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-black text-amber-800">
                                                        ⏳ Thiếu {{ $groupSummary['required_missing_count'] + $groupSummary['required_rejected_count'] }} tài liệu
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            @foreach($groupReqs as $item)
                                                @php
                                                    $req = $item['requirement'];
                                                    $docs = $item['documents'];
                                                    $status = $item['status'];
                                                @endphp
                                                <div class="rounded-2xl border {{ $req->is_required ? ($item['is_fulfilled'] ? 'border-emerald-200 bg-white' : 'border-amber-200 bg-white') : 'border-slate-200 bg-white' }} p-4 dark:bg-slate-800/80 transition">
                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-2.5 dark:border-slate-700">
                                                        <div>
                                                            <div class="flex flex-wrap items-center gap-2">
                                                                <h6 class="font-black text-xs text-slate-900 dark:text-white">{{ $req->document_title }}</h6>
                                                                @if($req->is_required)
                                                                    <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-black text-rose-700">Bắt buộc</span>
                                                                @else
                                                                    <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-bold text-slate-600">Tùy chọn</span>
                                                                @endif

                                                                @if($status === 'approved')
                                                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">✔ Đã duyệt</span>
                                                                @elseif($status === 'pending')
                                                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">⏳ Chờ duyệt ({{ $docs->count() }})</span>
                                                                @elseif($status === 'rejected')
                                                                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">✖ Bị từ chối</span>
                                                                @else
                                                                    <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-600">Chưa nộp</span>
                                                                @endif
                                                            </div>
                                                            @if($req->description)
                                                                <p class="text-[11px] text-slate-500 mt-1">{{ $req->description }}</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                            @if($docs->isNotEmpty())
                                                <div class="mt-3 space-y-3">
                                                    @foreach($docs as $doc)
                                                        <div class="rounded-xl bg-white p-3.5 shadow-xs dark:bg-slate-800 border border-slate-100 dark:border-slate-700 space-y-2.5">
                                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                                <div class="flex items-center gap-3 min-w-0">
                                                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $doc->isPdf() ? 'bg-rose-50 text-rose-600 text-xs font-black' : 'bg-blue-50 text-blue-600 text-xs' }}">
                                                                        {{ $doc->isPdf() ? 'PDF' : 'IMG' }}
                                                                    </span>
                                                                    <div class="min-w-0">
                                                                        <div class="flex flex-wrap items-center gap-2">
                                                                            <h6 class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $doc->title ?: $doc->original_name }}</h6>
                                                                            @if($doc->status === 'approved')
                                                                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">✔ Đã duyệt</span>
                                                                            @elseif($doc->status === 'rejected')
                                                                                <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">✖ Bị từ chối</span>
                                                                            @else
                                                                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">⏳ Chờ duyệt</span>
                                                                            @endif
                                                                        </div>
                                                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $doc->original_name }} · {{ $doc->formattedFileSize() }} · Tải lên: {{ $doc->uploaded_at ? $doc->uploaded_at->format('d/m/Y H:i') : '' }}</p>
                                                                    </div>
                                                                </div>

                                                                <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                                                    <a href="{{ route('admin.instructors.applications.certificates.view', $doc) }}" target="_blank" class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700 hover:bg-slate-200">
                                                                        Xem tệp
                                                                    </a>
                                                                    @if($doc->status === 'pending')
                                                                        <form method="POST" action="{{ route('admin.instructors.applications.documents.review', [$application, $doc]) }}">
                                                                            @csrf
                                                                            <input type="hidden" name="status" value="approved">
                                                                            <button type="submit" class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-emerald-700">
                                                                                ✔ Duyệt
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                    @if($doc->status !== 'rejected')
                                                                        <button type="button" @click="selectedDocId = {{ $doc->id }}; selectedDocTitle = '{{ addslashes($doc->title ?: $doc->original_name) }}'; rejectDocModal = true" class="rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 hover:bg-rose-100">
                                                                            ✖ Từ chối
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            @if($doc->isRejected() && $doc->rejection_reason)
                                                                <p class="text-[11px] font-semibold text-rose-600">Lý do từ chối trước đó: {{ $doc->rejection_reason }}</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="mt-2 text-xs italic text-slate-400">
                                                    Ứng viên chưa tải lên tài liệu nào cho yêu cầu này.
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500 italic">Chưa có danh mục yêu cầu tài liệu cụ thể nào cho ngành này.</p>
                @endif

                        {{-- Tài liệu khác (Unassigned) nếu có --}}
                        @if(!empty($requirementData['unassigned_certificates']) && $requirementData['unassigned_certificates']->isNotEmpty())
                            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Tài liệu minh chứng bổ sung khác ({{ $requirementData['unassigned_certificates']->count() }})</h5>
                                <div class="space-y-2">
                                    @foreach($requirementData['unassigned_certificates'] as $doc)
                                        <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                                            <div>
                                                <h6 class="font-bold text-xs text-slate-800 dark:text-white">{{ $doc->title ?: $doc->original_name }}</h6>
                                                <p class="text-[11px] text-slate-400">{{ $doc->documentTypeLabel() }} · {{ $doc->formattedFileSize() }}</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.instructors.applications.certificates.view', $doc) }}" target="_blank" class="rounded-lg bg-slate-200 px-2.5 py-1 text-xs font-bold text-slate-700">
                                                    Xem tệp
                                                </a>
                                            @if($doc->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.instructors.applications.documents.review', [$application, $doc]) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-emerald-700">
                                                            ✔ Duyệt
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL PHÊ DUYỆT HỒ SƠ --}}
        <div x-show="approveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Xác nhận Phê duyệt Giảng viên</h3>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                    Bạn chắc chắn muốn duyệt hồ sơ giảng viên cho <strong>{{ $application->name }}</strong> ({{ $application->email }})?
                </p>
                <p class="mt-1 text-xs text-slate-500">Sau khi duyệt, tài khoản này sẽ chính thức có quyền tạo khóa học trên hệ thống.</p>

                <form action="{{ route('admin.instructors.applications.approve', $application) }}" method="POST" class="mt-6 flex items-center justify-end gap-3">
                    @csrf
                    <button type="button" @click="approveModal = false" class="cursor-pointer rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300">
                        Hủy
                    </button>
                    <button type="submit" class="cursor-pointer rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                        ✔ Đồng ý Phê duyệt
                    </button>
                </form>
            </div>
        </div>

        {{-- MODAL TỪ CHỐI HỒ SƠ CHUNG --}}
        <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Từ chối hồ sơ Giảng viên</h3>
                <p class="mt-1 text-xs text-slate-500">Ứng viên: <strong>{{ $application->name }}</strong></p>

                <form action="{{ route('admin.instructors.applications.reject', $application) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Lý do từ chối *
                        </label>
                        <textarea name="rejected_reason" rows="4" required placeholder="Nhập lý do từ chối để gửi cho giảng viên..."
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50 p-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="rejectModal = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300">
                            Hủy
                        </button>
                        <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700">
                            Xác nhận từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL TỪ CHỐI TỪNG TÀI LIỆU RIÊNG LẺ --}}
        <div x-show="rejectDocModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Từ chối tài liệu minh chứng</h3>
                <p class="mt-1 text-xs text-slate-500">Tài liệu: <strong x-text="selectedDocTitle"></strong></p>

                <form :action="'{{ url('admin/instructors/applications/' . $application->id . '/documents') }}/' + selectedDocId + '/review'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Lý do từ chối tài liệu này *
                        </label>
                        <textarea name="rejection_reason" rows="4" required placeholder="Tài liệu mờ, hết hạn, không đúng chuyên ngành..."
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50 p-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="rejectDocModal = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300">
                            Hủy
                        </button>
                        <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700">
                            Xác nhận từ chối tài liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL TỪ CHỐI YÊU CẦU CẤP LẠI (REACTIVATION) --}}
        <div x-show="rejectReactivationModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Từ chối yêu cầu cấp lại quyền Giảng viên</h3>
                <p class="mt-1 text-xs text-slate-500">Giảng viên: <strong>{{ $application->name }}</strong></p>

                <form action="{{ route('admin.instructors.applications.reactivation.reject', $application) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Lý do không chấp thuận cấp lại *
                        </label>
                        <textarea name="notes" rows="4" required placeholder="Hồ sơ bổ sung chưa đạt yêu cầu..."
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50 p-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="rejectReactivationModal = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300">
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
