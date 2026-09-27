const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function navigation(marked, type = 'navigate') {
    const handlers = {};
    const state = { removed: 0, reloads: 0 };
    vm.runInNewContext(fs.readFileSync('resources/js/navigation-state.js', 'utf8'), {
        window: {
            addEventListener: (name, handler) => { handlers[name] = handler; },
            performance: { getEntriesByType: () => [{type}] },
            location: { reload: () => { state.reloads++; } },
        },
        document: {
            querySelectorAll: () => [{ remove: () => { state.removed++; } }],
            querySelector: () => marked ? {} : null,
        },
    });
    return { handlers, state };
}

test('leaving a page removes flash notifications from its cached snapshot', () => {
    const {handlers, state} = navigation(true);
    handlers.pagehide();
    assert.equal(state.removed, 1);
});

test('back/forward cache and ordinary history navigation refresh marked order pages', () => {
    for (const [persisted, type] of [[true, 'navigate'], [false, 'back_forward']]) {
        const {handlers, state} = navigation(true, type);
        handlers.pageshow({persisted});
        assert.equal(state.reloads, 1);
        assert.equal(state.removed, 1);
    }
});

test('normal navigation, reload and unrelated forms do not trigger a reload loop', () => {
    for (const type of ['navigate', 'reload']) {
        const {handlers, state} = navigation(true, type);
        handlers.pageshow({persisted: false});
        assert.equal(state.reloads, 0);
        assert.equal(state.removed, 0);
    }
    const {handlers, state} = navigation(false);
    handlers.pageshow({persisted: true});
    assert.equal(state.reloads, 0);
});
