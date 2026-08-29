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

$canManageGroup = $studyGroup->canManage(Auth::user());
@endphp

<div class="ui-container py-8">
    <div class="mx-auto max-w-7xl space-y-6">
        
        {{-- Breadcrumb / Back button --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('study-groups.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại danh sách nhóm
            </a>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-[#0056D2] ring-1 ring-inset ring-blue-700/10 dark:bg-blue-950/30 dark:text-blue-300">
                    {{ $studyGroup->course->title }}
                </span>
            </div>
        </div>

        @if(isset($pendingInvitation) && $pendingInvitation)
            <div class="rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 shadow-sm dark:border-blue-900/40 dark:bg-slate-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="text-xs font-bold uppercase tracking-wider text-[#0056D2] dark:text-blue-400">Lời mời tham gia nhóm học tập</div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ $pendingInvitation->inviter->name }} đã mời bạn tham gia nhóm "{{ $studyGroup->name }}"
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400">
                        Sau khi chấp nhận, bạn có thể tham gia trò chuyện và trao đổi tài liệu cùng các thành viên trong nhóm.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <form action="{{ route('study-groups.invitations.reject', $pendingInvitation) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition cursor-pointer">
                            Từ chối
                        </button>
                    </form>
                    <form action="{{ route('study-groups.invitations.accept', $pendingInvitation) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0056D2] px-5 text-xs font-bold text-white hover:bg-[#0046B8] transition cursor-pointer shadow-sm">
                            Chấp nhận tham gia
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6 items-start">
            
            {{-- Left column: Group Details & Members --}}
            <div class="w-full lg:w-80 shrink-0 space-y-6">
                {{-- Group info --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <h1 class="text-xl font-extrabold text-slate-900 dark:text-white break-words">{{ $studyGroup->name }}</h1>
                        @if($canManageGroup)
                            <button onclick="openEditLimitModal()" class="shrink-0 p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200" title="Chỉnh sửa giới hạn thành viên">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-2.036a5 5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Người tạo: <strong class="text-slate-700 dark:text-slate-300">{{ $studyGroup->creator->name }}</strong>
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Giới hạn: <strong class="text-[#0056D2] dark:text-blue-400">{{ $studyGroup->max_members ? $studyGroup->max_members . ' thành viên' : 'Không giới hạn' }}</strong>
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-400 break-words">
                        {{ $studyGroup->description ?? 'Không có mô tả cho nhóm học tập này.' }}
                    </p>

                    @if($canManageGroup)
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
                            <button onclick="openInviteModal()" class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#0056D2] px-3 py-2 text-xs font-bold text-white hover:bg-[#0046B8] transition cursor-pointer shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                <span>+ Mời thành viên</span>
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Members list --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Thành viên ({{ $studyGroup->members->count() }}{{ $studyGroup->max_members ? '/' . $studyGroup->max_members : ' · Không giới hạn' }})
                        </h2>
                    </div>
                    <div class="space-y-3 max-h-[300px] overflow-y-auto chat-scroll pr-1">
                        @foreach($studyGroup->members as $member)
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $member->avatarUrl() }}" alt="{{ $member->name }}" class="h-8 w-8 rounded-full object-cover shrink-0 border border-slate-200 dark:border-slate-700" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=0056D2&color=fff'">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" title="{{ $member->name }}">
                                        {{ $member->name }}
                                    </p>
                                    <span class="inline-block text-[10px] px-1.5 py-0.5 rounded font-bold uppercase {{ $member->pivot->role === 'moderator' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                        {{ $member->pivot->role === 'moderator' ? 'Trưởng nhóm' : 'Học viên' }}
                                    </span>
                                </div>
                                
                                {{-- Kick button for creator or admin --}}
                                @if($canManageGroup && $member->id !== $studyGroup->creator_id)
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

                {{-- Pending Invitations List (For Owner / Instructor) --}}
                @if($canManageGroup && $studyGroup->pendingInvitations->isNotEmpty())
                    <div class="rounded-xl border border-amber-200/80 bg-amber-50/40 p-4 shadow-sm dark:border-amber-900/40 dark:bg-amber-950/20">
                        <h2 class="text-xs font-extrabold uppercase tracking-wider text-amber-900 dark:text-amber-300 mb-3 flex items-center gap-1.5">
                            <span>🟡</span> Lời mời đang chờ ({{ $studyGroup->pendingInvitations->count() }})
                        </h2>
                        <div class="space-y-2.5 max-h-[220px] overflow-y-auto chat-scroll pr-1">
                            @foreach($studyGroup->pendingInvitations as $invite)
                                <div class="flex items-center justify-between gap-2 p-2 bg-white dark:bg-slate-900 rounded-lg border border-amber-200/50 dark:border-slate-800">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <img src="{{ $invite->invitedUser->avatarUrl() }}" alt="{{ $invite->invitedUser->name }}" class="w-7 h-7 rounded-full object-cover shrink-0">
                                        <div class="min-w-0 leading-tight">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $invite->invitedUser->name }}</p>
                                            <p class="text-[10px] text-slate-500 truncate">{{ '@' . ($invite->invitedUser->username ?? $invite->invitedUser->email) }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('study-groups.invitations.cancel', [$studyGroup, $invite]) }}" method="POST" class="shrink-0" onsubmit="return confirm('Bạn có chắc muốn hủy lời mời này?')">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/30 px-2 py-1 rounded transition cursor-pointer" title="Hủy lời mời">
                                            Hủy
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right column: Chat area --}}
            <div class="w-full lg:flex-1 min-w-0">
                <div class="flex flex-col h-[650px] rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                    
                    {{-- Chat header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 font-extrabold text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900 dark:text-white">Thảo luận nhóm</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Trò chuyện trực tuyến và hỗ trợ học tập theo thời gian thực</p>
                            </div>
                        </div>
                        
                        {{-- Status indicator --}}
                        <div class="flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500" id="chat-status">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Trực tuyến
                        </div>
                    </div>

                    {{-- Message box --}}
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-3.5 bg-slate-50/20 dark:bg-slate-950/5" id="chat-box">
                        @if($studyGroup->messages->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full py-12 text-center text-slate-400 dark:text-slate-500 space-y-3" id="no-messages-placeholder">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <p class="text-sm font-medium">Chưa có tin nhắn nào trong nhóm này.</p>
                                <p class="text-xs">Hãy gửi tin nhắn đầu tiên để bắt đầu thảo luận!</p>
                            </div>
                        @else
                            @foreach($studyGroup->messages as $msg)
                                @php
                                    $isMe = (int) $msg->user_id === (int) Auth::id();
                                    $timeFormatted = $msg->created_at->format('H:i, d/m/Y');
                                    $cleanContent = preg_replace('/\s+/', ' ', (string) $msg->message);
                                    $senderName = $msg->user->name ?? 'Thành viên';
                                    $firstLetter = strtoupper(mb_substr($senderName, 0, 1));
                                    $avatarUrl = $msg->user?->avatarUrl();
                                    $hasRealAvatar = $msg->user?->avatar && $avatarUrl && !str_contains($avatarUrl, 'ui-avatars.com');
                                @endphp

                                @if($isMe)
                                    {{-- RIGHT: CHÍNH MÌNH --}}
                                    <div id="msg-{{ $msg->id }}" class="group flex items-start justify-end gap-2.5 my-1.5 p-1 rounded-xl transition-all duration-300">
                                        {{-- Khối tin nhắn --}}
                                        <div class="flex flex-col items-end max-w-[85%] sm:max-w-[75%] space-y-1">
                                            {{-- Header: Actions + Thời gian · Bạn --}}
                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 px-1">
                                                @if(!$msg->is_recalled)
                                                    <button type="button"
                                                            onclick="event.preventDefault(); event.stopPropagation(); recallMessage({{ $msg->id }}); return false;"
                                                            class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 px-1 py-0.5 rounded transition cursor-pointer"
                                                            title="Thu hồi tin nhắn">
                                                        <span>Thu hồi</span>
                                                    </button>
                                                    <button type="button" 
                                                            onclick="setReplyContext('{{ $msg->id }}', '{{ addslashes($senderName) }}', '{{ addslashes(Str::limit($cleanContent, 60)) }}')"
                                                            class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 hover:text-[#0056D2] dark:hover:text-blue-300 px-1.5 py-0.5 rounded bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-750 transition cursor-pointer mr-1" 
                                                            title="Trả lời tin nhắn này">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                                        <span>Trả lời</span>
                                                    </button>
                                                @endif
                                                <span>{{ $timeFormatted }}</span>
                                                <span>·</span>
                                                <span class="font-bold text-slate-700 dark:text-slate-300">Bạn</span>
                                            </div>

                                            @if($msg->is_recalled)
                                                {{-- Recalled box --}}
                                                <div class="rounded-2xl rounded-tr-none px-4 py-2.5 text-xs italic bg-slate-100 text-slate-400 border border-slate-200 dark:bg-slate-800/60 dark:text-slate-500 dark:border-slate-750 flex items-center gap-1.5 select-none">
                                                    <span>🚫</span>
                                                    <span>Tin nhắn đã được thu hồi</span>
                                                </div>
                                            @else
                                                {{-- Bubble --}}
                                                <div class="rounded-2xl rounded-tr-none px-4 py-2.5 text-sm shadow-2xs break-words bg-[#0056D2] text-white">
                                                    {{-- QUOTE PREVIEW --}}
                                                    @if($msg->replyTo)
                                                        <div onclick="scrollToMessage('msg-{{ $msg->replyTo->id }}')" class="cursor-pointer mb-2 rounded-xl bg-white/20 text-white border-l-3 border-white px-3 py-1.5 text-xs transition hover:opacity-85 select-none text-left" title="Bấm để xem tin nhắn gốc">
                                                            <div class="font-bold text-[11px] flex items-center gap-1">
                                                                <span>↪</span> {{ $msg->replyTo->user?->name ?? 'Người dùng' }}
                                                            </div>
                                                            <p class="truncate text-[11px] opacity-90 mt-0.5 italic">
                                                                "{{ $msg->replyTo->is_recalled ? 'Tin nhắn đã được thu hồi' : Str::limit($msg->replyTo->message ?? '[Tệp đính kèm]', 60) }}"
                                                            </p>
                                                        </div>
                                                    @endif

                                                    {{-- Attachments --}}
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
                                                        <div class="text-left whitespace-pre-line">{{ $msg->message }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Avatar bên phải của chính mình --}}
                                        <div class="shrink-0 pt-0.5" title="{{ $senderName }}">
                                            @if($hasRealAvatar)
                                                <img src="{{ $avatarUrl }}" alt="{{ $senderName }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-2xs" onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#0056D2] text-white font-bold flex items-center justify-center text-sm shadow-2xs select-none\'>{{ $firstLetter }}</div>'">
                                            @else
                                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#0056D2] text-white font-bold flex items-center justify-center text-sm shadow-2xs select-none">
                                                    {{ $firstLetter }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- LEFT: THÀNH VIÊN KHÁC --}}
                                    <div id="msg-{{ $msg->id }}" class="group flex items-start gap-2.5 my-1.5 p-1 rounded-xl transition-all duration-300">
                                        {{-- Avatar bên trái của người gửi --}}
                                        <div class="shrink-0 pt-0.5" title="{{ $senderName }}">
                                            @if($hasRealAvatar)
                                                <img src="{{ $avatarUrl }}" alt="{{ $senderName }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-2xs" onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-sm shadow-2xs select-none\'>{{ $firstLetter }}</div>'">
                                            @else
                                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-sm shadow-2xs select-none">
                                                    {{ $firstLetter }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Khối tin nhắn --}}
                                        <div class="flex flex-col items-start max-w-[85%] sm:max-w-[75%] space-y-1">
                                            {{-- Header: Tên người gửi · Thời gian + Nút trả lời --}}
                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 px-1">
                                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $senderName }}</span>
                                                <span>·</span>
                                                <span>{{ $timeFormatted }}</span>

                                                @if(!$msg->is_recalled)
                                                    <button type="button" 
                                                            onclick="setReplyContext('{{ $msg->id }}', '{{ addslashes($senderName) }}', '{{ addslashes(Str::limit($cleanContent, 60)) }}')"
                                                            class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 hover:text-[#0056D2] dark:hover:text-blue-300 px-1.5 py-0.5 rounded bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-750 transition cursor-pointer ml-1" 
                                                            title="Trả lời tin nhắn này">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                                        <span>Trả lời</span>
                                                    </button>
                                                @endif
                                            </div>

                                            @if($msg->is_recalled)
                                                {{-- Recalled box --}}
                                                <div class="rounded-2xl rounded-tl-none px-4 py-2.5 text-xs italic bg-slate-100 text-slate-400 border border-slate-200 dark:bg-slate-800/60 dark:text-slate-500 dark:border-slate-750 flex items-center gap-1.5 select-none">
                                                    <span>Tin nhắn đã được thu hồi</span>
                                                </div>
                                            @else
                                                {{-- Bubble --}}
                                                <div class="rounded-2xl rounded-tl-none px-4 py-2.5 text-sm shadow-2xs break-words bg-white text-slate-900 border border-slate-150 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-750">
                                                    {{-- QUOTE PREVIEW --}}
                                                    @if($msg->replyTo)
                                                        <div onclick="scrollToMessage('msg-{{ $msg->replyTo->id }}')" class="cursor-pointer mb-2 rounded-xl bg-slate-100 text-slate-700 border-l-3 border-[#0056D2] dark:bg-slate-700/60 dark:text-slate-200 px-3 py-1.5 text-xs transition hover:opacity-85 select-none text-left" title="Bấm để xem tin nhắn gốc">
                                                            <div class="font-bold text-[11px] flex items-center gap-1">
                                                                <span>↪</span> {{ $msg->replyTo->user?->name ?? 'Người dùng' }}
                                                            </div>
                                                            <p class="truncate text-[11px] opacity-90 mt-0.5 italic">
                                                                "{{ $msg->replyTo->is_recalled ? 'Tin nhắn đã được thu hồi' : Str::limit($msg->replyTo->message ?? '[Tệp đính kèm]', 60) }}"
                                                            </p>
                                                        </div>
                                                    @endif

                                                    {{-- Attachments --}}
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
                                                        <div class="text-left whitespace-pre-line">{{ $msg->message }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    {{-- Chat input form --}}
                    <div class="border-t border-slate-100 dark:border-slate-800 p-4 bg-white dark:bg-slate-900">
                        
                        {{-- REPLY CONTEXT BAR --}}
                        <div id="reply-context-bar" class="hidden items-center justify-between bg-blue-50 dark:bg-blue-950/40 border-l-4 border-[#0056D2] px-3.5 py-2 rounded-xl mb-2.5 transition-all">
                            <div class="min-w-0 flex-1 pr-2">
                                <div class="text-xs font-bold text-[#0056D2] dark:text-blue-300 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                    <span>Đang trả lời <span id="reply-target-sender" class="font-extrabold underline"></span></span>
                                </div>
                                <p id="reply-target-text" class="text-[11px] text-slate-600 dark:text-slate-400 truncate mt-0.5 italic"></p>
                            </div>
                            <button type="button" onclick="cancelReplyContext()" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg font-bold leading-none p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 shrink-0 cursor-pointer" title="Hủy trả lời tin nhắn này">&times;</button>
                        </div>

                        {{-- Preview container --}}
                        <div id="attachment-preview-container" class="hidden mb-3 p-3 bg-slate-50 dark:bg-slate-950 rounded-xl flex items-center justify-between border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-3 min-w-0">
                                <div id="attachment-preview-media" class="relative w-16 h-16 shrink-0 rounded-lg overflow-hidden border border-slate-300 dark:border-slate-700 flex items-center justify-center bg-white dark:bg-slate-900">
                                    <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                                    <div id="file-icon-preview" class="text-slate-400"></div>
                                </div>
                                <div class="min-w-0 leading-snug">
                                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[200px]" id="attachment-preview-name">file.pdf</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" id="attachment-preview-size">0 KB</p>
                                </div>
                            </div>
                            <button type="button" onclick="clearSelectedAttachment()" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 transition cursor-pointer" aria-label="Bỏ tệp đính kèm">
                                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Progress bar --}}
                        <div id="upload-progress-container" class="hidden mb-3 p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 font-semibold" id="upload-progress-filename">Đang tải lên...</span>
                                <span class="text-xs font-bold text-blue-600" id="upload-progress-percent">0%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div id="upload-progress-bar" class="bg-blue-600 h-full rounded-full transition-all duration-150" style="width: 0%"></div>
                            </div>
                        </div>

                        <form id="send-form" action="{{ route('study-groups.messages.store', $studyGroup) }}" method="POST" onsubmit="event.preventDefault(); handleSendMessage(event); return false;" class="flex gap-3 items-center">
                            @csrf
                            <input type="hidden" name="reply_to_message_id" id="reply-to-message-id" value="">
                            <div class="relative flex-1 flex items-center">
                                {{-- Attachment Button --}}
                                <button type="button" 
                                        onclick="document.getElementById('attachment-input').click()"
                                        class="absolute left-3 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 p-1.5 rounded-lg transition cursor-pointer"
                                        aria-label="Đính kèm tệp tin"
                                        aria-describedby="attachment-validation-error"
                                        title="Đính kèm tệp tin">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                </button>
                                
                                <input type="file" 
                                       id="attachment-input" 
                                       name="file" 
                                       accept="image/*,video/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-zip-compressed,text/plain" 
                                       aria-describedby="attachment-validation-error"
                                       class="hidden" 
                                       onchange="handleAttachmentSelect(event)">

                                <input type="text" 
                                       id="message-input" 
                                       name="message"
                                       placeholder="Nhập nội dung tin nhắn..." 
                                       autocomplete="off"
                                       class="flex-1 rounded-xl border border-slate-300 bg-white pl-12 pr-4 py-3 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white transition">
                            </div>
                            <button type="submit" 
                                    id="send-button"
                                    aria-label="Gửi tin nhắn"
                                    class="inline-flex h-11 items-center justify-center rounded-xl bg-[#0056D2] px-6 text-sm font-bold text-white transition hover:bg-[#0046B8] dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer disabled:opacity-50">
                                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </form>
                        <p id="attachment-validation-error" class="mt-2 hidden text-xs font-semibold text-rose-600 dark:text-rose-400" role="status" aria-live="polite"></p>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

