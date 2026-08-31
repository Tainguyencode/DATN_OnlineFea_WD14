function chatToast(message, type = 'error') {
    if (window.AppToast?.show) window.AppToast.show({ type, message });
    else console.error(message);
}

function escapeChat(value) {
    const node = document.createElement('div');
    node.textContent = value ?? '';
    return node.innerHTML;
}

function appendChatMessage(form, message, result = {}) {
    let container = document.getElementById(form.dataset.chatMessages);
    if (!container && form.id === 'initial-qa-form') {
        const footer = form.parentElement;
        const shell = footer?.parentElement;
        if (shell?.firstElementChild) {
            container = document.createElement('div');
            container.id = 'student-chat-body';
            container.className = 'flex-1 p-4 bg-[#f8fafc] overflow-y-auto space-y-4';
            shell.replaceChild(container, shell.firstElementChild);
            form.id = 'student-reply-form';
            if (result.reply_url) form.action = result.reply_url;
            if (!form.querySelector('[name="reply_to_message_id"]')) {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'reply_to_message_id'; input.id = 'reply-to-message-id';
                form.prepend(input);
            }
        }
    }
    const kind = message.kind || result.kind || 'reply';
    const messageId = `${kind === 'discussion' ? 'msg-disc' : 'msg-reply'}-${message.id}`;
    if (!container || document.getElementById(messageId)) return;
    const root = form.closest('[data-course-chat-root]');
    const mine = Number(message.user_id) === Number(root?.dataset.currentUserId);
    const instructorPage = container.id === 'chat-messages';
    const color = instructorPage ? 'bg-emerald-600' : 'bg-[#0056D2]';
    const time = new Date(message.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    const row = document.createElement('div');
    row.id = messageId;
    row.className = `group flex items-end gap-2.5 ${mine ? 'justify-end' : 'justify-start'} rounded-2xl p-1.5`;
    const avatar = `<div class="h-8 w-8 shrink-0 rounded-full ${mine ? color : 'bg-slate-700'} text-white font-bold flex items-center justify-center text-xs">${escapeChat((message.user?.name || 'U')[0].toUpperCase())}</div>`;
    const attachment = message.attachment_url
        ? `<a class="mt-2 block text-xs font-bold underline" target="_blank" href="${escapeChat(message.attachment_url)}">📎 ${escapeChat(message.attachment_name || 'Tệp đính kèm')}</a>` : '';
    const bubble = `<div class="max-w-[85%] space-y-1 ${mine ? 'text-right' : 'text-left'}"><div class="px-1 text-[11px] font-bold text-slate-600">${mine ? 'Bạn' : escapeChat(message.user?.name || 'Người dùng')} <span class="ml-1 font-normal text-slate-400">${time}</span></div><div class="rounded-2xl px-4 py-2.5 text-sm shadow-xs ${mine ? `${color} text-white` : 'border border-slate-200 bg-white text-slate-900'}"><p class="whitespace-pre-line text-left">${escapeChat(message.content)}</p>${attachment}</div></div>`;
    row.innerHTML = mine ? bubble + avatar : avatar + bubble;
    container.appendChild(row);
    container.scrollTop = container.scrollHeight;
}

async function sendChat(form) {
    if (form.dataset.sending === '1') return;
    const text = form.querySelector('textarea');
    const file = form.querySelector('input[type="file"]');
    if (!text?.value.trim() && !file?.files?.length) {
        chatToast('Vui lòng nhập nội dung hoặc đính kèm tệp.');
        text?.focus();
        return;
    }
    form.dataset.sending = '1';
    const button = form.querySelector('[type="submit"]');
    if (button) button.disabled = true;
    try {
        const headers = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
        const socketId = window.Echo?.socketId?.();
        if (socketId) headers['X-Socket-ID'] = socketId;
        const response = await fetch(form.action, { method: 'POST', headers, body: new FormData(form) });
        const result = await response.json().catch(() => ({}));
        if (!response.ok || !result.success) throw new Error(result.message || Object.values(result.errors || {})[0]?.[0] || 'Không thể gửi tin nhắn.');
        appendChatMessage(form, result.data, result);
        const root = form.closest('[data-course-chat-root]');
        if (root && result.discussion_id && !root.dataset.discussionId) {
            root.dataset.discussionId = result.discussion_id;
            root.dataset.messagesUrl = result.messages_url || '';
            subscribeCourseChat(root);
        }
        if (text) text.value = '';
        if (file) file.value = '';
        form.querySelectorAll('[id$="file-preview"]').forEach((node) => node.classList.add('hidden'));
    } catch (error) {
        chatToast(error.message || 'Không thể gửi tin nhắn.');
    } finally {
        form.dataset.sending = '0';
        if (button) button.disabled = false;
    }
}

async function recallChat(form) {
    if (!window.confirm('Bạn có chắc muốn thu hồi tin nhắn này?')) return;
    try {
        const response = await fetch(form.action, { method: 'POST', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(form) });
        const result = await response.json().catch(() => ({}));
        if (!response.ok || !result.success) throw new Error(result.message || 'Không thể thu hồi tin nhắn.');
        const target = document.getElementById(form.dataset.chatTarget);
        if (target) target.innerHTML = '<div class="ml-auto rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm italic text-slate-400">Tin nhắn đã được thu hồi</div>';
    } catch (error) {
        chatToast(error.message || 'Không thể thu hồi tin nhắn.');
    }
}

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.matches('[data-course-chat-send]')) {
        event.preventDefault();
        event.stopImmediatePropagation();
        sendChat(form);
    } else if (form.matches('[data-course-chat-recall]')) {
        event.preventDefault();
        event.stopImmediatePropagation();
        recallChat(form);
    }
}, true);

