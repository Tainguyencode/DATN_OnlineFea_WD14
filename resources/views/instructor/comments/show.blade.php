<x-instructor-layout title="Chi tiết Bình luận" page-title="Chi tiết bình luận" :back-url="route('instructor.comments.index')">
    <x-slot:title>Chi tiết Bình luận Bài học</x-slot:title>

    <div class="max-w-4xl space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('instructor.comments.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors">
                <span>&larr;</span> Quay lại danh sách bình luận
            </a>
        </div>

        <!-- Header Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs space-y-4">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4 space-y-1">
                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                    Khóa học: {{ $comment->lesson->course->title }}
                </div>
                @php
                    $chapterTitle = $comment->lesson->chapter ? $comment->lesson->chapter->title : ($comment->lesson->section ? $comment->lesson->section->title : null);
                @endphp
                @if($chapterTitle)
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                        Chương: {{ $chapterTitle }}
                    </div>
                @endif
                <h2 class="text-lg font-bold text-slate-900 dark:text-white pt-1">
                    Video bài học: {{ $comment->lesson->title }}
                </h2>
            </div>

            <!-- Main Comment -->
            <div class="flex items-start gap-4">
                <img src="{{ $comment->user ? $comment->user->avatarUrl() : 'https://ui-avatars.com/api/?name=User' }}" alt="Avatar" class="rounded-full border border-slate-200 shrink-0" style="width: 44px; height: 44px; object-fit: cover;">
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $comment->user ? $comment->user->name : 'NĐT' }}</span>
                            <span class="text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                            @if($comment->is_hidden)
                                <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[11px] font-bold text-rose-700 border border-rose-200">🔒 Đã ẩn</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <form action="{{ route('comments.toggle-hide', $comment) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-lg border px-2.5 py-1 text-xs font-bold transition cursor-pointer {{ $comment->is_hidden ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                    {{ $comment->is_hidden ? 'Bỏ ẩn' : 'Ẩn bình luận' }}
                                </button>
                            </form>

                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center rounded-lg border border-rose-300 bg-white px-2.5 py-1 text-xs font-bold text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line leading-relaxed">{{ $comment->content }}</p>
                </div>
            </div>
        </div>

        <!-- Replies Section -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                Các phản hồi trong cuộc trò chuyện ({{ $comment->replies->count() }})
            </h3>

            <div class="space-y-4 pl-4 border-l-2 border-slate-200 dark:border-slate-800">
                @forelse($comment->replies as $reply)
                    @php
                        $replyUserIsInstructor = $reply->user && $reply->user->isInstructor() && (int) $comment->lesson->course->instructor_id === (int) $reply->user->id;
                    @endphp
                    <div class="flex items-start gap-3 p-3 rounded-xl border {{ $replyUserIsInstructor ? 'bg-amber-50/60 border-amber-200 dark:bg-amber-950/20 dark:border-amber-900/40' : 'bg-slate-50 border-slate-100 dark:bg-slate-800/50 dark:border-slate-800' }} {{ $reply->is_hidden ? 'opacity-60 border-dashed border-rose-300' : '' }}">
                        <img src="{{ $reply->user ? $reply->user->avatarUrl() : 'https://ui-avatars.com/api/?name=User' }}" alt="Avatar" class="rounded-full border border-slate-200 shrink-0" style="width: 34px; height: 34px; object-fit: cover;">
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $reply->user ? $reply->user->name : 'NĐT' }}</span>
                                    @if($replyUserIsInstructor)
                                        <span class="inline-flex items-center rounded bg-amber-100 px-1.5 py-0.2 text-[10px] font-bold text-amber-800">Giảng viên</span>
                                    @elseif($reply->user && $reply->user->isAdmin())
                                        <span class="inline-flex items-center rounded bg-purple-100 px-1.5 py-0.2 text-[10px] font-bold text-purple-800">Admin</span>
                                    @endif

                                    @if($reply->is_hidden)
                                        <span class="inline-flex items-center rounded bg-rose-100 px-1.5 py-0.2 text-[10px] font-bold text-rose-800">🔒 Đã ẩn</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="text-xs text-slate-800 dark:text-slate-200 whitespace-pre-line leading-relaxed mb-2">{{ $reply->content }}</p>

                            <div class="flex items-center justify-end gap-3 text-[11px]">
                                <form action="{{ route('comments.toggle-hide', $reply) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="font-semibold {{ $reply->is_hidden ? 'text-emerald-600 hover:text-emerald-800' : 'text-slate-600 hover:text-slate-800' }} hover:underline cursor-pointer">
                                        {{ $reply->is_hidden ? 'Bỏ ẩn' : 'Ẩn phản hồi' }}
                                    </button>
                                </form>

                                <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn phản hồi này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-rose-600 hover:text-rose-800 hover:underline cursor-pointer">
                                        Xóa phản hồi
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">Chưa có phản hồi nào cho bình luận này.</p>
                @endforelse
            </div>

            <!-- Instructor Reply Form -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                <h4 class="text-xs font-bold text-slate-900 dark:text-white mb-2">Gửi phản hồi từ Giảng viên</h4>
                <form action="{{ route('lessons.comments.store', $comment->lesson) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="content" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Viết phản hồi cho học viên..." required></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition cursor-pointer">
                            Gửi phản hồi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-instructor-layout>
