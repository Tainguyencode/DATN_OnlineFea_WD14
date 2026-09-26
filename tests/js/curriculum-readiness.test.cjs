const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

test('readiness updates after completion and can return to incomplete without reload', () => {
    const source = fs.readFileSync('public/js/s3-multipart-uploader.js', 'utf8');
    const nodes = Object.fromEntries(['count', 'bar', 'percent', 'items'].map(key =>
        [`[data-readiness-${key}]`, { style: {}, innerHTML: '', textContent: '' }]));
    const context = vm.createContext({ document: { querySelector: selector => nodes[selector] } });
    vm.runInContext(source.slice(source.indexOf('function updateCurriculumReadiness('), source.indexOf('function initCurriculumHlsPolling(')), context);
    vm.runInContext(source.slice(source.indexOf('function escapeHtml('), source.indexOf('async function refreshCurriculumLesson(')), context);
    context.updateCurriculumReadiness([{ label: 'Video', passed: true }, { label: 'HLS', passed: false, message: '<script>bad()</script>' }]);
    assert.equal(nodes['[data-readiness-bar]'].style.width, '50%');
    assert.ok(nodes['[data-readiness-items]'].innerHTML.includes('&lt;script&gt;'));
    context.updateCurriculumReadiness([{ label: 'Video', passed: true }, { label: 'HLS', passed: true }]);
    assert.equal(nodes['[data-readiness-percent]'].textContent, '100%');
    assert.ok(!nodes['[data-readiness-items]'].innerHTML.includes('bg-amber-500'));
    context.updateCurriculumReadiness([{ label: 'Video', passed: false, message: 'Processing' }]);
    assert.equal(nodes['[data-readiness-percent]'].textContent, '0%');
    context.updateCurriculumReadiness(undefined);
    assert.equal(nodes['[data-readiness-percent]'].textContent, '0%');
});