{{-- MODAL MỜI THÀNH VIÊN VÀO NHÓM --}}
<div id="inviteModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>✉️</span> Mời thành viên vào nhóm
            </h3>
            <button onclick="closeInviteModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="space-y-3">
            <div class="flex gap-2">
                <input type="text" id="invite-search-input" placeholder="Nhập email hoặc username..." class="flex-1 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white" onkeyup="if(event.key==='Enter') searchUsersForInvite()">
                <button type="button" onclick="searchUsersForInvite()" class="inline-flex items-center justify-center rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white hover:bg-[#0046B8] transition cursor-pointer">
                    Tìm kiếm
                </button>
            </div>

            <div id="invite-search-loading" class="hidden text-center py-4 text-xs text-slate-400">
                Đang tìm kiếm...
            </div>

            <div id="invite-search-results" class="space-y-2 max-h-[280px] overflow-y-auto chat-scroll pr-1">
                {{-- Dynamic Search Results --}}
            </div>
        </div>
    </div>
</div>

{{-- MODAL SỬA GIỚI HẠN THÀNH VIÊN (CHO OWNER / INSTRUCTOR) --}}
@if($canManageGroup)
<div id="editLimitModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cài đặt nhóm học tập</h3>
            <button onclick="closeEditLimitModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('study-groups.update', $studyGroup) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label for="modal_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Tên nhóm</label>
                <input type="text" name="name" id="modal_name" required value="{{ $studyGroup->name }}" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </div>

            <div>
                <label for="modal_description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Mô tả</label>
                <textarea name="description" id="modal_description" rows="3" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ $studyGroup->description }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giới hạn thành viên</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                        <input type="radio" name="max_members_type" value="unlimited" {{ $studyGroup->max_members === null ? 'checked' : '' }} onchange="toggleModalLimitInput(false)" class="text-[#0056D2] focus:ring-[#0056D2]">
                        <span>Không giới hạn</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                        <input type="radio" name="max_members_type" value="custom" {{ $studyGroup->max_members !== null ? 'checked' : '' }} onchange="toggleModalLimitInput(true)" class="text-[#0056D2] focus:ring-[#0056D2]">
                        <span>Giới hạn số lượng</span>
                    </label>
                </div>
                <div id="modal_max_members_wrapper" class="mt-2 {{ $studyGroup->max_members === null ? 'hidden' : '' }}">
                    <input type="number" name="max_members" id="modal_max_members" min="{{ $studyGroup->members->count() }}" value="{{ $studyGroup->max_members ?? 20 }}" placeholder="Số thành viên tối đa" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <p class="text-[11px] text-slate-400 mt-1">Số thành viên hiện tại: {{ $studyGroup->members->count() }}. Không thể đặt thấp hơn số này.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditLimitModal()" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 cursor-pointer">
                    Hủy
                </button>
                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#0056D2] px-4 text-sm font-bold text-white hover:bg-[#0046B8] dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Image Lightbox Modal --}}
