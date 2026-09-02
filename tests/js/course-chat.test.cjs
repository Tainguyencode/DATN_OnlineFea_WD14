const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function loadChat() {
    const document = {
        readyState: 'loading',
        visibilityState: 'visible',
        addEventListener() {},
        querySelector() { return null; },
        querySelectorAll() { return []; },
        createElement() {
            return {
                set textContent(value) { this.innerHTML = String(value ?? ''); },
            };
        },
    };
    const context = {
        document,
        window: { addEventListener() {}, setTimeout() {}, clearTimeout() {} },
        console,
        Intl,
        Date,
        URL,
        Map,
        Set,
        queueMicrotask() {},
        IntersectionObserver: class { observe() {} },
    };
    context.window.document = document;
    vm.createContext(context);
    vm.runInContext(fs.readFileSync('resources/js/course-chat.js', 'utf8'), context);
    return context.window.CourseChat;
}

function message(overrides = {}) {
    return {
        key: 'reply:7',
        id: 7,
        kind: 'reply',
        conversation_id: 3,
        sender: {
            id: 2,
            name: 'Giảng viên A',
            avatar_url: '/avatar.jpg',
            role: 'instructor',
        },
        content: 'Nội dung trả lời',
        created_at: '2026-09-03T08:15:00Z',
        lesson: { id: 9, title: 'Bài học realtime' },
        attachment: null,
        reply_to: {
            key: 'discussion:3',
            content: 'Câu hỏi gốc',
            sender: { id: 1, name: 'Học viên B', role: 'student' },
        },
        is_recalled: false,
        is_helpful: false,
        permissions: {
            can_reply: true,
            can_recall: true,
            can_delete: true,
            can_mark_helpful: true,
        },
        actions: {
            recall_url: '/recall',
            delete_url: '/delete',
            helpful_url: '/helpful',
        },
        ...overrides,
    };
}

test('canonical renderer includes avatar, role, timestamp, lesson, reply and permissions', () => {
    const chat = loadChat();
    const html = chat.renderMessageHtml({ dataset: { currentUserId: '1', chatAccent: 'blue' } }, message());

    assert.match(html, /\/avatar\.jpg/);
    assert.match(html, /Giảng viên/);
    assert.match(html, /Bài học realtime/);
    assert.match(html, /Câu hỏi gốc/);
    assert.match(html, /data-chat-action="reply"/);
    assert.match(html, /data-chat-action="recall"/);
    assert.match(html, /data-chat-action="delete"/);
    assert.match(html, /data-chat-action="helpful"/);
    assert.match(html, /<time/);
});

test('canonical renderer previews images and renders a consistent recall tombstone', () => {
    const chat = loadChat();
    const imageHtml = chat.renderMessageHtml(
        { dataset: { currentUserId: '2', chatAccent: 'emerald' } },
        message({ attachment: { url: '/private/image.jpg', name: 'image.jpg', type: 'image' } }),
    );
    assert.match(imageHtml, /<img src="\/private\/image\.jpg"/);

    const recalledHtml = chat.renderMessageHtml(
        { dataset: { currentUserId: '1', chatAccent: 'blue' } },
        message({ is_recalled: true, content: null, attachment: null, reply_to: null, permissions: {} }),
    );
    assert.match(recalledHtml, /Tin nhắn đã được thu hồi/);
    assert.doesNotMatch(recalledHtml, /data-chat-action=/);
});

test('realtime deletion clears a deleted conversation and disables its composer', () => {
    const chat = loadChat();
    let messagesCleared = false;
    let composerHidden = false;
    let emptyShown = false;
    const root = {
        _chatMessages: new Map([['discussion:3', message({ key: 'discussion:3' })]]),
        querySelector(selector) {
            if (selector === '[data-chat-messages]') {
                return { replaceChildren() { messagesCleared = true; } };
            }
            if (selector === '[data-course-chat-send]') {
                return { classList: { add(name) { composerHidden = name === 'hidden'; } } };
            }
            if (selector === '[data-chat-empty]') {
                return { classList: { remove(name) { emptyShown = name === 'hidden'; } } };
            }
            return null;
        },
    };

    chat.applyDeletedMessage(root, 'discussion:3');

    assert.equal(messagesCleared, true);
    assert.equal(composerHidden, true);
    assert.equal(emptyShown, true);
    assert.equal(root._chatMessages.size, 0);
});
