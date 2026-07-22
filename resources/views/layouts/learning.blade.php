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
    
    @auth
    <script>
        setInterval(function() {
            fetch('/api/session/check', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.status === 401) {
                    alert('Tài khoản đã được đăng nhập trên thiết bị khác.');
                    window.location.href = '/login';
                }
                return response.json();
            }).then(data => {
                if (data && data.active === false) {
                    alert(data.message || 'Tài khoản đã được đăng nhập trên thiết bị khác.');
                    window.location.href = '/login';
                }
            }).catch(e => console.error(e));
        }, 15000);
    </script>
    @endauth
</body>
</html>
