<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $courseId = $request->integer('course_id') ?: null;
        $rating = $request->integer('rating') ?: null;
        $status = $request->query('status');
        $replyState = $request->query('reply');

        $reviews = Review::query()
            ->whereNull('parent_id')
            ->whereHas('course', fn ($query) => $query->where('instructor_id', $request->user()->id))
            ->with(['user:id,name,avatar', 'course:id,instructor_id,title,slug', 'replies.user:id,name'])
            ->when($courseId, fn ($query) => $query->where('course_id', $courseId))
            ->when($rating && $rating >= 1 && $rating <= 5, fn ($query) => $query->where('rating', $rating))
            ->when(in_array($status, ['pending', 'approved', 'rejected', 'hidden'], true), fn ($query) => $query->where('status', $status))
            ->when($replyState === 'replied', fn ($query) => $query->whereHas('replies'))
            ->when($replyState === 'unreplied', fn ($query) => $query->whereDoesntHave('replies'))
            ->latest()
            ->paginate(config('reviews.per_page', 8))
            ->withQueryString();

        $courses = Course::query()
            ->where('instructor_id', $request->user()->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('instructor.reviews.index', compact('reviews', 'courses', 'courseId', 'rating', 'status', 'replyState'));
    }
}
