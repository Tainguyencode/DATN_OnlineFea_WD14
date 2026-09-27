const chatChannels = new Map();

function chatToast(message, type = 'error') {
    if (window.AppToast?.show) window.AppToast.show({ type, message });
    else console.error(message);
}

function escapeChat(value) {
    const node = document.createElement('div');
    node.textContent = value ?? '';
    return node.innerHTML;
}

function chatCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function roleLabel(role) {
    if (role === 'instructor') return 'Giảng viên';
    if (role === 'admin') return 'Quản trị viên';
    return 'Học viên';
}

function formatChatTime(value) {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '';
    const today = new Date();
    const sameDay = date.toDateString() === today.toDateString();
    return new Intl.DateTimeFormat('vi-VN', sameDay
        ? { hour: '2-digit', minute: '2-digit' }
        : { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }
    ).format(date);
}

function attachmentHtml(attachment) {
    if (!attachment?.url) return '';
    const url = escapeChat(attachment.url);
    const name = escapeChat(attachment.name || 'Tệp đính kèm');
    if (attachment.type === 'image') {
        return `<a href="${url}" target="_blank" rel="noopener" class="mt-2 block overflow-hidden rounded-xl border border-black/10 bg-black/5"><img src="${url}" alt="${name}" loading="lazy" class="max-h-64 w-full object-contain"></a>`;
    }
    if (attachment.type === 'video') {
        return `<video controls preload="metadata" class="mt-2 max-h-64 w-full rounded-xl bg-black"><source src="${url}"></video>`;
    }
    return `<a href="${url}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1.5 rounded-lg border border-current/20 px-2.5 py-1.5 text-xs font-bold underline-offset-2 hover:underline"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m15.2 7-6.6 6.6a2 2 0 1 0 2.8 2.8l6.4-6.6a4 4 0 0 0-5.6-5.6l-6.4 6.6a6 6 0 1 0 8.4 8.4l6.3-6.2"/></svg><span>${name}</span></a>`;
}

function actionButton(label, action, tone = 'slate') {
    const toneClass = tone === 'rose'
        ? 'text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40'
        : tone === 'amber'
            ? 'text-amber-700 hover:bg-amber-50 dark:text-amber-300 dark:hover:bg-amber-950/40'
            : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800';
    return `<button type="button" data-chat-action="${action}" class="cursor-pointer rounded-md px-1.5 py-0.5 text-[10px] font-bold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 ${toneClass}">${label}</button>`;
}

