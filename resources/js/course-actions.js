const COURSE_ACTIONS_SELECTOR = '[data-course-actions]';
const COURSE_ACTION_STATES = new WeakMap();

function getCourseActionParts(wrapper) {
    return {
        trigger: wrapper.querySelector('[data-course-actions-trigger]'),
        menu: wrapper.querySelector('[data-course-actions-menu]'),
        chevron: wrapper.querySelector('[data-course-actions-chevron]'),
    };
}

function getCourseActionState(wrapper) {
    if (!COURSE_ACTION_STATES.has(wrapper)) {
        COURSE_ACTION_STATES.set(wrapper, {
            openedByClick: false,
            openedByHover: false,
            suppressFocus: false,
            suppressHover: false,
        });
    }

    return COURSE_ACTION_STATES.get(wrapper);
}

function hideCourseActionMenu(wrapper, focusTrigger = false) {
    const { trigger, menu, chevron } = getCourseActionParts(wrapper);

    if (!trigger || !menu) return;

    trigger.setAttribute('aria-expanded', 'false');
    menu.hidden = true;
    wrapper.classList.remove('is-open');
    chevron?.classList.remove('rotate-180');

    if (focusTrigger) trigger.focus();
}

function closeCourseActionMenu(wrapper, focusTrigger = false) {
    const state = getCourseActionState(wrapper);
    state.openedByClick = false;
    state.openedByHover = false;
    state.suppressFocus = false;
    state.suppressHover = false;

    if (focusTrigger) state.suppressFocus = true;
    hideCourseActionMenu(wrapper, focusTrigger);
    state.suppressFocus = false;
}

function openCourseActionMenu(wrapper, focusFirstItem = false) {
    const { trigger, menu, chevron } = getCourseActionParts(wrapper);

    if (!trigger || !menu) return;

    document.querySelectorAll(COURSE_ACTIONS_SELECTOR).forEach((otherWrapper) => {
        if (otherWrapper !== wrapper) closeCourseActionMenu(otherWrapper);
    });

    trigger.setAttribute('aria-expanded', 'true');
    menu.hidden = false;
    wrapper.classList.add('is-open');
    chevron?.classList.add('rotate-180');

    if (focusFirstItem) menu.querySelector('[data-course-actions-item]')?.focus();
}

function moveCourseActionFocus(menu, direction) {
    const items = [...menu.querySelectorAll('[data-course-actions-item]')];
    const currentIndex = items.indexOf(document.activeElement);

    if (!items.length) return;

    const nextIndex = currentIndex === -1
        ? 0
        : (currentIndex + direction + items.length) % items.length;

    items[nextIndex].focus();
}

function initCourseActionMenus() {
    const wrappers = document.querySelectorAll(COURSE_ACTIONS_SELECTOR);

    wrappers.forEach((wrapper) => {
        if (wrapper.dataset.courseActionsBound === 'true') return;

        const { trigger, menu } = getCourseActionParts(wrapper);
        if (!trigger || !menu) return;

        wrapper.dataset.courseActionsBound = 'true';
        const state = getCourseActionState(wrapper);
        let lastPointerType = null;

        trigger.addEventListener('pointerdown', (event) => {
            lastPointerType = event.pointerType;
            window.setTimeout(() => {
                lastPointerType = null;
            }, 0);
        });

        wrapper.addEventListener('pointerenter', (event) => {
            if (event.pointerType === 'mouse' && !state.suppressHover) {
                state.openedByHover = true;
                openCourseActionMenu(wrapper);
            }
        });

        wrapper.addEventListener('pointerleave', (event) => {
            if (event.pointerType === 'mouse') closeCourseActionMenu(wrapper);
        });

        trigger.addEventListener('click', () => {
            if (state.openedByClick) {
                state.openedByClick = false;
                state.openedByHover = false;
                state.suppressHover = true;
                hideCourseActionMenu(wrapper);
            } else {
                state.openedByClick = true;
                openCourseActionMenu(wrapper);
            }
        });

        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openCourseActionMenu(wrapper, event.key === 'ArrowDown');
            } else if (event.key === 'Escape') {
                event.preventDefault();
                closeCourseActionMenu(wrapper, true);
            }
        });

        wrapper.addEventListener('focusin', (event) => {
            if (
                lastPointerType !== 'touch'
                && lastPointerType !== 'mouse'
                && !state.suppressFocus
                && (event.target === trigger || menu.contains(event.target))
            ) {
                openCourseActionMenu(wrapper);
            }
        });

        wrapper.addEventListener('focusout', (event) => {
            if (!wrapper.contains(event.relatedTarget)) closeCourseActionMenu(wrapper);
        });

        menu.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                moveCourseActionFocus(menu, 1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                moveCourseActionFocus(menu, -1);
            } else if (event.key === 'Home') {
                event.preventDefault();
                menu.querySelector('[data-course-actions-item]')?.focus();
            } else if (event.key === 'End') {
                event.preventDefault();
                const items = menu.querySelectorAll('[data-course-actions-item]');
                items[items.length - 1]?.focus();
            } else if (event.key === 'Escape') {
                event.preventDefault();
                closeCourseActionMenu(wrapper, true);
            }
        });

        menu.addEventListener('click', () => {
            window.setTimeout(() => closeCourseActionMenu(wrapper), 0);
        });
    });
}

document.addEventListener('click', (event) => {
    if (!event.target.closest(COURSE_ACTIONS_SELECTOR)) {
        document.querySelectorAll(COURSE_ACTIONS_SELECTOR).forEach((wrapper) => closeCourseActionMenu(wrapper));
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll(COURSE_ACTIONS_SELECTOR).forEach((wrapper) => closeCourseActionMenu(wrapper));
    }
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCourseActionMenus, { once: true });
} else {
    initCourseActionMenus();
}
