@props([
    'title' => 'Admin Dashboard',
    'pageTitle' => 'Admin Dashboard',
    'breadcrumb' => null,
])

@php
    $pendingInstructorsCount = \App\Models\User::where('role', 'instructor')->where('instructor_status', 'pending')->count();
    $pendingCoursesCount = \App\Models\Course::where('status', 'under_review')->count();
    $pendingQuizInvalidationsCount = \App\Models\QuizVersionQuestionInvalidation::pending()->count();

    $menu = [
        [
            'route' => 'admin.dashboard',
            'label' => 'Tổng quan',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>',
        ],
        [
            'id' => 'user-management',
            'label' => 'Quản lý người dùng',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            'children' => [
                [
                    'route' => 'admin.users',
                    'active' => ['admin.users', 'admin.users.*'],
                    'label' => 'Người dùng',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                ],
                [
                    'route' => 'admin.instructors.applications.index',
                    'active' => ['admin.instructors.applications.*'],
                    'label' => 'Quản lý giảng viên',
                    'badge' => $pendingInstructorsCount > 0 ? (string) $pendingInstructorsCount : null,
                    'badge_color' => 'bg-amber-500',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0l-3 3m3-3l3 3M9 7h6"/></svg>',
                ],
                [
                    'route' => 'admin.roles.index',
                    'active' => ['admin.roles.*'],
                    'label' => 'Vai trò',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 4h10a2 2 0 012 2v14l-7-3-7 3V6a2 2 0 012-2z"/></svg>',
                ],
            ],
        ],
        [
            'id' => 'course-management',
            'label' => 'Quản lý khóa học',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>',
            'children' => [
                [
                    'route' => 'admin.courses.index',
                    'active' => ['admin.courses.index', 'admin.courses.show', 'admin.courses.students'],
                    'label' => 'Khóa học',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>',
                ],
                [
                    'route' => 'admin.categories.index',
                    'active' => ['admin.categories.*'],
                    'label' => 'Danh mục khóa học',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h6M4 10h6m-6 4h6m6-8h4m-4 4h4m-4 4h4M4 18h16"/></svg>',
                ],
                [
                    'route' => 'admin.learning-paths.index',
                    'active' => ['admin.learning-paths.*'],
                    'label' => 'Lộ trình học tập',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
                ],
                [
                    'route' => 'admin.instructors.requirements.index',
                    'active' => ['admin.instructors.requirements.*'],
                    'label' => 'Yêu cầu hồ sơ ngành',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                ],
                [
                    'route' => 'admin.courses.pending',
                    'active' => ['admin.courses.pending', 'admin.courses.review', 'admin.course-reviews.*'],
                    'label' => 'Duyệt khóa học',
                    'badge' => $pendingCoursesCount > 0 ? (string) $pendingCoursesCount : null,
                    'badge_color' => 'bg-amber-500',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                ],
                [
                    'route' => 'admin.student-reviews.index',
                    'active' => ['admin.student-reviews.*'],
                    'label' => 'Đánh giá học viên',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                ],
                [
                    'route' => 'admin.quiz-invalidations.index',
                    'active' => ['admin.quiz-invalidations.*'],
                    'label' => 'Hủy câu hỏi Quiz',
                    'badge' => $pendingQuizInvalidationsCount > 0 ? (string) $pendingQuizInvalidationsCount : null,
                    'badge_color' => 'bg-rose-500',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.9 2.6 17.1A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.9L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>',
                ],
            ],
        ],
        [
            'id' => 'finance',
            'label' => 'Tài chính',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2"/></svg>',
            'children' => [
                [
                    'route' => 'admin.revenue',
                    'active' => ['admin.revenue'],
                    'label' => 'Doanh thu',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2"/></svg>',
                ],
                [
                    'route' => 'admin.commissions.index',
                    'active' => ['admin.commissions.*'],
                    'label' => 'Quản lý chiết khấu',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                ],
                [
                    'route' => 'admin.withdrawals.index',
                    'active' => ['admin.withdrawals.*'],
                    'label' => 'Duyệt Rút tiền GV',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                ],
                [
                    'route' => 'admin.refunds.index',
                    'active' => ['admin.refunds.*'],
                    'label' => 'Yêu cầu Hoàn tiền',
                    'badge' => \App\Models\Refund::where('status', 'pending')->count() > 0 ? (string) \App\Models\Refund::where('status', 'pending')->count() : null,
                    'badge_color' => 'bg-purple-600',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>',
                ],
                [
                    'route' => 'admin.coupons.index',
                    'active' => ['admin.coupons.index', 'admin.coupons.create', 'admin.coupons.edit'],
                    'label' => 'Mã giảm giá',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>',
                ],
                [
                    'route' => 'admin.coupons.reward_config',
                    'active' => ['admin.coupons.reward_config'],
                    'label' => 'Cấu hình thưởng TOP',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V6a2 2 0 10-2 2h2zm0 13C10.832 18.477 9.246 18 7.5 18S4.168 18.477 3 19.253V6.253C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0 13C13.168 18.477 14.754 18 16.5 18c1.747 0 3.332.477 4.5 1.253V6.253C19.832 5.477 18.247 5 16.5 5c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                ],
                [
                    'route' => 'admin.coupons.reward_history',
                    'active' => ['admin.coupons.reward_history'],
                    'label' => 'Lịch sử thưởng TOP',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                ],
            ],
        ],
        [
            'id' => 'system-support',
            'label' => 'Hệ thống & hỗ trợ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>',
            'children' => [
                [
                    'route' => 'admin.notifications.index',
                    'active' => ['admin.notifications.*'],
                    'label' => 'Thông báo',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>',
                ],
                [
                    'route' => 'admin.support-tickets.index',
                    'active' => ['admin.support-tickets.*'],
                    'label' => 'Ticket hỗ trợ',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>',
                ],
                [
                    'route' => 'admin.activity-logs',
                    'active' => ['admin.activity-logs'],
                    'label' => 'Nhật ký',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                ],
            ],
        ],
        [
            'route' => 'admin.homepage',
            'label' => 'Trang chủ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
        ],
        [
            'route' => 'admin.profile',
            'label' => 'Hồ sơ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
        ],
    ];
@endphp

<x-layouts.dashboard
    role="admin"
    roleLabel="Quản trị viên"
    accent="rose"
    :menu="$menu"
    :title="$title"
    :pageTitle="$pageTitle"
    :breadcrumb="$breadcrumb"
>
    {{ $slot }}
</x-layouts.dashboard>
