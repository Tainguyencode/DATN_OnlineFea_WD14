@props([
    'standalone' => false,
    'title' => config('app.name', 'FEA Learning'),
])

@if ($standalone)
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white">
    <x-toast-container />
@endif

<div class="bg-white">
    <div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-5 py-10 sm:py-14">
        <div class="mx-auto w-full max-w-[420px]">
            {{ $slot }}
        </div>
    </div>
</div>

@if ($standalone)
</body>
</html>
@endif
