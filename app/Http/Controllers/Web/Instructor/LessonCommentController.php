<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonComment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonCommentController extends Controller
{
    /**
     * Display a listing of lesson comments for the instructor's courses.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Get all courses owned by this instructor
        $courses = Course::where('instructor_id', $user->id)->get();
        $courseIds = $courses->pluck('id')->all();

        // Get main comments (parent_id is null) in these courses
        $query = LessonComment::whereNull('parent_id')
            ->whereHas('lesson', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })
            ->with(['user', 'lesson.course', 'lesson.chapter', 'lesson.section', 'replies.user']);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('lesson', function ($q) {
                $q->where('course_id', request()->integer('course_id'));
            });
        }

        // Filter by status (visible / hidden)
        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if ($status === 'hidden') {
                $query->where('is_hidden', true);
            } elseif ($status === 'visible') {
                $query->where('is_hidden', false);
            }
        }

        $comments = $query->latest()->paginate(15)->withQueryString();

        return view('instructor.comments.index', [
            'comments' => $comments,
            'courses' => $courses,
            'filters' => [
                'course_id' => $request->integer('course_id'),
                'status' => $request->string('status')->toString(),
            ],
        ]);
    }

    /**
     * Display a single comment thread with its replies and reply form.
     */
    public function show(LessonComment $comment): View
    {
        $user = auth()->user();

        // Check if instructor owns the course
        abort_unless((int) $comment->lesson->course->instructor_id === (int) $user->id, 403);

        $comment->load(['user', 'lesson.course', 'lesson.chapter', 'lesson.section', 'replies.user']);

        return view('instructor.comments.show', [
            'comment' => $comment,
        ]);
    }
}
