<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreLearningPathRequest;
use App\Http\Requests\Instructor\UpdateLearningPathRequest;
use App\Models\Course;
use App\Models\LearningPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LearningPathController extends Controller
{
    /**
     * Danh sách Lộ trình học tập do Giảng viên này tạo.
     */
    public function index(Request $request): View
    {
        $instructorId = auth()->id();
        $query = LearningPath::query()
            ->where('created_by', $instructorId)
            ->withCount('courses');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('target_role', 'like', "%{$search}%");
            });
        }

        $learningPaths = $query->latest()->paginate(10)->withQueryString();

        return view('instructor.learning-paths.index', compact('learningPaths'));
    }

    /**
     * Giao diện Tạo Lộ trình học tập mới cho Giảng viên.
     */
    public function create(): View
    {
        // Chỉ lấy danh sách các khóa học do chính Giảng viên này sở hữu
        $courses = Course::where('instructor_id', auth()->id())
            ->orderBy('title')
            ->get(['id', 'title', 'status', 'is_published']);

        return view('instructor.learning-paths.create', compact('courses'));
    }

    /**
     * Lưu Lộ trình học tập mới của Giảng viên.
     */
    public function store(StoreLearningPathRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Đảm bảo chỉ được chọn các khóa học do chính Giảng viên này làm chủ
        $instructorCourseIds = Course::where('instructor_id', auth()->id())->pluck('id')->toArray();
        $selectedCourseIds = array_intersect($validated['courses'] ?? [], $instructorCourseIds);

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
            'created_by' => auth()->id(),
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

        if (! empty($selectedCourseIds)) {
            $syncData = [];
            foreach ($selectedCourseIds as $courseId) {
                $sortOrder = $request->input("sort_orders.{$courseId}", 0);
                $stageName = $request->input("stage_names.{$courseId}", 'Giai đoạn học tập');
                $syncData[$courseId] = [
                    'sort_order' => (int) $sortOrder,
                    'stage_name' => $stageName,
                ];
            }
            $learningPath->courses()->sync($syncData);
        }

        return redirect()->route('instructor.learning-paths.index')
            ->with('success', 'Đã tạo Lộ trình học tập mới thành công!');
    }

    /**
     * Giao diện chỉnh sửa Lộ trình học tập của Giảng viên.
     */
    public function edit(LearningPath $learningPath): View
    {
        $this->authorizeInstructor($learningPath);

        $learningPath->load('courses');
        $courses = Course::where('instructor_id', auth()->id())
            ->orderBy('title')
            ->get(['id', 'title', 'status', 'is_published']);

        return view('instructor.learning-paths.edit', compact('learningPath', 'courses'));
    }

    /**
     * Cập nhật Lộ trình học tập của Giảng viên.
     */
    public function update(UpdateLearningPathRequest $request, LearningPath $learningPath): RedirectResponse
    {
        $this->authorizeInstructor($learningPath);

        $validated = $request->validated();

        // Đảm bảo chỉ được chọn các khóa học do chính Giảng viên này làm chủ
        $instructorCourseIds = Course::where('instructor_id', auth()->id())->pluck('id')->toArray();
        $selectedCourseIds = array_intersect($validated['courses'] ?? [], $instructorCourseIds);

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
        if (! empty($selectedCourseIds)) {
            foreach ($selectedCourseIds as $courseId) {
                $sortOrder = $request->input("sort_orders.{$courseId}", 0);
                $stageName = $request->input("stage_names.{$courseId}", 'Giai đoạn học tập');
                $syncData[$courseId] = [
                    'sort_order' => (int) $sortOrder,
                    'stage_name' => $stageName,
                ];
            }
        }
        $learningPath->courses()->sync($syncData);

        return redirect()->route('instructor.learning-paths.index')
            ->with('success', 'Đã cập nhật Lộ trình học tập thành công!');
    }

    /**
     * Xóa Lộ trình học tập của Giảng viên.
     */
    public function destroy(LearningPath $learningPath): RedirectResponse
    {
        $this->authorizeInstructor($learningPath);

        $learningPath->delete();

        return redirect()->route('instructor.learning-paths.index')
            ->with('success', 'Đã xóa lộ trình học tập!');
    }

    private function authorizeInstructor(LearningPath $learningPath): void
    {
        if ((int) $learningPath->created_by !== (int) auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403, 'Bạn không có quyền chỉnh sửa lộ trình học tập này.');
        }
    }
}
