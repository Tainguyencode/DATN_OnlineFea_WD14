<x-instructor-layout title="Chỉnh sửa Lộ trình học tập - FEA Instructor">
    <div class="w-full space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('instructor.learning-paths.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400">
                    ➔ Quay lại danh sách
                </a>
                <h1 class="mt-1 text-2xl font-black text-slate-900 dark:text-white">Chỉnh Sửa Lộ Trình Học Tập</h1>
            </div>
            <a href="{{ route('learning-paths.show', $learningPath->slug) }}" target="_blank" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                Xem giao diện công khai ➔
            </a>
        </div>

        <form method="POST" action="{{ route('instructor.learning-paths.update', $learningPath) }}" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#161615] space-y-5">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white border-b border-slate-100 pb-3 dark:border-slate-800">
                    Thông tin cơ bản Lộ trình
                </h3>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Tên Lộ trình học tập *
                        </label>
                        <input type="text" name="title" value="{{ old('title', $learningPath->title) }}"
                            class="h-11 w-full rounded-xl border @error('title') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror bg-slate-50 px-4 text-xs text-slate-900 focus:border-indigo-500 outline-none dark:bg-slate-900 dark:text-white">
                        @error('title')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Cấp độ *
                        </label>
                        <select name="level" class="h-11 w-full rounded-xl border @error('level') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror bg-slate-50 px-4 text-xs text-slate-900 focus:border-indigo-500 outline-none dark:bg-slate-900 dark:text-white">
                            <option value="">-- Chọn cấp độ --</option>
                            <option value="beginner" {{ old('level', $learningPath->level) === 'beginner' ? 'selected' : '' }}>Cơ bản (Beginner)</option>
                            <option value="intermediate" {{ old('level', $learningPath->level) === 'intermediate' ? 'selected' : '' }}>Trung cấp (Intermediate)</option>
                            <option value="advanced" {{ old('level', $learningPath->level) === 'advanced' ? 'selected' : '' }}>Nâng cao (Advanced)</option>
                        </select>
                        @error('level')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Vị trí việc làm mục tiêu
                        </label>
                        <input type="text" name="target_role" value="{{ old('target_role', $learningPath->target_role) }}" placeholder="Ví dụ: Fullstack Developer / UI UX Designer"
                            class="h-11 w-full rounded-xl border @error('target_role') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror bg-slate-50 px-4 text-xs text-slate-900 focus:border-indigo-500 outline-none dark:bg-slate-900 dark:text-white">
                        @error('target_role')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Thời lượng ước tính
                        </label>
                        <input type="text" name="estimated_duration" value="{{ old('estimated_duration', $learningPath->estimated_duration) }}" placeholder="Ví dụ: 6 - 8 tháng (180h học)"
                            class="h-11 w-full rounded-xl border @error('estimated_duration') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror bg-slate-50 px-4 text-xs text-slate-900 focus:border-indigo-500 outline-none dark:bg-slate-900 dark:text-white">
                        @error('estimated_duration')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $skillsStr = is_array($learningPath->skills) ? implode(', ', $learningPath->skills) : ($learningPath->skills ?? '');
                    @endphp
                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Bộ kỹ năng đạt được (nhập phân cách bằng dấu phẩy)
                        </label>
                        <input type="text" name="skills_input" value="{{ old('skills_input', $skillsStr) }}" placeholder="HTML5, CSS3, JavaScript, Vue.js, Laravel, RESTful API"
                            class="h-11 w-full rounded-xl border @error('skills_input') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror bg-slate-50 px-4 text-xs text-slate-900 focus:border-indigo-500 outline-none dark:bg-slate-900 dark:text-white">
                        @error('skills_input')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Mô tả chi tiết Lộ trình
                        </label>
                        <textarea name="description" rows="5" placeholder="Mô tả mục tiêu, giá trị đầu ra và lý do học viên nên lựa chọn lộ trình này..."
                            class="w-full rounded-xl border @error('description') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror bg-slate-50 p-4 text-xs text-slate-900 focus:border-indigo-500 outline-none dark:bg-slate-900 dark:text-white">{{ old('description', $learningPath->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Giai đoạn & Chọn khóa học của Giảng viên --}}
            @php
                $attachedCourses = $learningPath->courses->keyBy('id');
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#161615] space-y-4">
                <h3 class="text-base font-extrabold text-slate-950 dark:text-white border-b border-slate-100 pb-3 dark:border-slate-800">
                    Phân bổ Giai đoạn & Môn học của bạn
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tích chọn khóa học và điều chỉnh tên Giai đoạn cũng như thứ tự sắp xếp.</p>

                @if($courses->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-xs text-slate-500 dark:border-slate-800">
                        Bạn chưa có khóa học nào trên hệ thống.
                    </div>
                @else
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        @foreach($courses as $course)
                            @php
                                $pivot = $attachedCourses->get($course->id)?->pivot;
                                $isChecked = $attachedCourses->has($course->id);
                                $defaultStage = $pivot?->stage_name ?? 'Giai đoạn 1: Nền tảng';
                                $defaultSort = $pivot?->sort_order ?? $loop->iteration;
                            @endphp
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-900/40">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="font-extrabold text-xs text-slate-900 dark:text-white">{{ $course->title }}</span>
                                        <span class="ml-2 text-[10px] font-bold px-2 py-0.5 rounded {{ $course->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $course->status === 'published' ? 'Published' : $course->status }}
                                        </span>
                                    </div>
                                </label>

                                <div class="flex items-center gap-2">
                                    <input type="text" name="stage_names[{{ $course->id }}]" value="{{ old("stage_names.{$course->id}", $defaultStage) }}" placeholder="Tên giai đoạn"
                                        class="h-9 w-52 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-900 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                    <input type="number" name="sort_orders[{{ $course->id }}]" value="{{ old("sort_orders.{$course->id}", $defaultSort) }}" min="1" placeholder="Thứ tự"
                                        class="h-9 w-16 rounded-lg border border-slate-200 bg-white text-center text-xs text-slate-900 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('instructor.learning-paths.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-300">
                    Hủy bỏ
                </a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-bold text-white shadow-lg transition hover:bg-indigo-700">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</x-instructor-layout>
