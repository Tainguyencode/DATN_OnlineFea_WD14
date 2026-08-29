@props([
    'title' => 'Student Dashboard',
    'pageTitle' => 'Student Dashboard',
    'breadcrumb' => null,
])

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - FEA Learning</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-student-public-layout class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-200">
    <x-public.header />

    <x-toast-container />

    <main class="flex-1">
        <div class="ui-container py-6 sm:py-8">
            <div class="mb-6">
                <h1 class="text-2xl font-extrabold leading-tight text-slate-950 dark:text-white sm:text-3xl">{{ $pageTitle }}</h1>
                @if($breadcrumb)
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ $breadcrumb }}</p>
                @endif
            </div>

            {{ $slot }}
        </div>
    </main>

    @include('components.public.footer')

    <x-session-invalidation-monitor />
</body>
</html>
