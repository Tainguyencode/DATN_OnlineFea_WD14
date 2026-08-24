<x-instructor-layout title="Trao đổi với học viên" pageTitle="Chi tiết trao đổi" breadcrumb="Giảng viên / Trao đổi / Chi tiết">
    <div class="space-y-4 max-w-4xl">
        <!-- NÚT QUAY LẠI -->
        <div class="flex items-center justify-between">
            <a href="{{ route('instructor.discussions.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors">
                &larr; Quay lại danh sách tin nhắn
            </a>

            @if($discussion->needsReply())
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-800 border border-amber-300">
                
                    ĐANG CHỜ BẠN TRẢ LỜI
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
        
                    ĐÃ TRẢ LỜI
                </span>
            @endif
        </div>

        <!-- KHUNG CHAT ROOM -->
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm dark:border-slate-800 dark:bg-slate-900 flex flex-col min-h-[560px]">
            <!-- HEADER KHUNG CHAT -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white px-5 py-4 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="shrink-0 relative">
                        @if($discussion->user?->avatar)
                            <img src="{{ $discussion->user->avatarUrl() }}" alt="{{ $discussion->user->name }}" class="w-11 h-11 rounded-full object-cover border-2 border-emerald-400">
                        @else
                            <div class="w-11 h-11 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm border-2 border-emerald-400">
                                {{ strtoupper(mb_substr($discussion->user?->name ?? 'H', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-base leading-tight text-white truncate">
                                {{ $discussion->user?->name ?? 'Học viên ẩn danh' }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Học viên</span>
                        </div>
                        <p class="text-xs text-slate-300 truncate mt-1">
                            Khóa: <span class="font-semibold text-emerald-300">{{ $discussion->course?->title ?? $discussion->lesson?->course?->title }}</span>
                            @if($discussion->lesson)
                                &bull; Bài bắt đầu: {{ $discussion->lesson->title }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="text-right shrink-0 hidden sm:block">
                    <span class="text-xs text-slate-400">Chủ đề câu hỏi:</span>
                    <div class="text-xs font-bold text-white max-w-[200px] truncate" title="{{ $discussion->title }}">
                        {{ $discussion->title }}
                    </div>
                </div>
            </div>

            <!-- CHAT BODY (DANH SÁCH TIN NHẮN) -->
            <div id="chat-messages" class="flex-1 p-4 sm:p-6 bg-[#f8fafc] dark:bg-slate-950 overflow-y-auto space-y-4 max-h-[500px]">
                <!-- TIN NHẮN ĐẦU TIÊN TỪ HỌC VIÊN -->
                @php
                    $isDiscussionStudent = (int) $discussion->user_id !== (int) auth()->id();
                    $timeFirstMsg = $discussion->created_at->isToday() ? $discussion->created_at->format('H:i') : $discussion->created_at->format('d/m/Y H:i');
                    $cleanFirstMsgContent = preg_replace('/\s+/', ' ', $discussion->content);
                    $canRecallDisc = ((int) $discussion->user_id === (int) auth()->id() && $discussion->created_at >= now()->subHours(24)) || auth()->user()->role === 'admin';
                    $canDeleteDisc = (int) $discussion->user_id === (int) auth()->id() || auth()->user()->role === 'admin' || auth()->user()->role === 'instructor';
                @endphp
                <div id="msg-disc-{{ $discussion->id }}" class="group flex items-end gap-2.5 justify-start transition-all duration-300 rounded-2xl p-1.5 hover:bg-slate-100/60 dark:hover:bg-slate-900/60">
                    <div class="shrink-0 mb-1">
                        @if($discussion->user?->avatar)
                            <img src="{{ $discussion->user->avatarUrl() }}" alt="{{ $discussion->user->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                        @else
                            <div class="w-9 h-9 rounded-full bg-slate-700 text-white font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(mb_substr($discussion->user?->name ?? 'H', 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="max-w-[85%] sm:max-w-[75%] space-y-1 items-start text-left">
                        <div class="flex flex-wrap items-center gap-2 px-1 justify-start">
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200">{{ $discussion->user?->name ?? 'Học viên' }}</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Học viên</span>
                            @if($discussion->lesson)
                                <span class="text-[9px] font-medium px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Bài: {{ Str::limit($discussion->lesson->title, 25) }}</span>
                            @endif
                            <span class="text-[10px] text-slate-400">{{ $timeFirstMsg }}</span>

                            @if(! $discussion->is_recalled)
                                <!-- NÚT TRẢ LỜI CÂU HỎI GỐC -->
                                <button type="button" 
                                        onclick="setInstructorReplyContext('', '{{ addslashes($discussion->user?->name ?? 'Học viên') }}', '{{ addslashes(Str::limit($cleanFirstMsgContent, 70)) }}')"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 transition cursor-pointer shadow-2xs"
                                        title="Trả lời tin nhắn này">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                    <span>Trả lời</span>
                                </button>

                                @if($canRecallDisc)
                                    <form action="{{ route('discussions.recall', $discussion) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn thu hồi tin nhắn này?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[11px] font-semibold text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition cursor-pointer" title="Thu hồi tin nhắn">
                                            <span>Thu hồi</span>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>

                        @if($discussion->is_recalled)
                            <div class="rounded-2xl px-4 py-3 text-sm italic text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-bl-xs flex items-center gap-1.5 select-none">
                                <span>Tin nhắn đã được thu hồi</span>
                            </div>
                        @else
                            <div class="relative rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-xs bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-800 rounded-bl-xs">
                                @if($discussion->content)
                                    <p class="whitespace-pre-line">{{ $discussion->content }}</p>
                                @endif
                                
                                <!-- Đính kèm tin gốc -->
                                @if($discussion->attachment_path)
                                    @php $discussionAttachUrl = $discussion->attachmentUrl(); @endphp
                                    @if($discussionAttachUrl)
                                        <div class="{{ $discussion->content ? 'mt-3 pt-2 border-t border-slate-100 dark:border-slate-800' : '' }}">
                                            @if($discussion->attachment_type === 'image')
                                                <a href="{{ $discussionAttachUrl }}" target="_blank" class="block">
                                                    <img src="{{ $discussionAttachUrl }}" alt="Attachment" class="rounded-xl border border-slate-200 dark:border-slate-700 max-h-[220px] object-contain bg-black/5" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Ảnh+Không+Tồn+Tại&background=fee2e2&color=dc2626'">
                                                </a>
                                            @elseif($discussion->attachment_type === 'video')
                                                <video controls class="rounded-xl w-full max-h-[220px] max-w-[340px] bg-black">
                                                    <source src="{{ $discussionAttachUrl }}">
                                                </video>
                                            @else
                                                <a href="{{ $discussionAttachUrl }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                    <span>📎</span> Tải tệp: {{ Str::limit($discussion->attachment_name, 35) }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- CÁC TIN NHẮN TRẢ LỜI -->
                @foreach($discussion->replies as $reply)
                    @php
                        $isMe = (int) $reply->user_id === (int) auth()->id();
                        $isInstructorReply = $reply->is_instructor_answer;
                        $replyTime = $reply->created_at->isToday() ? $reply->created_at->format('H:i') : $reply->created_at->format('d/m/Y H:i');
                        $cleanReplyContent = preg_replace('/\s+/', ' ', $reply->content);
                        $canRecallReply = ($isMe && $reply->created_at >= now()->subHours(24)) || auth()->user()->role === 'admin';
                    @endphp

                    <div id="msg-reply-{{ $reply->id }}" class="group flex items-end gap-2.5 {{ $isMe ? 'justify-end' : 'justify-start' }} transition-all duration-300 rounded-2xl p-1.5 hover:bg-slate-100/60 dark:hover:bg-slate-900/60">
                        @if(! $isMe)
                            <div class="shrink-0 mb-1">
                                @if($reply->user?->avatar)
                                    <img src="{{ $reply->user->avatarUrl() }}" alt="{{ $reply->user->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-slate-700 text-white font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(mb_substr($reply->user?->name ?? 'H', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="max-w-[85%] sm:max-w-[75%] space-y-1 {{ $isMe ? 'items-end text-right' : 'items-start text-left' }}">
                            <div class="flex flex-wrap items-center gap-2 px-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <span class="text-[11px] font-bold {{ $isMe ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-200' }}">
                                    {{ $isMe ? 'Bạn (Giảng viên)' : ($reply->user?->name ?? 'Học viên') }}
                                </span>
                                @if($isInstructorReply)
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Giảng viên</span>
                                @else
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Học viên</span>
                                @endif
                                @if($reply->lesson)
                                    <span class="text-[9px] font-medium px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Bài: {{ Str::limit($reply->lesson->title, 25) }}</span>
                                @endif
                                <span class="text-[10px] text-slate-400">{{ $replyTime }}</span>
                                @if($reply->is_helpful)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 rounded">✔️ Học viên thấy hữu ích</span>
                                @endif

                                @if(! $reply->is_recalled)
                                    <!-- NÚT TRẢ LỜI DÀNH CHO GIẢNG VIÊN -->
                                    <button type="button" 
                                            onclick="setInstructorReplyContext('{{ $reply->id }}', '{{ addslashes($reply->user?->name ?? 'Người dùng') }}', '{{ addslashes(Str::limit($cleanReplyContent, 70)) }}')"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isMe ? 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200' : 'text-slate-700 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 border-slate-200' }} dark:bg-slate-800 dark:text-slate-300 dark:hover:text-emerald-400 border transition cursor-pointer shadow-2xs"
                                            title="Trả lời tin nhắn này">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                        <span>Trả lời</span>
                                    </button>

                                    <!-- NÚT THU HỒI -->
                                    @if($canRecallReply)
                                        <form action="{{ route('discussions.replies.recall', $reply) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn thu hồi tin nhắn này?')">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[11px] font-semibold text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition cursor-pointer" title="Thu hồi tin nhắn">
                                                <span>Thu hồi</span>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>

                            @if($reply->is_recalled)
                                <div class="rounded-2xl px-4 py-3 text-sm italic {{ $isMe ? 'bg-emerald-900/20 text-emerald-300 border border-emerald-800' : 'bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-800' }} rounded-bl-xs flex items-center gap-1.5 select-none">
                                    <span>Tin nhắn đã được thu hồi</span>
                                </div>
                            @else
                                <div class="relative rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-xs {{ $isMe ? 'bg-emerald-600 text-white rounded-br-xs' : 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-800 rounded-bl-xs' }}">
                                    <!-- QUOTE TRÍCH DẪN TIN NHẮN NẾU CÓ -->
                                    @if($reply->replyTo)
                                        <div onclick="scrollToMessage('msg-reply-{{ $reply->replyTo->id }}')" class="cursor-pointer mb-2.5 rounded-xl {{ $isMe ? 'bg-white/20 text-white border-l-3 border-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-l-3 border-emerald-500' }} px-3 py-1.5 text-xs transition hover:opacity-80 select-none text-left" title="Bấm để xem tin nhắn gốc">
                                            <div class="font-bold text-[11px] flex items-center gap-1">
                                                <span>↪</span> {{ $reply->replyTo->user?->name ?? 'Người dùng' }}
                                                @if($reply->replyTo->is_instructor_answer)
                                                    <span class="text-[9px] font-semibold px-1 rounded {{ $isMe ? 'bg-white/30 text-white' : 'bg-emerald-100 text-emerald-800' }}">Giảng viên</span>
                                                @else
                                                    <span class="text-[9px] font-semibold px-1 rounded {{ $isMe ? 'bg-white/30 text-white' : 'bg-blue-100 text-blue-800' }}">Học viên</span>
                                                @endif
                                            </div>
                                            <p class="truncate text-[11px] opacity-90 mt-0.5 italic">"{{ Str::limit($reply->replyTo->content, 65) }}"</p>
                                        </div>
                                    @endif

                                    @if($reply->content)
                                        <p class="whitespace-pre-line text-left">{{ $reply->content }}</p>
                                    @endif

                                    <!-- Đính kèm ở reply -->
                                    @if($reply->attachment_path)
                                        @php $replyAttachUrl = $reply->attachmentUrl(); @endphp
                                        @if($replyAttachUrl)
                                            <div class="{{ $reply->content ? 'mt-3 pt-2 border-t ' . ($isMe ? 'border-white/20' : 'border-slate-100 dark:border-slate-800') : '' }}">
                                                @if($reply->attachment_type === 'image')
                                                    <a href="{{ $replyAttachUrl }}" target="_blank" class="block">
                                                        <img src="{{ $replyAttachUrl }}" alt="Attachment" class="rounded-xl border max-h-[200px] object-contain bg-black/10" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Ảnh+Không+Tồn+Tại&background=fee2e2&color=dc2626'">
                                                    </a>
                                                @elseif($reply->attachment_type === 'video')
                                                    <video controls class="rounded-xl w-full max-h-[200px] max-w-[320px] bg-black">
                                                        <source src="{{ $replyAttachUrl }}">
                                                    </video>
                                                @else
                                                    <a href="{{ $replyAttachUrl }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold {{ $isMe ? 'text-white underline' : 'text-emerald-600 dark:text-emerald-400 hover:underline' }}">
                                                        <span>📎</span> Tải tệp: {{ Str::limit($reply->attachment_name, 35) }}
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($isMe)
                            <div class="shrink-0 mb-1">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-emerald-500">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- CHAT INPUT FOOTER (FORM TRẢ LỜI CỦA GIẢNG VIÊN) -->
            <div class="bg-white dark:bg-slate-900 p-4 border-t border-slate-200 dark:border-slate-800">
                <!-- THANH HIỂN THỊ ĐANG TRẢ LỜI TIN NHẮN NÀO -->
                <div id="instructor-reply-context-bar" class="hidden items-center justify-between bg-emerald-50 dark:bg-emerald-950/40 border-l-4 border-emerald-500 px-3.5 py-2 rounded-xl mb-3 transition-all">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                            <span>Đang trả lời <span id="instructor-reply-target-sender" class="font-extrabold underline"></span></span>
                        </div>
                        <p id="instructor-reply-target-text" class="text-[11px] text-slate-600 dark:text-slate-400 truncate mt-0.5 italic"></p>
                    </div>
                    <button type="button" onclick="cancelInstructorReplyContext()" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg font-bold leading-none p-1 rounded-full hover:bg-emerald-100 dark:hover:bg-emerald-900/40 shrink-0 cursor-pointer" title="Hủy trả lời tin nhắn này">&times;</button>
                </div>

                <form id="reply-form" action="{{ route('discussions.replies.store', $discussion) }}" method="POST" enctype="multipart/form-data" class="space-y-3" onsubmit="return validateInstructorReplyForm()">
                    @csrf
                    <input type="hidden" name="reply_to_message_id" id="instructor-reply-to-message-id" value="">
                    <div>
                        <label for="reply-comment" class="block text-xs font-bold uppercase text-slate-500 mb-1.5">
                            Phản hồi của giảng viên
                        </label>
                        <textarea id="reply-comment" 
                                  name="content" 
                                  rows="3" 
                                  oninput="clearInstructorReplyError()"
                                  class="w-full rounded-xl border border-slate-300 bg-slate-50/50 dark:bg-slate-950 p-3 text-sm text-slate-900 dark:text-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:border-slate-700" 
                                  placeholder="Nhập câu trả lời chi tiết và hướng dẫn học viên tại đây...">{{ old('content') }}</textarea>
                        <p id="instructor-reply-validation-error" class="hidden mt-1.5 text-xs font-semibold text-rose-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Vui lòng nhập nội dung tin nhắn hoặc đính kèm tệp tin.</span>
                        </p>
                        @error('content')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition" for="file-attach">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span>Đính kèm ảnh / video / tài liệu</span>
                            </label>
                            <input type="file" name="attachment" id="file-attach" class="hidden" onchange="handleFileSelected(this, 'instructor-file-preview', 'instructor-file-name')">
                            
                            <!-- BOX HIỂN THỊ TÊN FILE KHI ĐÃ CHỌN -->
                            <div id="instructor-file-preview" class="hidden items-center gap-2 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-300 px-3 py-1.5 rounded-xl text-xs font-semibold shadow-2xs">
                                <span>📎</span>
                                <span id="instructor-file-name" class="max-w-[220px] sm:max-w-[300px] truncate"></span>
                                <button type="button" onclick="removeSelectedFile('file-attach', 'instructor-file-preview')" class="text-rose-500 hover:text-rose-700 font-bold ml-1 cursor-pointer text-sm leading-none" title="Hủy tệp này">&times;</button>
                            </div>
                        </div>
                        
                        <button type="submit" class="inline-flex items-center gap-2 cursor-pointer rounded-xl bg-emerald-600 px-6 py-2.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700 shadow-xs">
                            <span>Gửi phản hồi cho học viên</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                    <div class="text-[11px] text-slate-400">Hỗ trợ Ảnh chụp màn hình, Video quay lỗi, Tài liệu nén dạng Zip/PDF (tối đa 50MB).</div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .reply-highlight {
            animation: highlightFlash 2s ease-out;
        }
        @keyframes highlightFlash {
            0% { background-color: rgba(254, 240, 138, 0.7); box-shadow: 0 0 0 2px #eab308; border-radius: 1rem; }
            70% { background-color: rgba(254, 240, 138, 0.4); box-shadow: 0 0 0 1px #eab308; }
            100% { background-color: transparent; box-shadow: none; }
        }
    </style>

    <script>
        function validateInstructorReplyForm() {
            const textarea = document.getElementById('reply-comment');
            const fileInput = document.getElementById('file-attach');
            const errEl = document.getElementById('instructor-reply-validation-error');
            const hasText = textarea && textarea.value.trim().length > 0;
            const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

            if (!hasText && !hasFile) {
                if (errEl) errEl.classList.remove('hidden');
                if (textarea) {
                    textarea.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                    textarea.focus();
                }
                return false;
            }
            if (errEl) errEl.classList.add('hidden');
            return true;
        }

        function clearInstructorReplyError() {
            const textarea = document.getElementById('reply-comment');
            const errEl = document.getElementById('instructor-reply-validation-error');
            if (errEl) errEl.classList.add('hidden');
            if (textarea) textarea.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
        }

        function handleFileSelected(input, previewId, nameId) {
            clearInstructorReplyError();
            const previewEl = document.getElementById(previewId);
            const nameEl = document.getElementById(nameId);
            if (!input.files || !input.files[0]) {
                if (previewEl) {
                    previewEl.classList.add('hidden');
                    previewEl.classList.remove('flex');
                }
                return;
            }
            const file = input.files[0];
            const fileSize = (file.size / (1024 * 1024)).toFixed(1);
            if (nameEl) {
                nameEl.textContent = `${file.name} (${fileSize} MB)`;
            }
            if (previewEl) {
                previewEl.classList.remove('hidden');
                previewEl.classList.add('flex');
            }
        }

        function removeSelectedFile(inputId, previewId) {
            const input = document.getElementById(inputId);
            const previewEl = document.getElementById(previewId);
            if (input) {
                input.value = '';
            }
            if (previewEl) {
                previewEl.classList.add('hidden');
                previewEl.classList.remove('flex');
            }
        }

        function setInstructorReplyContext(replyId, senderName, snippet) {
            const replyBar = document.getElementById('instructor-reply-context-bar');
            const replyInput = document.getElementById('instructor-reply-to-message-id');
            const replySender = document.getElementById('instructor-reply-target-sender');
            const replyText = document.getElementById('instructor-reply-target-text');
            const contentInput = document.getElementById('reply-comment');

            if (replyBar && replyInput && replySender && replyText) {
                replyInput.value = replyId;
                replySender.textContent = senderName;
                replyText.textContent = `"${snippet}"`;
                replyBar.classList.remove('hidden');
                replyBar.classList.add('flex');
                
                if (contentInput) {
                    contentInput.focus();
                    contentInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }

        function cancelInstructorReplyContext() {
            const replyBar = document.getElementById('instructor-reply-context-bar');
            const replyInput = document.getElementById('instructor-reply-to-message-id');

            if (replyBar && replyInput) {
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

        document.addEventListener('DOMContentLoaded', () => {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
    </script>
</x-instructor-layout>
