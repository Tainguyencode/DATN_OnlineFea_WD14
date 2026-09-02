<?php

namespace App\Http\Controllers\Web;

use App\Events\CourseDiscussionConversationUpdated;
use App\Events\CourseDiscussionMessageBroadcasted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\StoreDiscussionReplyRequest;
use App\Http\Requests\Learning\StoreDiscussionRequest;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\Lesson;
use App\Models\UserPoint;
use App\Services\DiscussionChatService;
use App\Services\LessonNoteAccessService;
use App\Services\NotificationService;
use App\Services\PointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DiscussionController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected LessonNoteAccessService $lessonAccess,
        protected DiscussionChatService $chat,
    ) {}

    public function messages(Discussion $discussion): JsonResponse
    {
        Gate::authorize('view', $discussion);
        $result = $this->chat->messages($discussion, request()->user(), request()->query('after'));

        return response()->json(['success' => true, ...$result]);
    }

    public function message(Discussion $discussion, string $messageKey): JsonResponse
    {
        Gate::authorize('view', $discussion);
        $message = $this->chat->message($discussion, $messageKey, request()->user());
        abort_unless($message, 404);

        return response()->json(['success' => true, 'data' => $message]);
    }

    public function markRead(Discussion $discussion): JsonResponse
    {
        Gate::authorize('view', $discussion);
        $this->chat->markRead($discussion, request()->user());
        $this->broadcastConversation($discussion, 'read');

        return response()->json(['success' => true, 'conversation_id' => $discussion->id]);
    }

    public function attachment(string $kind, int $message): StreamedResponse
    {
        abort_unless(in_array($kind, ['discussion', 'reply'], true), 404);
        $model = $kind === 'discussion' ? Discussion::findOrFail($message) : DiscussionReply::findOrFail($message);
        $discussion = $model instanceof Discussion ? $model : $model->discussion;
        Gate::authorize('view', $discussion);
        abort_unless($model->attachment_path, 404);
        $disk = Storage::disk('local')->exists($model->attachment_path) ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($model->attachment_path), 404);
        $downloadName = preg_replace('/[\x00-\x1F\x7F"\\\\\/]+/u', '-', basename((string) $model->attachment_name)) ?: 'attachment';

        return Storage::disk($disk)->response(
            $model->attachment_path,
            $downloadName,
            ['Content-Disposition' => 'inline; filename="'.$downloadName.'"']
        );
    }

    /**
     * Store a new student question (discussion) or append as reply to existing course conversation.
     */
    public function store(StoreDiscussionRequest $request, Course $course, Lesson $lesson): RedirectResponse|JsonResponse
    {
        Gate::authorize('create', [Discussion::class, $course]);
        abort_unless($this->lessonAccess->lessonBelongsToCourse($course, $lesson), 404);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('discussions/attachments', 'local');
            $attachmentName = $file->getClientOriginalName();
            $mime = $file->getMimeType();

            if (str_starts_with($mime, 'image/')) {
                $attachmentType = 'image';
            } elseif (str_starts_with($mime, 'video/')) {
                $attachmentType = 'video';
            } else {
                $attachmentType = 'file';
            }
        }

        $content = $request->validated('content');
        $discussionTitle = $request->validated('title')
            ?: ($content ? Str::limit(strip_tags((string) $content), 80) : ($attachmentName ? "Đính kèm: {$attachmentName}" : 'Tệp đính kèm'));

        // Unique(course_id, user_id) + firstOrCreate keeps concurrent first messages in one conversation.
        $discussion = Discussion::firstOrCreate([
            'course_id' => $course->id,
            'user_id' => auth()->id(),
        ], [
            'lesson_id' => $lesson->id,
            'title' => $discussionTitle,
            'content' => $content,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'is_resolved' => false,
            'last_message_at' => now(),
            'last_message_user_id' => auth()->id(),
        ]);

        if (! $discussion->wasRecentlyCreated) {
            // Đã có conversation của Course -> Gửi dưới dạng DiscussionReply kèm context lesson_id
            $reply = DiscussionReply::create([
                'discussion_id' => $discussion->id,
                'reply_to_message_id' => null,
                'reply_to_discussion_id' => null,
                'lesson_id' => $lesson->id,
                'user_id' => auth()->id(),
                'content' => $content,
                'is_instructor_answer' => false,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_type' => $attachmentType,
            ]);
        } else {
            // Cộng +2 XP cho học viên tạo thảo luận (tối đa 10 XP/ngày)
            app(PointService::class)->awardDiscussionPoints(
                auth()->id(),
                $course->id,
                $discussion->id
            );
        }

        // Gửi thông báo cho Giảng viên sở hữu khóa học
        $instructor = $course->instructor;
        if ($instructor && (int) $instructor->id !== (int) auth()->id()) {
            $title = 'Học viên gửi câu hỏi mới';
            $previewText = $content ? Str::limit($content, 60) : ($attachmentName ? "[Đính kèm: {$attachmentName}]" : '[Tệp đính kèm]');
            $message = auth()->user()->name.' vừa hỏi trong khóa học "'.$course->title.'" (Bài: '.$lesson->title.'): "'.$previewText.'"';
            $url = route('instructor.discussions.show', $discussion);

            $this->notificationService->send(
                $instructor,
                $title,
                $message,
                'student_question',
                $url
            );
        }

        $message = isset($reply) ? $reply->load(['user', 'lesson', 'replyTo.user', 'replyToDiscussion.user']) : $discussion->load(['user', 'lesson']);
        $this->chat->recordMessage($discussion, $request->user(), $message);
        $canonical = $this->chat->presentMessage($discussion, $message, $request->user());
        $this->broadcastChat($discussion, 'created', $canonical['key']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'kind' => isset($reply) ? 'reply' : 'discussion',
                'discussion_id' => $discussion->id,
                'messages_url' => route('discussions.messages', $discussion),
                'data' => $canonical,
                'reply_url' => route('discussions.replies.store', $discussion),
                'read_url' => route('discussions.read', $discussion),
                'message_url_template' => route('discussions.message', [$discussion, '__MESSAGE_KEY__']),
            ], 201);
        }

        return redirect()->route('courses.lessons.show', [
            'course' => $course,
            'lesson' => $lesson,
            'open_chat' => 1,
        ])->with('success', 'Đã gửi câu hỏi trao đổi thành công.');
    }

    /**
     * Store a response to a discussion.
     */
    public function storeReply(StoreDiscussionReplyRequest $request, Discussion $discussion): RedirectResponse|JsonResponse
    {
        Gate::authorize('reply', $discussion);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('discussions/attachments', 'local');
            $attachmentName = $file->getClientOriginalName();
            $mime = $file->getMimeType();

            if (str_starts_with($mime, 'image/')) {
                $attachmentType = 'image';
            } elseif (str_starts_with($mime, 'video/')) {
                $attachmentType = 'video';
            } else {
                $attachmentType = 'file';
            }
        }

        $replyToMessageId = $request->validated('reply_to_message_id');
        $replyToDiscussionId = null;
        $replyToKey = $request->validated('reply_to_key');
        if ($replyToKey && str_starts_with($replyToKey, 'discussion:')) {
            $candidateId = (int) Str::after($replyToKey, 'discussion:');
            if ($candidateId !== (int) $discussion->id) {
                return $this->invalidReplyTarget();
            }
            $replyToDiscussionId = $discussion->id;
            $replyToMessageId = null;
        } elseif ($replyToKey && str_starts_with($replyToKey, 'reply:')) {
            $replyToMessageId = (int) Str::after($replyToKey, 'reply:');
        }
        $replyToReply = null;
        if ($replyToMessageId) {
            $replyToReply = DiscussionReply::where('id', $replyToMessageId)
                ->where('discussion_id', $discussion->id)
                ->first();

            if (! $replyToReply) {
                return $this->invalidReplyTarget();
            }
        }

        $course = $discussion->course ?: $discussion->lesson?->course;
        $isInstructor = $course && (int) $course->instructor_id === (int) auth()->id();
        $lessonId = $request->validated('lesson_id') ?: $discussion->lesson_id;
        if ($lessonId) {
            $replyLesson = Lesson::findOrFail($lessonId);
            abort_unless($course && $this->lessonAccess->lessonBelongsToCourse($course, $replyLesson), 404);
        }

        $reply = DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'reply_to_message_id' => $replyToReply?->id,
            'reply_to_discussion_id' => $replyToDiscussionId,
            'lesson_id' => $lessonId,
            'user_id' => auth()->id(),
            'content' => $request->validated('content'),
            'is_instructor_answer' => $isInstructor,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);

        $instructor = $course?->instructor;

        // If lecturer replies, send push notification to student
        if ($isInstructor && $discussion->user) {
            $title = 'Giảng viên đã trả lời câu hỏi của bạn';
            if ($replyToReply && (int) $replyToReply->user_id === (int) $discussion->user_id) {
                $quotedText = $replyToReply->content ? Str::limit($replyToReply->content, 40) : '[Tệp đính kèm]';
                $message = auth()->user()->name.' đã trả lời tin nhắn của bạn: "'.$quotedText.'"';
            } else {
                $message = auth()->user()->name.' đã trả lời câu hỏi trong khóa học: "'.Str::limit($course?->title ?? $discussion->title, 40).'"';
            }

            $targetLesson = $lessonId ? Lesson::find($lessonId) : ($discussion->lesson ?: $course?->lessons()->first());
            $url = $targetLesson
                ? route('courses.lessons.show', ['course' => $course, 'lesson' => $targetLesson, 'open_chat' => 1])
                : route('courses.show', $course);

            $this->notificationService->send(
                $discussion->user,
                $title,
                $message,
                'discussion_reply',
                $url
            );
        } elseif (! $isInstructor && $instructor && (int) $instructor->id !== (int) auth()->id()) {
            // Học viên gửi phản hồi -> thông báo cho Giảng viên
            $title = 'Học viên gửi phản hồi mới';
            if ($replyToReply && (int) $replyToReply->user_id === (int) $instructor->id) {
                $quotedText = $replyToReply->content ? Str::limit($replyToReply->content, 40) : '[Tệp đính kèm]';
                $message = auth()->user()->name.' đã trả lời tin nhắn của bạn: "'.$quotedText.'"';
            } else {
                $message = auth()->user()->name.' vừa phản hồi trong khóa học "'.($course?->title ?? '').'": "'.Str::limit($discussion->title, 40).'"';
            }
            $url = route('instructor.discussions.show', $discussion);

            $this->notificationService->send(
                $instructor,
                $title,
                $message,
                'student_question',
                $url
            );
        }

        $reply->load(['user', 'lesson', 'replyTo.user', 'replyToDiscussion.user']);
        $this->chat->recordMessage($discussion, $request->user(), $reply);
        $canonical = $this->chat->presentMessage($discussion, $reply, $request->user());
        $this->broadcastChat($discussion, 'created', $canonical['key']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'kind' => 'reply',
                'discussion_id' => $discussion->id,
                'data' => $canonical,
            ], 201);
        }

        return back()->with('success', 'Đã gửi phản hồi.');
    }

    /**
     * Đánh dấu hoặc hủy đánh dấu câu trả lời hữu ích.
     */
    public function toggleHelpful(DiscussionReply $reply): RedirectResponse|JsonResponse
    {
        $discussion = $reply->discussion;
        $user = auth()->user();

        $course = $discussion->course ?: $discussion->lesson?->course;
        $canMarkHelpful = $user->role === 'admin' || (
            $user->isStudent()
            && (int) $discussion->user_id === (int) $user->id
            && (int) $reply->user_id !== (int) $user->id
            && $reply->is_instructor_answer
            && ! $reply->is_recalled
        );

        abort_unless($canMarkHelpful, 403);

        $reply->is_helpful = ! $reply->is_helpful;
        $reply->save();

        $pointService = app(PointService::class);
        $replyTag = "reply_id:{$reply->id}";

        if ($reply->is_helpful) {
            // Cộng +20 điểm cho người viết câu trả lời hữu ích
            $pointService->awardPoints(
                $reply->user_id,
                20,
                'reply_marked_helpful',
                "Câu trả lời được đánh dấu hữu ích trong thảo luận: {$discussion->title} ({$replyTag})",
                $course?->id
            );
        } else {
            // Thu hồi điểm khi hủy đánh dấu
            UserPoint::where('user_id', $reply->user_id)
                ->where('source', 'reply_marked_helpful')
                ->where('description', 'like', "%{$replyTag}%")
                ->delete();
        }

        $this->broadcastChat($discussion, 'updated', 'reply:'.$reply->id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $this->chat->presentMessage($discussion, $reply->load(['user', 'lesson', 'replyTo.user', 'replyToDiscussion.user']), $user),
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái câu trả lời thành công.');
    }

    /**
     * Thu hồi tin nhắn phản hồi (Chỉ trong vòng 24 giờ).
     */
    public function recallReply(DiscussionReply $reply): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $isOwner = (int) $reply->user_id === (int) $user->id;
        $isAdmin = $user->role === 'admin';
        abort_unless($isOwner || $isAdmin, 403, 'Bạn không có quyền thu hồi tin nhắn này.');

        if ($reply->is_recalled) {
            return back()->with('error', 'Tin nhắn này đã được thu hồi trước đó.');
        }

        // Backend validation: Chỉ được thu hồi trong vòng 24 giờ
        if (! $isAdmin && $reply->created_at < now()->subHours(24)) {
            return back()->withErrors(['recall' => 'Tin nhắn đã quá 24 giờ và không thể thu hồi.']);
        }

        $this->deleteAttachment($reply->attachment_path);

        $reply->update([
            'is_recalled' => true,
            'content' => 'Tin nhắn đã được thu hồi',
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_type' => null,
        ]);
        $this->broadcastChat($reply->discussion, 'recalled', 'reply:'.$reply->id);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'kind' => 'reply', 'id' => $reply->id]);
        }

        return back()->with('success', 'Đã thu hồi tin nhắn thành công.');
    }

    /**
     * Xóa tin nhắn phản hồi.
     */
    public function destroyReply(DiscussionReply $reply): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $course = $reply->discussion->course ?: $reply->discussion->lesson?->course;
        $isOwner = (int) $reply->user_id === (int) $user->id;
        $isInstructor = $user->role === 'admin' || ($user->role === 'instructor' && $course && (int) $course->instructor_id === (int) $user->id);
        abort_unless($isOwner || $isInstructor, 403, 'Bạn không có quyền xóa tin nhắn này.');

        $this->deleteAttachment($reply->attachment_path);

        $discussion = $reply->discussion;
        $messageKey = 'reply:'.$reply->id;
        $reply->delete();
        $this->chat->refreshLastMessage($discussion);
        $this->broadcastChat($discussion, 'deleted', $messageKey);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'key' => $messageKey]);
        }

        return back()->with('success', 'Đã xóa tin nhắn thành công.');
    }

    /**
     * Thu hồi câu hỏi gốc (Chỉ trong vòng 24 giờ).
     */
    public function recallDiscussion(Discussion $discussion): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $isOwner = (int) $discussion->user_id === (int) $user->id;
        $isAdmin = $user->role === 'admin';
        abort_unless($isOwner || $isAdmin, 403, 'Bạn không có quyền thu hồi tin nhắn này.');

        if ($discussion->is_recalled) {
            return back()->with('error', 'Tin nhắn này đã được thu hồi trước đó.');
        }

        // Backend validation: Chỉ được thu hồi trong vòng 24 giờ
        if (! $isAdmin && $discussion->created_at < now()->subHours(24)) {
            return back()->withErrors(['recall' => 'Tin nhắn đã quá 24 giờ và không thể thu hồi.']);
        }

        $this->deleteAttachment($discussion->attachment_path);

        $discussion->update([
            'is_recalled' => true,
            'content' => 'Tin nhắn đã được thu hồi',
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_type' => null,
        ]);
        $this->broadcastChat($discussion, 'recalled', 'discussion:'.$discussion->id);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'kind' => 'discussion', 'id' => $discussion->id]);
        }

        return back()->with('success', 'Đã thu hồi tin nhắn thành công.');
    }

    private function broadcastChat(Discussion $discussion, string $action, string $messageKey): void
    {
        try {
            broadcast(new CourseDiscussionMessageBroadcasted($discussion->id, $action, $messageKey))->toOthers();
            $this->broadcastConversation($discussion, $action, $messageKey);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function broadcastConversation(Discussion $discussion, string $action, ?string $messageKey = null): void
    {
        try {
            broadcast(new CourseDiscussionConversationUpdated(
                $this->chat->participantIds($discussion),
                $discussion->id,
                $action,
                $messageKey,
            ))->toOthers();
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function invalidReplyTarget(): RedirectResponse|JsonResponse
    {
        $message = 'Tin nhắn được chọn không thuộc cuộc trao đổi này.';

        return request()->expectsJson()
            ? response()->json(['message' => $message, 'errors' => ['reply_to_key' => [$message]]], 422)
            : back()->withErrors(['reply_to_key' => $message]);
    }

    private function deleteAttachment(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }

    /**
     * Xóa toàn bộ cuộc trao đổi.
     */
    public function destroyDiscussion(Discussion $discussion): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $course = $discussion->course ?: $discussion->lesson?->course;
        $isOwner = (int) $discussion->user_id === (int) $user->id;
        $isInstructor = $user->role === 'admin' || ($user->role === 'instructor' && $course && (int) $course->instructor_id === (int) $user->id);
        abort_unless($isOwner || $isInstructor, 403, 'Bạn không có quyền xóa cuộc trao đổi này.');

        $this->deleteAttachment($discussion->attachment_path);

        foreach ($discussion->replies as $r) {
            $this->deleteAttachment($r->attachment_path);
        }

        $lesson = $discussion->lesson;
        $messageKey = 'discussion:'.$discussion->id;
        $this->broadcastChat($discussion, 'deleted', $messageKey);
        $discussion->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'key' => $messageKey, 'conversation_deleted' => true]);
        }

        if ($user->role === 'instructor' || $user->role === 'admin') {
            return redirect()->route('instructor.discussions.index')->with('success', 'Đã xóa cuộc trao đổi thành công.');
        }

        return redirect()->route('courses.lessons.show', ['course' => $course, 'lesson' => $lesson])->with('success', 'Đã xóa cuộc trao đổi thành công.');
    }
}
