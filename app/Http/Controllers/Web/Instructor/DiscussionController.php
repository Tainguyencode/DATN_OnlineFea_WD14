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

        // Get discussions in these courses
        $query = Discussion::whereHas('lesson', function ($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->with(['user', 'lesson.course', 'replies']);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('lesson', function ($q) {
                $q->where('course_id', request()->integer('course_id'));
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if ($status === 'answered') {
                $query->whereHas('replies', function ($q) {
                    $q->where('is_instructor_answer', true);
                });
            } elseif ($status === 'pending') {
                $query->whereDoesntHave('replies', function ($q) {
                    $q->where('is_instructor_answer', true);
                });
            }
        }

        $discussions = $query->latest()->paginate(10)->withQueryString();

        return view('instructor.discussions.index', [
            'discussions' => $discussions,
            'courses' => $courses,
            'filters' => [
                'course_id' => $request->integer('course_id'),
                'status' => $request->string('status')->toString(),
            ]
        ]);
    }

    /**
     * Display a single discussion with its replies and reply form.
     */
    public function show(Discussion $discussion): View
    {
        $user = auth()->user();

        // Check if instructor owns the course
        abort_unless((int) $discussion->lesson->course->instructor_id === (int) $user->id, 403);

        $discussion->load(['user', 'lesson.course', 'replies.user']);

        return view('instructor.discussions.show', [
            'discussion' => $discussion,
        ]);
    }
}
