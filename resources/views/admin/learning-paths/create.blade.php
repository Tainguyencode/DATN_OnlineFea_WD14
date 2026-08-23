<x-admin-layout title="Tạo Lộ trình học tập mới" page-title="Thêm Lộ trình học tập mới" breadcrumb="Tạo lộ trình đào tạo bài bản cho học viên">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.learning-paths.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                ← Quay lại danh sách
            </a>
        </div>

        <form method="POST" action="{{ route('admin.learning-paths.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Thông tin lộ trình & Định hướng nghề nghiệp</h3>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tên lộ trình học tập *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ví dụ: Lộ trình trở thành Web Fullstack Developer" class="ui-input">
                    @error('title')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Cấp độ phù hợp *</label>
                        <select name="level" required class="ui-select">
                            <option value="beginner" @selected(old('level') === 'beginner')>🌱 Cơ bản (Beginner)</option>
                            <option value="intermediate" @selected(old('level', 'intermediate') === 'intermediate')>⚡ Trung cấp (Intermediate)</option>
                            <option value="advanced" @selected(old('level') === 'advanced')>🚀 Nâng cao (Advanced)</option>
                        </select>
                        @error('level')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Vị trí việc làm mục tiêu</label>
                        <input type="text" name="target_role" value="{{ old('target_role') }}" placeholder="Ví dụ: Fullstack Web Developer" class="ui-input">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mức lương tham khảo</label>
                        <input type="text" name="salary_range" value="{{ old('salary_range') }}" placeholder="Ví dụ: 15 - 35 triệu/tháng" class="ui-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Thời lượng ước tính</label>
                        <input type="text" name="estimated_duration" value="{{ old('estimated_duration') }}" placeholder="Ví dụ: 6 - 8 tháng (180h học)" class="ui-input">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Danh sách kỹ năng (Phân cách bởi dấu phẩy ,)</label>
                        <input type="text" name="skills_input" value="{{ old('skills_input') }}" placeholder="HTML5, CSS3, JavaScript, Vue.js, Laravel, Docker" class="ui-input">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mô tả ngắn lộ trình</label>
                    <textarea name="description" rows="3" placeholder="Mô tả mục tiêu đầu ra và đối tượng phù hợp của lộ trình học tập này..." class="ui-input">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Course Selection and Stage Ordering Section --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Giai đoạn & Môn học thuộc lộ trình</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tích chọn môn học, nhập Tên Giai đoạn (stage) và Số thứ tự bước (1, 2, 3...) để xây dựng cây lộ trình chuẩn.</p>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 border rounded-2xl border-slate-200 dark:border-slate-800 overflow-hidden">
                    @forelse($courses as $course)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 gap-3 bg-slate-50/50 hover:bg-slate-50 dark:bg-slate-900/50 dark:hover:bg-slate-800/50">
                            <label class="flex items-center gap-3 cursor-pointer min-w-0 flex-1">
                                <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                    @checked(is_array(old('courses')) && in_array($course->id, old('courses')))`
                                    class="h-5 w-5 rounded border-slate-300 text-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800">
                                <span class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $course->title }}</span>
                            </label>
                            <div class="flex flex-wrap items-center gap-2 shrink-0 sm:ml-4">
                                <input type="text" name="stage_names[{{ $course->id }}]" value="{{ old("stage_names.{$course->id}", 'Giai đoạn 1: Nền tảng') }}" placeholder="Tên giai đoạn" class="h-9 w-48 rounded-xl border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-slate-500 font-semibold">Bước:</span>
                                    <input type="number" name="sort_orders[{{ $course->id }}]" value="{{ old("sort_orders.{$course->id}", $loop->iteration) }}" min="1" class="h-9 w-16 rounded-xl border border-slate-300 bg-white px-2 text-center text-sm font-bold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
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
                <button type="submit" class="ui-button-primary px-8">Tạo lộ trình</button>
            </div>
        </form>
    </div>
</x-admin-layout>
