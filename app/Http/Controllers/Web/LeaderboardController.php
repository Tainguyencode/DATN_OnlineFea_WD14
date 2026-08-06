<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'all');
        $courseId = $request->query('course_id');
        $search = $request->query('search');

        // Date constraints
        $dateConstraint = null;
        if ($period === 'week') {
            $dateConstraint = now()->startOfWeek();
        } elseif ($period === 'month') {
            $dateConstraint = now()->startOfMonth();
        } elseif ($period === 'year') {
            $dateConstraint = now()->startOfYear();
        }

        // 1. Build subquery to aggregate points per student user
        $pointsSubquery = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->when($dateConstraint, fn($q) => $q->where('created_at', '>=', $dateConstraint))
            ->when($courseId, fn($q) => $q->where('course_id', $courseId))
            ->groupBy('user_id');

        // 2. Query student users who have points
        $leaderboardQuery = User::query()
            ->where('role', 'student')
            ->joinSub($pointsSubquery, 'points_table', 'users.id', '=', 'points_table.user_id')
            ->select('users.*', 'points_table.total_points')
            ->when($search, fn($q) => $q->where('users.name', 'like', "%{$search}%"))
            ->orderByDesc('points_table.total_points')
            ->orderBy('users.id');

        // Limit the leaderboard to top 100 students (in total) but paginated
        $leaderboard = $leaderboardQuery->paginate(20);

        // Fetch additional metrics for each user on the current page
        foreach ($leaderboard->items() as $user) {
            // Rank of the student (number of students with higher points + 1)
            $user->rank = DB::table('user_points')
                ->select('user_id', DB::raw('SUM(points) as total_points'))
                ->when($dateConstraint, fn($q) => $q->where('created_at', '>=', $dateConstraint))
                ->when($courseId, fn($q) => $q->where('course_id', $courseId))
                ->groupBy('user_id')
                ->having('total_points', '>', $user->total_points)
                ->get()
                ->count() + 1;

            $user->completed_courses_count = Enrollment::where('user_id', $user->id)
                ->where('status', Enrollment::STATUS_COMPLETED)
                ->count();

            $user->avg_quiz_score = QuizAttempt::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->groupBy('quiz_id')
                ->select(DB::raw('MAX(percent) as max_percent'))
                ->get()
                ->avg('max_percent') ?? 0;
        }

        // 3. Persistent stats for logged in student user
        $currentUserData = null;
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->role === 'student') {
            $currentUserPoints = (int) DB::table('user_points')
                ->where('user_id', $currentUser->id)
                ->when($dateConstraint, fn($q) => $q->where('created_at', '>=', $dateConstraint))
                ->when($courseId, fn($q) => $q->where('course_id', $courseId))
                ->sum('points');

            $currentUserRank = DB::table('user_points')
                ->select('user_id', DB::raw('SUM(points) as total_points'))
                ->when($dateConstraint, fn($q) => $q->where('created_at', '>=', $dateConstraint))
                ->when($courseId, fn($q) => $q->where('course_id', $courseId))
                ->groupBy('user_id')
                ->having('total_points', '>', $currentUserPoints)
                ->get()
                ->count() + 1;

            $currentUserCompletedCourses = Enrollment::where('user_id', $currentUser->id)
                ->where('status', Enrollment::STATUS_COMPLETED)
                ->count();

            $currentUserAvgQuizScore = QuizAttempt::where('user_id', $currentUser->id)
                ->whereNotNull('completed_at')
                ->groupBy('quiz_id')
                ->select(DB::raw('MAX(percent) as max_percent'))
                ->get()
                ->avg('max_percent') ?? 0;

            $currentUserData = [
                'user' => $currentUser,
                'total_points' => $currentUserPoints,
                'rank' => $currentUserRank,
                'completed_courses_count' => $currentUserCompletedCourses,
                'avg_quiz_score' => $currentUserAvgQuizScore,
                'badges' => $currentUser->badges
            ];
        }

        // Fetch courses for the dropdown filter
        $courses = Course::where('status', 'published')->get();

        return view('leaderboard.index', compact(
            'leaderboard',
            'currentUserData',
            'courses',
            'period',
            'courseId',
            'search'
        ));
    }
}
