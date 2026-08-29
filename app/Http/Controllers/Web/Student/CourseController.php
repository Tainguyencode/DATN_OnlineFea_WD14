<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->isStudent(), 403);

        $status = $request->string('status')->toString();
        $status = in_array($status, ['all', 'in_progress', 'completed'], true) ? $status : 'all';

        $query = Enrollment::query()
            ->where('user_id', $request->user()->id)
            ->withLearningAccess()
            ->with(['course.instructor:id,name,avatar', 'course.category:id,name', 'course.lessons:id,course_id,sort_order'])
            ->orderByDesc('enrolled_at')
            ->orderByDesc('created_at');

        if ($status === 'in_progress') {
            $query->whereNull('completed_at')->where('progress_percent', '<', 100);
        } elseif ($status === 'completed') {
            $query->where(fn ($builder) => $builder
                ->whereNotNull('completed_at')
                ->orWhere('status', Enrollment::STATUS_COMPLETED));
        }

        $enrollments = $query->paginate(9)->withQueryString();

        return view('student.dashboard.courses.index', compact('enrollments', 'status'));
    }
}
