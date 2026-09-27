import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const view = readFileSync(new URL('../../resources/views/student/study_groups/_show_content.blade.php', import.meta.url), 'utf8');
const source = view.slice(view.indexOf('    function renderInviteButton('), view.indexOf('    // EDIT LIMIT MODAL JS'));

for (const failure of ['http', 'network']) {
    test(`malicious display name remains data through ${failure} failure and retry`, async () => {
        const name = "');globalThis.injected=true;//<img src=x onerror=alert(1)>";
        let button;
        const requests = [];
        const messages = [];
        const container = {
            set innerHTML(value) {
                assert.doesNotMatch(value, /onerror|onclick|injected/);
            },
            replaceChildren(child) { button = child; },
        };
        const context = vm.createContext({
            groupId: 10,
            document: {
                createElement(tag) {
                    assert.equal(tag, 'button');
                    return { addEventListener(event, callback) { this.click = callback; } };
                },
                getElementById: () => container,
                querySelector: () => ({ value: 'test-csrf' }),
            },
            fetch: async (url, options) => {
                requests.push(JSON.parse(options.body));
                if (requests.length === 1 && failure === 'network') throw new Error('offline');
                return { ok: requests.length > 1, json: async () => ({ success: requests.length > 1 }) };
            },
            showStudyGroupToast: (message) => messages.push(message),
            setTimeout: () => {},
            console: { error: () => {} },
        });
        vm.runInContext(source, context);
        context.renderInviteButton(container, 42, name);
        await button.click();
        await button.click();
        assert.deepEqual(requests, [{ user_id: 42 }, { user_id: 42 }]);
        assert.equal(context.injected, undefined);
        assert.ok(messages.some((message) => message.includes(name)));
        assert.equal(button.textContent, 'Mời');
    });
}
