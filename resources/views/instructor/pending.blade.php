@extends('layouts.app')

@section('title', 'Đơn đăng ký Giảng viên - Website học online FEA')

@section('content')
<div class="min-h-[80vh] bg-slate-50 py-12 dark:bg-slate-950">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        {{-- Header Breadcrumb / Return --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-violet-600 dark:text-slate-400 dark:hover:text-violet-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                Trang chủ
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-red-600 hover:underline dark:text-red-400">
                    Đăng xuất
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-900/20 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Status Card --}}
        @if($user->instructor_status === 'rejected')
            {{-- REJECTED STATE --}}
            <div class="overflow-hidden rounded-2xl border border-red-200 bg-white shadow-xl dark:border-red-900/30 dark:bg-slate-900">
                <div class="border-b border-red-100 bg-red-50/70 p-6 dark:border-red-900/20 dark:bg-red-950/30">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Đơn đăng ký của bạn bị từ chối</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Vui lòng kiểm tra lý do và gửi lại thông tin cập nhật bên dưới.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="mb-8 rounded-xl border border-red-200 bg-red-50/50 p-4 dark:border-red-900/40 dark:bg-red-900/10">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-red-700 dark:text-red-400">Lý do từ chối từ Ban Quản Trị:</h4>
                        <p class="mt-2 text-sm font-medium text-slate-800 dark:text-slate-200">
                            "{{ $user->rejected_reason ?? 'Thông tin hồ sơ chưa đầy đủ hoặc không chính xác.' }}"
                        </p>
                    </div>

                    <h3 class="mb-4 text-base font-bold text-slate-900 dark:text-white">Cập nhật & Gửi lại hồ sơ</h3>

                    <x-auth.errors />

                    <form method="POST" action="{{ route('instructor.resubmit') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Họ và tên</label>
                                <input type="text" value="{{ $user->name }}" disabled class="w-full rounded-lg border border-slate-200 bg-slate-100 p-2.5 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-800 dark:text-slate-400">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Email</label>
                                <input type="text" value="{{ $user->email }}" disabled class="w-full rounded-lg border border-slate-200 bg-slate-100 p-2.5 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-800 dark:text-slate-400">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Số điện thoại *</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone ?? $user->phone) }}" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label for="specialty" class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Lĩnh vực chuyên môn *</label>
                            <input type="text" id="specialty" name="specialty" value="{{ old('specialty', $profile->specialty ?? '') }}" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label for="experience" class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Kinh nghiệm giảng dạy / làm việc *</label>
                            <textarea id="experience" name="experience" rows="3" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('experience', $profile->experience ?? '') }}</textarea>
                        </div>

                        <div>
                            <label for="bio" class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Giới thiệu bản thân *</label>
                            <textarea id="bio" name="bio" rows="3" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('bio', $profile->bio ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div>
                                <label for="linkedin_url" class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">LinkedIn</label>
                                <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url ?? '') }}" class="w-full rounded-lg border border-slate-300 bg-white p-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label for="github_url" class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">GitHub</label>
                                <input type="url" id="github_url" name="github_url" value="{{ old('github_url', $profile->github_url ?? '') }}" class="w-full rounded-lg border border-slate-300 bg-white p-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label for="website_url" class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Website</label>
                                <input type="url" id="website_url" name="website_url" value="{{ old('website_url', $profile->website_url ?? '') }}" class="w-full rounded-lg border border-slate-300 bg-white p-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Tải lên CV mới (PDF, max 5MB)</label>
                            <input type="file" name="cv" accept="application/pdf" class="w-full rounded-lg border border-slate-300 bg-white p-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white @error('cv') border-red-500 @enderror">
                            @error('cv')
                                <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @if($profile && $profile->cv)
                                <p class="mt-1 text-xs text-slate-500">CV đã tải lên hiện tại: <a href="{{ Storage::url($profile->cv) }}" target="_blank" class="text-violet-600 underline">Xem CV</a></p>
                            @endif
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Tải lên Chứng chỉ mới (PDF, JPG, PNG, max 5MB)</label>
                            <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-slate-300 bg-white p-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white @error('certificate') border-red-500 @enderror">
                            @error('certificate')
                                <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @php
                                $certPath = $application->certificate_path ?? null;
                            @endphp
                            @if($certPath)
                                <p class="mt-1 text-xs text-slate-500">Chứng chỉ đã tải lên hiện tại: <a href="{{ route('admin.instructors.applications.certificate', $user) }}" target="_blank" class="text-violet-600 underline">Xem chứng chỉ</a></p>
                            @else
                                <p class="mt-1 text-xs text-amber-600 italic">Chưa tải lên chứng chỉ</p>
                            @endif
                        </div>

                        <div class="space-y-2 pt-2">
                            <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                <input type="checkbox" name="agree_information" value="1" required checked class="rounded border-slate-300 text-violet-600">
                                <span>Tôi cam kết thông tin trên là chính xác.</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                <input type="checkbox" name="agree_terms" value="1" required checked class="rounded border-slate-300 text-violet-600">
                                <span>Tôi đồng ý Điều khoản dành cho Giảng viên.</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-violet-600 px-6 py-3 font-semibold text-white transition hover:bg-violet-700">
                            Gửi lại hồ sơ xét duyệt
                        </button>
                    </form>
                </div>
            </div>
        @else
            {{-- PENDING STATE (UDEMY STYLE) --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
                {{-- Banner top --}}
                <div class="bg-gradient-to-r from-violet-600 via-indigo-600 to-purple-600 p-8 text-center text-white">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white/10 backdrop-blur-md">
                        <svg class="h-10 w-10 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Đơn đăng ký giảng viên của bạn đang được xem xét</h1>
                    <p class="mt-2 text-sm text-violet-100 max-w-xl mx-auto">
                        Cảm ơn bạn đã đăng ký trở thành Giảng viên trên nền tảng của chúng tôi. Ban quản trị đang tiến hành xác minh thông tin hồ sơ của bạn.
                    </p>
                </div>

                {{-- Status Step List --}}
                <div class="p-6 sm:p-10 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Trạng thái hồ sơ đăng ký:</h3>

                    <div class="space-y-4">
                        {{-- Step 1: Email --}}
                        <div class="flex items-start gap-4 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-emerald-900 dark:text-emerald-200">Email đã xác thực</h4>
                                <p class="text-xs text-emerald-700 dark:text-emerald-400">Địa chỉ email {{ $user->email }} đã được xác minh thành công.</p>
                            </div>
                        </div>

                        {{-- Step 2: Profile Submitted --}}
                        <div class="flex items-start gap-4 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-emerald-900 dark:text-emerald-200">Hồ sơ đã gửi</h4>
                                <p class="text-xs text-emerald-700 dark:text-emerald-400">Thông tin cá nhân, chuyên môn và tài liệu CV của bạn đã được ghi nhận hệ thống.</p>
                            </div>
                        </div>

                        {{-- Step 3: Admin Review --}}
                        <div class="flex items-start gap-4 rounded-xl border border-amber-200 bg-amber-50/60 p-4 dark:border-amber-900/40 dark:bg-amber-950/30">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500 text-white">
                                <span class="text-base font-extrabold">⏳</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-900 dark:text-amber-200">Chờ Admin duyệt</h4>
                                <p class="text-xs text-amber-700 dark:text-amber-400">Hồ sơ đang trong quá trình duyệt thủ công (thông thường từ 24 - 48 giờ làm việc). Chúng tôi sẽ gửi email thông báo khi có kết quả.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="mt-8 rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/40 text-xs text-slate-500 dark:text-slate-400 space-y-2">
                        <p class="font-semibold text-slate-700 dark:text-slate-300">Lưu ý trong thời gian chờ duyệt:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Menu quản lý Giảng viên và chức năng Tạo khóa học tạm thời ẩn cho đến khi được Admin chấp thuận.</li>
                            <li>Bạn vẫn có thể trải nghiệm các khóa học khác trên hệ thống với vai trò Học viên.</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
