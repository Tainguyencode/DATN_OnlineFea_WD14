@extends('layouts.app')

@section('title', 'Hoàn thiện hồ sơ & Đơn đăng ký Giảng viên - Website học online FEA')

@section('content')
<div
    class="min-h-[85vh] bg-slate-50 py-10 dark:bg-slate-950"
    x-data="{
        uploadModal: false,
        isDragging: false,
        selectedFiles: [],
        uploadError: '',
        addFiles(newFiles) {
            this.uploadError = '';
            const allowedExtensions = ['.pdf', '.jpg', '.jpeg', '.png'];
            const maxSizeBytes = 5 * 1024 * 1024; // 5MB

            Array.from(newFiles).forEach(file => {
                const ext = '.' + file.name.split('.').pop().toLowerCase();
                
                if (!allowedExtensions.includes(ext)) {
                    this.uploadError = `Tệp '${file.name}' không đúng định dạng (chỉ nhận PDF, JPG, JPEG, PNG).`;
                    return;
                }

                if (file.size > maxSizeBytes) {
                    this.uploadError = `Tệp '${file.name}' vượt quá dung lượng tối đa 5MB.`;
                    return;
                }

                if (this.selectedFiles.length >= 10) {
                    this.uploadError = 'Bạn chỉ có thể chọn tối đa 10 tệp cùng một lúc.';
                    return;
                }

                const exists = this.selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    this.selectedFiles.push(file);
                }
            });

            this.syncFileInput();
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            this.syncFileInput();
            if (this.selectedFiles.length === 0) {
                this.uploadError = '';
            }
        },
        syncFileInput() {
            try {
                const dt = new DataTransfer();
                this.selectedFiles.forEach(file => dt.items.add(file));
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.files = dt.files;
                }
            } catch (e) {
                // Fallback for environments without DataTransfer constructor
            }
        },
        resetUploadModal() {
            this.uploadModal = false;
            this.selectedFiles = [];
            this.uploadError = '';
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        }
    }"
    x-init="
        window.addEventListener('dragover', (e) => { if (uploadModal) e.preventDefault(); }, false);
        window.addEventListener('drop', (e) => { if (uploadModal) e.preventDefault(); }, false);
    "