function messageHtml(root, message) {
    const mine = Number(message.sender?.id) === Number(root.dataset.currentUserId);
    const accent = root.dataset.chatAccent === 'emerald' ? 'bg-emerald-600' : 'bg-blue-600';
    const peerBubble = 'border border-slate-200 bg-white text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-white';
    const role = roleLabel(message.sender?.role);
    const roleClass = message.sender?.role === 'instructor'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300'
        : 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-300';
    const avatar = message.sender?.avatar_url
        ? `<img src="${escapeChat(message.sender.avatar_url)}" alt="${escapeChat(message.sender.name)}" class="h-9 w-9 rounded-full border border-slate-200 object-cover dark:border-slate-700">`
        : `<span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-xs font-bold text-white">${escapeChat((message.sender?.name || 'U').charAt(0).toUpperCase())}</span>`;
    const lesson = message.lesson?.title
        ? `<span class="max-w-40 truncate rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300" title="${escapeChat(message.lesson.title)}">Bài: ${escapeChat(message.lesson.title)}</span>`
        : '';
    const reply = message.reply_to
        ? `<button type="button" data-chat-scroll-to="${escapeChat(message.reply_to.key)}" class="mb-2 block w-full cursor-pointer rounded-lg border-l-4 ${mine ? 'border-white/80 bg-white/15 text-white' : 'border-blue-500 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200'} px-3 py-2 text-left text-xs transition-opacity hover:opacity-80"><span class="block font-bold">${escapeChat(message.reply_to.sender?.name || 'Người dùng')}</span><span class="block truncate opacity-80">${escapeChat(message.reply_to.content || '[Tệp đính kèm]')}</span></button>`
        : '';
    const actions = [];
    if (message.permissions?.can_reply) actions.push(actionButton('Trả lời', 'reply'));
    if (message.permissions?.can_recall) actions.push(actionButton('Thu hồi', 'recall', 'amber'));
    if (message.permissions?.can_mark_helpful) actions.push(actionButton(message.is_helpful ? 'Bỏ hữu ích' : 'Hữu ích', 'helpful'));
    if (message.permissions?.can_delete) actions.push(actionButton('Xóa', 'delete', 'rose'));
    const helpful = message.is_helpful
        ? '<span class="rounded-md border border-emerald-200 bg-emerald-50 px-1.5 py-0.5 text-[9px] font-bold text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">Hữu ích</span>'
        : '';
    const body = message.is_recalled
        ? `<div class="rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm italic text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Tin nhắn đã được thu hồi</div>`
        : `<div class="rounded-2xl px-4 py-3 text-sm leading-6 shadow-sm ${mine ? `${accent} text-white` : peerBubble}">${reply}${message.content ? `<p class="whitespace-pre-wrap break-words">${escapeChat(message.content)}</p>` : ''}${attachmentHtml(message.attachment)}</div>`;

    return `
        <div class="flex items-end gap-2.5 ${mine ? 'justify-end' : 'justify-start'}">
            ${mine ? '' : avatar}
            <div class="max-w-[84%] space-y-1 sm:max-w-[76%]">
                <div class="flex flex-wrap items-center gap-1 ${mine ? 'justify-end' : 'justify-start'}">
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200">${mine ? 'Bạn' : escapeChat(message.sender?.name || 'Người dùng')}</span>
                    <span class="rounded-md border px-1.5 py-0.5 text-[9px] font-bold ${roleClass}">${role}</span>
                    ${lesson}
                    <time class="text-[10px] text-slate-500 dark:text-slate-400" datetime="${escapeChat(message.created_at)}">${formatChatTime(message.created_at)}</time>
                    ${helpful}
                    ${actions.join('')}
                </div>
                ${body}
            </div>
            ${mine ? avatar : ''}
        </div>`;
}

function upsertChatMessage(root, message, { scroll = true } = {}) {
    const container = root.querySelector('[data-chat-messages]');
    if (!container || !message?.key) return;
    root._chatMessages ||= new Map();
    root._chatMessages.set(message.key, message);

    let row = [...container.children].find((child) => child.dataset.messageKey === message.key);
    if (!row) {
        row = document.createElement('article');
        row.dataset.messageKey = message.key;
        row.className = 'rounded-2xl p-1 transition-colors duration-200';
        container.append(row);
    }
    row.innerHTML = messageHtml(root, message);
    root.querySelector('[data-chat-empty]')?.classList.add('hidden');
    if (scroll) container.scrollTop = container.scrollHeight;
}

function removeChatMessage(root, key) {
    const container = root.querySelector('[data-chat-messages]');
    const row = container ? [...container.children].find((child) => child.dataset.messageKey === key) : null;
    row?.remove();
    root._chatMessages?.delete(key);
}

function applyDeletedMessage(root, key) {
    if (String(key).startsWith('discussion:')) {
        root.querySelector('[data-chat-messages]')?.replaceChildren();
        root._chatMessages = new Map();
        root.querySelector('[data-course-chat-send]')?.classList.add('hidden');
        root.querySelector('[data-chat-empty]')?.classList.remove('hidden');
        return;
    }

    removeChatMessage(root, key);
}

function renderInitialChat(root) {
    if (root.dataset.chatInitialized === '1') return;
    root.dataset.chatInitialized = '1';
    root._chatMessages = new Map();
    const payloadNode = root.querySelector('[data-chat-initial]');
    if (payloadNode) {
        try {
            const context = JSON.parse(payloadNode.textContent);
            root.dataset.chatCursor = context.cursor || '';
            (context.messages || []).forEach((message) => upsertChatMessage(root, message, { scroll: false }));
            const container = root.querySelector('[data-chat-messages]');
            if (container) container.scrollTop = container.scrollHeight;
        } catch (error) {
            console.error('Invalid initial course chat payload', error);
        }
    }
    registerChatRoot(root);
    scheduleChatFallback(root);
    const visibilityObserver = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) maybeMarkChatRead(root);
    });
    visibilityObserver.observe(root);
    root._chatVisibilityObserver = visibilityObserver;
    queueMicrotask(() => maybeMarkChatRead(root));
}

