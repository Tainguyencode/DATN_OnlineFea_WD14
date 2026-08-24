@props([
    'title' => 'Instructor Dashboard',
    'pageTitle' => 'Instructor Dashboard',
    'pageTitleClass' => 'text-base sm:text-lg font-semibold leading-tight text-slate-900 truncate',
    'breadcrumb' => null,
])

@php
    $currentUser = auth()->user();
    $instructorDiscussionsPendingCount = 0;
    if ($currentUser && $currentUser->isInstructor()) {
        $instructorCourseIds = \App\Models\Course::where('instructor_id', $currentUser->id)->pluck('id');
        if ($instructorCourseIds->isNotEmpty()) {
            $discussions = \App\Models\Discussion::where(function ($q) use ($instructorCourseIds) {
                $q->whereIn('course_id', $instructorCourseIds)
                  ->orWhereHas('lesson', function ($lq) use ($instructorCourseIds) {
                      $lq->whereIn('course_id', $instructorCourseIds);
                  });
            })->with('replies')->get();

            $instructorDiscussionsPendingCount = $discussions->filter(fn ($d) => $d->needsReply())->count();
        }
    }

    $menu = [
        [
            'route' => 'instructor.dashboard',
            'label' => 'Tổng quan',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'
        ],
        [
            'label' => 'Quản lý khóa học',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
            'children' => [
                [
                    'route' => 'instructor.courses.index',
                    'active' => [
                        'instructor.courses.index',
                        'instructor.courses.edit',
                        'instructor.courses.curriculum',
                        'instructor.courses.lessons.*',
                        'instructor.courses.students',
                        'instructor.courses.students.*',
                        'instructor.courses.sections.*',
                        'instructor.courses.content-updates.*',
                        'instructor.courses.chapters.*',
                        'instructor.courses.submit.*',
                    ],
                    'label' => 'Khóa học',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                ],
                [
                    'route' => 'instructor.courses.create',
                    'label' => 'Tạo khóa học',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>',
                ],
                [
                    'route' => 'instructor.submissions.index',
                    'active' => ['instructor.submissions.*'],
                    'label' => 'Bài tập đã nộp',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                ],
                [
                    'route' => 'instructor.coupons.index',
                    'active' => ['instructor.coupons.*'],
                    'label' => 'Mã giảm giá',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>',
                ],
                [
                    'route' => 'instructor.learning-paths.index',
                    'active' => ['instructor.learning-paths.*'],
                    'label' => 'Lộ trình học tập',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
                ],
            ],
        ],

        [
            'label' => 'Học viên & tương tác',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
            'children' => [
                [
                    'route' => 'instructor.reviews.index',
                    'active' => ['instructor.reviews.*'],
                    'label' => 'Đánh giá học viên',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-7 8l-4-4V6a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2H8l-2 2z"/></svg>',
                ],
                [
                    'route' => 'instructor.discussions.index',
                    'active' => ['instructor.discussions.*'],
                    'label' => 'Trao đổi với học viên',
                    'badge' => $instructorDiscussionsPendingCount > 0 ? $instructorDiscussionsPendingCount : null,
                    'badge_color' => 'bg-amber-500',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                ],
                [
                    'route' => 'instructor.comments.index',
                    'active' => ['instructor.comments.*'],
                    'label' => 'Bình luận bài học',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>',
                ],
                [
                    'route' => 'study-groups.index',
                    'active' => ['study-groups.*'],
                    'label' => 'Nhóm học tập',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                ],
            ],
        ],
        [
            'label' => 'Tài chính',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2"/></svg>',
            'children' => [
                [
                    'route' => 'instructor.revenue',
                    'label' => 'Doanh thu',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2"/></svg>',
                ],
                [
                    'route' => 'instructor.wallet.index',
                    'label' => 'Ví tiền & Rút tiền',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>',
                ],
            ],
        ],
        [
            'label' => 'Hệ thống & hỗ trợ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>',
            'children' => [
                [
                    'route' => 'notifications.index',
                    'label' => 'Thông báo',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>',
                ],
                [
                    'route' => 'support.tickets.index',
                    'active' => ['support.tickets.*'],
                    'label' => 'Ticket hỗ trợ',
                    'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>',
                ],
            ],
        ],
        [
            'route' => 'instructor.profile',
            'active' => ['instructor.profile', 'instructor.profile.*', 'instructor.pending'],
            'label' => 'Hồ sơ & Chứng chỉ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
        ],
    ];

    $currentUser = auth()->user();
