import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

const TOAST_TYPES = new Set(['success', 'error', 'warning', 'info']);
const DEFAULT_TOAST_DURATION = 3000;
const TOAST_ICON_ELEMENTS = {
    success: [
        { tag: 'path', attributes: { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'm5 12 4 4L19 6' } },
    ],
    error: [
        { tag: 'path', attributes: { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 8v4m0 4h.01M10.3 3.9 2.6 17.1A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.9L13.7 3.9a2 2 0 0 0-3.4 0Z' } },
    ],
    warning: [
        { tag: 'path', attributes: { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 9v4m0 4h.01M10.3 3.9 2.6 17.1A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.9L13.7 3.9a2 2 0 0 0-3.4 0Z' } },
    ],
    info: [
        { tag: 'circle', attributes: { cx: '12', cy: '12', r: '9' } },
        { tag: 'path', attributes: { 'stroke-linecap': 'round', d: 'M12 11v5m0-8h.01' } },
    ],
};

function normalizeToastType(type) {
    return TOAST_TYPES.has(type) ? type : 'info';
}

function normalizeToastDuration(duration) {
    const parsedDuration = Number.parseInt(duration, 10);

    return Number.isFinite(parsedDuration) && parsedDuration > 0
        ? parsedDuration
        : DEFAULT_TOAST_DURATION;
}

function getToastAccessibility(type) {
    return type === 'error'
        ? { role: 'alert', live: 'assertive' }
        : { role: 'status', live: 'polite' };
}

function createToastSvg(elements) {
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '2');
    svg.setAttribute('aria-hidden', 'true');

    elements.forEach(({ tag, attributes }) => {
        const element = document.createElementNS('http://www.w3.org/2000/svg', tag);

        Object.entries(attributes).forEach(([name, value]) => {
            element.setAttribute(name, value);
        });

        svg.append(element);
    });

    return svg;
}

function createToastIcon(type) {
    const icon = document.createElement('span');
    icon.className = 'app-toast__icon';
    icon.setAttribute('aria-hidden', 'true');
    icon.append(createToastSvg(TOAST_ICON_ELEMENTS[type]));

    return icon;
}

function createToastDismissButton() {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'app-toast__close';
    button.setAttribute('data-toast-dismiss', '');
    button.setAttribute('aria-label', 'Đóng thông báo');
    button.append(createToastSvg([
        { tag: 'path', attributes: { 'stroke-linecap': 'round', d: 'm6 6 12 12M18 6 6 18' } },
    ]));

    return button;
}

function getToastContainer() {
    const existingContainer = document.querySelector('[data-toast-container]');

    if (existingContainer) return existingContainer;
    if (!document.body) return null;

    const container = document.createElement('div');
    container.className = 'toast-container';
    container.setAttribute('data-toast-container', '');
    container.setAttribute('aria-label', 'Thông báo hệ thống');
    document.body.append(container);

    return container;
}

function initializeToasts() {
    document.querySelectorAll('[data-toast]').forEach(initializeToast);
}

function initializeToast(toast) {
    if (toast.dataset.toastInitialized === 'true') return;

    toast.dataset.toastInitialized = 'true';

    const duration = normalizeToastDuration(toast.dataset.toastDuration);
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let remaining = duration;
    let startedAt = 0;
    let timerId = null;
    let dismissed = false;
    let isHovered = false;
    let isFocused = false;

    const clearTimer = () => {
        if (timerId === null) return;

        window.clearTimeout(timerId);
        timerId = null;
    };

    const dismissToast = () => {
        if (dismissed) return;

        dismissed = true;
        clearTimer();
        toast.classList.add('app-toast--dismissing');
        toast.setAttribute('aria-hidden', 'true');

        window.setTimeout(() => toast.remove(), prefersReducedMotion ? 0 : 220);
    };

    const pauseTimer = () => {
        if (timerId === null || dismissed) return;

        remaining = Math.max(0, remaining - (performance.now() - startedAt));
        clearTimer();
    };

    const startTimer = () => {
        if (dismissed || timerId !== null || isHovered || isFocused) return;

        if (remaining <= 0) {
            dismissToast();
            return;
        }

        startedAt = performance.now();
        timerId = window.setTimeout(dismissToast, remaining);
    };

    toast.addEventListener('mouseenter', () => {
        isHovered = true;
        pauseTimer();
    });
    toast.addEventListener('mouseleave', () => {
        isHovered = false;
        startTimer();
    });
    toast.addEventListener('focusin', () => {
        isFocused = true;
        pauseTimer();
    });
    toast.addEventListener('focusout', (event) => {
        if (toast.contains(event.relatedTarget)) return;

        isFocused = false;
        startTimer();
    });
    toast.querySelector('[data-toast-dismiss]')?.addEventListener('click', dismissToast);

    startTimer();
}

function showAppToast({ type = 'info', message = '', duration = DEFAULT_TOAST_DURATION } = {}) {
    const container = getToastContainer();

    if (!container) return null;

    const toastType = normalizeToastType(type);
    const accessibility = getToastAccessibility(toastType);
    const toast = document.createElement('div');
    toast.className = `app-toast app-toast--${toastType}`;
    toast.setAttribute('data-toast', '');
    toast.dataset.toastDuration = String(normalizeToastDuration(duration));
    toast.setAttribute('role', accessibility.role);
    toast.setAttribute('aria-live', accessibility.live);

    const messageElement = document.createElement('p');
    messageElement.className = 'app-toast__message';
    messageElement.textContent = message === null || message === undefined ? '' : String(message);

    toast.append(createToastIcon(toastType), messageElement, createToastDismissButton());
    container.append(toast);
    initializeToast(toast);

    return toast;
}

window.AppToast = {
    show: showAppToast,
};

Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeToasts, { once: true });
} else {
    initializeToasts();
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
