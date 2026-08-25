import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

function initializeFlashMessages() {
    document.querySelectorAll('[data-flash-message="success"]').forEach((flash) => {
        if (flash.dataset.flashDismissScheduled === 'true') return;

        flash.dataset.flashDismissScheduled = 'true';
        window.setTimeout(() => {
            flash.classList.add('flash-message--fading');

            window.setTimeout(() => {
                flash.remove();
            }, 250);
        }, 2000);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFlashMessages, { once: true });
} else {
    initializeFlashMessages();
}

function syncThemeToggleState() {
    const isDark = document.documentElement.classList.contains('dark');

    document.querySelectorAll('[onclick="toggleTheme()"], [data-theme-toggle]').forEach((toggle) => {
        toggle.setAttribute('aria-pressed', String(isDark));
    });
}

function applyTheme(theme, persist = true) {
    const isDark = theme === 'dark';
    document.documentElement.classList.toggle('dark', isDark);

    if (persist) {
        try {
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        } catch (error) {
            // Keep the current page usable when storage is unavailable.
        }
    }

    syncThemeToggleState();
    window.dispatchEvent(new CustomEvent('themechange', { detail: { theme: isDark ? 'dark' : 'light' } }));
}

// Bind functions to window so they are globally accessible from inline HTML event handlers.
window.toggleTheme = function () {
    applyTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
};

window.addEventListener('storage', (event) => {
    if (event.key === 'theme' && (event.newValue === 'dark' || event.newValue === 'light')) {
        applyTheme(event.newValue, false);
    }
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', syncThemeToggleState, { once: true });
} else {
    syncThemeToggleState();
}

function initAdminSidebar() {
    const toggles = document.querySelectorAll('[data-sidebar-menu-toggle]');

    toggles.forEach((toggle) => {
        if (toggle.dataset.sidebarBound === 'true') return;

        toggle.dataset.sidebarBound = 'true';
        const panel = document.getElementById(toggle.getAttribute('aria-controls'));
        const chevron = toggle.querySelector('[data-sidebar-menu-chevron]');

        if (!panel) return;

        toggle.addEventListener('click', () => {
            const willOpen = toggle.getAttribute('aria-expanded') !== 'true';

            toggles.forEach((otherToggle) => {
                if (otherToggle === toggle) return;

                const otherPanel = document.getElementById(otherToggle.getAttribute('aria-controls'));
                const otherChevron = otherToggle.querySelector('[data-sidebar-menu-chevron]');

                otherToggle.setAttribute('aria-expanded', 'false');
                otherPanel?.classList.remove('grid-rows-[1fr]');
                otherPanel?.classList.add('grid-rows-[0fr]');
                otherPanel?.setAttribute('aria-hidden', 'true');
                otherPanel?.toggleAttribute('inert', true);
                otherChevron?.classList.remove('rotate-90', 'text-white');
            });

            toggle.setAttribute('aria-expanded', String(willOpen));
            panel.classList.toggle('grid-rows-[1fr]', willOpen);
            panel.classList.toggle('grid-rows-[0fr]', !willOpen);
            panel.setAttribute('aria-hidden', String(!willOpen));
            panel.toggleAttribute('inert', !willOpen);
            chevron?.classList.toggle('rotate-90', willOpen);
            chevron?.classList.toggle('text-white', willOpen);
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminSidebar, { once: true });
} else {
    initAdminSidebar();
}