<div id="lightbox-modal" class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300" onclick="closeLightbox()">
    <button type="button" class="absolute top-4 right-4 text-white/80 hover:text-white p-2 transition cursor-pointer hover:bg-white/10 rounded-full" onclick="closeLightbox()">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <img id="lightbox-img" src="#" alt="Phóng to ảnh" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl transition duration-300" onclick="event.stopPropagation()">
</div>

<script>
    const groupId = {{ $studyGroup->id }};
    const currentUserId = {{ Auth::id() }};
    const sendForm = document.getElementById('send-form');
    const chatBox = document.getElementById('chat-box');
    const chatStatus = document.getElementById('chat-status');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const replyInput = document.getElementById('reply-to-message-id');
    const replyBar = document.getElementById('reply-context-bar');
    const replyTargetSender = document.getElementById('reply-target-sender');
    const replyTargetText = document.getElementById('reply-target-text');
    const attachmentInput = document.getElementById('attachment-input');
    const attachmentPreviewContainer = document.getElementById('attachment-preview-container');
    const imagePreview = document.getElementById('image-preview');
    const fileIconPreview = document.getElementById('file-icon-preview');
    const attachmentPreviewName = document.getElementById('attachment-preview-name');
    const attachmentPreviewSize = document.getElementById('attachment-preview-size');
    const attachmentValidationError = document.getElementById('attachment-validation-error');
    const uploadProgressContainer = document.getElementById('upload-progress-container');
    const uploadProgressPercent = document.getElementById('upload-progress-percent');
    const uploadProgressBar = document.getElementById('upload-progress-bar');
    let lastMessageId = {{ $studyGroup->messages->last()->id ?? 0 }};
    let isSendingMessage = false;
    let realtimeConnected = false;
    let pollTimer = null;

    function showStudyGroupToast(message, type = 'error') {
        const safeMessage = typeof message === 'string' && message.trim() !== ''
            ? message
            : 'Không thể thực hiện thao tác. Vui lòng thử lại.';

        if (window.AppToast?.show) {
            window.AppToast.show({ type, message: safeMessage });
            return;
        }

        console.error(safeMessage);
    }

    function setAttachmentValidationError(message = '') {
        const safeMessage = typeof message === 'string' ? message.trim() : '';
        attachmentValidationError.textContent = safeMessage;
        attachmentValidationError.classList.toggle('hidden', safeMessage === '');
        attachmentInput.setAttribute('aria-invalid', safeMessage === '' ? 'false' : 'true');
    }

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
        const lowerMime = (mime || '').toLowerCase();
        const lowerName = (name || '').toLowerCase();
        if (lowerMime.includes('pdf') || lowerName.endsWith('.pdf')) return pdf;
        if (lowerMime.includes('word') || lowerMime.includes('document') || lowerName.endsWith('.doc') || lowerName.endsWith('.docx')) return word;
        if (lowerMime.includes('excel') || lowerMime.includes('sheet') || lowerName.endsWith('.xls') || lowerName.endsWith('.xlsx')) return excel;
        if (lowerMime.includes('zip') || lowerMime.includes('compressed') || lowerName.endsWith('.zip')) return zip;
        return text;
    }

    // REPLY LOGIC
    function setReplyContext(msgId, senderName, snippet) {
        if (replyInput && replyBar && replyTargetSender && replyTargetText) {
            replyInput.value = msgId;
            replyTargetSender.textContent = senderName;
            replyTargetText.textContent = snippet ? `"${snippet}"` : '[Tin nhắn]';
            replyBar.classList.remove('hidden');
            replyBar.classList.add('flex');
            messageInput.focus();
        }
    }

    function cancelReplyContext() {
        if (replyInput && replyBar) {
            replyInput.value = '';
            replyBar.classList.add('hidden');
            replyBar.classList.remove('flex');
        }
    }

    function scrollToMessage(elementId) {
        const el = document.getElementById(elementId);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('reply-highlight');
            setTimeout(() => {
                el.classList.remove('reply-highlight');
            }, 2100);
        }
    }

    // RECALL LOGIC
    async function recallMessage(msgId) {
        if (!confirm('Bạn có chắc chắn muốn thu hồi tin nhắn này?')) return;
        try {
            const token = document.querySelector('input[name="_token"]').value;
            const res = await fetch(`/study-groups/${groupId}/messages/${msgId}/recall`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (res.ok && data.success) {
                // Update message in DOM
                const msgEl = document.getElementById(`msg-${msgId}`);
                if (msgEl) {
                    renderRecalledMessage(msgEl, data.data || { id: msgId, user_id: currentUserId, created_at: new Date().toISOString() });
                }
            } else {
                showStudyGroupToast(data.message || 'Không thể thu hồi tin nhắn.');
            }
        } catch (e) {
            console.error(e);
            showStudyGroupToast('Không thể kết nối tới máy chủ. Vui lòng thử lại.');
        }
    }

    function getAvatarHtml(user, isMe) {
        const name = user?.name || 'Người dùng';
        const firstLetter = escapeHtml(name.trim().charAt(0).toUpperCase() || 'U');
        const avatarUrl = user?.avatar_url || (user?.avatar ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`) : null);
        const bgClass = isMe ? 'bg-[#0056D2]' : 'bg-blue-600';
        const escapedName = escapeHtml(name);

        if (avatarUrl && !avatarUrl.includes('ui-avatars.com')) {
            return `
                <div class="shrink-0 pt-0.5" title="${escapedName}">
                    <img src="${escapeHtml(avatarUrl)}" alt="${escapedName}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-2xs" onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\\'w-9 h-9 sm:w-10 sm:h-10 rounded-full ${bgClass} text-white font-bold flex items-center justify-center text-sm shadow-2xs select-none\\'>${firstLetter}</div>'">
                </div>
            `;
        }

        return `
            <div class="shrink-0 pt-0.5" title="${escapedName}">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full ${bgClass} text-white font-bold flex items-center justify-center text-sm shadow-2xs select-none">
                    ${firstLetter}
                </div>
            </div>
        `;
    }

    function renderRecalledMessage(msgEl, msg) {
        const isMe = parseInt(msg.user_id) === currentUserId;
        const dateStr = formatMessageDate(msg.created_at);
        const senderName = escapeHtml(msg.user?.name || (isMe ? 'Bạn' : 'Người dùng'));
        const avatarHtml = getAvatarHtml(msg.user, isMe);

        if (isMe) {
            msgEl.className = 'group flex items-start justify-end gap-2.5 my-1.5 p-1 rounded-xl transition-all duration-300';
            msgEl.innerHTML = `
                <div class="flex flex-col items-end max-w-[85%] sm:max-w-[75%] space-y-1">
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 px-1">
                        <span>${dateStr}</span>
                        <span>·</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">Bạn</span>
                    </div>
                    <div class="rounded-2xl rounded-tr-none px-4 py-2.5 text-xs italic bg-slate-100 text-slate-400 border border-slate-200 dark:bg-slate-800/60 dark:text-slate-500 dark:border-slate-750 flex items-center gap-1.5 select-none">
                        <span>🚫</span>
                        <span>Tin nhắn đã được thu hồi</span>
                    </div>
                </div>
                ${avatarHtml}
            `;
        } else {
            msgEl.className = 'group flex items-start gap-2.5 my-1.5 p-1 rounded-xl transition-all duration-300';
            msgEl.innerHTML = `
                ${avatarHtml}
                <div class="flex flex-col items-start max-w-[85%] sm:max-w-[75%] space-y-1">
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 px-1">
                        <span class="font-bold text-slate-800 dark:text-slate-200">${senderName}</span>
                        <span>·</span>
                        <span>${dateStr}</span>
                    </div>
                    <div class="rounded-2xl rounded-tl-none px-4 py-2.5 text-xs italic bg-slate-100 text-slate-400 border border-slate-200 dark:bg-slate-800/60 dark:text-slate-500 dark:border-slate-750 flex items-center gap-1.5 select-none">
                        <span>🚫</span>
                        <span>Tin nhắn đã được thu hồi</span>
                    </div>
                </div>
            `;
        }
    }

    function handleAttachmentSelect(event) {
        const file = event.target.files[0];
        if (!file) return;
        const sizeMb = file.size / (1024 * 1024);
        if (file.type.startsWith('image/') && sizeMb > 5) {
            clearSelectedAttachment();
            setAttachmentValidationError('Ảnh có dung lượng tối đa 5 MB. Vui lòng chọn ảnh nhỏ hơn.');
            return;
        }
        if (file.type.startsWith('video/') && sizeMb > 100) {
            clearSelectedAttachment();
            setAttachmentValidationError('Video có dung lượng tối đa 100 MB. Vui lòng chọn video nhỏ hơn.');
            return;
        }
        if (!file.type.startsWith('image/') && !file.type.startsWith('video/') && sizeMb > 20) {
            clearSelectedAttachment();
            setAttachmentValidationError('Tệp có dung lượng tối đa 20 MB. Vui lòng chọn tệp nhỏ hơn.');
            return;
        }
        setAttachmentValidationError();
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
        setAttachmentValidationError();
    }

    function showLightbox(src) { document.getElementById('lightbox-img').src = src; document.getElementById('lightbox-modal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeLightbox() { document.getElementById('lightbox-modal').classList.add('hidden'); document.body.style.overflow = ''; }

    function appendMessage(msg, isMe) {
        const existingEl = document.getElementById(`msg-${msg.id}`);
        if (existingEl) {
            if (msg.is_recalled) {
                renderRecalledMessage(existingEl, msg);
            }
            return;
        }

        const shouldStickToBottom = isMe || (chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 120);
        const placeholder = document.getElementById('no-messages-placeholder');
        if (placeholder) placeholder.remove();

        const dateStr = formatMessageDate(msg.created_at);
        const messageDiv = document.createElement('div');
        messageDiv.id = `msg-${msg.id}`;

        if (msg.is_recalled) {
            renderRecalledMessage(messageDiv, msg);
            insertMessageInOrder(messageDiv, msg.id);
            if (shouldStickToBottom) scrollToBottom();
            return;
        }

        const cleanMsg = (msg.message || '').replace(/\s+/g, ' ');
        const escapedName = escapeHtml(msg.user?.name || (isMe ? 'Bạn' : 'Thành viên'));
        const avatarHtml = getAvatarHtml(msg.user, isMe);

        let headerHtml = '';
        if (isMe) {
            messageDiv.className = 'group flex items-start justify-end gap-2.5 my-1.5 p-1 rounded-xl transition-all duration-300';
            headerHtml = `
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 px-1">
                    <button type="button"
                            onclick="event.preventDefault(); event.stopPropagation(); recallMessage(${msg.id}); return false;"
                            class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 px-1 py-0.5 rounded transition cursor-pointer"
                            title="Thu hồi tin nhắn">
                        <span>Thu hồi</span>
                    </button>
                    <button type="button" 
                            onclick="setReplyContext('${msg.id}', '${escapedName.replace(/'/g, "\\'")}', '${escapeHtml(cleanMsg).substring(0, 60).replace(/'/g, "\\'")}')"
                            class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 hover:text-[#0056D2] dark:hover:text-blue-300 px-1.5 py-0.5 rounded bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-750 transition cursor-pointer mr-1" 
                            title="Trả lời tin nhắn này">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                        <span>Trả lời</span>
                    </button>
                    <span>${dateStr}</span>
                    <span>·</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">Bạn</span>
                </div>
            `;
        } else {
            messageDiv.className = 'group flex items-start gap-2.5 my-1.5 p-1 rounded-xl transition-all duration-300';
            headerHtml = `
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 px-1">
                    <span class="font-bold text-slate-800 dark:text-slate-200">${escapedName}</span>
                    <span>·</span>
                    <span>${dateStr}</span>
                    <button type="button" 
                            onclick="setReplyContext('${msg.id}', '${escapedName.replace(/'/g, "\\'")}', '${escapeHtml(cleanMsg).substring(0, 60).replace(/'/g, "\\'")}')"
                            class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 hover:text-[#0056D2] dark:hover:text-blue-300 px-1.5 py-0.5 rounded bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-750 transition cursor-pointer ml-1" 
                            title="Trả lời tin nhắn này">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                        <span>Trả lời</span>
                    </button>
                </div>
            `;
        }

        let quoteHtml = '';
        if (msg.reply_to) {
            const parentSender = escapeHtml(msg.reply_to.user?.name || 'Người dùng');
            const parentText = msg.reply_to.is_recalled ? 'Tin nhắn đã được thu hồi' : escapeHtml((msg.reply_to.message || '[Tệp đính kèm]').substring(0, 60));
            quoteHtml = `
                <div onclick="scrollToMessage('msg-${msg.reply_to.id}')" class="cursor-pointer mb-2 rounded-xl ${isMe ? 'bg-white/20 text-white border-l-3 border-white' : 'bg-slate-100 text-slate-700 border-l-3 border-[#0056D2] dark:bg-slate-700/60 dark:text-slate-200'} px-3 py-1.5 text-xs transition hover:opacity-85 select-none text-left" title="Bấm để xem tin nhắn gốc">
                    <div class="font-bold text-[11px] flex items-center gap-1">
                        <span>↪</span> ${parentSender}
                    </div>
                    <p class="truncate text-[11px] opacity-90 mt-0.5 italic">"${parentText}"</p>
                </div>
            `;
        }

        let contentHtml = '';
        if (msg.message_type === 'image' && msg.image_url) {
            contentHtml = `<img src="${msg.image_url}" class="max-w-full max-h-[250px] rounded-lg cursor-zoom-in mb-1 object-cover hover:opacity-95 transition" onclick="showLightbox('${msg.image_url}')" alt="Chat Image">`;
        } else if (msg.message_type === 'video' && msg.file_url) {
            contentHtml = `<div class="mb-1"><video class="max-w-full max-h-[250px] rounded-lg shadow-sm border border-slate-200" controls preload="metadata"><source src="${msg.file_url}" type="${msg.mime_type}"></video></div>`;
        } else if (msg.message_type === 'file' && msg.file_url) {
            contentHtml = `<div class="mb-2 p-3 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-between gap-3 border border-slate-200 dark:border-slate-700 min-w-[200px] sm:min-w-[280px]"><div class="flex items-center gap-2.5 min-w-0"><div class="w-9 h-9 shrink-0 flex items-center justify-center bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-250 dark:border-slate-700">${getFileIconSvg(msg.mime_type || '', msg.file_name || '')}</div><div class="min-w-0 leading-tight text-left"><p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" title="${escapeHtml(msg.file_name)}">${escapeHtml(msg.file_name)}</p><p class="text-[10px] text-slate-400 font-medium">${formatBytes(msg.file_size || 0)}</p></div></div><a href="${msg.file_url}" download class="shrink-0 p-1.5 bg-[#0056D2] hover:bg-[#0046B8] text-white rounded-lg transition" title="Tải xuống"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a></div>`;
        }

        const bubbleHtml = `
            <div class="flex flex-col ${isMe ? 'items-end' : 'items-start'} max-w-[85%] sm:max-w-[75%] space-y-1">
                ${headerHtml}
                <div class="rounded-2xl ${isMe ? 'rounded-tr-none bg-[#0056D2] text-white' : 'rounded-tl-none bg-white text-slate-900 border border-slate-150 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-750'} px-4 py-2.5 text-sm shadow-2xs break-words">
                    ${quoteHtml}
                    ${contentHtml}
                    ${msg.message ? `<div class="text-left whitespace-pre-line">${escapeHtml(msg.message)}</div>` : ''}
                </div>
            </div>
        `;

        if (isMe) {
            messageDiv.innerHTML = bubbleHtml + avatarHtml;
        } else {
            messageDiv.innerHTML = avatarHtml + bubbleHtml;
        }

        insertMessageInOrder(messageDiv, msg.id);
        lastMessageId = Math.max(lastMessageId, Number(msg.id) || 0);
        if (shouldStickToBottom) scrollToBottom();
    }

    function insertMessageInOrder(messageElement, messageId) {
        const nextMessage = Array.from(chatBox.children).find((element) => {
            const id = Number.parseInt(element.id?.replace('msg-', ''), 10);
            return Number.isFinite(id) && id > Number(messageId);
        });

        if (nextMessage) {
            chatBox.insertBefore(messageElement, nextMessage);
        } else {
            chatBox.appendChild(messageElement);
        }
    }

    function handleSendMessage(event) {
        event.preventDefault();
        if (isSendingMessage) return;
        const message = messageInput.value.trim();
        const hasAttachment = attachmentInput.files.length > 0;
        if (!message && !hasAttachment) return;
        isSendingMessage = true;
        messageInput.disabled = true; sendButton.disabled = true;
        const formData = new FormData();
        if (message) formData.append('message', message);
        if (replyInput.value) formData.append('reply_to_message_id', replyInput.value);
        if (hasAttachment) formData.append('file', attachmentInput.files[0]);
        if (hasAttachment) { uploadProgressContainer.classList.remove('hidden'); uploadProgressBar.style.width = '0%'; uploadProgressPercent.textContent = '0%'; }
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/study-groups/${groupId}/messages`, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', sendForm.querySelector('input[name="_token"]').value);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        const socketId = window.Echo?.socketId?.();
        if (socketId) xhr.setRequestHeader('X-Socket-ID', socketId);
        if (hasAttachment) {
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) { const percent = Math.round((e.loaded / e.total) * 100); uploadProgressBar.style.width = percent + '%'; uploadProgressPercent.textContent = percent + '%'; }
            });
        }
        xhr.onload = function() {
            isSendingMessage = false;
            messageInput.disabled = false; sendButton.disabled = false; uploadProgressContainer.classList.add('hidden');
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const result = JSON.parse(xhr.responseText);
                    if (result.success) {
                        messageInput.value = '';
                        cancelReplyContext();
                        clearSelectedAttachment();
                        appendMessage(result.data, true);
                        messageInput.focus();
                    }
                } catch (error) {
                    showStudyGroupToast('Máy chủ trả về dữ liệu không hợp lệ. Tin nhắn sẽ được đồng bộ lại.');
                    schedulePoll(0);
                }
            } else {
                try {
                    const err = JSON.parse(xhr.responseText);
                    showStudyGroupToast(err.message || 'Không thể gửi tin nhắn. Vui lòng thử lại.');
                } catch(e) {
                    showStudyGroupToast('Không thể gửi tin nhắn. Vui lòng thử lại.');
                }
            }
        };
        xhr.onerror = function() {
            isSendingMessage = false;
            messageInput.disabled = false; sendButton.disabled = false; uploadProgressContainer.classList.add('hidden');
            showStudyGroupToast('Không thể kết nối tới máy chủ. Vui lòng thử lại.');
        };
        xhr.send(formData);
    }

    async function pollMessages() {
        try {
            const response = await fetch(`/study-groups/${groupId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data && result.data.messages) {
                    result.data.messages.forEach(msg => {
                        const existing = document.getElementById(`msg-${msg.id}`);
                        if (!existing) {
                            appendMessage(msg, parseInt(msg.user_id) === currentUserId);
                        } else if (msg.is_recalled) {
                            const el = document.getElementById(`msg-${msg.id}`);
                            if (el && !el.innerHTML.includes('Tin nhắn đã được thu hồi')) {
                                renderRecalledMessage(el, msg);
                            }
                        }
                    });
                }
            }
        } catch (error) { console.error(error); }
        schedulePoll(realtimeConnected ? 30000 : 3500);
    }

    function schedulePoll(delay = 3500) {
        window.clearTimeout(pollTimer);
        pollTimer = window.setTimeout(pollMessages, delay);
    }

    function setRealtimeStatus(connected) {
        realtimeConnected = connected;
        chatStatus.innerHTML = connected
            ? '<span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> Trực tuyến'
            : '<span class="h-2 w-2 rounded-full bg-amber-500"></span> Đang kết nối lại';
        schedulePoll(connected ? 30000 : 0);
    }

    if (window.Echo) {
        window.Echo.private(`study-group.${groupId}`)
            .listen('.study-group.message.created', (event) => {
                appendMessage(event.message, Number(event.message.user_id) === currentUserId);
            })
            .listen('.study-group.message.recalled', (event) => {
                const element = document.getElementById(`msg-${event.message.id}`);
                if (element) renderRecalledMessage(element, event.message);
            })
            .error((error) => {
                console.error('Study group channel error:', error);
                setRealtimeStatus(false);
            });

        const connection = window.Echo.connector?.pusher?.connection;
        connection?.bind('connected', () => setRealtimeStatus(true));
        connection?.bind('disconnected', () => setRealtimeStatus(false));
        connection?.bind('error', (error) => {
            console.error('Reverb connection error:', error);
            setRealtimeStatus(false);
        });
        setRealtimeStatus(connection?.state === 'connected');
    } else {
        setRealtimeStatus(false);
    }

    // INVITATION MODAL JS
    function openInviteModal() {
        document.getElementById('inviteModal').classList.remove('hidden');
        document.getElementById('invite-search-input').focus();
    }
    function closeInviteModal() {
        document.getElementById('inviteModal').classList.add('hidden');
    }

    async function searchUsersForInvite() {
        const query = document.getElementById('invite-search-input').value.trim();
        if (!query) return;
        const loadingEl = document.getElementById('invite-search-loading');
        const resultsEl = document.getElementById('invite-search-results');
        loadingEl.classList.remove('hidden');
        resultsEl.innerHTML = '';

        try {
            const res = await fetch(`/study-groups/${groupId}/search-users?q=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            loadingEl.classList.add('hidden');

            if (data.success && data.data) {
                if (data.data.length === 0) {
                    resultsEl.innerHTML = '<p class="text-center text-xs text-slate-500 py-4">Không tìm thấy người dùng phù hợp.</p>';
                    return;
                }
                resultsEl.innerHTML = data.data.map(u => {
                    let actionBtn = '';
                    if (u.is_self) {
                        actionBtn = '<span class="text-xs text-slate-400 italic">Bạn</span>';
                    } else if (u.is_member) {
                        actionBtn = '<span class="text-xs text-emerald-600 font-semibold">Đã tham gia</span>';
                    } else if (u.has_pending_invite) {
                        actionBtn = '<span class="text-xs text-amber-600 font-semibold">Đã gửi lời mời</span>';
                    } else {
                        actionBtn = `<button type="button" onclick="sendInvite(${u.id}, '${escapeHtml(u.name)}')" class="px-3 py-1 bg-[#0056D2] hover:bg-[#0046B8] text-white text-xs font-bold rounded-lg transition cursor-pointer">Mời</button>`;
                    }

                    return `
                        <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="${u.avatar_url}" alt="${escapeHtml(u.name)}" class="w-9 h-9 rounded-full object-cover shrink-0">
                                <div class="min-w-0 leading-tight">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">${escapeHtml(u.name)}</p>
                                    <p class="text-xs text-slate-500 truncate">@${escapeHtml(u.username)} · ${escapeHtml(u.email)}</p>
                                </div>
                            </div>
                            <div class="shrink-0" id="invite-btn-${u.id}">
                                ${actionBtn}
                            </div>
                        </div>
                    `;
                }).join('');
            }
        } catch (e) {
            loadingEl.classList.add('hidden');
            resultsEl.innerHTML = '<p class="text-center text-xs text-rose-500 py-4">Lỗi tìm kiếm.</p>';
        }
    }

    async function sendInvite(userId, userName) {
        const btnContainer = document.getElementById(`invite-btn-${userId}`);
        if (btnContainer) btnContainer.innerHTML = '<span class="text-xs text-slate-400">Đang gửi...</span>';

        try {
            const token = document.querySelector('input[name="_token"]').value;
            const res = await fetch(`/study-groups/${groupId}/invite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ user_id: userId })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                if (btnContainer) btnContainer.innerHTML = '<span class="text-xs text-amber-600 font-semibold">Đã gửi lời mời</span>';
                showStudyGroupToast(`Đã gửi lời mời tham gia nhóm tới ${userName}!`, 'success');
                setTimeout(() => window.location.reload(), 1200);
            } else {
                showStudyGroupToast(data.message || 'Không thể gửi lời mời.');
                if (btnContainer) btnContainer.innerHTML = `<button type="button" onclick="sendInvite(${userId}, '${userName}')" class="px-3 py-1 bg-[#0056D2] hover:bg-[#0046B8] text-white text-xs font-bold rounded-lg transition cursor-pointer">Mời</button>`;
            }
        } catch (e) {
            console.error(e);
            showStudyGroupToast('Không thể kết nối tới máy chủ. Vui lòng thử lại.');
            if (btnContainer) btnContainer.innerHTML = `<button type="button" onclick="sendInvite(${userId}, '${userName}')" class="px-3 py-1 bg-[#0056D2] hover:bg-[#0046B8] text-white text-xs font-bold rounded-lg transition cursor-pointer">Mời</button>`;
        }
    }

    // EDIT LIMIT MODAL JS
    function openEditLimitModal() {
        const modal = document.getElementById('editLimitModal');
        if (modal) modal.classList.remove('hidden');
    }
    function closeEditLimitModal() {
        const modal = document.getElementById('editLimitModal');
        if (modal) modal.classList.add('hidden');
    }
    function toggleModalLimitInput(isCustom) {
        const wrapper = document.getElementById('modal_max_members_wrapper');
        const input = document.getElementById('modal_max_members');
        if (wrapper && input) {
            if (isCustom) {
                wrapper.classList.remove('hidden');
                input.required = true;
                if (!input.value || parseInt(input.value) <= 0) input.value = 20;
            } else {
                wrapper.classList.add('hidden');
                input.required = false;
                input.value = '';
            }
        }
    }
</script>

<style>
    .cursor-zoom-in { cursor: zoom-in; }
    .chat-scroll::-webkit-scrollbar { width: 4px; }
    .chat-scroll::-webkit-scrollbar-track { background: transparent; }
    .chat-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    .dark .chat-scroll::-webkit-scrollbar-thumb { background: #475569; }

    .reply-highlight {
        animation: highlight-pulse 2s ease;
    }
    @keyframes highlight-pulse {
        0%, 100% { background-color: transparent; }
        20%, 60% { background-color: rgba(251, 191, 36, 0.35); border-radius: 0.75rem; }
    }
</style>
