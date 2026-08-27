import renderMathInElement from 'katex/contrib/auto-render';

const MATH_CONTENT_SELECTOR = '[data-math-content]';
const UNRENDERED_MATH_CONTENT_SELECTOR = `${MATH_CONTENT_SELECTOR}:not([data-math-rendered])`;

const DELIMITERS = [
    { left: '\\[', right: '\\]', display: true },
    { left: '\\(', right: '\\)', display: false },
    { left: '$$', right: '$$', display: true },
];

/**
 * Render LaTeX in escaped text nodes without treating quiz content as HTML.
 *
 * @param {ParentNode|Element|null} root
 */
export function renderMath(root) {
    if (!root || typeof root.querySelectorAll !== 'function') return;

    const elements = typeof Element !== 'undefined' && root instanceof Element && root.matches(MATH_CONTENT_SELECTOR)
        ? [root]
        : [...root.querySelectorAll(UNRENDERED_MATH_CONTENT_SELECTOR)];

    elements.forEach((element) => {
        if (element.dataset.mathRendered === 'true') return;

        const source = element.textContent ?? '';

        try {
            renderMathInElement(element, {
                delimiters: DELIMITERS,
                throwOnError: false,
                strict: 'ignore',
            });
        } catch (error) {
            // Restore source text if an unexpected parser error occurs. The
            // source was inserted with textContent or escaped Blade output.
            element.textContent = source;
            element.dataset.mathRenderError = 'true';
        } finally {
            element.dataset.mathRendered = 'true';
        }
    });
}

/**
 * Render existing targets and observe targets inserted after page load.
 *
 * @param {ParentNode|Element|null} root
 * @returns {MutationObserver|null}
 */
export function observeMath(root = document) {
    if (!root || typeof root.querySelectorAll !== 'function' || typeof MutationObserver === 'undefined') {
        return null;
    }

    renderMath(root);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach(({ addedNodes }) => {
            addedNodes.forEach((node) => {
                if (node.nodeType === Node.ELEMENT_NODE) renderMath(node);
            });
        });
    });

    observer.observe(root, { childList: true, subtree: true });

    return observer;
}
