const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('resources/js/learning-player.js', 'utf8');
const code = source.slice(source.indexOf('function initReadingProgress()'), source.indexOf('function initQuizPlayer()'));

test('reading waits for 30 visible seconds, saves once and updates the sidebar', async () => {
    let tick, requests = 0, cleared = 0, sidebar;
    const document = {hidden: false, querySelector: () => ({dataset: {lessonType: 'document', progressUrl: '/progress'}})};
    vm.runInNewContext(code + '\ninitReadingProgress();', {
        document, window: {addEventListener() {}},
        setInterval(fn) {tick = fn; return 1;}, clearInterval() {cleared++;},
        getCsrfToken: () => 'token', showToast() {}, updateHeaderProgress() {},
        updateCurrentLessonProgress: (...args) => {sidebar = args;},
        fetch: async () => {requests++; return {ok: true, json: async () => ({lesson_completed: true, course_progress: 50})};},
    });
    document.hidden = true;
    for (let i = 0; i < 30; i++) await tick();
    assert.equal(requests, 0);
    document.hidden = false;
    for (let i = 0; i < 29; i++) await tick();
    assert.equal(requests, 0);
    await tick();
    await tick();
    assert.equal(requests, 1);
    assert.equal(cleared, 1);
    assert.deepEqual(sidebar, [100, true]);
});
