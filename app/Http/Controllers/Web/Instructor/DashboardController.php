<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $courseIds = Course::where('instructor_id', $user->id)->pluck('id');

        $orders = \App\Models\Order::where('status', 'paid')->get();
        $revenue = 0;
        foreach ($orders as $order) {
            foreach (($order->items ?? []) as $item) {
                if (in_array($item['course_id'] ?? null, $courseIds->toArray())) {
                    $revenue += $item['price'] ?? 0;
                }
            }
        }

        $stats = [
            'courses' => $courseIds->count(),
            'published' => Course::where('instructor_id', $user->id)->where('status', Course::STATUS_PUBLISHED)->count(),
            'students' => Enrollment::whereIn('course_id', $courseIds)->where('status', 'active')->distinct('user_id')->count('user_id'),
            'revenue' => $revenue,
        ];

        $recentCourses = Course::where('instructor_id', $user->id)
            ->with('category:id,name')
            ->withCount(['enrollments' => fn ($query) => $query->where('status', 'active')])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        $recentStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->with(['user:id,name,email', 'course:id,title'])
            ->orderByDesc('enrolled_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('instructor.dashboard', compact('stats', 'recentCourses', 'recentStudents'));
    }
}
