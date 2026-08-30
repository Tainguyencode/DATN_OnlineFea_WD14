<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\LearningPath;
use Illuminate\View\View;

class PageController extends Controller
{
    public function academy(): View
    {
        $featuredCourses = Course::published()
            ->with(['instructor:id,name,avatar', 'category:id,name,slug'])
            ->orderByDesc('rating_avg')
            ->limit(4)
            ->get();

        $categories = Category::active()->parent()->withCount(['courses' => fn ($q) => $q->published()])->limit(6)->get();

        return view('pages.academy', compact('featuredCourses', 'categories'));
    }

    public function innovationLab(): View
    {
        $learningPaths = LearningPath::withCount('courses')->limit(3)->get();

        return view('pages.innovation-lab', compact('learningPaths'));
    }

    public function careerAccelerator(): View
    {
        $learningPaths = LearningPath::withCount('courses')->limit(4)->get();

        return view('pages.career-accelerator', compact('learningPaths'));
    }

    public function corporateTraining(): View
    {
        $stats = [
            'courses' => Course::published()->count(),
            'categories' => Category::active()->parent()->count(),
        ];

        return view('pages.corporate-training', compact('stats'));
    }
}
