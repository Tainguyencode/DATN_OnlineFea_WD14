<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $reviews = Review::query()
            ->where('user_id', $request->user()->id)
            ->with(['course:id,instructor_id,title,slug,thumbnail', 'course.instructor:id,name', 'replier:id,name'])
            ->when(in_array($status, ['pending', 'approved', 'rejected', 'hidden'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(config('reviews.per_page', 8))
            ->withQueryString();

        return view('student.reviews.index', compact('reviews', 'status'));
    }
}