function setReplyContext(root, message) {
    const input = root.querySelector('[data-chat-reply-input]');
    const context = root.querySelector('[data-chat-reply-context]');
    if (!input || !context) return;
    input.value = message.key;
    context.querySelector('[data-chat-reply-name]').textContent = message.sender?.name || 'Người dùng';
    context.querySelector('[data-chat-reply-snippet]').textContent = message.content || (message.is_recalled ? 'Tin nhắn đã được thu hồi' : '[Tệp đính kèm]');
    context.classList.remove('hidden');
    context.classList.add('flex');
    root.querySelector('[data-chat-content]')?.focus();
}

function clearReplyContext(root) {
    const input = root.querySelector('[data-chat-reply-input]');
    const context = root.querySelector('[data-chat-reply-context]');
    if (input) input.value = '';
    context?.classList.add('hidden');
    context?.classList.remove('flex');
}

async function fetchJson(url, options = {}) {
    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': chatCsrfToken(),
        ...(options.headers || {}),
    };
    const socketId = window.Echo?.socketId?.();
    if (socketId) headers['X-Socket-ID'] = socketId;
    const response = await fetch(url, { ...options, headers });
    const result = await response.json().catch(() => ({}));
    if (!response.ok || result.success === false) {
        throw new Error(result.message || Object.values(result.errors || {})[0]?.[0] || 'Không thể xử lý yêu cầu.');
    }
    return result;
}

async function sendChat(form) {
    if (form.dataset.sending === '1') return;
    const root = form.closest('[data-course-chat-root]');
    const text = form.querySelector('[data-chat-content]');
    const file = form.querySelector('[data-chat-file]');
    const error = root?.querySelector('[data-chat-error]');
    if (!text?.value.trim() && !file?.files?.length) {
        if (error) {
            error.textContent = 'Vui lòng nhập nội dung hoặc đính kèm tệp.';
            error.classList.remove('hidden');
        }
        text?.focus();
        return;
    }
    form.dataset.sending = '1';
    form.querySelector('[type="submit"]')?.setAttribute('disabled', 'disabled');
    error?.classList.add('hidden');
    try {
        const result = await fetchJson(form.action, { method: 'POST', body: new FormData(form) });
        if (root && result.discussion_id && !root.dataset.discussionId) {
            root.dataset.discussionId = result.discussion_id;
            root.dataset.messagesUrl = result.messages_url || '';
            root.dataset.messageUrlTemplate = result.message_url_template || '';
            root.dataset.readUrl = result.read_url || '';
            form.action = result.reply_url;
            registerChatRoot(root);
        }
        document.querySelectorAll(`[data-course-chat-root][data-discussion-id="${result.discussion_id}"]`).forEach((target) => upsertChatMessage(target, result.data));
        if (text) text.value = '';
        if (file) file.value = '';
        const label = form.querySelector('[data-chat-file-label]');
        if (label) label.textContent = 'Đính kèm';
        if (root) clearReplyContext(root);
        window.dispatchEvent(new CustomEvent('course-chat:changed', { detail: { conversationId: result.discussion_id } }));
    } catch (errorValue) {
        const message = errorValue.message || 'Không thể gửi tin nhắn.';
        if (error) {
            error.textContent = message;
            error.classList.remove('hidden');
        }
        chatToast(message);
    } finally {
        form.dataset.sending = '0';
        form.querySelector('[type="submit"]')?.removeAttribute('disabled');
    }
}

async function performMessageAction(root, message, action) {
    const url = action === 'recall'
        ? message.actions?.recall_url
        : action === 'delete'
            ? message.actions?.delete_url
            : message.actions?.helpful_url;
    if (!url) return;
    if ((action === 'recall' || action === 'delete') && !window.confirm(action === 'delete' ? 'Xóa tin nhắn này?' : 'Thu hồi tin nhắn này?')) return;
    try {
        const result = await fetchJson(url, { method: action === 'delete' ? 'DELETE' : 'POST' });
        if (action === 'delete') {
            document.querySelectorAll(`[data-course-chat-root][data-discussion-id="${message.conversation_id}"]`).forEach((target) => {
                if (result.conversation_deleted) applyDeletedMessage(target, message.key);
                else removeChatMessage(target, message.key);
            });
        } else if (result.data) {
            document.querySelectorAll(`[data-course-chat-root][data-discussion-id="${message.conversation_id}"]`).forEach((target) => upsertChatMessage(target, result.data, { scroll: false }));
        } else {
            await refreshCanonicalMessage(root, message.key);
        }
        window.dispatchEvent(new CustomEvent('course-chat:changed', { detail: { conversationId: message.conversation_id } }));
    } catch (error) {
        chatToast(error.message || 'Không thể cập nhật tin nhắn.');
    }
}

