<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use App\Services\DiscussionChatService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscussionController extends Controller
{
    public function __construct(private readonly DiscussionChatService $chat) {}

    /**
     * Display a listing of student discussions for the instructor's courses.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Get all courses owned by this instructor
        $courses = Course::where('instructor_id', $user->id)->get();
        $courseIds = $courses->pluck('id')->all();

        // Base query for discussions in these courses (Course-level)
        $baseQuery = Discussion::where(function ($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds)
                ->orWhereHas('lesson', function ($lq) use ($courseIds) {
                    $lq->whereIn('course_id', $courseIds);
                });
        });

        // Filter by course
        if ($request->filled('course_id')) {
            $courseId = request()->integer('course_id');
            $baseQuery->where(function ($q) use ($courseId) {
                $q->where('course_id', $courseId)
                    ->orWhereHas('lesson', function ($lq) use ($courseId) {
                        $lq->where('course_id', $courseId);
                    });
            });
        }

        // Search by student name, title, or content
        if ($request->filled('search')) {
            $search = '%'.trim($request->string('search')->toString()).'%';
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('content', 'like', $search)
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', $search)->orWhere('email', 'like', $search);
                    });
            });
        }

        $totalCount = (clone $baseQuery)->count();
        $pendingConstraint = fn ($query) => $query
            ->whereNull('last_message_user_id')
            ->orWhere('last_message_user_id', '!=', $user->id);
        $pendingCount = (clone $baseQuery)->where($pendingConstraint)->count();
        $answeredCount = (clone $baseQuery)->where('last_message_user_id', $user->id)->count();

        $status = $request->string('status')->toString();
        if ($status === 'pending') {
            $baseQuery->where($pendingConstraint);
        } elseif ($status === 'answered') {
            $baseQuery->where('last_message_user_id', $user->id);
        }

        $paginated = $baseQuery
            ->with(['user', 'course', 'lesson.course', 'lastReply.user', 'lastReply.lesson'])
            ->withCount('replies')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        $unreadCounts = $this->chat->unreadCountsFor($paginated->getCollection()->pluck('id'), $user);
        $paginated->getCollection()->each(function (Discussion $discussion) use ($unreadCounts): void {
            $discussion->setAttribute('chat_unread_count', $unreadCounts[$discussion->id] ?? 0);
        });

        return view('instructor.discussions.index', [
            'discussions' => $paginated,
            'courses' => $courses,
            'counts' => [
                'total' => $totalCount,
                'pending' => $pendingCount,
                'answered' => $answeredCount,
            ],
            'filters' => [
                'course_id' => $request->integer('course_id'),
                'status' => $status,
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    /**
     * Display a single discussion with its replies and reply form.
     */
    public function show(Discussion $discussion): View
    {
        $this->authorize('view', $discussion);
        $discussion->load(['user', 'course', 'course.instructor', 'lesson']);

        return view('instructor.discussions.show', [
            'discussion' => $discussion,
            'chatContext' => $this->chat->context($discussion, request()->user()),
        ]);
    }
}
