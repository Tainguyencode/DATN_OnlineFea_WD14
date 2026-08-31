const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function chat() {
    const nodes = new Map();
    nodes.set('student-chat-body', { appendChild(node) { nodes.set(node.id, node); }, scrollHeight: 0 });
    const context = {
        document: {
            addEventListener() {},
            getElementById(id) { return nodes.get(id); },
            createElement() { return { set textContent(v) { this.innerHTML = String(v); } }; },
        },
        window: {}, console,
    };
    vm.createContext(context);
    vm.runInContext(fs.readFileSync('resources/js/course-chat.js', 'utf8'), context);
    const form = { dataset: { chatMessages: 'student-chat-body' }, closest() { return { dataset: { currentUserId: '1' } }; } };
    return { nodes, context, form };
}

test('initial question and reply with equal numeric IDs both render; replay does not duplicate', () => {
    const { nodes, context, form } = chat();
    const message = { id: 7, user_id: 1, content: 'Question', created_at: '2026-08-31T00:00:00Z', user: {name: 'Student'} };
    context.appendChatMessage(form, message, { kind: 'discussion' });
    context.appendChatMessage(form, { ...message, kind: 'reply', user_id: 2, content: 'Answer' });
    context.appendChatMessage(form, { ...message, kind: 'reply', user_id: 2, content: 'Answer' });
    assert.ok(nodes.get('msg-disc-7').innerHTML.includes('Question'));
    assert.ok(nodes.get('msg-reply-7').innerHTML.includes('Answer'));
    assert.equal(nodes.size, 3);
});

test('broadcast kind preserves identity and recall only changes the intended message', () => {
    const { nodes, context, form } = chat();
    const message = { id: 1, user_id: 1, content: 'Question', created_at: '2026-08-31T00:00:00Z', user: {name: 'Student'} };
    context.appendChatMessage(form, { ...message, kind: 'discussion' });
    context.appendChatMessage(form, { ...message, kind: 'reply', content: 'Answer' });
    context.renderRemoteRecall({querySelector: (selector) => nodes.get(selector.slice(1))}, {id: 1, kind: 'discussion'});
    assert.ok(nodes.get('msg-disc-1').innerHTML.includes('thu hồi'));
    assert.ok(nodes.get('msg-reply-1').innerHTML.includes('Answer'));
});
