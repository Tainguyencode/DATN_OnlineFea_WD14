<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LearningPathController extends Controller
{
    public function index(Request $request): View
    {
        $query = LearningPath::withCount('courses');

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('target_role', 'like', "%{$search}%");
        }

        $learningPaths = $query->latest()->paginate(10)->withQueryString();

        return view('admin.learning-paths.index', compact('learningPaths'));
    }

    public function create(): View
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.learning-paths.create', compact('courses'));
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'target_role' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'estimated_duration' => 'nullable|string|max:255',
            'skills_input' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'sort_orders' => 'nullable|array',
            'stage_names' => 'nullable|array',
        ]);

        $skills = [];
        if (! empty($validated['skills_input'])) {
            $skills = array_values(array_filter(array_map('trim', explode(',', $validated['skills_input']))));
        }

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;
        while (LearningPath::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $learningPath = LearningPath::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'target_role' => $validated['target_role'] ?? null,
            'salary_range' => $validated['salary_range'] ?? null,
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'skills' => $skills,
            'is_featured' => (bool) ($request->input('is_featured', false)),
        ]);

        if (! empty($validated['courses'])) {
            $syncData = [];
            foreach ($validated['courses'] as $courseId) {
                $sortOrder = $request->input("sort_orders.{$courseId}", 0);
                $stageName = $request->input("stage_names.{$courseId}", 'Giai đoạn học tập');
                $syncData[$courseId] = [
                    'sort_order' => (int) $sortOrder,
                    'stage_name' => $stageName,
                ];
            }
            $learningPath->courses()->sync($syncData);
        }

        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Đã tạo mới lộ trình học tập chuyên nghiệp thành công!');
    }

    public function edit(LearningPath $learningPath): View
    {
        $learningPath->load('courses');
        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.learning-paths.edit', compact('learningPath', 'courses'));
    }


    public function update(Request $request, LearningPath $learningPath): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'target_role' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'estimated_duration' => 'nullable|string|max:255',
            'skills_input' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'sort_orders' => 'nullable|array',
            'stage_names' => 'nullable|array',
        ]);

        $skills = [];
        if (! empty($validated['skills_input'])) {
            $skills = array_values(array_filter(array_map('trim', explode(',', $validated['skills_input']))));
        }

        if ($learningPath->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $count = 1;
            while (LearningPath::where('slug', $slug)->where('id', '!=', $learningPath->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $learningPath->slug = $slug;
        }

        $learningPath->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'target_role' => $validated['target_role'] ?? null,
            'salary_range' => $validated['salary_range'] ?? null,
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'skills' => $skills,
            'is_featured' => (bool) ($request->input('is_featured', false)),
        ]);

        $syncData = [];
        if (! empty($validated['courses'])) {
            foreach ($validated['courses'] as $courseId) {
                $sortOrder = $request->input("sort_orders.{$courseId}", 0);
                $stageName = $request->input("stage_names.{$courseId}", 'Giai đoạn học tập');
                $syncData[$courseId] = [
                    'sort_order' => (int) $sortOrder,
                    'stage_name' => $stageName,
                ];
            }
        }
        $learningPath->courses()->sync($syncData);

        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Đã cập nhật thông tin lộ trình học tập!');
    }

    public function destroy(LearningPath $learningPath): RedirectResponse
    {
        $learningPath->delete();

        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Đã xóa lộ trình học tập!');
    }
}
