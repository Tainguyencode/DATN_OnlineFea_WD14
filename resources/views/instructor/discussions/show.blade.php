<x-instructor-layout title="Chi tiết trao đổi" pageTitle="Chi tiết trao đổi" breadcrumb="Giảng viên / Trao đổi / Chi tiết">
    <div class="space-y-6 max-w-4xl">
        <!-- NÚT QUAY LẠI -->
        <div>
            <a href="{{ route('instructor.discussions.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors">
                &larr; Quay lại danh sách câu hỏi
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        <script>
            (function() {
                const scrollBottom = (isSmooth = false) => {
                    const replyForm = document.getElementById('reply-form');
                    if (replyForm) {
                        replyForm.scrollIntoView({ behavior: isSmooth ? 'smooth' : 'auto', block: 'end' });
                    }
                };
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => scrollBottom(false));
                } else {
                    scrollBottom(false);
                }
                setTimeout(() => scrollBottom(false), 100);
                setTimeout(() => scrollBottom(false), 300);
            })();
        </script>

        <!-- THÔNG TIN CÂU HỎI GỐC -->
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-300">
                        {{ $discussion->lesson->course->title }}
                    </span>
                    <span class="text-slate-400 mx-1">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">
                        Bài học: {{ $discussion->lesson->title }}
                    </span>
                    <h3 class="mt-2 font-extrabold text-lg text-slate-950 dark:text-white">
                        {{ $discussion->title }}
                    </h3>
                </div>
                <div>
                    @if($discussion->is_resolved)
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                            Đã giải quyết
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                            Đang chờ phản hồi
                        </span>
                    @endif
                </div>
            </div>

            <!-- NỘI DUNG CHAT THREAD -->
            <div class="mt-6 space-y-6">
                <!-- Tin nhắn đầu tiên (Học viên hỏi) -->
                <div class="flex items-start gap-4">
                    <img src="{{ $discussion->user->avatarUrl() }}" alt="Avatar" class="rounded-full border border-slate-200 shrink-0" style="width: 44px; height: 44px; object-fit: cover;">
                    <div class="flex-1 space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $discussion->user->name }}</span>
                            <span class="inline-flex items-center rounded bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 border border-blue-100">Học viên</span>
                            <span class="text-xs text-slate-400">{{ $discussion->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl text-sm leading-6 text-slate-700 dark:text-slate-300">
                            <p class="whitespace-pre-line">{{ $discussion->content }}</p>

                            <!-- Đính kèm tin gốc -->
                            @if($discussion->attachment_path)
                                @php $discussionAttachUrl = $discussion->attachmentUrl(); @endphp
                                @if($discussionAttachUrl)
                                    <div class="mt-3 p-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 inline-block max-w-full">
                                        @if($discussion->attachment_type === 'image')
                                            <a href="{{ $discussionAttachUrl }}" target="_blank">
                                                <img src="{{ $discussionAttachUrl }}" alt="Attachment" class="rounded max-h-[220px] object-contain border border-slate-100" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Ảnh+Không+Tồn+Tại&background=fee2e2&color=dc2626'">
                                            </a>
                                        @elseif($discussion->attachment_type === 'video')
                                            <video controls class="rounded w-full max-h-[220px] max-w-[360px]">
                                                <source src="{{ $discussionAttachUrl }}">
                                                Trình duyệt không hỗ trợ xem video.
                                            </video>
                                        @else
                                            <a href="{{ $discussionAttachUrl }}" target="_blank" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                                <span>📎</span> Tải xuống: {{ $discussion->attachment_name }}
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-3 px-3 py-2 rounded bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 inline-flex items-center gap-1.5">
                                        <span>⚠️</span> File đính kèm không tồn tại hoặc đã bị xóa
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Lịch sử các phản hồi -->
                @foreach($discussion->replies as $reply)
                    <div class="flex items-start gap-4 {{ (int) $reply->user_id === (int) auth()->id() ? 'flex-row-reverse' : '' }}">
                        <img src="{{ $reply->user->avatarUrl() }}" alt="Avatar" class="rounded-full border border-slate-200 shrink-0" style="width: 44px; height: 44px; object-fit: cover;">
                        <div class="flex-1 space-y-1 {{ (int) $reply->user_id === (int) auth()->id() ? 'text-right' : '' }}">
                            <div class="flex items-center gap-2 {{ (int) $reply->user_id === (int) auth()->id() ? 'flex-row-reverse' : '' }}">
                                <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $reply->user->name }}</span>
                                @if($reply->is_instructor_answer)
                                    <span class="inline-flex items-center rounded bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-800 border border-amber-100">Giảng viên</span>
                                @else
                                    <span class="inline-flex items-center rounded bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 border border-blue-100">Học viên</span>
                                @endif
                                <span class="text-xs text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="inline-block text-left p-4 rounded-2xl text-sm leading-6 {{ $reply->is_instructor_answer ? 'bg-amber-50/50 dark:bg-amber-950/20 text-slate-800 dark:text-slate-200 border border-amber-100/50 dark:border-amber-900/30' : 'bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-300' }}">
                                <p class="whitespace-pre-line">{{ $reply->content }}</p>

                                <!-- Đính kèm ở reply -->
                                @if($reply->attachment_path)
                                    @php $replyAttachUrl = $reply->attachmentUrl(); @endphp
                                    @if($replyAttachUrl)
                                        <div class="mt-3 p-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 inline-block max-w-full">
                                            @if($reply->attachment_type === 'image')
                                                <a href="{{ $replyAttachUrl }}" target="_blank">
                                                    <img src="{{ $replyAttachUrl }}" alt="Attachment" class="rounded max-h-[180px] object-contain border border-slate-100" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Ảnh+Không+Tồn+Tại&background=fee2e2&color=dc2626'">
                                                </a>
                                            @elseif($reply->attachment_type === 'video')
                                                <video controls class="rounded w-full max-h-[180px] max-w-[320px]">
                                                    <source src="{{ $replyAttachUrl }}">
                                                    Trình duyệt không hỗ trợ xem video.
                                                </video>
                                            @else
                                                <a href="{{ $replyAttachUrl }}" target="_blank" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                                    <span>📎</span> Tải xuống: {{ $reply->attachment_name }}
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <div class="mt-3 px-3 py-2 rounded bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 inline-flex items-center gap-1.5">
                                            <span>⚠️</span> File đính kèm không tồn tại hoặc đã bị xóa
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Ô NHẬP PHẢN HỒI GỬI ĐI -->
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                <form id="reply-form" action="{{ route('discussions.replies.store', $discussion) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="reply-comment" class="block text-sm font-bold text-slate-800 dark:text-slate-100 mb-2">
                            Phản hồi của giảng viên
                        </label>
                        <textarea id="reply-comment" name="content" rows="4" minlength="2" required class="w-full rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Viết câu trả lời hoặc trao đổi thêm với học viên tại đây..."></textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-500 uppercase cursor-pointer" for="file-attach">
                                📎 Đính kèm tệp:
                            </label>
                            <input type="file" name="attachment" id="file-attach" class="text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        </div>
                        
                        <button type="submit" class="cursor-pointer rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white transition-colors hover:bg-emerald-700">
                            Gửi phản hồi
                        </button>
                    </div>
                    <div class="text-[11px] text-slate-400">Hỗ trợ Ảnh chụp màn hình, Video quay lỗi, Tài liệu nén dạng Zip/PDF (tối đa 50MB).</div>
                </form>
            </div>
        </article>
    </div>
</x-instructor-layout>
