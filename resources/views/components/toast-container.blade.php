@php
    $toastTypes = [
        'success' => ['role' => 'status', 'live' => 'polite'],
        'error' => ['role' => 'alert', 'live' => 'assertive'],
        'warning' => ['role' => 'status', 'live' => 'polite'],
        'info' => ['role' => 'status', 'live' => 'polite'],
    ];

    $toasts = [];

    foreach ($toastTypes as $type => $attributes) {
        $messages = session($type);

        if ($messages === null || $messages === '') {
            continue;
        }

        foreach (is_iterable($messages) ? $messages : [$messages] as $message) {
            if ($message !== null && $message !== '') {
                $toasts[] = compact('type', 'attributes', 'message');
            }
        }
    }
@endphp

@if ($toasts !== [])
    <div class="toast-container" data-toast-container aria-label="Thông báo hệ thống">
        @foreach ($toasts as $toast)
            <div
                class="app-toast app-toast--{{ $toast['type'] }}"
                data-toast
                data-toast-duration="3000"
                role="{{ $toast['attributes']['role'] }}"
                @if ($toast['attributes']['live']) aria-live="{{ $toast['attributes']['live'] }}" @endif
            >
                <span class="app-toast__icon" aria-hidden="true">
                    @if ($toast['type'] === 'success')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                    @elseif ($toast['type'] === 'error')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M10.3 3.9 2.6 17.1A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.9L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>
                    @elseif ($toast['type'] === 'warning')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.6 17.1A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.9L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 11v5m0-8h.01"/></svg>
                    @endif
                </span>

                <p class="app-toast__message">{{ $toast['message'] }}</p>

                <button type="button" class="app-toast__close" data-toast-dismiss aria-label="Đóng thông báo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
