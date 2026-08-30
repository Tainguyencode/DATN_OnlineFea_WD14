<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Enrollment;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $items = Wishlist::query()
            ->where('user_id', $userId)
            ->whereHas('course', fn ($query) => $query->published())
            ->with(['course' => fn ($query) => $query
                ->with(['instructor:id,name,avatar', 'category:id,name,slug', 'lessons:id,course_id,sort_order'])
                ->withCount('lessons')])
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        $cartCourseIds = Cart::query()
            ->where('user_id', $userId)
            ->first()?->courses()
            ->pluck('courses.id')
            ->all() ?? [];

        $enrolledCourseIds = Enrollment::query()
            ->where('user_id', $userId)
            ->withLearningAccess()
            ->pluck('course_id')
            ->all();

        return view('student.dashboard.wishlist.index', compact('items', 'cartCourseIds', 'enrolledCourseIds'));
    }
}