function registerChatRoot(root) {
    const id = root.dataset.discussionId;
    if (!id || !window.Echo) return;
    unregisterChatRoot(root);
    let entry = chatChannels.get(id);
    if (!entry) {
        const roots = new Set();
        const channel = window.Echo.private(`course-discussion.${id}`)
            .listen('.course-discussion.message.created', (event) => roots.forEach((target) => refreshCanonicalMessage(target, event.messageKey)))
            .listen('.course-discussion.message.updated', (event) => roots.forEach((target) => refreshCanonicalMessage(target, event.messageKey)))
            .listen('.course-discussion.message.recalled', (event) => roots.forEach((target) => refreshCanonicalMessage(target, event.messageKey)))
            .listen('.course-discussion.message.deleted', (event) => roots.forEach((target) => applyDeletedMessage(target, event.messageKey)))
            .error((error) => console.error('Course chat realtime error:', error));
        entry = { channel, roots };
        chatChannels.set(id, entry);
    }
    entry.roots.add(root);
    root._chatChannelId = id;
}

function unregisterChatRoot(root) {
    const id = root._chatChannelId;
    if (!id) return;
    const entry = chatChannels.get(id);
    entry?.roots.delete(root);
    root._chatChannelId = null;
}

async function refreshCanonicalMessage(root, key) {
    const template = root.dataset.messageUrlTemplate;
    if (!template || !key) return;
    try {
        const result = await fetchJson(template.replace('__MESSAGE_KEY__', encodeURIComponent(key)));
        upsertChatMessage(root, result.data);
        maybeMarkChatRead(root);
    } catch (error) {
        if (!String(error.message).includes('404')) console.debug('Course chat message refresh unavailable', error);
    }
}

function echoConnected() {
    return window.Echo?.connector?.pusher?.connection?.state === 'connected';
}

async function syncCourseChat(root) {
    if (!root.dataset.messagesUrl || root.dataset.syncing === '1') return;
    root.dataset.syncing = '1';
    try {
        const url = new URL(root.dataset.messagesUrl, window.location.origin);
        if (root.dataset.chatCursor) url.searchParams.set('after', root.dataset.chatCursor);
        const result = await fetchJson(url.toString());
        (result.data || []).forEach((message) => upsertChatMessage(root, message, { scroll: false }));
        if (result.cursor) root.dataset.chatCursor = result.cursor;
        if (result.data?.length) {
            const container = root.querySelector('[data-chat-messages]');
            if (container) container.scrollTop = container.scrollHeight;
            maybeMarkChatRead(root);
        }
    } catch (error) {
        console.debug('Course chat fallback sync unavailable', error);
    } finally {
        root.dataset.syncing = '0';
    }
}

function scheduleChatFallback(root) {
    if (root._chatFallbackTimer) window.clearTimeout(root._chatFallbackTimer);
    const tick = async () => {
        if (!echoConnected()) await syncCourseChat(root);
        root._chatFallbackTimer = window.setTimeout(tick, 5000);
    };
    root._chatFallbackTimer = window.setTimeout(tick, 5000);
}

function rootIsVisible(root) {
    return document.visibilityState === 'visible' && root.isConnected && root.getClientRects().length > 0;
}

async function maybeMarkChatRead(root) {
    if (!root.dataset.readUrl || !rootIsVisible(root) || root.dataset.markingRead === '1') return;
    root.dataset.markingRead = '1';
    try {
        await fetchJson(root.dataset.readUrl, { method: 'POST' });
        window.dispatchEvent(new CustomEvent('course-chat:read', { detail: { conversationId: Number(root.dataset.discussionId) } }));
    } catch (error) {
        console.debug('Could not mark course chat as read', error);
    } finally {
        root.dataset.markingRead = '0';
    }
}