>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        {{-- Header Breadcrumb & Logout --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-violet-600 dark:text-slate-400 dark:hover:text-violet-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                Quay lại Trang chủ
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 text-sm font-semibold text-rose-600 transition hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Đăng xuất
                </button>
            </form>
        </div>

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm text-emerald-900 shadow-sm dark:border-emerald-800/40 dark:bg-emerald-950/30 dark:text-emerald-200">
                <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-900 shadow-sm dark:border-rose-800/40 dark:bg-rose-950/30 dark:text-rose-200">
                <svg class="h-5 w-5 shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-900 shadow-sm dark:border-rose-800/40 dark:bg-rose-950/30 dark:text-rose-200">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ========================================== --}}
        {{-- TRẠNG THÁI 5 — ĐƯỢC DUYỆT (APPROVED)       --}}
        {{-- ========================================== --}}
        @if($state === 5)
            <div class="overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-xl dark:border-emerald-900/30 dark:bg-slate-900">
                <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 p-8 text-center text-white">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white/20 backdrop-blur-md">
                        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h1 class="text-2xl font-black tracking-tight sm:text-3xl">Hồ sơ giảng viên đã được duyệt!</h1>
                    <p class="mt-2 text-sm text-emerald-100 max-w-lg mx-auto">
                        Chúc mừng bạn! Bạn đã có thể sử dụng đầy đủ các chức năng quản trị, xuất bản bài học và tạo khóa học giảng dạy.
                    </p>
                </div>
                <div class="p-8 text-center">
                    <a href="{{ route('instructor.dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-8 py-3.5 text-base font-bold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-700 hover:shadow-xl">
                        <span>Vào trang quản trị Giảng viên</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

        {{-- ========================================== --}}
        {{-- TRẠNG THÁI 4 — BỊ TỪ CHỐI (REJECTED)       --}}
        {{-- ========================================== --}}
        @elseif($state === 4)
            <div class="overflow-hidden rounded-3xl border border-rose-200 bg-white shadow-xl dark:border-rose-900/30 dark:bg-slate-900">
                <div class="border-b border-rose-100 bg-rose-50/80 p-6 sm:p-8 dark:border-rose-900/20 dark:bg-rose-950/40">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 shadow-sm dark:bg-rose-900/50 dark:text-rose-300">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-black text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">
                                ❌ Hồ sơ chưa được duyệt
                            </span>
                            <h1 class="mt-1 text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Đơn đăng ký của bạn cần bổ sung hoặc chỉnh sửa</h1>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    {{-- Rejection Reason Box --}}
                    <div class="rounded-2xl border border-rose-200 bg-rose-50/60 p-5 dark:border-rose-900/40 dark:bg-rose-900/10">
                        <h4 class="text-xs font-black uppercase tracking-wider text-rose-700 dark:text-rose-400">Lý do từ Ban Quản Trị:</h4>
                        <p class="mt-2 text-sm font-medium leading-relaxed text-slate-800 dark:text-slate-200">
                            "{{ $user->rejected_reason ?? 'Thông tin hoặc chứng chỉ chưa đáp ứng đủ tiêu chuẩn. Vui lòng bổ sung chứng chỉ hợp lệ và cập nhật lại hồ sơ.' }}"
                        </p>
                    </div>

                    {{-- Certificate List --}}
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Chứng chỉ / Bằng cấp hiện có ({{ $certificatesCount }})</h3>
                            <button type="button" @click="uploadModal = true" class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Thêm chứng chỉ mới</span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            @forelse($certificates as $cert)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50/70 p-4 transition hover:border-violet-300 dark:border-slate-800 dark:bg-slate-800/40">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $cert->isPdf() ? 'bg-rose-100 text-rose-600' : 'bg-blue-100 text-blue-600' }} dark:bg-slate-700">
                                            @if($cert->isPdf())
                                                <span class="text-xs font-black">PDF</span>
                                            @else
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-bold text-slate-900 dark:text-white truncate max-w-xs sm:max-w-md">{{ $cert->title ?? $cert->original_name }}</h5>
                                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                                <span>{{ $cert->formattedFileSize() }}</span>
                                                <span>•</span>
                                                <span>{{ $cert->uploaded_at->format('d/m/Y H:i') }}</span>
                                                <span>•</span>
                                                <span class="font-semibold text-rose-600 dark:text-rose-400">Bị từ chối</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 self-end sm:self-center">
                                        <a href="{{ route('instructor.certificates.view', $cert) }}" target="_blank" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            Xem
                                        </a>
                                        <form method="POST" action="{{ route('instructor.certificates.delete', $cert) }}" onsubmit="return confirm('Bạn có chắc muốn xóa chứng chỉ này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-300">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700">
                                    Chưa có chứng chỉ nào. Vui lòng bấm <strong>[+ Thêm chứng chỉ mới]</strong> trước khi gửi lại hồ sơ.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Resubmit Form --}}
                    <div class="border-t border-slate-100 pt-6 dark:border-slate-800">
                        <h3 class="mb-4 text-base font-bold text-slate-900 dark:text-white">Cập nhật thông tin và gửi lại hồ sơ</h3>
                        <form method="POST" action="{{ route('instructor.resubmit') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Họ và tên</label>
                                    <input type="text" value="{{ $user->name }}" disabled class="w-full rounded-xl border border-slate-200 bg-slate-100 p-3 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-800 dark:text-slate-400">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Email</label>
                                    <input type="text" value="{{ $user->email }}" disabled class="w-full rounded-xl border border-slate-200 bg-slate-100 p-3 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-800 dark:text-slate-400">
                                </div>
                            </div>

                            <div>
                                <label for="phone" class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Số điện thoại *</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone ?? $user->phone) }}" required class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>

                            <div>
                                <label for="specialty" class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Lĩnh vực chuyên môn *</label>
                                <input type="text" id="specialty" name="specialty" value="{{ old('specialty', $profile->specialty ?? '') }}" required class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>

                            <div>
                                <label for="experience" class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Kinh nghiệm giảng dạy / làm việc *</label>
                                <textarea id="experience" name="experience" rows="3" required class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('experience', $profile->experience ?? '') }}</textarea>
                            </div>

                            <div>
                                <label for="bio" class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Giới thiệu bản thân *</label>
                                <textarea id="bio" name="bio" rows="3" required class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('bio', $profile->bio ?? '') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label for="linkedin_url" class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">LinkedIn</label>
                                    <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url ?? '') }}" class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label for="github_url" class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">GitHub</label>
                                    <input type="url" id="github_url" name="github_url" value="{{ old('github_url', $profile->github_url ?? '') }}" class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label for="website_url" class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Website</label>
                                    <input type="url" id="website_url" name="website_url" value="{{ old('website_url', $profile->website_url ?? '') }}" class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Tải lên CV mới (PDF, tối đa 5MB)</label>
                                <input type="file" name="cv" accept="application/pdf" class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                @if($profile && $profile->cv)
                                    <p class="mt-1 text-xs text-slate-500">CV hiện tại: <a href="{{ Storage::url($profile->cv) }}" target="_blank" class="text-violet-600 underline">Xem CV</a></p>
                                @endif
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                    <input type="checkbox" name="agree_information" value="1" required checked class="rounded border-slate-300 text-violet-600">
                                    <span>Tôi cam kết các thông tin và chứng chỉ đính kèm là hoàn toàn chính xác.</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                    <input type="checkbox" name="agree_terms" value="1" required checked class="rounded border-slate-300 text-violet-600">
                                    <span>Tôi đồng ý với Điều khoản dành cho Giảng viên.</span>
                                </label>
                            </div>

                            <button type="submit" class="w-full rounded-2xl bg-violet-600 px-6 py-3.5 font-bold text-white shadow-lg transition hover:bg-violet-700 cursor-pointer">
                                Gửi lại hồ sơ xét duyệt
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        {{-- ========================================== --}}
        {{-- TRẠNG THÁI 1, 2, 3 (CHƯA DUYỆT / CHỜ DUYỆT)--}}
        {{-- ========================================== --}}
        @else
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
                {{-- Banner top --}}
                <div class="bg-gradient-to-r from-violet-600 via-indigo-600 to-purple-600 p-8 text-center text-white">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white/10 backdrop-blur-md">
                        @if($state === 3)
                            <svg class="h-10 w-10 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @endif
                    </div>
                    @if($state === 3)
                        <h1 class="text-2xl font-black tracking-tight sm:text-3xl">Hồ sơ của bạn đang chờ Admin kiểm tra</h1>
                        <p class="mt-2 text-sm text-violet-100 max-w-xl mx-auto">
                            Hồ sơ đăng ký giảng viên của bạn đã được gửi thành công. Ban quản trị đang tiến hành xét duyệt trong thời gian từ 24 - 48 giờ làm việc.
                        </p>
                    @else
                        <h1 class="text-2xl font-black tracking-tight sm:text-3xl">Hoàn thiện hồ sơ Giảng viên</h1>
                        <p class="mt-2 text-sm text-violet-100 max-w-xl mx-auto">
                            Vui lòng bổ sung chứng chỉ / bằng cấp chuyên môn để Ban quản trị có thể phê duyệt quyền Giảng viên của bạn.
                        </p>
                    @endif
                </div>

                {{-- Status Step List --}}
                <div class="p-6 sm:p-10 space-y-6">
                    <div class="space-y-4">
                        {{-- Step 1: Email Verified --}}
                        <div class="flex items-start gap-4 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-emerald-950 dark:text-emerald-200">✅ Email đã xác thực</h4>
                                <p class="text-xs text-emerald-700 dark:text-emerald-400">Địa chỉ email <strong>{{ $user->email }}</strong> đã được xác minh.</p>
                            </div>
                        </div>

                        {{-- Step 2: Certificates Status & 7-Day Deadline --}}
                        @if($state === 1)
                            {{-- STATE 1: Chưa có chứng chỉ --}}
                            <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5 dark:border-amber-900/40 dark:bg-amber-950/30 space-y-3">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white shadow-sm">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-amber-950 dark:text-amber-200">⚠️ Hồ sơ chứng chỉ chưa hoàn thiện</h4>
                                        <p class="mt-1 text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                                            Bạn cần bổ sung chứng chỉ trong vòng <strong>{{ $daysRemaining }} ngày</strong> kể từ khi xác thực email để Admin có thể xét duyệt hồ sơ giảng viên.
                                        </p>
                                        <div class="mt-2 text-xs font-semibold text-amber-900 dark:text-amber-200">
                                            Thời hạn hoàn thiện hồ sơ: <span class="rounded-lg bg-amber-200/80 px-2 py-0.5 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200">còn {{ $daysRemaining }} ngày</span> (hết hạn lúc {{ $deadlineAt?->format('d/m/Y H:i') }})
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-2 flex justify-end">
                                    <button type="button" @click="uploadModal = true" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-amber-700 cursor-pointer">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Bổ sung chứng chỉ</span>
                                    </button>
                                </div>
                            </div>
                        @else
                            {{-- STATE 2 & 3: Đã có chứng chỉ --}}
                            <div class="flex items-start gap-4 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-emerald-950 dark:text-emerald-200">✅ Đã bổ sung chứng chỉ ({{ $certificatesCount }} chứng chỉ)</h4>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-400">Các chứng chỉ/bằng cấp đã được tải lên an toàn.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Step 3: Submitted Status --}}
                        @if($state === 3)
                            <div class="flex items-start gap-4 rounded-2xl border border-indigo-200 bg-indigo-50/70 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/30">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
                                    <span class="text-sm font-black animate-spin">⏳</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-indigo-950 dark:text-indigo-200">✅ Hồ sơ đã gửi xét duyệt</h4>
                                    <p class="text-xs text-indigo-700 dark:text-indigo-400">Gửi lúc {{ $user->submitted_for_review_at?->format('d/m/Y H:i:s') }} • ⏳ Đang chờ Admin kiểm tra</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Danh sách yêu cầu theo ngành đã đăng ký --}}
                    @if(!empty($requirementData['requirements']))
                        <div class="rounded-2xl border border-blue-100 bg-blue-50/40 p-5 dark:border-blue-900/30 dark:bg-slate-900 space-y-3">
                            <div class="flex items-center justify-between border-b border-blue-100 pb-3 dark:border-blue-900/30">
                                <div>
                                    <h4 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                                        <span class="rounded-full bg-blue-600 px-2 py-0.5 text-[10px] text-white font-bold">Ngành: {{ $requirementData['category']?->name }}</span>
                                        <span>Danh mục hồ sơ yêu cầu</span>
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Tất cả tài liệu [Bắt buộc] phải được nộp đầy đủ trước khi gửi hồ sơ xét duyệt.</p>
                                </div>
                                @if($requirementData['summary']['can_approve'])
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-black text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        ✔ Đã nộp đủ
                                    </span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-black text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                        Thiếu {{ $requirementData['summary']['required_missing_count'] + $requirementData['summary']['required_rejected_count'] }} mục bắt buộc
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-2">
                                @foreach($requirementData['requirements'] as $item)
                                    @php
                                        $req = $item['requirement'];
                                        $status = $item['status'];
                                    @endphp
                                    <div class="flex items-center justify-between rounded-xl bg-white p-3 shadow-xs dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                        <div class="min-w-0 pr-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $req->document_title }}</span>
                                                @if($req->is_required)
                                                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-black text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">Bắt buộc</span>
                                                @else
                                                    <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[9px] font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300">Tùy chọn</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            @if($status === 'approved')
                                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">✔ Đã duyệt</span>
                                            @elseif($status === 'pending')
                                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">⏳ Đã nộp</span>
                                            @elseif($status === 'rejected')
                                                <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">✖ Bị từ chối</span>
                                            @else
                                                <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-600">Chưa nộp</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Danh sách chứng chỉ (Nếu đã upload) --}}
                    @if($certificatesCount > 0)
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Chứng chỉ / Bằng cấp đã tải lên ({{ $certificatesCount }})</h3>
                                    @if($state === 2)
                                        <p class="text-xs text-slate-500">Thời hạn hoàn thiện hồ sơ: <span class="font-bold text-violet-600">còn {{ $daysRemaining }} ngày</span></p>
                                    @endif
                                </div>
                                @if($state === 2)
                                    <button type="button" @click="uploadModal = true" class="inline-flex items-center gap-1.5 rounded-xl bg-violet-100 px-3.5 py-2 text-xs font-bold text-violet-700 transition hover:bg-violet-200 dark:bg-violet-950/60 dark:text-violet-300">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Thêm chứng chỉ</span>
                                    </button>
                                @endif
                            </div>

                            <div class="space-y-3">
                                @foreach($certificates as $cert)
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-slate-200/80 bg-slate-50/60 p-3.5 dark:border-slate-800 dark:bg-slate-800/40">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $cert->isPdf() ? 'bg-rose-100 text-rose-600' : 'bg-blue-100 text-blue-600' }} dark:bg-slate-700">
                                                @if($cert->isPdf())
                                                    <span class="text-[10px] font-black">PDF</span>
                                                @else
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate max-w-xs sm:max-w-md">{{ $cert->title ?? $cert->original_name }}</h5>
                                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                                    <span>{{ $cert->formattedFileSize() }}</span>
                                                    <span>•</span>
                                                    <span>Trạng thái: <strong class="text-amber-600 dark:text-amber-400">Chờ duyệt</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 self-end sm:self-center">
                                            <a href="{{ route('instructor.certificates.view', $cert) }}" target="_blank" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                                Xem
                                            </a>
                                            @if($state === 2)
                                                <form method="POST" action="{{ route('instructor.certificates.delete', $cert) }}" onsubmit="return confirm('Bạn có chắc muốn xóa chứng chỉ này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-300">
                                                        Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Action buttons for State 2 --}}
                            @if($state === 2)
                                <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 dark:border-slate-800">
                                    <button type="button" @click="uploadModal = true" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Thêm chứng chỉ</span>
                                    </button>

                                    <form method="POST" action="{{ route('instructor.submit-review') }}" class="w-full sm:w-auto">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-violet-700 cursor-pointer">
                                            <span>Gửi hồ sơ xét duyệt</span>
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Notice info box --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/40 text-xs text-slate-500 dark:text-slate-400 space-y-2">
                        <p class="font-bold text-slate-700 dark:text-slate-300">Lưu ý về quy trình xét duyệt Giảng viên:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Bạn chưa thể truy cập trang quản trị cho đến khi hồ sơ được phê duyệt.</li>
                            <li>Chỉ khi Admin chấp thuận (APPROVE), bạn mới có thể bắt đầu tạo và quản lý khóa học.</li>
                            <li>Thời hạn hoàn thiện hồ sơ là 7 ngày kể từ khi xác thực email. Quá hạn này, tài khoản sẽ được chuyển về vai trò Học viên.</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ========================================== --}}
    {{-- UPLOAD CERTIFICATE MODAL                   --}}
    {{-- ========================================== --}}
    <div
        x-show="uploadModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
        @dragover.prevent
        @dragenter.prevent
        @drop.prevent
    >
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800" @click.stop @dragover.prevent @dragenter.prevent @drop.prevent>
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Tải lên Chứng chỉ / Bằng cấp
                </h3>
                <button type="button" @click="resetUploadModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form
                action="{{ route('instructor.certificates.upload') }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-6 space-y-4"
                novalidate
                @submit="
                    if (!selectedFiles.length) {
                        $event.preventDefault();
                        uploadError = 'Vui lòng chọn ít nhất một tệp chứng chỉ để tải lên.';
                        return false;
                    }
                    syncFileInput();
                    uploadError = '';
                "
            >
                @csrf

                @if(!empty($requirementData['requirements']))
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Hồ sơ theo ngành ({{ $requirementData['category']?->name ?? 'Chuyên ngành' }}) *
                        </label>
                        <select name="requirement_id" class="w-full rounded-xl border border-slate-300 bg-slate-50 p-3 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            @foreach($requirementData['requirements'] as $reqItem)
                                <option value="{{ $reqItem['requirement']->id }}">
                                    {{ $reqItem['requirement']->document_title }} {{ $reqItem['requirement']->is_required ? '— [Bắt buộc]' : '— [Tùy chọn]' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Tên / Tiêu đề chứng chỉ (Tùy chọn)
                    </label>
                    <input type="text" name="title" placeholder="Ví dụ: Chứng chỉ AWS Certified Solutions Architect..." class="w-full rounded-xl border border-slate-300 bg-slate-50 p-3 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Chọn tệp chứng chỉ (PDF, JPG, JPEG, PNG - Tối đa 5MB/file)
                        </label>
                        <span class="text-[11px] font-semibold text-violet-600 dark:text-violet-400" x-text="selectedFiles.length + '/10 tệp'"></span>
                    </div>

                    <div
                        class="mt-1 flex cursor-pointer justify-center rounded-2xl border-2 border-dashed px-6 pt-5 pb-6 transition"
                        :class="isDragging ? 'border-violet-600 bg-violet-100/70 dark:bg-violet-950/40 ring-4 ring-violet-500/20' : (uploadError ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 dark:border-slate-700 hover:border-violet-500 hover:bg-violet-50/20')"
                        @click="$refs.fileInput.click()"
                        @dragenter.prevent.stop="isDragging = true"
                        @dragover.prevent.stop="isDragging = true"
                        @dragleave.prevent.stop="isDragging = false"
                        @drop.prevent.stop="
                            isDragging = false;
                            if ($event.dataTransfer && $event.dataTransfer.files.length) {
                                addFiles($event.dataTransfer.files);
                            }
                        "
                    >
                        <div class="space-y-2 text-center pointer-events-none">
                            <svg class="mx-auto h-12 w-12" :class="isDragging ? 'text-violet-600 animate-bounce' : 'text-slate-400'" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600 dark:text-slate-300 justify-center">
                                <span class="font-bold text-violet-600 hover:text-violet-500">Bấm để chọn tệp từ máy tính</span>
                                <p class="pl-1">hoặc thả tệp PDF vào đây</p>
                            </div>
                            <p class="text-xs text-slate-500">Cho phép kéo thả nhiều tệp cùng lúc</p>
                        </div>
                        <input
                            id="cert-file-upload"
                            x-ref="fileInput"
                            name="files[]"
                            type="file"
                            multiple
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="hidden"
                            @change="addFiles($event.target.files); $event.target.value = '';"
                        >
                    </div>

                    {{-- Validation Error Alert --}}
                    <template x-if="uploadError">
                        <div class="mt-2 flex items-center gap-2 rounded-xl bg-rose-50 p-3 text-xs font-semibold text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-900/50">
                            <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span x-text="uploadError"></span>
                        </div>
                    </template>
                </div>

                {{-- Selected Files Preview list --}}
                <template x-if="selectedFiles.length > 0">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-3.5 space-y-2 max-h-48 overflow-y-auto dark:border-slate-800 dark:bg-slate-800/60">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Danh sách tệp đã chọn (<span x-text="selectedFiles.length"></span>):
                            </p>
                            <button type="button" @click="selectedFiles = []; syncFileInput();" class="text-[11px] font-semibold text-rose-600 hover:underline cursor-pointer">
                                Xóa tất cả
                            </button>
                        </div>
                        <template x-for="(f, idx) in selectedFiles" :key="idx">
                            <div class="flex items-center justify-between rounded-xl bg-white p-2 text-xs shadow-xs border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                <div class="flex items-center gap-2 truncate pr-2">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-violet-100 text-[10px] font-bold text-violet-700 dark:bg-violet-950 dark:text-violet-300" x-text="idx + 1"></span>
                                    <span class="truncate font-medium text-slate-800 dark:text-slate-200" x-text="f.name"></span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-slate-400 text-[11px]" x-text="(f.size / 1024 / 1024).toFixed(2) + ' MB'"></span>
                                    <button type="button" @click="removeFile(idx)" class="rounded-md p-1 text-slate-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/40 cursor-pointer" title="Xóa tệp này">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="resetUploadModal()" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 cursor-pointer">
                        Hủy
                    </button>
                    <button type="submit" class="rounded-xl bg-violet-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-violet-700 cursor-pointer">
                        Tải lên chứng chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
