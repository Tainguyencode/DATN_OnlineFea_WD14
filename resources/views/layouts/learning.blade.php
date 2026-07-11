<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Học tập - FEA Learning')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/learning-player.js'])
</head>
<body class="learning-player-body min-h-screen bg-white font-sans text-[#1c1d1f] antialiased">
    @yield('content')
    <div id="learning-toast" class="learning-toast" role="status" aria-live="polite" hidden></div>
</body>
</html>
