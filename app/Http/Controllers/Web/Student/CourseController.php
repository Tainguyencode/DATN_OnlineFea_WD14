<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->isStudent(), 403);

        $enrollments = Enrollment::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with(['course.instructor:id,name,avatar', 'course.category:id,name'])
            ->orderByDesc('enrolled_at')
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('student.courses.index', compact('enrollments'));
    }
}
