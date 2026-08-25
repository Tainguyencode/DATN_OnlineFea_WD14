@auth
<div
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
    data-session-invalidation-modal
    role="alertdialog"
    aria-modal="true"
    aria-labelledby="session-invalidation-title"
    aria-describedby="session-invalidation-message"
    aria-hidden="true"
>
    <div class="w-full max-w-md overscroll-contain rounded-2xl border border-rose-200 bg-white p-6 shadow-2xl dark:border-rose-900/60 dark:bg-slate-900">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300" aria-hidden="true">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.6 17.1A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.9L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
            </svg>
        </div>

        <h2 id="session-invalidation-title" class="mt-4 text-xl font-bold text-slate-950 dark:text-white">
            Phiên đăng nhập đã kết thúc
        </h2>
        <p id="session-invalidation-message" class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300" data-session-invalidation-message>
            Tài khoản đã được đăng nhập trên thiết bị khác.
        </p>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
            Vui lòng đăng nhập lại để tiếp tục sử dụng hệ thống.
        </p>

        <button
            type="button"
            class="mt-6 inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
            data-session-invalidation-confirm
        >
            Đăng nhập lại
        </button>
    </div>
</div>

<script>
(() => {
    const modal = document.querySelector('[data-session-invalidation-modal]');
    if (!modal || modal.dataset.initialized === 'true') return;

    modal.dataset.initialized = 'true';

    const messageElement = modal.querySelector('[data-session-invalidation-message]');
    const confirmButton = modal.querySelector('[data-session-invalidation-confirm]');
    const loginUrl = @js(route('login'));
    const fallbackMessage = 'Tài khoản đã được đăng nhập trên thiết bị khác.';
    let isVisible = false;

    const redirectToLogin = () => {
        window.location.assign(loginUrl);
    };

    const showInvalidationModal = (message) => {
        if (isVisible) return;

        isVisible = true;
        messageElement.textContent = typeof message === 'string' && message.trim() !== ''
            ? message
            : fallbackMessage;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        confirmButton.focus();
    };

    confirmButton.addEventListener('click', redirectToLogin);
    document.addEventListener('keydown', (event) => {
        if (!isVisible) return;

        if (event.key === 'Escape') {
            event.preventDefault();
            redirectToLogin();
            return;
        }

        if (event.key === 'Tab') {
            event.preventDefault();
            confirmButton.focus();
        }
    });

    window.setInterval(async () => {
        if (isVisible) return;

        try {
            const response = await fetch('/api/session/check', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.status === 401) {
                showInvalidationModal(fallbackMessage);
                return;
            }

            if (!response.ok) return;

            const data = await response.json().catch(() => null);
            if (data?.active === false) {
                showInvalidationModal(data.message);
            }
        } catch (error) {
            console.error('Session validity check failed.', error);
        }
    }, 15000);
})();
</script>
@endauth
