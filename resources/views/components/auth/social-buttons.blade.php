<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
    @foreach(['google' => 'Google', 'github' => 'GitHub'] as $provider => $label)
        <a href="{{ route('social.redirect', $provider) }}" class="auth-social-btn">
            Đăng nhập {{ $label }}
        </a>
    @endforeach
</div>
