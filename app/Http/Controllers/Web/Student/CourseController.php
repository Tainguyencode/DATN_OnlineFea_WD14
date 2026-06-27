<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $enrollments = Enrollment::where('user_id', auth()->id())
            ->with(['course.instructor:id,name,avatar', 'course.category:id,name'])
            ->orderByDesc('updated_at')
            ->paginate(9);

        return view('student.courses.index', compact('enrollments'));
    }
}
