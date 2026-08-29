<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Khu vực học viên' }} - FEA Learning</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-student-public-layout data-student-dashboard-shell
      x-data="{ studentSidebarOpen: false }"
      x-on:open-student-sidebar.window="studentSidebarOpen = true; document.body.classList.add('overflow-hidden')"
      x-on:close-student-sidebar.window="studentSidebarOpen = false; document.body.classList.remove('overflow-hidden')"
      x-on:keydown.escape.window="$dispatch('close-student-sidebar')"
      class="min-h-screen overflow-x-hidden bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <x-public.header :student-dashboard="true" />
    <x-toast-container />

    <div class="flex w-full">
        <aside class="sticky left-0 top-20 hidden h-[calc(100vh-5rem)] w-72 shrink-0 border-r border-slate-200 bg-white lg:block dark:border-slate-800 dark:bg-slate-900">
            @include('student.dashboard.partials.sidebar', ['mobile' => false])
        </aside>

        @include('student.dashboard.partials.mobile-menu')

        <main id="student-main-content" class="min-w-0 flex-1 px-4 py-5 sm:px-6 sm:py-7 xl:px-10 xl:py-9">
            <div class="mx-auto w-full max-w-[1440px]">
                @if($pageTitle ?? false)
                    <x-student.dashboard.page-header :title="$pageTitle" :description="$breadcrumb" />
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    <x-session-invalidation-monitor />
</body>
</html>