@endphp

<x-layouts.dashboard
    role="instructor"
    roleLabel="Giảng viên"
    accent="emerald"
    :menu="$menu"
    :title="$title"
    :pageTitle="$pageTitle"
    :pageTitleClass="$pageTitleClass"
    :breadcrumb="$breadcrumb"
>
    @if(config('auth.email_verification_enabled', true) && auth()->check() && ! auth()->user()->hasVerifiedEmail())
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-5 text-amber-900 shadow-sm dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-100">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-bold">Tài khoản giảng viên chưa xác thực email.</p>
                    <p class="mt-1 text-sm font-medium text-amber-800 dark:text-amber-200">Bạn vẫn có thể xem khu vực giảng viên, nhưng cần xác thực email trước khi lưu thay đổi hoặc gửi khóa học.</p>
                </div>
                <form method="POST" action="{{ route('verification.send') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-amber-600 px-4 text-sm font-bold text-white transition hover:bg-amber-700">
                        Gửi lại email
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Banner cảnh báo trạng thái Hồ sơ Giảng viên trên Dashboard/Toàn trang --}}
    @if($currentUser && $currentUser->role === 'instructor' && ! request()->routeIs('instructor.profile*'))
        @if($currentUser->isLocked())
            <div class="mb-6 rounded-2xl border-2 border-rose-500/40 bg-gradient-to-r from-rose-900 to-slate-900 p-5 text-white shadow-md">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-sm">🔒 Tài khoản giảng viên của bạn đang bị tạm khóa.</p>
                            <p class="mt-0.5 text-xs text-rose-200">{{ $currentUser->locked_reason ?: 'Bạn chưa hoàn thiện hồ sơ chứng chỉ trong thời hạn 7 ngày.' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('instructor.profile') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-xs font-bold text-rose-900 transition hover:bg-rose-50 shrink-0">
                        Kiểm tra Hồ sơ & Cấp lại
                    </a>
                </div>
            </div>
        @elseif($currentUser->instructor_status === 'pending')
            <div class="mb-6 rounded-2xl border border-amber-300 bg-amber-50/90 p-4 text-amber-900 shadow-sm dark:border-amber-900/50 dark:bg-slate-900 dark:text-amber-100">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-xs sm:text-sm">⚠️ Hồ sơ giảng viên đang chờ xét duyệt.</p>
                            <p class="mt-0.5 text-xs text-amber-800 dark:text-amber-300">Bạn vẫn có thể sử dụng các chức năng giảng viên bình thường. Vui lòng hoàn thiện và cập nhật hồ sơ để duy trì trạng thái giảng viên.</p>
                        </div>
                    </div>
                    <a href="{{ route('instructor.profile') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-amber-700 shrink-0">
                        Cập nhật hồ sơ
                    </a>
                </div>
            </div>
        @elseif($currentUser->instructor_status === 'rejected')
            <div class="mb-6 rounded-2xl border border-rose-300 bg-rose-50/90 p-4 text-rose-900 shadow-sm dark:border-rose-900/50 dark:bg-slate-900 dark:text-rose-100">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-bold text-xs sm:text-sm text-rose-800 dark:text-rose-300">⚠️ Hồ sơ giảng viên cần bổ sung thông tin.</p>
                        <p class="mt-0.5 text-xs text-rose-700 dark:text-rose-400"><span class="font-bold">Lý do:</span> {{ $currentUser->rejected_reason ?: 'Vui lòng bổ sung hồ sơ chứng chỉ.' }}</p>
                    </div>
                    <a href="{{ route('instructor.profile') }}" class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-rose-700 shrink-0">
                        Cập nhật hồ sơ
                    </a>
                </div>
            </div>
        @endif
    @endif

    {{ $slot }}
</x-layouts.dashboard>
