@props([
    'course',
    'lesson',
    'isEnrolled' => false,
    'courseDiscussion' => null,
])

@php
    $user = auth()->user();
    $instructor = $course->instructor;
    $isInstructor = $user && $user->isInstructor() && (int) $course->instructor_id === (int) $user->id;
    $isAdmin = $user && $user->isAdmin();
    $canAsk = $user && $user->isStudent() && $isEnrolled;
    $activeDiscussion = $courseDiscussion;
@endphp

<!-- COURSE CHAT DRAWER / PANEL (SLIDE-OVER FROM RIGHT) -->
<div x-show="chatOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-hidden pointer-events-none" 
     role="dialog" 
     aria-modal="false">

    <div class="fixed inset-y-0 right-0 max-w-full flex pointer-events-auto shadow-2xl">
        <div x-show="chatOpen"
             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-md sm:max-w-lg bg-white shadow-2xl flex flex-col h-full border-l border-slate-200">

            <!-- HEADER CHAT (LUÔN HIỂN THỊ AVATAR + TÊN GIẢNG VIÊN) -->
            <div class="bg-white border-b border-slate-200 px-4 py-3.5 flex items-center justify-between shrink-0 shadow-2xs">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        @if($instructor?->avatar)
                            <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="w-11 h-11 rounded-full object-cover border border-slate-200 shadow-2xs">
                        @else
                            <div class="w-11 h-11 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center text-sm border border-slate-200 shadow-2xs">
                                {{ strtoupper(mb_substr($instructor?->name ?? 'G', 0, 1)) }}
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-sm text-slate-900 leading-tight truncate">
                                {{ $instructor?->name ?? 'Giảng viên' }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Giảng viên
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 truncate mt-0.5" title="{{ $course->title }}">
                            Khóa học: {{ $course->title }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($activeDiscussion)
                        @if($activeDiscussion->needsReply())
                            <span class="hidden sm:inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-800 border border-amber-200">
                                 Chờ phản hồi
                            </span>
                        @else
                            <span class="hidden sm:inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                                 Đã có phản hồi
                            </span>
                        @endif
                    @endif

                    <button type="button" 
                            @click="chatOpen = false"
                            class="cursor-pointer inline-flex items-center justify-center w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                            title="Đóng cửa sổ trao đổi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            @if(!$isEnrolled && !$isInstructor && !$isAdmin)
                <div class="flex-1 p-6 flex flex-col items-center justify-center text-center bg-slate-50">
                    <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-2xl mb-3">
                        🔒
                    </div>
                    <h5 class="font-bold text-base text-slate-900 mb-1">Ghi danh để trao đổi với giảng viên</h5>
                    <p class="text-xs text-slate-600 max-w-xs">Bạn cần ghi danh khóa học này để có thể gửi câu hỏi và trò chuyện trực tiếp với giảng viên.</p>
                </div>
            @else
                <!-- NỘI DUNG CHAT ROOM -->
                @if($activeDiscussion)
                    <!-- DANH SÁCH BONG BÓNG TIN NHẮN (CHAT BODY KHI ĐÃ CÓ CONVERSATION) -->
                    <div id="student-chat-body" class="flex-1 p-4 bg-[#f8fafc] overflow-y-auto space-y-4">
                        <!-- BANNER THÔNG BÁO SCOPE KHÓA HỌC -->
                        <div class="text-center my-1">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-200/80 text-[11px] font-medium text-slate-600">
                                💬 Cuộc trò chuyện của khóa học
                            </span>
                        </div>

                        <!-- TIN NHẮN GỐC (CÂU HỎI BAN ĐẦU) -->
                        @php
                            $isOriginalByMe = (int) $activeDiscussion->user_id === (int) $user->id;
                            $isOriginalInstructor = $instructor && (int) $activeDiscussion->user_id === (int) $instructor->id;
                            $originalTimeFormat = $activeDiscussion->created_at->isToday() ? $activeDiscussion->created_at->format('H:i') : $activeDiscussion->created_at->format('d/m/Y H:i');
                            $cleanFirstMsgContent = preg_replace('/\s+/', ' ', (string) $activeDiscussion->content);
                            $canRecallOriginal = ($isOriginalByMe && $activeDiscussion->created_at >= now()->subHours(24)) || ($user && $user->isAdmin());
                        @endphp

                        <div id="msg-disc-{{ $activeDiscussion->id }}" class="group flex items-end gap-2.5 {{ $isOriginalByMe ? 'justify-end' : 'justify-start' }} transition-all duration-300 rounded-2xl p-1.5 hover:bg-slate-100/60">
                            @if(! $isOriginalByMe)
                                <div class="shrink-0 mb-1">
                                    @if($activeDiscussion->user?->avatar)
                                        <img src="{{ $activeDiscussion->user->avatarUrl() }}" alt="{{ $activeDiscussion->user->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                                    @else
                                        <div class="w-8 h-8 rounded-full {{ $isOriginalInstructor ? 'bg-emerald-600' : 'bg-slate-700' }} text-white font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(mb_substr($activeDiscussion->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="max-w-[85%] sm:max-w-[80%] space-y-1 {{ $isOriginalByMe ? 'items-end text-right' : 'items-start text-left' }}">
                                <div class="flex flex-wrap items-center gap-1.5 px-1 {{ $isOriginalByMe ? 'justify-end' : 'justify-start' }}">
                                    <span class="text-[11px] font-bold text-slate-700">{{ $isOriginalByMe ? 'Bạn' : ($activeDiscussion->user?->name ?? 'Người dùng') }}</span>
                                    @if($isOriginalInstructor)
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">Giảng viên</span>
                                    @endif
                                    @if($activeDiscussion->lesson)
                                        <span class="text-[9px] font-medium px-1.5 py-0.2 rounded bg-slate-200 text-slate-700" title="{{ $activeDiscussion->lesson->title }}">
                                            Bài: {{ Str::limit($activeDiscussion->lesson->title, 20) }}
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-slate-400">{{ $originalTimeFormat }}</span>

                                    @if(! $activeDiscussion->is_recalled)
                                        <!-- NÚT TRẢ LỜI CÂU HỎI BAN ĐẦU -->
                                        <button type="button" 
                                                onclick="setReplyContext('', '{{ addslashes($activeDiscussion->user?->name ?? 'Người dùng') }}', '{{ addslashes(Str::limit($cleanFirstMsgContent, 70)) }}')"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isOriginalByMe ? 'text-[#0056D2] bg-blue-50 hover:bg-blue-100 border-blue-200' : 'text-slate-700 bg-slate-100 hover:bg-blue-50 hover:text-[#0056D2] border-slate-200' }} border transition cursor-pointer shadow-2xs"
                                                title="Trả lời tin nhắn này">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                            <span>Trả lời</span>
                                        </button>

                                        <!-- NÚT THU HỒI CHỈ HIỂN THỊ KHI <= 24 GIỜ -->
                                        @if($canRecallOriginal)
                                            <form action="{{ route('discussions.recall', $activeDiscussion) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn thu hồi tin nhắn này?')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[11px] font-semibold text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Thu hồi tin nhắn (trong vòng 24 giờ)">
                                                    <span>Thu hồi</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>

                                @if($activeDiscussion->is_recalled)
                                    <div class="rounded-2xl px-4 py-3 text-xs italic bg-slate-100 text-slate-400 border border-slate-200 rounded-bl-xs flex items-center gap-1.5 select-none">
                                        <span>Tin nhắn đã được thu hồi</span>
                                    </div>
                                @else
                                    <div class="relative rounded-2xl px-4 py-2.5 text-xs sm:text-sm leading-relaxed shadow-xs {{ $isOriginalByMe ? 'bg-[#0056D2] text-white rounded-br-xs' : 'bg-white text-slate-900 border border-slate-200 rounded-bl-xs' }}">
                                        @if($activeDiscussion->content)
                                            <p class="whitespace-pre-line text-left">{{ $activeDiscussion->content }}</p>
                                        @endif
                                        
                                        <!-- Đính kèm -->
                                        @if($activeDiscussion->attachment_path)
                                            <div class="{{ $activeDiscussion->content ? 'mt-2 pt-2 border-t ' . ($isOriginalByMe ? 'border-white/20' : 'border-slate-100') : '' }}">
                                                @if($activeDiscussion->attachment_type === 'image')
                                                    <a href="{{ $activeDiscussion->attachmentUrl() }}" target="_blank" class="block">
                                                        <img src="{{ $activeDiscussion->attachmentUrl() }}" alt="Attachment" class="rounded-xl border max-h-[180px] object-contain bg-black/10">
                                                    </a>
                                                @elseif($activeDiscussion->attachment_type === 'video')
                                                    <video controls class="rounded-xl w-full max-h-[200px] max-w-[280px] bg-black">
                                                        <source src="{{ $activeDiscussion->attachmentUrl() }}">
                                                    </video>
                                                @else
                                                    <a href="{{ $activeDiscussion->attachmentUrl() }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold {{ $isOriginalByMe ? 'text-white underline' : 'text-[#0056D2] hover:underline' }}">
                                                        <span>📎</span> Tải tệp: {{ Str::limit($activeDiscussion->attachment_name, 35) }}
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            @if($isOriginalByMe)
                                <div class="shrink-0 mb-1">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-[#0056D2]">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-[#0056D2] text-white font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- CÁC TIN NHẮN PHẢN HỒI (REPLIES) -->
                        @foreach($activeDiscussion->replies as $reply)
                            @php
                                $isMyReply = (int) $reply->user_id === (int) $user->id;
                                $isInstructorReply = $reply->is_instructor_answer || ($instructor && (int) $reply->user_id === (int) $instructor->id);
                                $replyTimeFormat = $reply->created_at->isToday() ? $reply->created_at->format('H:i') : $reply->created_at->format('d/m/Y H:i');
                                $cleanReplyContent = preg_replace('/\s+/', ' ', (string) $reply->content);
                                $canRecallReply = ($isMyReply && $reply->created_at >= now()->subHours(24)) || ($user && $user->isAdmin());
                            @endphp

                            <div id="msg-reply-{{ $reply->id }}" class="group flex items-end gap-2.5 {{ $isMyReply ? 'justify-end' : 'justify-start' }} transition-all duration-300 rounded-2xl p-1.5 hover:bg-slate-100/60">
                                @if(! $isMyReply)
                                    <div class="shrink-0 mb-1">
                                        @if($reply->user?->avatar)
                                            <img src="{{ $reply->user->avatarUrl() }}" alt="{{ $reply->user->name }}" class="w-8 h-8 rounded-full object-cover border {{ $isInstructorReply ? 'border-emerald-500' : 'border-slate-200' }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full {{ $isInstructorReply ? 'bg-emerald-600' : 'bg-slate-700' }} text-white font-bold flex items-center justify-center text-xs">
                                                {{ strtoupper(mb_substr($reply->user?->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="max-w-[85%] sm:max-w-[80%] space-y-1 {{ $isMyReply ? 'items-end text-right' : 'items-start text-left' }}">
                                    <div class="flex flex-wrap items-center gap-1.5 px-1 {{ $isMyReply ? 'justify-end' : 'justify-start' }}">
                                        <span class="text-[11px] font-bold text-slate-700">{{ $isMyReply ? 'Bạn' : ($reply->user?->name ?? 'Người dùng') }}</span>
                                        @if($isInstructorReply)
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">Giảng viên</span>
                                        @endif
                                        @if($reply->lesson)
                                            <span class="text-[9px] font-medium px-1.5 py-0.2 rounded bg-slate-200 text-slate-700" title="{{ $reply->lesson->title }}">
                                                Bài: {{ Str::limit($reply->lesson->title, 20) }}
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-slate-400">{{ $replyTimeFormat }}</span>
                                        @if($reply->is_helpful)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 rounded">✔️ Hữu ích</span>
                                        @endif
                                        
                                        @if(! $reply->is_recalled)
                                            <!-- NÚT TRẢ LỜI -->
                                            <button type="button" 
                                                    onclick="setReplyContext('{{ $reply->id }}', '{{ addslashes($reply->user?->name ?? 'Người dùng') }}', '{{ addslashes(Str::limit($cleanReplyContent, 70)) }}')"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isMyReply ? 'text-[#0056D2] bg-blue-50 hover:bg-blue-100 border-blue-200' : 'text-slate-700 bg-slate-100 hover:bg-blue-50 hover:text-[#0056D2] border-slate-200' }} border transition cursor-pointer shadow-2xs"
                                                    title="Trả lời tin nhắn này">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                                <span>Trả lời</span>
                                            </button>

                                            <!-- NÚT THU HỒI CHỈ HIỂN THỊ KHI <= 24 GIỜ -->
                                            @if($canRecallReply)
                                                <form action="{{ route('discussions.replies.recall', $reply) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn thu hồi tin nhắn này?')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[11px] font-semibold text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Thu hồi tin nhắn (trong vòng 24 giờ)">
                                                        <span>Thu hồi</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>

                                    @if($reply->is_recalled)
                                        <div class="rounded-2xl px-4 py-2.5 text-xs italic {{ $isMyReply ? 'bg-blue-50 text-blue-400 border border-blue-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }} rounded-bl-xs flex items-center gap-1.5 select-none">
                                        
                                            <span>Tin nhắn đã được thu hồi</span>
                                        </div>
                                    @else
                                        <div class="relative rounded-2xl px-4 py-2.5 text-xs sm:text-sm leading-relaxed shadow-xs {{ $isMyReply ? 'bg-[#0056D2] text-white rounded-br-xs' : ($isInstructorReply ? 'bg-[#ecfdf5] text-slate-900 border border-emerald-200 rounded-bl-xs' : 'bg-white text-slate-900 border border-slate-200 rounded-bl-xs') }}">
                                            <!-- QUOTE TRÍCH DẪN TIN NHẮN GỐC NẾU CÓ -->
                                            @if($reply->replyTo)
                                                <div onclick="scrollToMessage('msg-reply-{{ $reply->replyTo->id }}')" class="cursor-pointer mb-2 rounded-xl {{ $isMyReply ? 'bg-white/20 text-white border-l-3 border-white' : 'bg-slate-100/90 text-slate-700 border-l-3 border-[#0056D2]' }} px-3 py-1.5 text-xs transition hover:opacity-80 select-none text-left" title="Bấm để xem tin nhắn gốc">
                                                    <div class="font-bold text-[11px] flex items-center gap-1">
                                                        <span>↪</span> {{ $reply->replyTo->user?->name ?? 'Người dùng' }}
                                                        @if($reply->replyTo->is_instructor_answer)
                                                            <span class="text-[9px] font-semibold px-1 rounded {{ $isMyReply ? 'bg-white/30 text-white' : 'bg-emerald-100 text-emerald-800' }}">Giảng viên</span>
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
                                                <div class="{{ $reply->content ? 'mt-2 pt-2 border-t ' . ($isMyReply ? 'border-white/20' : 'border-slate-200/60') : '' }}">
                                                    @if($reply->attachment_type === 'image')
                                                        <a href="{{ $reply->attachmentUrl() }}" target="_blank" class="block">
                                                            <img src="{{ $reply->attachmentUrl() }}" alt="Attachment" class="rounded-xl border max-h-[160px] object-contain bg-black/10">
                                                        </a>
                                                    @elseif($reply->attachment_type === 'video')
                                                        <video controls class="rounded-xl w-full max-h-[180px] max-w-[260px] bg-black">
                                                            <source src="{{ $reply->attachmentUrl() }}">
                                                        </video>
                                                    @else
                                                        <a href="{{ $reply->attachmentUrl() }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold {{ $isMyReply ? 'text-white underline' : 'text-[#0056D2] hover:underline' }}">
                                                            <span>📎</span> Tải tệp: {{ Str::limit($reply->attachment_name, 30) }}
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif

                                            @if(auth()->check() && (int)$reply->user_id !== (int)auth()->id())
                                                @php
                                                    $isDiscussionOwner = (int) $activeDiscussion->user_id === (int) auth()->id();
                                                    $isInstructorUser = auth()->user()->role === 'admin' || (auth()->user()->role === 'instructor' && (int) $course->instructor_id === (int) auth()->id());
                                                @endphp
                                                @if($isDiscussionOwner || $isInstructorUser)
                                                    <div class="mt-2 pt-2 border-t border-slate-100 flex justify-end">
                                                        <form action="{{ route('discussions.replies.toggle-helpful', $reply) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="text-[10px] font-bold px-2 py-0.5 rounded border transition duration-200 cursor-pointer {{ $reply->is_helpful ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-300' }}">
                                                                {{ $reply->is_helpful ? 'Bỏ đánh dấu hữu ích' : '👍 Hữu ích' }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if($isMyReply)
                                    <div class="shrink-0 mb-1">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-[#0056D2]">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-[#0056D2] text-white font-bold flex items-center justify-center text-xs">
                                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- FORM GỬI TIN NHẮN PHẢN HỒI (CHAT INPUT FOOTER) -->
                    <div class="bg-white p-3.5 border-t border-slate-200 shrink-0">
                        <!-- THANH HIỂN THỊ ĐANG TRẢ LỜI TIN NHẮN NÀO -->
                        <div id="reply-context-bar" class="hidden items-center justify-between bg-blue-50 border-l-4 border-[#0056D2] px-3 py-1.5 rounded-xl mb-2 transition-all">
                            <div class="min-w-0 flex-1 pr-2">
                                <div class="text-xs font-bold text-[#0056D2] flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                    <span>Đang trả lời <span id="reply-target-sender" class="font-extrabold underline"></span></span>
                                </div>
                                <p id="reply-target-text" class="text-[11px] text-slate-600 truncate mt-0.5 italic"></p>
                            </div>
                            <button type="button" onclick="cancelReplyContext()" class="text-slate-400 hover:text-slate-700 text-lg font-bold leading-none p-1 rounded-full hover:bg-blue-100 shrink-0 cursor-pointer" title="Hủy trả lời tin nhắn này">&times;</button>
                        </div>

                        <form id="student-reply-form" action="{{ route('discussions.replies.store', $activeDiscussion) }}" method="POST" enctype="multipart/form-data" class="space-y-2.5" onsubmit="return validateStudentReplyForm()">
                            @csrf
                            <input type="hidden" name="reply_to_message_id" id="reply-to-message-id" value="">
                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                            <div class="relative">
                                <textarea id="student-reply-content" 
                                          name="content" 
                                          rows="2" 
                                          oninput="clearStudentReplyError()"
                                          class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs sm:text-sm text-[#1c1d1f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0056D2] @error('content') border-rose-500 focus:ring-rose-500 @enderror" 
                                          placeholder="Nhập câu trả lời hoặc trao đổi thêm với giảng viên...">{{ old('content') }}</textarea>
                                <p id="student-reply-validation-error" class="hidden mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Vui lòng nhập nội dung phản hồi hoặc đính kèm tệp tin.</span>
                                </p>
                                @error('content') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition" for="reply-file">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span>Đính kèm</span>
                                    </label>
                                    <input type="file" name="attachment" id="reply-file" class="hidden" onchange="handleFileSelected(this, 'reply-file-preview', 'reply-file-name', 'student')">
                                    
                                    <!-- BOX HIỂN THỊ TÊN FILE KHI ĐÃ CHỌN -->
                                    <div id="reply-file-preview" class="hidden items-center gap-1.5 bg-blue-50 border border-blue-300 text-[#0056D2] px-2.5 py-1 rounded-lg text-xs font-medium shadow-2xs">
                                        <span>📎</span>
                                        <span id="reply-file-name" class="max-w-[150px] sm:max-w-[200px] truncate font-semibold"></span>
                                        <button type="button" onclick="removeSelectedFile('reply-file', 'reply-file-preview')" class="text-rose-500 hover:text-rose-700 font-bold ml-1 cursor-pointer text-sm leading-none" title="Hủy tệp này">&times;</button>
                                    </div>

                                    <span id="reply-file-hint" class="text-[11px] text-slate-400 hidden sm:inline">Ảnh, video hoặc tài liệu (tối đa 50MB)</span>
                                </div>
                                <button type="submit" class="cursor-pointer inline-flex items-center gap-1.5 rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white hover:bg-[#0046B8] transition shadow-xs">
                                    <span>Gửi</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- MÀN HÌNH CHƯA CÓ CONVERSATION (EMPTY STATE + FORM GỬI CÂU HỎI BAN ĐẦU) -->
                    <div class="flex-1 flex flex-col justify-between bg-[#f8fafc]">
                        <!-- THÔNG ĐIỆP CHÀO MỪNG TỪ GIẢNG VIÊN -->
                        <div class="flex-1 flex flex-col items-center justify-center py-10 px-4 text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3 overflow-hidden border border-slate-200 shadow-2xs">
                                @if($instructor?->avatar)
                                    <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xl font-bold text-slate-700">{{ strtoupper(mb_substr($instructor?->name ?? 'G', 0, 1)) }}</span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">{{ $instructor?->name ?? 'Giảng viên' }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Giảng viên khóa học</p>
                            <p class="text-xs text-slate-600 mt-3 max-w-xs leading-relaxed">Bạn có câu hỏi hoặc thắc mắc trong quá trình học? Hãy gửi tin nhắn trực tiếp cho giảng viên tại đây.</p>
                        </div>

                        <!-- FORM NHẬP CÂU HỎI BAN ĐẦU -->
                        <div class="bg-white p-3.5 border-t border-slate-200 shrink-0">
                            <form id="initial-qa-form" action="{{ route('courses.lessons.discussions.store', [$course, $lesson]) }}" method="POST" enctype="multipart/form-data" class="space-y-3" onsubmit="return validateInitialQaForm()">
                                @csrf
                                <div class="relative">
                                    <textarea id="initial-qa-content" 
                                              name="content" 
                                              rows="3" 
                                              oninput="clearInitialQaError()"
                                              class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs sm:text-sm text-[#1c1d1f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0056D2] @error('content') border-rose-500 focus:ring-rose-500 @enderror" 
                                              placeholder="Nhập câu hỏi hoặc vấn đề của bạn trong khóa học...">{{ old('content') }}</textarea>
                                    <p id="initial-qa-validation-error" class="hidden mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Vui lòng nhập câu hỏi hoặc đính kèm tệp tin.</span>
                                    </p>
                                    @error('content') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition" for="initial-qa-file">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            <span>Đính kèm</span>
                                        </label>
                                        <input type="file" name="attachment" id="initial-qa-file" class="hidden" onchange="handleFileSelected(this, 'initial-file-preview', 'initial-file-name', 'initial')">
                                        
                                        <!-- BOX HIỂN THỊ TÊN FILE KHI ĐÃ CHỌN -->
                                        <div id="initial-file-preview" class="hidden items-center gap-1.5 bg-blue-50 border border-blue-300 text-[#0056D2] px-2.5 py-1 rounded-lg text-xs font-medium shadow-2xs">
                                            <span>📎</span>
                                            <span id="initial-file-name" class="max-w-[150px] sm:max-w-[200px] truncate font-semibold"></span>
                                            <button type="button" onclick="removeSelectedFile('initial-qa-file', 'initial-file-preview')" class="text-rose-500 hover:text-rose-700 font-bold ml-1 cursor-pointer text-sm leading-none" title="Hủy tệp này">&times;</button>
                                        </div>

                                        <span id="initial-file-hint" class="text-[11px] text-slate-400 hidden sm:inline">Ảnh, video hoặc tài liệu (tối đa 50MB)</span>
                                    </div>
                                    <button type="submit" class="cursor-pointer inline-flex items-center gap-1.5 rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white hover:bg-[#0046B8] transition shadow-xs">
                                        <span>Gửi</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endif
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
    function validateStudentReplyForm() {
        const textarea = document.getElementById('student-reply-content');
        const fileInput = document.getElementById('reply-file');
        const errEl = document.getElementById('student-reply-validation-error');
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

    function clearStudentReplyError() {
        const textarea = document.getElementById('student-reply-content');
        const errEl = document.getElementById('student-reply-validation-error');
        if (errEl) errEl.classList.add('hidden');
        if (textarea) textarea.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
    }

    function validateInitialQaForm() {
        const textarea = document.getElementById('initial-qa-content');
        const fileInput = document.getElementById('initial-qa-file');
        const errEl = document.getElementById('initial-qa-validation-error');
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

    function clearInitialQaError() {
        const textarea = document.getElementById('initial-qa-content');
        const errEl = document.getElementById('initial-qa-validation-error');
        if (errEl) errEl.classList.add('hidden');
        if (textarea) textarea.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
    }

    function handleFileSelected(input, previewId, nameId, formType) {
        if (formType === 'student') clearStudentReplyError();
        if (formType === 'initial') clearInitialQaError();
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

    function setReplyContext(replyId, senderName, snippet) {
        const replyBar = document.getElementById('reply-context-bar');
        const replyInput = document.getElementById('reply-to-message-id');
        const replySender = document.getElementById('reply-target-sender');
        const replyText = document.getElementById('reply-target-text');
        const contentInput = document.getElementById('student-reply-content');

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

    function cancelReplyContext() {
        const replyBar = document.getElementById('reply-context-bar');
        const replyInput = document.getElementById('reply-to-message-id');

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

    function scrollStudentChatToBottom() {
        const studentChatBody = document.getElementById('student-chat-body');
        if (studentChatBody) {
            studentChatBody.scrollTop = studentChatBody.scrollHeight;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(scrollStudentChatToBottom, 150);
    });

    window.addEventListener('load', () => {
        setTimeout(scrollStudentChatToBottom, 150);
    });
</script>
