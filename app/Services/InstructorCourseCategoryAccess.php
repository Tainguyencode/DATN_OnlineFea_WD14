<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Resolves an instructor's course permissions based on their registered teaching fields,
 * with hierarchical support (parent -> child categories) and fallback for legacy profiles.
 */
class InstructorCourseCategoryAccess
{
    /** @return Collection<int, Category> */
    public function selectableCategories(User $instructor): Collection
    {
        $registeredCategories = $this->getRegisteredCategories($instructor);

        if ($registeredCategories->isEmpty()) {
            return new Collection;
        }

        $selectableCategoryIds = [];

        foreach ($registeredCategories as $cat) {
            if (! $cat->status) {
                continue;
            }

            if ($cat->parent_id) {
                // Danh mục con: kiểm tra danh mục cha còn hoạt động không
                if ($cat->parent && ! $cat->parent->status) {
                    continue;
                }
                $selectableCategoryIds[] = (int) $cat->id;
            } else {
                // Danh mục cha (ngành):
                // Nếu có các danh mục con đang hoạt động, lấy tất cả danh mục con để tạo khóa học
                $activeChildren = $cat->children()->active()->pluck('id')->all();
                if (! empty($activeChildren)) {
                    $selectableCategoryIds = array_merge($selectableCategoryIds, array_map('intval', $activeChildren));
                } else {
                    // Nếu không có danh mục con, bản thân danh mục cha được chọn
                    $selectableCategoryIds[] = (int) $cat->id;
                }
            }
        }

        $uniqueIds = array_values(array_unique($selectableCategoryIds));

        if (empty($uniqueIds)) {
            return new Collection;
        }

        return Category::query()
            ->whereIn('id', $uniqueIds)
            ->with('parent:id,name,status')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'status', 'sort_order']);
    }

    public function canTeachCategory(User $instructor, int $categoryId): bool
    {
        if ($categoryId <= 0) {
            return false;
        }

        $category = Category::find($categoryId);
        if (! $category || ! $category->status) {
            return false;
        }

        $registeredCategories = $this->getRegisteredCategories($instructor);
        if ($registeredCategories->isEmpty()) {
            return false;
        }

        $registeredIds = $registeredCategories->pluck('id')->map(fn ($id) => (int) $id)->all();

        // 1. Trực tiếp thuộc danh mục đã đăng ký
        if (in_array($categoryId, $registeredIds, true)) {
            return true;
        }

        // 2. Nếu là danh mục con, kiểm tra xem danh mục cha có nằm trong các ngành đã đăng ký không
        if ($category->parent_id && in_array((int) $category->parent_id, $registeredIds, true)) {
            return true;
        }

        return false;
    }

    public function canManageCourse(User $instructor, Course $course): bool
    {
        return $course->isOwnedBy($instructor)
            && $this->canTeachCategory($instructor, (int) $course->category_id);
    }

    /**
     * Lấy danh sách các danh mục/ngành mà giảng viên đã đăng ký (kèm fallback).
     *
     * @return Collection<int, Category>
     */
    public function getRegisteredCategories(User $instructor): Collection
    {
        $profile = $instructor->relationLoaded('instructorProfile')
            ? $instructor->instructorProfile
            : $instructor->instructorProfile()->first();

        if (! $profile) {
            return new Collection;
        }

        $teachingCategories = $profile->relationLoaded('teachingCategories')
            ? $profile->teachingCategories
            : $profile->teachingCategories()->with('parent:id,name,status')->get();

        if ($teachingCategories->isNotEmpty()) {
            return $teachingCategories;
        }

        // Fallback: nếu pivot table chưa có bản ghi, lấy từ category_id / teaching_field
        $fallbackCategories = $instructor->getTeachingCategories();
        if ($fallbackCategories->isNotEmpty()) {
            // Tự động đồng bộ vào pivot để các lần query tiếp theo chuẩn xác
            $profile->syncTeachingCategories($fallbackCategories->pluck('id')->all());

            return $fallbackCategories->loadMissing('parent:id,name,status');
        }

        return new Collection;
    }
}