function conversationItemHtml(conversation) {
    const avatar = conversation.avatar_url
        ? `<img src="${escapeChat(conversation.avatar_url)}" alt="${escapeChat(conversation.title)}" class="h-11 w-11 rounded-full object-cover">`
        : `<span class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-800 text-sm font-bold text-white">${escapeChat((conversation.title || 'C').charAt(0).toUpperCase())}</span>`;
    const unread = conversation.unread_count > 0
        ? `<span class="min-w-5 rounded-full bg-blue-600 px-1.5 py-0.5 text-center text-[10px] font-black text-white">${conversation.unread_count > 99 ? '99+' : conversation.unread_count}</span>`
        : '';
    return `<button type="button" data-conversation-id="${conversation.id}" class="flex w-full cursor-pointer items-center gap-3 rounded-xl p-3 text-left transition-colors hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-slate-800">
        ${avatar}
        <span class="min-w-0 flex-1">
            <span class="flex items-center justify-between gap-2"><strong class="truncate text-sm text-slate-900 dark:text-white">${escapeChat(conversation.title)}</strong><time class="shrink-0 text-[10px] text-slate-500">${formatChatTime(conversation.last_message?.created_at)}</time></span>
            <span class="block truncate text-[11px] font-semibold text-blue-700 dark:text-blue-300">${escapeChat(conversation.course?.title || '')}</span>
            <span class="mt-0.5 block truncate text-xs ${conversation.unread_count ? 'font-bold text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-400'}">${escapeChat(conversation.last_message?.sender_name || '')}: ${escapeChat(conversation.last_message?.content || '')}</span>
        </span>
        ${unread}
    </button>`;
}

function initFloatingMessenger(widget) {
    if (widget.dataset.messengerInitialized === '1') return;
    widget.dataset.messengerInitialized = '1';
    const panel = widget.querySelector('[data-messenger-panel]');
    const toggle = widget.querySelector('[data-messenger-toggle]');
    const listView = widget.querySelector('[data-messenger-list-view]');
    const chatView = widget.querySelector('[data-messenger-chat-view]');
    const list = widget.querySelector('[data-messenger-conversations]');
    const loading = widget.querySelector('[data-messenger-loading]');
    const empty = widget.querySelector('[data-messenger-empty]');
    const badge = widget.querySelector('[data-messenger-badge]');
    const thread = widget.querySelector('[data-messenger-thread]');
    let conversations = [];

    const updateBadge = (count) => {
        badge.textContent = count > 99 ? '99+' : String(count || '');
        badge.classList.toggle('hidden', !count);
    };

    const loadConversations = async (showLoading = true) => {
        if (showLoading) {
            loading.classList.remove('hidden');
            list.classList.add('hidden');
            empty.classList.add('hidden');
        }
        try {
            const result = await fetchJson(widget.dataset.conversationsUrl);
            conversations = result.data || [];
            list.innerHTML = conversations.map(conversationItemHtml).join('');
            loading.classList.add('hidden');
            list.classList.toggle('hidden', conversations.length === 0);
            empty.classList.toggle('hidden', conversations.length > 0);
            empty.classList.toggle('flex', conversations.length === 0);
            updateBadge(result.meta?.unread_total || 0);
        } catch (error) {
            loading.textContent = 'Không thể tải cuộc trò chuyện.';
            chatToast(error.message);
        }
    };

    const openConversation = async (conversation) => {
        listView.classList.add('hidden');
        listView.classList.remove('flex');
        chatView.classList.remove('hidden');
        chatView.classList.add('flex');
        widget.querySelector('[data-messenger-chat-title]').textContent = conversation.title;
        widget.querySelector('[data-messenger-chat-course]').textContent = conversation.course?.title || '';
        unregisterChatRoot(thread);
        thread.dataset.discussionId = conversation.id;
        thread.dataset.messagesUrl = conversation.messages_url;
        thread.dataset.messageUrlTemplate = conversation.message_url_template;
        thread.dataset.readUrl = conversation.read_url;
        thread.dataset.chatCursor = '';
        thread.querySelector('[data-course-chat-send]').action = conversation.send_url;
        thread.querySelector('[data-chat-messages]').replaceChildren();
        thread._chatMessages = new Map();
        registerChatRoot(thread);
        try {
            const result = await fetchJson(conversation.messages_url);
            (result.data || []).forEach((message) => upsertChatMessage(thread, message, { scroll: false }));
            thread.dataset.chatCursor = result.cursor || '';
            const container = thread.querySelector('[data-chat-messages]');
            if (container) container.scrollTop = container.scrollHeight;
            await maybeMarkChatRead(thread);
            loadConversations(false);
        } catch (error) {
            chatToast(error.message);
        }
    };

    toggle.addEventListener('click', () => {
        const opening = panel.classList.contains('hidden');
        panel.classList.toggle('hidden', !opening);
        toggle.setAttribute('aria-expanded', String(opening));
        if (opening) loadConversations();
    });
    widget.querySelectorAll('[data-messenger-close]').forEach((button) => button.addEventListener('click', () => {
        panel.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
    }));
    widget.querySelector('[data-messenger-back]').addEventListener('click', () => {
        chatView.classList.add('hidden');
        chatView.classList.remove('flex');
        listView.classList.remove('hidden');
        listView.classList.add('flex');
        loadConversations(false);
    });
    list.addEventListener('click', (event) => {
        const button = event.target.closest('[data-conversation-id]');
        const conversation = conversations.find((item) => Number(item.id) === Number(button?.dataset.conversationId));
        if (conversation) openConversation(conversation);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.classList.contains('hidden')) {
            panel.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.focus();
        }
    });
    window.addEventListener('course-chat:changed', () => loadConversations(false));
    window.addEventListener('course-chat:read', () => loadConversations(false));

    const userId = widget.dataset.userId;
    if (window.Echo && userId) {
        window.Echo.private(`App.Models.User.${userId}`)
            .listen('.course-conversation.updated', () => loadConversations(false))
            .error((error) => console.error('Messenger realtime error:', error));
    }
    loadConversations(false);
}

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-course-chat-send]')) return;
    event.preventDefault();
    event.stopImmediatePropagation();
    sendChat(form);
}, true);

