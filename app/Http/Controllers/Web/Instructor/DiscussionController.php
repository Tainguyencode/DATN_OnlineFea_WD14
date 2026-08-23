<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscussionController extends Controller
{
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
            $search = '%' . trim($request->string('search')->toString()) . '%';
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('content', 'like', $search)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', $search)->orWhere('email', 'like', $search);
                  });
            });
        }

        // Load relations
        $allDiscussions = (clone $baseQuery)->with(['user', 'course', 'lesson', 'replies.user', 'replies.lesson'])->latest()->get();

        // Compute counts
        $totalCount = $allDiscussions->count();
        $pendingCount = $allDiscussions->filter(fn (Discussion $d) => $d->needsReply())->count();
        $answeredCount = $allDiscussions->filter(fn (Discussion $d) => $d->isAnswered())->count();

        // Filter by status in memory / collection or query
        $status = $request->string('status')->toString();
        $filteredCollection = $allDiscussions;
        if ($status === 'pending') {
            $filteredCollection = $allDiscussions->filter(fn (Discussion $d) => $d->needsReply());
        } elseif ($status === 'answered') {
            $filteredCollection = $allDiscussions->filter(fn (Discussion $d) => $d->isAnswered());
        }

        // Manual pagination from filtered collection
        $page = request()->integer('page', 1);
        $perPage = 15;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredCollection->forPage($page, $perPage)->values(),
            $filteredCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
        $user = auth()->user();
        $course = $discussion->course ?: $discussion->lesson?->course;

        // Check if instructor owns the course
        abort_unless($course && (int) $course->instructor_id === (int) $user->id, 403);

        $discussion->load(['user', 'course', 'lesson', 'replies.user', 'replies.lesson', 'replies.replyTo.user']);

        return view('instructor.discussions.show', [
            'discussion' => $discussion,
        ]);
    }
}
