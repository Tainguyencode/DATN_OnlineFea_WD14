<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\StoreDiscussionReplyRequest;
use App\Http\Requests\Learning\StoreDiscussionRequest;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\Lesson;
use App\Models\UserPoint;
use App\Services\NotificationService;
use App\Services\PointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiscussionController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Store a new student question (discussion) or append as reply to existing course conversation.
     */
    public function store(StoreDiscussionRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('create', [Discussion::class, $course]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('discussions/attachments', 'public');
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

        // Tìm conversation Course-level đã có giữa Học viên và Giảng viên
        $discussion = Discussion::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($discussion) {
            // Đã có conversation của Course -> Gửi dưới dạng DiscussionReply kèm context lesson_id
            $reply = DiscussionReply::create([
                'discussion_id' => $discussion->id,
                'reply_to_message_id' => null,
                'lesson_id' => $lesson->id,
                'user_id' => auth()->id(),
                'content' => $content,
                'is_instructor_answer' => false,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_type' => $attachmentType,
            ]);
        } else {
            // Chưa có conversation -> Tạo Discussion mới với scope course_id
            $discussion = Discussion::create([
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
                'user_id' => auth()->id(),
                'title' => $discussionTitle,
                'content' => $content,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_type' => $attachmentType,
                'is_resolved' => false,
            ]);

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

        return redirect()->route('courses.lessons.show', [
            'course' => $course,
            'lesson' => $lesson,
            'open_chat' => 1,
        ])->with('success', 'Đã gửi câu hỏi trao đổi thành công.');
    }

    /**
     * Store a response to a discussion.
     */
    public function storeReply(StoreDiscussionReplyRequest $request, Discussion $discussion): RedirectResponse
    {
        Gate::authorize('reply', $discussion);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('discussions/attachments', 'public');
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
        $replyToReply = null;
        if ($replyToMessageId) {
            $replyToReply = DiscussionReply::where('id', $replyToMessageId)
                ->where('discussion_id', $discussion->id)
                ->first();

            if (! $replyToReply) {
                return back()->withErrors(['reply_to_message_id' => 'Tin nhắn được chọn không thuộc cuộc trao đổi này.']);
            }
        }

        $course = $discussion->course ?: $discussion->lesson?->course;
        $isInstructor = $course && (int) $course->instructor_id === (int) auth()->id();
        $lessonId = $request->input('lesson_id') ?: $discussion->lesson_id;

        $reply = DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'reply_to_message_id' => $replyToReply?->id,
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

        return back()->with('success', 'Đã gửi phản hồi.');
    }

    /**
     * Đánh dấu hoặc hủy đánh dấu câu trả lời hữu ích.
     */
    public function toggleHelpful(DiscussionReply $reply): RedirectResponse
    {
        $discussion = $reply->discussion;
        $user = auth()->user();

        $course = $discussion->course ?: $discussion->lesson?->course;
        $isOwner = (int) $discussion->user_id === (int) $user->id;
        $isInstructor = $user->role === 'admin' || ($user->role === 'instructor' && $course && (int) $course->instructor_id === (int) $user->id);

        abort_unless($isOwner || $isInstructor, 403);

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

        return redirect()->back()->with('success', 'Cập nhật trạng thái câu trả lời thành công.');
    }

    /**
     * Thu hồi tin nhắn phản hồi (Chỉ trong vòng 24 giờ).
     */
    public function recallReply(DiscussionReply $reply): RedirectResponse
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

        if ($reply->attachment_path && Storage::disk('public')->exists($reply->attachment_path)) {
            Storage::disk('public')->delete($reply->attachment_path);
        }

        $reply->update([
            'is_recalled' => true,
            'content' => 'Tin nhắn đã được thu hồi',
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_type' => null,
        ]);

        return back()->with('success', 'Đã thu hồi tin nhắn thành công.');
    }

    /**
     * Xóa tin nhắn phản hồi.
     */
    public function destroyReply(DiscussionReply $reply): RedirectResponse
    {
        $user = auth()->user();
        $course = $reply->discussion->course ?: $reply->discussion->lesson?->course;
        $isOwner = (int) $reply->user_id === (int) $user->id;
        $isInstructor = $user->role === 'admin' || ($user->role === 'instructor' && $course && (int) $course->instructor_id === (int) $user->id);
        abort_unless($isOwner || $isInstructor, 403, 'Bạn không có quyền xóa tin nhắn này.');

        if ($reply->attachment_path && Storage::disk('public')->exists($reply->attachment_path)) {
            Storage::disk('public')->delete($reply->attachment_path);
        }

        $reply->delete();

        return back()->with('success', 'Đã xóa tin nhắn thành công.');
    }

    /**
     * Thu hồi câu hỏi gốc (Chỉ trong vòng 24 giờ).
     */
    public function recallDiscussion(Discussion $discussion): RedirectResponse
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

        if ($discussion->attachment_path && Storage::disk('public')->exists($discussion->attachment_path)) {
            Storage::disk('public')->delete($discussion->attachment_path);
        }

        $discussion->update([
            'is_recalled' => true,
            'content' => 'Tin nhắn đã được thu hồi',
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_type' => null,
        ]);

        return back()->with('success', 'Đã thu hồi tin nhắn thành công.');
    }

    /**
     * Xóa toàn bộ cuộc trao đổi.
     */
    public function destroyDiscussion(Discussion $discussion): RedirectResponse
    {
        $user = auth()->user();
        $course = $discussion->course ?: $discussion->lesson?->course;
        $isOwner = (int) $discussion->user_id === (int) $user->id;
        $isInstructor = $user->role === 'admin' || ($user->role === 'instructor' && $course && (int) $course->instructor_id === (int) $user->id);
        abort_unless($isOwner || $isInstructor, 403, 'Bạn không có quyền xóa cuộc trao đổi này.');

        if ($discussion->attachment_path && Storage::disk('public')->exists($discussion->attachment_path)) {
            Storage::disk('public')->delete($discussion->attachment_path);
        }

        foreach ($discussion->replies as $r) {
            if ($r->attachment_path && Storage::disk('public')->exists($r->attachment_path)) {
                Storage::disk('public')->delete($r->attachment_path);
            }
        }

        $lesson = $discussion->lesson;
        $discussion->delete();

        if ($user->role === 'instructor' || $user->role === 'admin') {
            return redirect()->route('instructor.discussions.index')->with('success', 'Đã xóa cuộc trao đổi thành công.');
        }

        return redirect()->route('courses.lessons.show', ['course' => $course, 'lesson' => $lesson])->with('success', 'Đã xóa cuộc trao đổi thành công.');
    }
}
