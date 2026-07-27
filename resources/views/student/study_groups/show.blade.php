@php
if (!function_exists('getFileIconSvg')) {
    function getFileIconSvg($mime) {
        $pdf = '<svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>';
        $word = '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>';
        $excel = '<svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>';
        $zip = '<svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6 10h-4v-2h4v2zm0-4h-4V8h4v4z"/></svg>';
        $text = '<svg class="w-5 h-5 text-slate-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13zM16 17H8v-2h8v2zm0-4H8v-2h8v2z"/></svg>';

        if (str_contains($mime, 'pdf')) return $pdf;
        if (str_contains($mime, 'word') || str_contains($mime, 'document')) return $word;
        if (str_contains($mime, 'excel') || str_contains($mime, 'sheet')) return $excel;
        if (str_contains($mime, 'zip') || str_contains($mime, 'compressed')) return $zip;
        
        return $text;
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 1) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
@endphp

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
                                        @if($msg->message_type === 'image' || $msg->image_path)
                                            <img src="{{ $msg->image_path ? Storage::disk('public')->url($msg->image_path) : $msg->image_url }}" 
                                                 class="max-w-full max-h-[250px] rounded-lg cursor-zoom-in mb-1 object-cover hover:opacity-95 transition" 
                                                 onclick="showLightbox('{{ $msg->image_path ? Storage::disk('public')->url($msg->image_path) : $msg->image_url }}')" 
                                                 alt="Chat Image">
                                        @elseif($msg->message_type === 'video')
                                            <div class="mb-1">
                                                <video class="max-w-full max-h-[250px] rounded-lg shadow-sm border border-slate-200" controls preload="metadata">
                                                    <source src="{{ $msg->file_url }}" type="{{ $msg->mime_type }}">
                                                    Trình duyệt của bạn không hỗ trợ phát video này.
                                                </video>
                                            </div>
                                        @elseif($msg->message_type === 'file')
                                            <div class="mb-2 p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-between gap-3 border border-slate-200 dark:border-slate-700 min-w-[200px] sm:min-w-[280px]">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div class="w-9 h-9 shrink-0 flex items-center justify-center bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-250 dark:border-slate-700">
                                                        {!! getFileIconSvg($msg->mime_type) !!}
                                                    </div>
                                                    <div class="min-w-0 leading-tight text-left">
                                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" title="{{ $msg->file_name }}">{{ $msg->file_name }}</p>
                                                        <p class="text-[10px] text-slate-400 font-medium">{{ formatBytes($msg->file_size) }}</p>
                                                    </div>
                                                </div>
                                                <a href="{{ $msg->file_url }}" download class="shrink-0 p-1.5 bg-[#0056D2] hover:bg-[#0046B8] dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-lg transition" title="Tải xuống">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            </div>
                                        @endif
                                        @if($msg->message)
                                            <div class="text-left">{{ $msg->message }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Chat input form --}}
                    <div class="border-t border-slate-100 dark:border-slate-800 p-4 bg-white dark:bg-slate-900">
                        {{-- Preview container --}}
                        <div id="attachment-preview-container" class="hidden mb-3 p-3 bg-slate-50 dark:bg-slate-950 rounded-xl flex items-center justify-between border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-3 min-w-0">
                                <div id="attachment-preview-media" class="relative w-16 h-16 shrink-0 rounded-lg overflow-hidden border border-slate-300 dark:border-slate-700 flex items-center justify-center bg-white dark:bg-slate-900">
                                    <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                                    <div id="file-icon-preview" class="text-slate-400">
                                        {{-- Icon set by JS --}}
                                    </div>
                                </div>
                                <div class="min-w-0 leading-snug">
                                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[200px]" id="attachment-preview-name">file.pdf</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" id="attachment-preview-size">0 KB</p>
                                </div>
                            </div>
                            <button type="button" onclick="clearSelectedAttachment()" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Progress bar --}}
                        <div id="upload-progress-container" class="hidden mb-3 p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300" id="upload-progress-filename font-semibold">Đang tải lên...</span>
                                <span class="text-xs font-bold text-blue-600" id="upload-progress-percent">0%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div id="upload-progress-bar" class="bg-blue-600 h-full rounded-full transition-all duration-150" style="width: 0%"></div>
                            </div>
                        </div>

                        <form id="send-form" class="flex gap-3 items-center" onsubmit="handleSendMessage(event)">
                            @csrf
                            <div class="relative flex-1 flex items-center">
                                {{-- Attachment Button --}}
                                <button type="button" 
                                        onclick="document.getElementById('attachment-input').click()"
                                        class="absolute left-3 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 p-1.5 rounded-lg transition cursor-pointer"
                                        title="Đính kèm tệp tin">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                </button>
                                
                                <input type="file" 
                                       id="attachment-input" 
                                       name="file" 
                                       accept="image/*,video/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-zip-compressed,text/plain" 
                                       class="hidden" 
                                       onchange="handleAttachmentSelect(event)">

                                <input type="text" 
                                       id="message-input" 
                                       placeholder="Nhập nội dung tin nhắn..." 
                                       autocomplete="off"
                                       class="flex-1 rounded-xl border border-slate-300 bg-white pl-12 pr-4 py-3 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white transition">
                            </div>
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
    const attachmentInput = document.getElementById('attachment-input');
    const attachmentPreviewContainer = document.getElementById('attachment-preview-container');
    const imagePreview = document.getElementById('image-preview');
    const fileIconPreview = document.getElementById('file-icon-preview');
    const attachmentPreviewName = document.getElementById('attachment-preview-name');
    const attachmentPreviewSize = document.getElementById('attachment-preview-size');
    const uploadProgressContainer = document.getElementById('upload-progress-container');
    const uploadProgressPercent = document.getElementById('upload-progress-percent');
    const uploadProgressBar = document.getElementById('upload-progress-bar');
    let lastMessageId = {{ $studyGroup->messages->last()->id ?? 0 }};

    function scrollToBottom() { chatBox.scrollTop = chatBox.scrollHeight; }
    window.addEventListener('DOMContentLoaded', () => { scrollToBottom(); messageInput.focus(); });

    function formatMessageDate(dateString) {
        const date = new Date(dateString);
        return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}, ${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}/${date.getFullYear()}`;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024, sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function getFileIconSvg(mime, name = '') {
        const pdf = '<svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>';
        const word = '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>';
        const excel = '<svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>';
        const zip = '<svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6 10h-4v-2h4v2zm0-4h-4V8h4v4z"/></svg>';
        const text = '<svg class="w-5 h-5 text-slate-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13zM16 17H8v-2h8v2zm0-4H8v-2h8v2z"/></svg>';
        const lowerMime = mime.toLowerCase();
        const lowerName = name.toLowerCase();
        if (lowerMime.includes('pdf') || lowerName.endsWith('.pdf')) return pdf;
        if (lowerMime.includes('word') || lowerMime.includes('document') || lowerName.endsWith('.doc') || lowerName.endsWith('.docx')) return word;
        if (lowerMime.includes('excel') || lowerMime.includes('sheet') || lowerName.endsWith('.xls') || lowerName.endsWith('.xlsx')) return excel;
        if (lowerMime.includes('zip') || lowerMime.includes('compressed') || lowerName.endsWith('.zip')) return zip;
        return text;
    }

    function handleAttachmentSelect(event) {
        const file = event.target.files[0];
        if (!file) return;
        const sizeMb = file.size / (1024 * 1024);
        if (file.type.startsWith('image/') && sizeMb > 5) { alert('Ảnh tối đa 5MB.'); attachmentInput.value = ''; return; }
        if (file.type.startsWith('video/') && sizeMb > 100) { alert('Video tối đa 100MB.'); attachmentInput.value = ''; return; }
        if (!file.type.startsWith('image/') && !file.type.startsWith('video/') && sizeMb > 20) { alert('Tệp tối đa 20MB.'); attachmentInput.value = ''; return; }
        attachmentPreviewName.textContent = file.name;
        attachmentPreviewSize.textContent = file.size > 1024 * 1024 ? `${sizeMb.toFixed(1)} MB` : `${(file.size / 1024).toFixed(1)} KB`;
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) { imagePreview.src = e.target.result; imagePreview.classList.remove('hidden'); fileIconPreview.classList.add('hidden'); };
            reader.readAsDataURL(file);
        } else {
            imagePreview.src = '#'; imagePreview.classList.add('hidden'); fileIconPreview.classList.remove('hidden');
            let iconSvg = file.type.startsWith('video/') ? '<svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' : getFileIconSvg(file.type, file.name);
            fileIconPreview.innerHTML = iconSvg;
        }
        attachmentPreviewContainer.classList.remove('hidden');
    }

    function clearSelectedAttachment() {
        attachmentInput.value = ''; imagePreview.src = '#'; imagePreview.classList.add('hidden');
        fileIconPreview.innerHTML = ''; fileIconPreview.classList.remove('hidden');
        attachmentPreviewContainer.classList.add('hidden');
    }

    function showLightbox(src) { document.getElementById('lightbox-img').src = src; document.getElementById('lightbox-modal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeLightbox() { document.getElementById('lightbox-modal').classList.add('hidden'); document.body.style.overflow = ''; }

    function appendMessage(msg, isMe) {
        const placeholder = document.getElementById('no-messages-placeholder');
        if (placeholder) placeholder.remove();
        const dateStr = formatMessageDate(msg.created_at);
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} space-y-1`;
        let headerHtml = !isMe ? `<span class="font-bold text-slate-700 dark:text-slate-300">${escapeHtml(msg.user.name)}</span>` : '';
        let contentHtml = '';
        if (msg.message_type === 'image' && msg.image_url) contentHtml = `<img src="${msg.image_url}" class="max-w-full max-h-[250px] rounded-lg cursor-zoom-in mb-1 object-cover hover:opacity-95 transition" onclick="showLightbox('${msg.image_url}')" alt="Chat Image">`;
        else if (msg.message_type === 'video' && msg.file_url) contentHtml = `<div class="mb-1"><video class="max-w-full max-h-[250px] rounded-lg shadow-sm border border-slate-200" controls preload="metadata"><source src="${msg.file_url}" type="${msg.mime_type}"></video></div>`;
        else if (msg.message_type === 'file' && msg.file_url) contentHtml = `<div class="mb-2 p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-between gap-3 border border-slate-200 dark:border-slate-700"><div class="flex items-center gap-2.5 min-w-0"><div class="w-9 h-9 shrink-0 flex items-center justify-center bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-250 dark:border-slate-700">${getFileIconSvg(msg.mime_type || '', msg.file_name || '')}</div><div class="min-w-0 leading-tight text-left"><p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" title="${escapeHtml(msg.file_name)}">${escapeHtml(msg.file_name)}</p><p class="text-[10px] text-slate-400 font-medium">${formatBytes(msg.file_size || 0)}</p></div></div><a href="${msg.file_url}" download class="shrink-0 p-1.5 bg-[#0056D2] hover:bg-[#0046B8] text-white rounded-lg transition" title="Tải xuống"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a></div>`;
        messageDiv.innerHTML = `<div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">${headerHtml}<span>${dateStr}</span></div><div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm shadow-sm break-words ${isMe ? 'bg-[#0056D2] text-white rounded-tr-none' : 'bg-white text-slate-900 border border-slate-100 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-750 rounded-tl-none'}">${contentHtml}${msg.message ? `<div class="text-left">${escapeHtml(msg.message)}</div>` : ''}</div>`;
        chatBox.appendChild(messageDiv); scrollToBottom();
    }

    function handleSendMessage(event) {
        event.preventDefault();
        const message = messageInput.value.trim();
        const hasAttachment = attachmentInput.files.length > 0;
        if (!message && !hasAttachment) return;
        messageInput.disabled = true; sendButton.disabled = true;
        const formData = new FormData();
        if (message) formData.append('message', message);
        if (hasAttachment) formData.append('file', attachmentInput.files[0]);
        if (hasAttachment) { uploadProgressContainer.classList.remove('hidden'); uploadProgressBar.style.width = '0%'; uploadProgressPercent.textContent = '0%'; }
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/study-groups/${groupId}/messages`, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        if (hasAttachment) {
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) { const percent = Math.round((e.loaded / e.total) * 100); uploadProgressBar.style.width = percent + '%'; uploadProgressPercent.textContent = percent + '%'; }
            });
        }
        xhr.onload = function() {
            messageInput.disabled = false; sendButton.disabled = false; uploadProgressContainer.classList.add('hidden');
            if (xhr.status >= 200 && xhr.status < 300) {
                const result = JSON.parse(xhr.responseText);
                if (result.success) { messageInput.value = ''; clearSelectedAttachment(); appendMessage(result.data, true); lastMessageId = result.data.id; messageInput.focus(); }
            }
        };
        xhr.send(formData);
    }

    async function pollMessages() {
        try {
            const response = await fetch(`/study-groups/${groupId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data && result.data.messages) {
                    result.data.messages.forEach(msg => { if (msg.id > lastMessageId) { appendMessage(msg, msg.user_id === currentUserId); lastMessageId = msg.id; } });
                }
            }
        } catch (error) { console.error(error); }
    }
    setInterval(pollMessages, 4000);
</script>

{{-- Image Lightbox Modal --}}
<div id="lightbox-modal" class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300" onclick="closeLightbox()">
    <button type="button" class="absolute top-4 right-4 text-white/80 hover:text-white p-2 transition cursor-pointer hover:bg-white/10 rounded-full" onclick="closeLightbox()">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <img id="lightbox-img" src="#" alt="Phóng to ảnh" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl transition duration-300" onclick="event.stopPropagation()">
</div>

<style>
    .cursor-zoom-in {
        cursor: zoom-in;
    }
    
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
