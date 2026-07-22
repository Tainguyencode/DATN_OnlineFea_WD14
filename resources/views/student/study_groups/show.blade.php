@extends('layouts.app')

@section('title', 'Nhóm học tập: ' . $studyGroup->name)

@section('content')
<div class="ui-container py-8">
    <div class="mx-auto max-w-5xl space-y-6">
        
        {{-- Breadcrumb / Back button --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('study-groups.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại danh sách nhóm
            </a>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-[#0056D2] ring-1 ring-inset ring-blue-700/10 dark:bg-blue-950/30 dark:text-blue-300">
                    {{ $studyGroup->course->title }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            
            {{-- Left column: Group Details & Members --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Group info --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white break-words">{{ $studyGroup->name }}</h1>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Người tạo: <strong class="text-slate-700 dark:text-slate-300">{{ $studyGroup->creator->name }}</strong>
                    </p>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 break-words">
                        {{ $studyGroup->description ?? 'Không có mô tả cho nhóm học tập này.' }}
                    </p>
                </div>

                {{-- Members list --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Thành viên ({{ $studyGroup->members->count() }}/{{ $studyGroup->max_members }})</h2>
                    </div>
                    <div class="space-y-3 max-h-[300px] overflow-y-auto chat-scroll pr-1">
                        @foreach($studyGroup->members as $member)
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300 text-xs">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" title="{{ $member->name }}">
                                        {{ $member->name }}
                                    </p>
                                    <span class="inline-block text-[10px] px-1.5 py-0.5 rounded font-bold uppercase {{ $member->pivot->role === 'moderator' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                        {{ $member->pivot->role === 'moderator' ? 'Trưởng nhóm' : 'Học viên' }}
                                    </span>
                                </div>
                                
                                {{-- Kick button for creator or admin --}}
                                @if((Auth::id() === $studyGroup->creator_id || Auth::user()->role === 'admin') && $member->id !== $studyGroup->creator_id)
                                    <form action="{{ route('study-groups.members.remove', [$studyGroup, $member]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thành viên {{ $member->name }} khỏi nhóm?');" class="shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 dark:hover:bg-rose-950/20 cursor-pointer transition" title="Xóa khỏi nhóm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right column: Chat area --}}
            <div class="lg:col-span-3">
                <div class="flex flex-col h-[600px] rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                    
                    {{-- Chat header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 font-extrabold text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900 dark:text-white">Thảo luận nhóm</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Trò chuyện trực tuyến với các thành viên khác</p>
                            </div>
                        </div>
                        
                        {{-- Status indicator --}}
                        <div class="flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500" id="chat-status">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Trực tuyến
                        </div>
                    </div>

                    {{-- Message box --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/20 dark:bg-slate-950/5" id="chat-box">
                        @if($studyGroup->messages->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full py-12 text-center text-slate-400 dark:text-slate-500 space-y-3" id="no-messages-placeholder">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <p class="text-sm font-medium">Chưa có tin nhắn nào trong nhóm này.</p>
                                <p class="text-xs">Hãy gửi tin nhắn đầu tiên để bắt đầu thảo luận!</p>
                            </div>
                        @else
                            @foreach($studyGroup->messages as $msg)
                                @php
                                    $isMe = $msg->user_id === Auth::id();
                                @endphp
                                <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} space-y-1">
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                                        @if(!$isMe)
                                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $msg->user->name }}</span>
                                        @endif
                                        <span>{{ $msg->created_at->format('H:i, d/m/Y') }}</span>
                                    </div>
                                    <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm shadow-sm break-words
                                        {{ $isMe 
                                            ? 'bg-[#0056D2] text-white rounded-tr-none' 
                                            : 'bg-white text-slate-900 border border-slate-100 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-750 rounded-tl-none' 
                                        }}">
                                        {{ $msg->message }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Chat input form --}}
                    <div class="border-t border-slate-100 dark:border-slate-800 p-4 bg-white dark:bg-slate-900">
                        <form id="send-form" class="flex gap-3" onsubmit="handleSendMessage(event)">
                            @csrf
                            <input type="text" 
                                   id="message-input" 
                                   placeholder="Nhập nội dung tin nhắn..." 
                                   autocomplete="off"
                                   required
                                   class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white transition">
                            <button type="submit" 
                                    id="send-button"
                                    class="inline-flex h-11 items-center justify-center rounded-xl bg-[#0056D2] px-6 text-sm font-bold text-white transition hover:bg-[#0046B8] dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer disabled:opacity-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<script>
    const groupId = {{ $studyGroup->id }};
    const currentUserId = {{ Auth::id() }};
    const chatBox = document.getElementById('chat-box');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const sendForm = document.getElementById('send-form');
    
    // Track the last loaded message ID
    let lastMessageId = {{ $studyGroup->messages->last()->id ?? 0 }};

    // Scroll to the bottom of the chat box
    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Scroll on load
    window.addEventListener('DOMContentLoaded', () => {
        scrollToBottom();
        messageInput.focus();
    });

    // Format Date string to matching layout: H:i, d/m/Y
    function formatMessageDate(dateString) {
        const date = new Date(dateString);
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${hours}:${minutes}, ${day}/${month}/${year}`;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Append a message to the chat box
    function appendMessage(msg, isMe) {
        // Remove placeholder if present
        const placeholder = document.getElementById('no-messages-placeholder');
        if (placeholder) {
            placeholder.remove();
        }

        const dateStr = formatMessageDate(msg.created_at);
        const escapedMsg = escapeHtml(msg.message);
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} space-y-1`;
        
        let headerHtml = '';
        if (!isMe) {
            headerHtml = `<span class="font-bold text-slate-700 dark:text-slate-300">${escapeHtml(msg.user.name)}</span>`;
        }

        messageDiv.innerHTML = `
            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                ${headerHtml}
                <span>${dateStr}</span>
            </div>
            <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm shadow-sm break-words
                ${isMe 
                    ? 'bg-[#0056D2] text-white rounded-tr-none' 
                    : 'bg-white text-slate-900 border border-slate-100 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-750 rounded-tl-none' 
                }">
                ${escapedMsg}
            </div>
        `;
        
        chatBox.appendChild(messageDiv);
        scrollToBottom();
    }

    // Send Message handler
    async function handleSendMessage(event) {
        event.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Disable input during submission
        messageInput.disabled = true;
        sendButton.disabled = true;

        try {
            const response = await fetch(`/study-groups/${groupId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });

            const result = await response.json();
            
            if (response.ok && result.success) {
                // Clear input
                messageInput.value = '';
                // Append message and update lastMessageId
                appendMessage(result.data, true);
                lastMessageId = result.data.id;
            } else {
                alert(result.message || 'Lỗi khi gửi tin nhắn.');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Lỗi kết nối mạng khi gửi tin nhắn.');
        } finally {
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        }
    }

    // Periodically fetch new messages (Polling)
    async function pollMessages() {
        try {
            const response = await fetch(`/study-groups/${groupId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data && result.data.messages) {
                    const messages = result.data.messages;
                    let hasNew = false;
                    
                    messages.forEach(msg => {
                        if (msg.id > lastMessageId) {
                            const isMe = msg.user_id === currentUserId;
                            appendMessage(msg, isMe);
                            lastMessageId = msg.id;
                            hasNew = true;
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error polling messages:', error);
        }
    }

    // Start polling every 4 seconds
    setInterval(pollMessages, 4000);
</script>

<style>
    /* Custom simple scrollbar style for chat list and message box */
    .chat-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .chat-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .chat-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }
    .dark .chat-scroll::-webkit-scrollbar-thumb {
        background: #475569;
    }
</style>
@endsection
