<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorDocumentRequirementController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        $selectedCategoryId = $request->query('category_id');
        if (! $selectedCategoryId && $categories->isNotEmpty()) {
            $selectedCategoryId = $categories->first()->id;
        }

        $selectedCategory = $selectedCategoryId ? Category::with('parent')->find($selectedCategoryId) : null;

        $requirements = $selectedCategoryId
            ? InstructorDocumentRequirement::where('category_id', $selectedCategoryId)->orderBy('sort_order')->get()
            : collect();

        $documentTypes = InstructorCertificate::documentTypeLabels();

        return view('admin.instructors.requirements.index', [
            'categories' => $categories,
            'selectedCategoryId' => (int) $selectedCategoryId,
            'selectedCategory' => $selectedCategory,
            'requirements' => $requirements,
            'documentTypes' => $documentTypes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'document_type' => ['required', 'string', 'in:certificate,degree,employment_contract,transcript,employment_confirmation,other'],
            'document_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'category_id.required' => 'Vui lòng chọn ngành / lĩnh vực.',
            'document_type.required' => 'Vui lòng chọn loại tài liệu.',
            'document_title.required' => 'Vui lòng nhập tên yêu cầu tài liệu.',
        ]);

        $requirement = InstructorDocumentRequirement::create([
            'category_id' => $validated['category_id'],
            'document_type' => $validated['document_type'],
            'document_title' => $validated['document_title'],
            'description' => $validated['description'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'is_active' => true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        ActivityLogService::log($request->user()->id, 'create_instructor_requirement', InstructorDocumentRequirement::class, $requirement->id, [
            'category_id' => $requirement->category_id,
            'title' => $requirement->document_title,
        ], $request);

        return back()->with('success', 'Đã thêm yêu cầu hồ sơ "'.$requirement->document_title.'" cho ngành thành công.');
    }

    public function update(Request $request, InstructorDocumentRequirement $requirement): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'in:certificate,degree,employment_contract,transcript,employment_confirmation,other'],
            'document_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'document_type.required' => 'Vui lòng chọn loại tài liệu.',
            'document_title.required' => 'Vui lòng nhập tên yêu cầu tài liệu.',
        ]);

        $requirement->update([
            'document_type' => $validated['document_type'],
            'document_title' => $validated['document_title'],
            'description' => $validated['description'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $requirement->is_active,
            'sort_order' => $validated['sort_order'] ?? $requirement->sort_order,
        ]);

        ActivityLogService::log($request->user()->id, 'update_instructor_requirement', InstructorDocumentRequirement::class, $requirement->id, [], $request);

        return back()->with('success', 'Đã cập nhật yêu cầu hồ sơ "'.$requirement->document_title.'" thành công.');
    }

    public function toggleStatus(Request $request, InstructorDocumentRequirement $requirement): RedirectResponse
    {
        $requirement->update([
            'is_active' => ! $requirement->is_active,
        ]);

        $statusText = $requirement->is_active ? 'Kích hoạt' : 'Vô hiệu hóa';
        ActivityLogService::log($request->user()->id, 'toggle_instructor_requirement_status', InstructorDocumentRequirement::class, $requirement->id, [
            'is_active' => $requirement->is_active,
        ], $request);

        return back()->with('success', "Đã {$statusText} yêu cầu hồ sơ \"{$requirement->document_title}\".");
    }

    public function destroy(Request $request, InstructorDocumentRequirement $requirement): RedirectResponse
    {
        $title = $requirement->document_title;
        $requirement->delete();

        ActivityLogService::log($request->user()->id, 'delete_instructor_requirement', InstructorDocumentRequirement::class, $requirement->id, [
            'title' => $title,
        ], $request);

        return back()->with('success', 'Đã xóa yêu cầu hồ sơ "'.$title.'".');
    }
}
