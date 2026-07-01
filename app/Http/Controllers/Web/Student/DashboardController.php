<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['course.instructor:id,name', 'course.category:id,name'])
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get();

        $stats = [
            'enrolled' => Enrollment::where('user_id', $user->id)->where('status', 'active')->count(),
            'in_progress' => Enrollment::where('user_id', $user->id)->where('status', 'active')->where('progress_percent', '<', 100)->whereNull('completed_at')->count(),
            'completed' => Enrollment::where('user_id', $user->id)->where('status', 'active')->whereNotNull('completed_at')->count(),
            'certificates' => Certificate::where('user_id', $user->id)->count(),
        ];

        $avgProgress = Enrollment::where('user_id', $user->id)->where('status', 'active')->avg('progress_percent') ?? 0;

        return view('student.dashboard', compact('enrollments', 'stats', 'avgProgress'));
    }
}
