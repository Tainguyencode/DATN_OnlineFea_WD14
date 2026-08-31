const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('public/js/s3-multipart-uploader.js', 'utf8');
const code = source.slice(source.indexOf('async function refreshCurriculumLesson('), source.indexOf('function appendLessonToCurriculumDOM('));

function setup(ok) {
    let replacement = null;
    const fresh = {id: 'lesson-item-12', status: 'published'};
    const current = {id: fresh.id, replaceWith(node) { replacement = node; }};
    const context = vm.createContext({
        window: {location: {href: '/curriculum'}},
        document: {getElementById: () => current, importNode: node => node},
        DOMParser: class { parseFromString() { return {getElementById: id => id === fresh.id ? fresh : null}; } },
        fetch: async () => ({ok, redirected: false, text: async () => '<html></html>'}),
    });
    vm.runInContext(code, context);
    return {context, fresh, replacement: () => replacement};
}

test('saving refreshes only the changed lesson row using server HTML', async () => {
    const state = setup(true);
    await state.context.refreshCurriculumLesson(12);
    assert.equal(state.replacement(), state.fresh);
});

test('failed refresh leaves the existing row intact', async () => {
    const state = setup(false);
    await assert.rejects(state.context.refreshCurriculumLesson(12));
    assert.equal(state.replacement(), null);
});
