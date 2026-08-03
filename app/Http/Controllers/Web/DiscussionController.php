<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\StoreDiscussionRequest;
use App\Http\Requests\Learning\StoreDiscussionReplyRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class DiscussionController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Store a new student question (discussion).
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

        $discussion = Discussion::create([
            'lesson_id' => $lesson->id,
            'user_id' => auth()->id(),
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'is_resolved' => false,
        ]);

        return redirect()->route('courses.lessons.show', [
            'course' => $course,
            'lesson' => $lesson,
            'discussion_id' => $discussion->id
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

        $isInstructor = (int) $discussion->lesson->course->instructor_id === (int) auth()->id();

        $reply = DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'user_id' => auth()->id(),
            'content' => $request->validated('content'),
            'is_instructor_answer' => $isInstructor,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);

        // If lecturer replies, send push notification to student
        if ($isInstructor && $discussion->user) {
            $course = $discussion->lesson->course;
            $lesson = $discussion->lesson;
            $title = 'Giảng viên đã trả lời câu hỏi của bạn';
            $message = auth()->user()->name . ' đã trả lời câu hỏi: "' . Str::limit($discussion->title, 40) . '"';
            $url = route('courses.lessons.show', [
                'course' => $course,
                'lesson' => $lesson,
                'discussion_id' => $discussion->id
            ]) . '#qa-tab';

            $this->notificationService->send(
                $discussion->user,
                $title,
                $message,
                'discussion_reply',
                $url
            );
        }

        return back()->with('success', 'Đã gửi phản hồi.');
    }
}
