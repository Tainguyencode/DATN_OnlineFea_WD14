<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\Category;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $parent = (string) $request->query('parent');
        $status = (string) $request->query('status');

        $categories = Category::query()
            ->with(['parent:id,name', 'children:id,parent_id,name,status,sort_order'])
            ->withCount(['children', 'courses'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($parent === 'root', fn ($query) => $query->whereNull('parent_id'))
            ->when(is_numeric($parent), fn ($query) => $query->where('parent_id', (int) $parent))
            ->when($status === 'active', fn ($query) => $query->where('status', true))
            ->when($status === 'inactive', fn ($query) => $query->where('status', false))
            ->orderByRaw('COALESCE(parent_id, id)')
            ->orderByRaw('parent_id IS NOT NULL')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $parents = Category::query()
            ->parent()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $stats = [
            'total' => Category::count(),
            'parents' => Category::query()->parent()->count(),
            'children' => Category::query()->child()->count(),
            'active' => Category::query()->active()->count(),
        ];

        return view('admin.categories.index', compact('categories', 'parents', 'stats', 'search', 'parent', 'status'));
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(['status' => true, 'sort_order' => 0]),
            'parents' => $this->parentOptions(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = DB::transaction(fn () => Category::create($this->payload($request)));
        ActivityLogService::log(auth()->id(), 'create_category', Category::class, $category->id, ['slug' => $category->slug], $request);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Tạo danh mục khóa học thành công.');
    }

    public function edit(Category $category): View
    {
        $category->load('parent');

        return view('admin.categories.edit', [
            'category' => $category,
            'parents' => $this->parentOptions($category),
        ]);
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        DB::transaction(fn () => $category->update($this->payload($request)));
        ActivityLogService::log(auth()->id(), 'update_category', Category::class, $category->id, ['slug' => $category->slug], $request);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục khóa học thành công.');
    }

    public function toggleStatus(Request $request, Category $category): RedirectResponse
    {
        $category->update(['status' => ! $category->status]);
        ActivityLogService::log(auth()->id(), 'toggle_category_status', Category::class, $category->id, ['status' => $category->status], $request);

        return back()->with('success', $category->status ? 'Đã bật danh mục.' : 'Đã tắt danh mục.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()->withErrors([
                'category' => 'Không thể xóa danh mục đang có danh mục con. Hãy chuyển hoặc xóa danh mục con trước, hoặc tắt trạng thái danh mục.',
            ]);
        }

        if ($category->courses()->exists()) {
            return back()->withErrors([
                'category' => 'Không thể xóa danh mục đang có khóa học sử dụng. Hãy chuyển khóa học sang danh mục khác hoặc tắt trạng thái danh mục.',
            ]);
        }

        $categoryId = $category->id;
        $category->delete();
        ActivityLogService::log(auth()->id(), 'delete_category', Category::class, $categoryId, null, $request);

        return back()->with('success', 'Đã xóa danh mục khóa học.');
    }

    private function parentOptions(?Category $category = null)
    {
        return Category::query()
            ->parent()
            ->when($category?->exists, fn ($query) => $query->whereKeyNot($category->id))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(StoreCategoryRequest $request): array
    {
        return [
            'name' => $request->string('name')->trim()->toString(),
            'parent_id' => $request->integer('parent_id') ?: null,
            'slug' => $request->string('slug')->trim()->toString() ?: null,
            'description' => $request->filled('description') ? $request->string('description')->trim()->toString() : null,
            'icon' => $request->filled('icon') ? $request->string('icon')->trim()->toString() : null,
            'status' => $request->boolean('status'),
            'sort_order' => $request->integer('sort_order'),
        ];
    }
}