function renderRemoteRecall(root, message) {
    const prefix = message.kind === 'discussion' ? 'msg-disc' : 'msg-reply';
    const target = root.querySelector(`#${prefix}-${message.id}`);
    if (target) target.innerHTML = '<div class="ml-auto rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm italic text-slate-400">Tin nhắn đã được thu hồi</div>';
}

function subscribeCourseChat(root) {
    const discussionId = root.dataset.discussionId;
    if (!window.Echo || !discussionId || root.dataset.realtimeSubscribed === '1') return;
    root.dataset.realtimeSubscribed = '1';
    window.Echo.private(`course-discussion.${discussionId}`)
        .listen('.course-discussion.message.created', (event) => {
            const form = root.querySelector('[data-course-chat-send]');
            if (form) appendChatMessage(form, event.message);
        })
        .listen('.course-discussion.message.recalled', (event) => renderRemoteRecall(root, event.message))
        .error((error) => console.error('Course chat realtime error:', error));
}

async function syncCourseChat(root) {
    if (!root.dataset.messagesUrl || root.dataset.syncing === '1') return;
    root.dataset.syncing = '1';
    try {
        const response = await fetch(root.dataset.messagesUrl, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) return;
        const result = await response.json();
        const form = root.querySelector('[data-course-chat-send]');
        if (!form || !Array.isArray(result.data)) return;
        result.data.forEach((message) => {
            const prefix = message.kind === 'discussion' ? 'msg-disc' : 'msg-reply';
            const existing = root.querySelector(`#${prefix}-${message.id}`);
            if (message.is_recalled) {
                if (existing && !existing.dataset.recalled) {
                    existing.dataset.recalled = '1';
                    renderRemoteRecall(root, message);
                }
            } else if (!existing && message.kind === 'reply') {
                appendChatMessage(form, message);
            }
        });
    } catch (error) {
        console.debug('Course chat fallback sync unavailable:', error);
    } finally {
        root.dataset.syncing = '0';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-course-chat-root]').forEach((root) => {
        subscribeCourseChat(root);
        syncCourseChat(root);
        window.setInterval(() => syncCourseChat(root), 1500);
    });
});
