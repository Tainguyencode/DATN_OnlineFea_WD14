<x-admin-layout title="Chỉnh sửa Lộ trình học tập" page-title="Chỉnh sửa Lộ trình học tập" breadcrumb="Cập nhật thông tin và sắp xếp danh sách bài học">
    <div class="w-full">

        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.learning-paths.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                ← Quay lại danh sách
            </a>
            <a href="{{ route('learning-paths.show', $learningPath->slug) }}" target="_blank" class="ui-button-secondary text-xs py-2">
                Xem trang hiển thị ↗
            </a>
        </div>

        <form method="POST" action="{{ route('admin.learning-paths.update', $learningPath) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Thông tin lộ trình & Định hướng nghề nghiệp</h3>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tên lộ trình học tập *</label>
                    <input type="text" name="title" value="{{ old('title', $learningPath->title) }}" required class="ui-input">
                    @error('title')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Cấp độ phù hợp *</label>
                        <select name="level" required class="ui-select">
                            <option value="beginner" @selected(old('level', $learningPath->level) === 'beginner')>🌱 Cơ bản (Beginner)</option>
                            <option value="intermediate" @selected(old('level', $learningPath->level) === 'intermediate')>⚡ Trung cấp (Intermediate)</option>
                            <option value="advanced" @selected(old('level', $learningPath->level) === 'advanced')>🚀 Nâng cao (Advanced)</option>
                        </select>
                        @error('level')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Vị trí việc làm mục tiêu</label>
                        <input type="text" name="target_role" value="{{ old('target_role', $learningPath->target_role) }}" placeholder="Ví dụ: Fullstack Web Developer" class="ui-input">
                    </div>
                </div>


                @php
                    $skillsString = is_array($learningPath->skills) ? implode(', ', $learningPath->skills) : '';
                @endphp

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Thời lượng ước tính</label>
                        <input type="text" name="estimated_duration" value="{{ old('estimated_duration', $learningPath->estimated_duration) }}" placeholder="Ví dụ: 6 - 8 tháng (180h học)" class="ui-input">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Danh sách kỹ năng (Phân cách bởi dấu phẩy ,)</label>
                        <input type="text" name="skills_input" value="{{ old('skills_input', $skillsString) }}" placeholder="HTML5, CSS3, JavaScript, Vue.js, Laravel, Docker" class="ui-input">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mô tả ngắn lộ trình</label>
                    <textarea name="description" rows="3" class="ui-input">{{ old('description', $learningPath->description) }}</textarea>
                </div>
            </div>

            {{-- Course Selection and Stage Ordering Section --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Giai đoạn & Môn học thuộc lộ trình</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tích chọn môn học, nhập Tên Giai đoạn (stage) và Số thứ tự bước (1, 2, 3...) để xây dựng cây lộ trình chuẩn.</p>
                </div>

                @php
                    $selectedCourseIds = $learningPath->courses->pluck('id')->toArray();
                    $coursePivotOrders = $learningPath->courses->pluck('pivot.sort_order', 'id')->toArray();
                    $coursePivotStages = $learningPath->courses->pluck('pivot.stage_name', 'id')->toArray();
                @endphp

                <div class="divide-y divide-slate-100 dark:divide-slate-800 border rounded-2xl border-slate-200 dark:border-slate-800 overflow-hidden">
                    @forelse($courses as $course)
                        @php
                            $isChecked = in_array($course->id, old('courses', $selectedCourseIds));
                            $orderValue = old("sort_orders.{$course->id}", $coursePivotOrders[$course->id] ?? $loop->iteration);
                            $stageValue = old("stage_names.{$course->id}", $coursePivotStages[$course->id] ?? 'Giai đoạn 1: Nền tảng');
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 gap-3 bg-slate-50/50 hover:bg-slate-50 dark:bg-slate-900/50 dark:hover:bg-slate-800/50">
                            <label class="flex items-center gap-3 cursor-pointer min-w-0 flex-1">
                                <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                    @checked($isChecked)
                                    class="h-5 w-5 rounded border-slate-300 text-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800">
                                <span class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $course->title }}</span>
                            </label>
                            <div class="flex flex-wrap items-center gap-2 shrink-0 sm:ml-4">
                                <input type="text" name="stage_names[{{ $course->id }}]" value="{{ $stageValue }}" placeholder="Tên giai đoạn" class="h-9 w-48 rounded-xl border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-slate-500 font-semibold">Bước:</span>
                                    <input type="number" name="sort_orders[{{ $course->id }}]" value="{{ $orderValue }}" min="1" class="h-9 w-16 rounded-xl border border-slate-300 bg-white px-2 text-center text-sm font-bold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400">
                            Chưa có khóa học nào đã xuất bản trên hệ thống.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.learning-paths.index') }}" class="ui-button-secondary">Hủy bỏ</a>
                <button type="submit" class="ui-button-primary px-8">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</x-admin-layout>
