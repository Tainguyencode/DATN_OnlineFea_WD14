@php
    $isEdit = isset($course) && $course?->exists;
    $selectedCategory = old('category_id', $course->category_id ?? '');
    $selectedLevel = old('level', $course->level ?? 'beginner');
    $selectedLanguage = old('language', $course->language ?? 'vi');
    $discountPrice = old('discount_price', $course->discount_price ?? $course->sale_price ?? null);
@endphp

@if ($errors->any())
    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
        <p class="font-bold">Vui lòng kiểm tra lại thông tin khóa học.</p>
        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="border-b border-slate-100 pb-5">
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Course landing</p>
                <h2 class="mt-1 text-lg font-bold text-slate-950">Thông tin khóa học</h2>
                <p class="mt-1 text-sm leading-6 text-slate-500">Những thông tin này sẽ xuất hiện trên trang giới thiệu khóa học sau khi được duyệt.</p>
            </div>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="title" class="mb-1.5 block text-sm font-bold text-slate-700">Tên khóa học <span class="text-rose-500">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title', $course->title ?? '') }}" required maxlength="255"
                           placeholder="Ví dụ: Laravel từ Zero đến Hero"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
                    @error('title') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="short_description" class="mb-1.5 block text-sm font-bold text-slate-700">Mô tả ngắn</label>
                    <textarea id="short_description" name="short_description" rows="3" maxlength="500"
                              placeholder="Tóm tắt giá trị khóa học trong 1-2 câu."
                              class="w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">{{ old('short_description', $course->short_description ?? '') }}</textarea>
                    @error('short_description') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="mb-1.5 block text-sm font-bold text-slate-700">Mô tả chi tiết</label>
                    <textarea id="description" name="description" rows="7"
                              placeholder="Nội dung khóa học, đối tượng phù hợp, kết quả sau khi hoàn thành..."
                              class="w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">{{ old('description', $course->description ?? '') }}</textarea>
                    @error('description') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="objectives" class="mb-1.5 block text-sm font-bold text-slate-700">Mục tiêu khóa học</label>
                    <textarea id="objectives" name="objectives" rows="4"
                              placeholder="Học viên sẽ đạt được những kỹ năng/kiến thức gì sau khóa học..."
                              class="w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">{{ old('objectives', $course->objectives ?? '') }}</textarea>
                    @error('objectives') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="category_id" class="mb-1.5 block text-sm font-bold text-slate-700">Danh mục</label>
                        <select id="category_id" name="category_id"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 cursor-pointer">
                            <option value="">Chưa chọn danh mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $selectedCategory === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="level" class="mb-1.5 block text-sm font-bold text-slate-700">Trình độ</label>
                        <select id="level" name="level"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 cursor-pointer">
                            <option value="beginner" @selected($selectedLevel === 'beginner')>Beginner</option>
                            <option value="intermediate" @selected($selectedLevel === 'intermediate')>Intermediate</option>
                            <option value="advanced" @selected($selectedLevel === 'advanced')>Advanced</option>
                        </select>
                        @error('level') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-950">Hình ảnh & video</h2>
                <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                    <div class="aspect-video">
                        @if($isEdit && $course->thumbnail)
                            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-emerald-700 text-sm font-bold text-white">Fea LMS</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <label for="thumbnail" class="mb-1.5 block text-sm font-bold text-slate-700">Ảnh thumbnail</label>
                    <input id="thumbnail" type="file" name="thumbnail" accept="image/*"
                           class="block w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-900 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-slate-800">
                    <p class="mt-1 text-xs text-slate-500">PNG/JPG/WebP, tối đa 2MB.</p>
                    @error('thumbnail') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4">
                    <label for="preview_video" class="mb-1.5 block text-sm font-bold text-slate-700">Video giới thiệu</label>
                    <input id="preview_video" type="text" name="preview_video" value="{{ old('preview_video', $course->preview_video ?? '') }}"
                           placeholder="https://..."
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
                    @error('preview_video') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-950">Giá bán</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="price" class="mb-1.5 block text-sm font-bold text-slate-700">Giá gốc <span class="text-rose-500">*</span></label>
                        <input id="price" type="number" name="price" value="{{ old('price', $course->price ?? 0) }}" min="0" step="1000" required
                               class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
                        @error('price') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="discount_price" class="mb-1.5 block text-sm font-bold text-slate-700">Giá khuyến mãi</label>
                        <input id="discount_price" type="number" name="discount_price" value="{{ $discountPrice }}" min="0" step="1000"
                               class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
                        @error('discount_price') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="language" class="mb-1.5 block text-sm font-bold text-slate-700">Ngôn ngữ</label>
                        <select id="language" name="language"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 cursor-pointer">
                            <option value="vi" @selected($selectedLanguage === 'vi')>Tiếng Việt</option>
                            <option value="en" @selected($selectedLanguage === 'en')>English</option>
                        </select>
                        @error('language') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        </aside>
    </div>

    <div class="sticky bottom-4 z-10 rounded-lg border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Khóa học sẽ được lưu ở trạng thái nháp cho đến khi bạn gửi duyệt.</p>
            <div class="flex gap-2">
                <a href="{{ route('instructor.courses.index') }}"
                   class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 cursor-pointer">
                    Hủy
                </a>
                <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 cursor-pointer">
                    {{ $submitLabel }}
                </button>
            </div>
        </div>
    </div>
</form>