document.addEventListener('click', (event) => {
    const root = event.target.closest('[data-course-chat-root]');
    if (!root) return;
    if (event.target.closest('[data-chat-cancel-reply]')) {
        clearReplyContext(root);
        return;
    }
    const scrollButton = event.target.closest('[data-chat-scroll-to]');
    if (scrollButton) {
        const target = [...root.querySelector('[data-chat-messages]').children].find((row) => row.dataset.messageKey === scrollButton.dataset.chatScrollTo);
        target?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        target?.classList.add('bg-amber-100/70', 'dark:bg-amber-900/30');
        window.setTimeout(() => target?.classList.remove('bg-amber-100/70', 'dark:bg-amber-900/30'), 1600);
        return;
    }
    const actionButtonElement = event.target.closest('[data-chat-action]');
    if (!actionButtonElement) return;
    const row = actionButtonElement.closest('[data-message-key]');
    const message = root._chatMessages?.get(row?.dataset.messageKey);
    if (!message) return;
    if (actionButtonElement.dataset.chatAction === 'reply') setReplyContext(root, message);
    else performMessageAction(root, message, actionButtonElement.dataset.chatAction);
});

document.addEventListener('change', (event) => {
    const file = event.target.closest('[data-chat-file]');
    if (!file) return;
    const label = file.closest('form')?.querySelector('[data-chat-file-label]');
    if (label) label.textContent = file.files?.[0]?.name || 'Đính kèm';
});

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        document.querySelectorAll('[data-course-chat-root]').forEach(maybeMarkChatRead);
    }
});

function initializeCourseChats() {
    document.querySelectorAll('[data-course-chat-root]').forEach(renderInitialChat);
    document.querySelectorAll('[data-floating-messenger]').forEach(initFloatingMessenger);
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeCourseChats, { once: true });
else initializeCourseChats();

window.CourseChat = {
    applyDeletedMessage,
    renderMessage: upsertChatMessage,
    renderMessageHtml: messageHtml,
    sync: syncCourseChat,
    markRead: maybeMarkChatRead,
};
