@props([
    'course',
    'courseProgress' => 0,
    'sidebarOpen' => true,
    'completedLessons' => 0,
    'totalLessons' => 0,
])

<header class="learning-header sticky top-0 z-50 flex h-14 items-center gap-3 border-b border-[#2d2f31] bg-[#1c1d1f] px-3 text-white sm:h-16 sm:px-4">
    <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-white/90 transition hover:text-white" aria-label="Quay lại khóa học">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span class="hidden sm:inline">Quay lại</span>
    </a>

    <div class="hidden min-w-0 flex-1 lg:block">
        <p class="truncate text-sm font-semibold">{{ $course->title }}</p>
    </div>

    <div class="ml-auto flex items-center gap-2 sm:gap-3">
        <div class="hidden items-center gap-2 sm:flex">
            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-white/20 sm:w-32">
                <div class="h-full rounded-full bg-[#0056D2] transition-all duration-300" data-header-progress-bar style="width: {{ min(100, max(0, $courseProgress)) }}%"></div>
            </div>
            <span class="text-xs font-semibold text-white/80" data-header-progress-text>{{ number_format($courseProgress, 0) }}%</span>
        </div>

        <a href="{{ route('courses.show', $course->slug) }}#reviews" class="hidden items-center gap-1.5 rounded border border-white/20 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/10 sm:inline-flex">
            <span>★</span> Đưa ra xếp hạng
        </a>

        <!-- Nút Nhận chứng chỉ dạng dropdown -->
        <div class="relative inline-block text-left" data-certificate-dropdown>
            <button
                type="button"
                class="hidden items-center gap-2 rounded border border-white/20 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/10 sm:inline-flex"
                data-cert-dropdown-trigger
            >
                <div class="flex h-5 w-5 items-center justify-center rounded-full border border-purple-500 bg-purple-950/60 text-purple-400">
                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616A7.002 7.002 0 0111 13.914V16h2a1 1 0 110 2H7a1 1 0 110-2h2v-2.086a7.002 7.002 0 01-5.214-6.403L2.553 8.9a1 1 0 11.894-1.79l-1.6.8L9 6.323V3a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                </div>
                <span>Nhận giấy chứng nhận hoàn thành khóa học</span>
                <svg class="h-3 w-3 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Khung dropdown chứa nút tải/nhận -->
            <div
                class="absolute right-0 mt-2 w-72 origin-top-right rounded-lg border border-slate-200 bg-white p-4 shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                data-cert-dropdown-panel
                style="z-index: 100;"
            >
                <p class="text-sm font-medium text-slate-800">Đã hoàn thành {{ $completedLessons }}/{{ $totalLessons }}.</p>
                <div class="mt-3">
                    @php
                        $certificate = \App\Models\Certificate::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->first();
                        $isEligible = (bool) $certificate;
                    @endphp

                    @if($isEligible)
                        <a
                            href="{{ route('certificates.public', $certificate->certificate_code) }}"
                            target="_blank"
                            class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-center text-sm font-bold text-white transition hover:bg-purple-700"
                        >
                            Xem chứng chỉ
                        </a>
                        <button
                            type="button"
                            id="btn-send-cert-email"
                            data-cert-id="{{ $certificate->id }}"
                            data-send-url="{{ route('student.certificates', ['send_email' => 1, 'certificate_id' => $certificate->id]) }}"
                            class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg border border-purple-200 bg-white px-4 py-2 text-center text-sm font-semibold text-purple-700 transition hover:bg-purple-50"
                            onclick="sendCertEmail(this)"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Gửi lại email chứng chỉ
                        </button>
                    @else
                        <button
                            type="button"
                            disabled
                            class="flex w-full items-center justify-center rounded-lg bg-slate-200 px-4 py-2 text-center text-sm font-bold text-slate-400 cursor-not-allowed"
                            title="Bạn cần hoàn thành tất cả video và bài trắc nghiệm để nhận chứng chỉ"
                        >
                            Nhận giấy chứng nhận
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <button
            type="button"
            class="inline-flex h-9 w-9 items-center justify-center rounded border border-white/20 text-white transition hover:bg-white/10 lg:hidden"
            data-toggle-sidebar
            aria-label="Mở nội dung khóa học"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <button
            type="button"
            class="hidden h-9 items-center justify-center rounded border border-white/20 px-3 text-xs font-semibold text-white transition hover:bg-white/10 lg:inline-flex"
            data-toggle-sidebar-desktop
            aria-label="Thu gọn nội dung khóa học"
        >
            Nội dung
        </button>

        @auth
            <a href="{{ auth()->user()->dashboardUrl() }}" class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#0056D2] text-xs font-bold text-white" aria-label="Tài khoản">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </a>
        @endauth
    </div>
</header>

{{-- Toast Notification --}}
<div id="cert-toast" style="display:none; position:fixed; bottom:24px; right:24px; z-index:99999; max-width:380px; border-radius:12px; padding:14px 18px; box-shadow:0 10px 40px rgba(0,0,0,0.18); align-items:center; gap:12px; font-family:inherit;" role="alert">
    <span id="cert-toast-msg" style="font-size:14px; font-weight:500; flex:1;"></span>
    <button onclick="document.getElementById('cert-toast').style.display='none'" style="background:none;border:none;cursor:pointer;opacity:0.6;padding:0;margin-left:8px;" aria-label="Đóng">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

<script>
function sendCertEmail(btn) {
    btn.disabled = true;

    // Hiển thị thông báo NGAY LẬP TỨC khi bấm
    showCertToast('success', '✅ Email chứng chỉ đã được gửi tới hòm thư của bạn!');

    // Gửi request ngầm (fire and forget)
    fetch(btn.dataset.sendUrl, {
        method: 'GET',
        redirect: 'follow',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
    })
    .catch(function() {
        // Nếu thực sự lỗi mạng thì cập nhật lại toast
        showCertToast('error', '❌ Không gửi được email. Vui lòng thử lại sau.');
    })
    .finally(function() {
        btn.disabled = false;
    });
}

function showCertToast(type, msg) {
    var toast = document.getElementById('cert-toast');
    var msgEl = document.getElementById('cert-toast-msg');
    msgEl.textContent = msg;
    if (type === 'success') {
        toast.style.background = '#f0fdf4';
        toast.style.border = '1px solid #bbf7d0';
        toast.style.color = '#166534';
    } else {
        toast.style.background = '#fef2f2';
        toast.style.border = '1px solid #fecaca';
        toast.style.color = '#991b1b';
    }
    toast.style.display = 'flex';
    clearTimeout(window._certToastTimer);
    window._certToastTimer = setTimeout(function() {
        toast.style.display = 'none';
    }, 5000);
}

if (!document.getElementById('cert-toast-style')) {
    var s = document.createElement('style');
    s.id = 'cert-toast-style';
    s.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
    document.head.appendChild(s);
}
</script>
