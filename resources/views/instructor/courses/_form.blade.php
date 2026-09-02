@php
    $isEdit = isset($course) && $course?->exists;
    $selectedCategory = old('category_id', $course->category_id ?? '');
    $currentCategory = $course?->category ?? null;
    $selectedCategoryIsVisible = $categories
        ->contains(fn ($cat) => (string) $cat->id === (string) $selectedCategory);
    $hasOneTeachingCategory = $categories->count() === 1;
    $selectedLevel = old('level', $course->level ?? 'beginner');
    $selectedLanguage = old('language', $course->language ?? 'vi');
    $discountPrice = old('discount_price', $course->discount_price ?? $course->sale_price ?? null);
    $wideLayout = $wideLayout ?? false;
    $editLayout = $editLayout ?? false;
    $showActionBar = $showActionBar ?? true;
    $formId = $formId ?? null;
    $formReadOnly = $formReadOnly ?? false;
    $existingThumbnailUrl = $isEdit && $course->thumbnail
        ? asset('storage/' . $course->thumbnail)
        : '';
    $existingThumbnailAlt = $isEdit ? ($course->title ?? 'Ảnh thumbnail khóa học') : 'Ảnh thumbnail khóa học';
    $previewVideoValue = old('preview_video', $course->preview_video ?? '');
    $existingPreviewVideoUrl = $isEdit && filled($previewVideoValue)
        ? $course->previewVideoUrl((string) $previewVideoValue)
        : '';
    $canUploadCoursePreview = $isEdit && ! $formReadOnly;
    $coursePreviewMaxBytes = max(1, (int) config('video.course_preview.max_bytes'));
    $coursePreviewMaxDurationSeconds = max(1, (int) config('video.course_preview.max_duration_seconds'));
@endphp

{{-- @if ($errors->any())
    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
        <p class="font-bold">Vui lòng kiểm tra lại thông tin khóa học.</p>
        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif --}}

<form @if($formId) id="{{ $formId }}" @endif data-read-only="{{ $formReadOnly ? 'true' : 'false' }}" method="POST" action="{{ $action }}" enctype="multipart/form-data" class="w-full min-w-0 space-y-4 {{ $wideLayout ? 'lg:flex lg:min-h-[calc(100vh-9rem)] lg:flex-col' : '' }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    @if($formReadOnly)
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm font-semibold text-blue-900">
            Phiên bản này đang chờ Admin duyệt. Thông tin đề xuất được hiển thị ở chế độ chỉ đọc.
        </div>
    @endif

    <div class="grid min-w-0 items-stretch gap-4 lg:flex-1 {{ $wideLayout ? 'lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.95fr)] xl:grid-cols-[minmax(0,1.35fr)_minmax(300px,0.95fr)_minmax(320px,1.05fr)]' : ($editLayout ? 'min-[992px]:grid-cols-[minmax(0,1.7fr)_minmax(330px,1fr)]' : 'lg:grid-cols-[minmax(0,1fr)_320px]') }}">
        <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm lg:h-full lg:p-5 {{ $wideLayout ? 'lg:row-span-2 xl:row-span-1' : ($editLayout ? 'min-[992px]:row-span-2' : 'lg:row-span-2') }}">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-950">Thông tin khóa học</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">Cung cấp thông tin cơ bản về khóa học của bạn.</p>
            </div>

            <div class="mt-4 space-y-3.5">
                <div>
                    <label for="title" class="mb-1 block text-sm font-semibold text-slate-700">Tên khóa học <span class="text-rose-500">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title', $course->title ?? '') }}" maxlength="255"
                           placeholder="Ví dụ: Laravel từ Zero đến Hero"
                           aria-describedby="title-count @error('title') title-error @enderror"
                           class="h-10 w-full rounded-lg border bg-white px-3 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('title') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">
                    <div class="mt-1 flex min-h-4 items-start justify-between gap-3">
                        @error('title') <p id="title-error" class="text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        <span id="title-count" class="ml-auto shrink-0 text-[11px] text-slate-400">0 / 255</span>
                    </div>
                </div>

                <div>
                    <label for="short_description" class="mb-1 block text-sm font-semibold text-slate-700">Mô tả ngắn</label>
                    <textarea id="short_description" name="short_description" rows="2" maxlength="500"
                              placeholder="Tóm tắt giá trị khóa học trong 1-2 câu."
                              class="{{ $editLayout ? 'h-[60px] min-h-[60px]' : 'h-[72px] min-h-[72px]' }} w-full resize-y rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('short_description') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">{{ old('short_description', $course->short_description ?? '') }}</textarea>
                    @error('short_description') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-semibold text-slate-700">Mô tả chi tiết</label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Nội dung khóa học, đối tượng phù hợp, kết quả sau khi hoàn thành..."
                              class="{{ $editLayout ? 'h-[72px] min-h-[72px]' : 'h-[96px] min-h-[96px]' }} w-full resize-y rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('description') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">{{ old('description', $course->description ?? '') }}</textarea>
                    @error('description') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="objectives" class="mb-1 block text-sm font-semibold text-slate-700">Mục tiêu khóa học</label>
                    <textarea id="objectives" name="objectives" rows="3"
                              placeholder="Học viên sẽ đạt được những kỹ năng/kiến thức gì sau khóa học..."
                              class="{{ $editLayout ? 'h-[72px] min-h-[72px]' : 'h-[88px] min-h-[88px]' }} w-full resize-y rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('objectives') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">{{ old('objectives', $course->objectives ?? '') }}</textarea>
                    @error('objectives') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <div class="min-w-0 space-y-4 {{ $editLayout ? 'min-[992px]:flex min-[992px]:h-full min-[992px]:flex-col min-[992px]:gap-4 min-[992px]:space-y-0' : 'lg:flex lg:h-full lg:flex-col lg:gap-4 lg:space-y-0' }} {{ $wideLayout ? 'lg:col-start-2 lg:row-start-2 xl:row-start-1' : '' }}">
            <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm lg:flex-[1.1] lg:p-5">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-950">Phân loại & thiết lập</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Giúp học viên tìm thấy khóa học phù hợp.</p>
                </div>

                <div class="mt-3 space-y-3 {{ $editLayout ? 'md:grid md:grid-cols-2 md:gap-x-3 md:gap-y-3 md:space-y-0' : '' }}">
                    <div>
                        <label for="category_id" class="mb-1 block text-sm font-semibold text-slate-700">Danh mục <span class="text-rose-500">*</span></label>
                        <select id="category_id" name="category_id"
                                class="h-10 w-full cursor-pointer rounded-lg border bg-white px-3 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('category_id') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">
                            @unless($hasOneTeachingCategory)<option value="">Chọn danh mục khóa học</option>@endunless
                            @if($selectedCategory && $currentCategory && ! $selectedCategoryIsVisible)
                                <option value="{{ $currentCategory->id }}" selected disabled>
                                    {{ $currentCategory->full_name }} (không còn khả dụng)
                                </option>
                            @endif
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) ($selectedCategory ?: ($hasOneTeachingCategory ? $cat->id : '')) === (string) $cat->id)>
                                    {{ $cat->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="level" class="mb-1 block text-sm font-semibold text-slate-700">Trình độ</label>
                        <select id="level" name="level"
                                class="h-10 w-full cursor-pointer rounded-lg border bg-white px-3 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('level') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">
                            <option value="beginner" @selected($selectedLevel === 'beginner')>Beginner</option>
                            <option value="intermediate" @selected($selectedLevel === 'intermediate')>Intermediate</option>
                            <option value="advanced" @selected($selectedLevel === 'advanced')>Advanced</option>
                        </select>
                        @error('level') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="language" class="mb-1 block text-sm font-semibold text-slate-700">Ngôn ngữ</label>
                        <select id="language" name="language"
                                class="h-10 w-full cursor-pointer rounded-lg border bg-white px-3 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('language') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">
                            <option value="vi" @selected($selectedLanguage === 'vi')>Tiếng Việt</option>
                            <option value="en" @selected($selectedLanguage === 'en')>English</option>
                        </select>
                        @error('language') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm lg:flex-[0.9] lg:p-5">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-950">Giá bán</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Thiết lập giá cho khóa học của bạn.</p>
                </div>

                <div class="mt-3 space-y-3">
                    <div>
                        <label for="price" class="mb-1 block text-sm font-semibold text-slate-700">Giá gốc (VNĐ) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="price" type="number" name="price" value="{{ old('price', $course->price ?? 0) }}" inputmode="numeric"
                                   oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9); formatPricePreview('price', 'price-preview-txt');"
                                   class="h-10 w-full rounded-lg border bg-white pl-3 pr-11 text-right text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('price') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex w-9 items-center justify-center border-l border-slate-200 text-sm font-semibold text-slate-500">₫</span>
                        </div>
                        <div class="mt-1 flex min-h-4 items-center justify-between gap-2 text-[11px]">
                            <span id="price-preview-txt" class="font-semibold text-emerald-600"></span>
                            <span class="ml-auto text-right text-slate-400">Tối đa 100.000.000 VNĐ</span>
                        </div>
                        @error('price') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="discount_price" class="mb-1 block text-sm font-semibold text-slate-700">Giá khuyến mãi</label>
                        <div class="relative">
                            <input id="discount_price" type="number" name="discount_price" value="{{ $discountPrice }}" inputmode="numeric"
                                   placeholder="Để trống nếu không giảm giá"
                                   oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9); formatPricePreview('discount_price', 'discount-preview-txt');"
                                   class="h-10 w-full rounded-lg border bg-white pl-3 pr-11 text-right text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-xs placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 @error('discount_price') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 @enderror">
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex w-9 items-center justify-center border-l border-slate-200 text-sm font-semibold text-slate-500">₫</span>
                        </div>
                        <div class="mt-1 flex min-h-4 items-center justify-between gap-2 text-[11px]">
                            <span id="discount-preview-txt" class="font-semibold text-emerald-600"></span>
                            <span class="ml-auto text-right text-slate-400">Không vượt quá giá gốc</span>
                        </div>
                        @error('discount_price') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        </div>

        <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm lg:h-full lg:p-5 {{ $wideLayout ? 'lg:col-start-2 lg:row-start-1 xl:col-start-3 xl:row-start-1' : ($editLayout ? 'min-[992px]:col-start-2' : 'lg:col-start-2') }}">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-950">Hình ảnh & video</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">Tải lên ảnh đại diện và thêm video giới thiệu.</p>
            </div>

            <!-- BLOCK 1: ẢNH THUMBNAIL -->
            <div class="mt-4 border-b border-slate-100 pb-4">
                <span class="mb-2 block text-sm font-semibold text-slate-700">Ảnh thumbnail</span>
                
                <div id="course-thumbnail-preview"
                     data-existing-image-url="{{ $existingThumbnailUrl }}"
                     data-existing-image-alt="{{ $existingThumbnailAlt }}"
                     class="aspect-video w-full max-h-[170px] overflow-hidden rounded-lg border border-slate-200 bg-slate-100"
                     aria-label="Xem trước ảnh thumbnail khóa học">
                    <div class="h-full w-full">
                        @if($existingThumbnailUrl)
                            <img src="{{ $existingThumbnailUrl }}" alt="{{ $existingThumbnailAlt }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-emerald-700 text-sm font-bold text-white">Fea LMS</div>
                        @endif
                    </div>
                </div>

                <div class="mt-2.5">
                    <input id="thumbnail" type="file" name="thumbnail" accept="image/*" class="peer sr-only" aria-describedby="thumbnail-help thumbnail-file-name" @disabled($formReadOnly)>
                    <label for="thumbnail"
                           class="flex h-9 w-full cursor-pointer items-center justify-center gap-2 rounded-lg border bg-white px-3 text-xs font-semibold text-slate-700 transition-colors duration-200 hover:border-emerald-400 hover:bg-emerald-50 peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500/20 {{ $formReadOnly ? 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-400' : 'border-slate-300' }} @error('thumbnail') border-rose-500 @enderror">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.5v2.25A1.25 1.25 0 0 0 5.25 20h13.5A1.25 1.25 0 0 0 20 18.75V16.5M12 4v11m0-11L8 8m4-4 4 4"/>
                        </svg>
                        <span id="thumbnail-btn-label">{{ $existingThumbnailUrl ? 'Thay ảnh' : 'Tải ảnh lên' }}</span>
                    </label>
                    <div class="mt-1 flex items-start justify-between gap-2 text-[11px]">
                        <p id="thumbnail-help" class="leading-4 text-slate-500">PNG, JPG, WebP. Tối đa 2MB. Khuyến nghị 16:9.</p>
                        <span id="thumbnail-file-name" class="max-w-28 shrink-0 truncate text-right text-slate-400">Chưa chọn tệp</span>
                    </div>
                    @error('thumbnail') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- BLOCK 2: VIDEO XEM THỬ -->
            <div class="mt-4">
                <label for="preview_video_file" class="mb-2 block text-sm font-semibold text-slate-700">Video xem thử</label>
                <input id="preview_video" type="hidden" name="preview_video" value="{{ $previewVideoValue }}">

                <!-- Player nhỏ cho video xem thử (nếu có) -->
                <div id="course-video-preview-wrapper"
                     data-existing-preview-video-url="{{ $existingPreviewVideoUrl }}"
                     class="{{ filled($existingPreviewVideoUrl) ? '' : 'hidden' }} mb-3">
                    <div id="course-video-preview-player" class="aspect-video w-full max-h-[170px] overflow-hidden rounded-lg border border-slate-200 bg-black">
                        @if(filled($existingPreviewVideoUrl))
                            <video src="{{ $existingPreviewVideoUrl }}" controls playsinline preload="metadata" class="h-full w-full object-contain"></video>
                        @endif
                    </div>
                </div>

                @if($isEdit)
                    <div id="course-preview-upload"
                         data-create-url="{{ route('instructor.courses.s3.multipart.create', $course) }}"
                         data-sign-url="{{ route('instructor.courses.s3.multipart.sign-part', $course) }}"
                         data-complete-url="{{ route('instructor.courses.s3.multipart.complete', $course) }}"
                         data-abort-url="{{ route('instructor.courses.s3.multipart.abort', $course) }}"
                         data-course-id="{{ $course->id }}"
                         data-max-bytes="{{ $coursePreviewMaxBytes }}"
                         data-max-duration-seconds="{{ $coursePreviewMaxDurationSeconds }}">
                        <input id="preview_video_file" type="file" accept="video/mp4" class="peer sr-only" @disabled(! $canUploadCoursePreview)>
                        <div class="flex items-center gap-2">
                            <label for="preview_video_file"
                                   class="flex h-9 flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg border bg-white px-3 text-xs font-semibold text-slate-700 transition-colors duration-200 hover:border-emerald-400 hover:bg-emerald-50 peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500/20 {{ $canUploadCoursePreview ? 'border-slate-300' : 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-400' }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.5v2.25A1.25 1.25 0 0 0 5.25 20h13.5A1.25 1.25 0 0 0 20 18.75V16.5M12 4v11m0-11L8 8m4-4 4 4"/></svg>
                                <span id="preview-video-btn-label">{{ filled($existingPreviewVideoUrl) ? 'Thay video' : 'Tải video preview lên' }}</span>
                            </label>
                            <button id="remove_preview_video" type="button" class="{{ filled($previewVideoValue) ? '' : 'hidden' }} inline-flex h-9 shrink-0 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-3 text-xs font-semibold text-rose-600 hover:bg-rose-100 hover:text-rose-700 disabled:cursor-not-allowed disabled:text-slate-400" @disabled(! $canUploadCoursePreview)>Xóa video</button>
                        </div>
                        <div class="mt-1 flex items-start justify-between gap-2 text-[11px] leading-4">
                            <p id="preview-video-help" class="text-slate-500">MP4, tối đa {{ number_format($coursePreviewMaxBytes / 1048576) }}MB, không quá {{ (int) floor($coursePreviewMaxDurationSeconds / 60) }} phút.</p>
                        </div>
                        <p id="preview-video-upload-status" class="mt-1 hidden text-xs font-semibold" aria-live="polite"></p>
                    </div>
                @else
                    <p id="preview-video-help" class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-3 text-[11px] leading-4 text-slate-600">Hãy lưu nháp khóa học trước, sau đó bạn có thể tải video giới thiệu MP4 lên S3.</p>
                @endif
                @error('preview_video') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>
        </section>
    </div>

    @if($showActionBar)
    <div class="sticky bottom-16 z-20 mt-4 w-full rounded-lg border border-slate-200 bg-white/95 p-2.5 shadow-lg backdrop-blur lg:bottom-2 lg:mt-auto">
        <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs leading-5 text-slate-500">Khóa học được lưu ở trạng thái nháp cho đến khi bạn gửi duyệt.</p>
            <div class="grid grid-cols-2 gap-2 sm:flex">
                <a href="{{ route('instructor.courses.index') }}"
                   class="inline-flex min-h-10 cursor-pointer items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                    Hủy
                </a>
                <button type="submit"
                        class="inline-flex min-h-10 cursor-pointer items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    {{ $submitLabel }}
                </button>
            </div>
        </div>
    </div>
    @endif
</form>

<script>
    (() => {
        const thumbnailPreview = document.getElementById('course-thumbnail-preview');
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailFileName = document.getElementById('thumbnail-file-name');
        const thumbnailBtnLabel = document.getElementById('thumbnail-btn-label');
        const videoInput = document.getElementById('preview_video');
        const videoFileInput = document.getElementById('preview_video_file');
        const videoWrapper = document.getElementById('course-video-preview-wrapper');
        const videoPlayer = document.getElementById('course-video-preview-player');
        const videoBtnLabel = document.getElementById('preview-video-btn-label');
        const previewUpload = document.getElementById('course-preview-upload');
        const previewUploadStatus = document.getElementById('preview-video-upload-status');
        const removePreviewButton = document.getElementById('remove_preview_video');

        if (!thumbnailInput || !videoInput) return;

        const state = {
            imageObjectUrl: null,
            videoError: false,
            videoObjectUrl: null,
        };

        const existingImageUrl = thumbnailPreview?.dataset?.existingImageUrl || '';
        const existingImageAlt = thumbnailPreview?.dataset?.existingImageAlt || 'Ảnh thumbnail khóa học';
        const existingPreviewVideoUrl = videoWrapper?.dataset?.existingPreviewVideoUrl || '';

        function clearImageObjectUrl() {
            if (state.imageObjectUrl) {
                URL.revokeObjectURL(state.imageObjectUrl);
                state.imageObjectUrl = null;
            }
        }

        function clearVideoObjectUrl() {
            if (state.videoObjectUrl) {
                URL.revokeObjectURL(state.videoObjectUrl);
                state.videoObjectUrl = null;
            }
        }

        function getSelectedImageUrl() {
            const file = thumbnailInput.files?.[0];

            if (!file || !file.type.startsWith('image/')) {
                clearImageObjectUrl();
                return null;
            }

            clearImageObjectUrl();
            state.imageObjectUrl = URL.createObjectURL(file);

            return state.imageObjectUrl;
        }

        function updateThumbnailFileName() {
            if (!thumbnailFileName) return;

            const fileName = thumbnailInput.files?.[0]?.name || 'Chưa chọn tệp';
            thumbnailFileName.textContent = fileName;
            thumbnailFileName.title = fileName;
        }

        function getYouTubeEmbedUrl(value) {
            let url;

            try {
                url = new URL(value);
            } catch {
                return null;
            }

            if (!['http:', 'https:'].includes(url.protocol)) return null;

            const hostname = url.hostname.toLowerCase().replace(/^www\./, '');
            let videoId = null;

            if (hostname === 'youtube.com') {
                if (url.pathname === '/watch') {
                    videoId = url.searchParams.get('v');
                } else if (url.pathname.startsWith('/embed/')) {
                    videoId = url.pathname.split('/')[2];
                }
            } else if (hostname === 'youtu.be') {
                videoId = url.pathname.split('/')[1];
            }

            if (!videoId || !/^[a-zA-Z0-9_-]+$/.test(videoId)) return null;

            return `https://www.youtube.com/embed/${encodeURIComponent(videoId)}`;
        }

        function getDirectVideoUrl(value) {
            let url;

            try {
                url = new URL(value);
            } catch {
                return null;
            }

            if (!['http:', 'https:'].includes(url.protocol)) return null;

            const pathname = url.pathname.toLowerCase();

            return /\.(mp4|webm)$/.test(pathname) ? url.href : null;
        }

        function getVideoSource(value) {
            const trimmedValue = (value || '').trim();

            if (state.videoObjectUrl) return { type: 'direct', url: state.videoObjectUrl };

            if (!trimmedValue) return null;

            const youtubeUrl = getYouTubeEmbedUrl(trimmedValue);
            if (youtubeUrl) return { type: 'youtube', url: youtubeUrl };

            const directVideoUrl = getDirectVideoUrl(trimmedValue);
            if (directVideoUrl) return { type: 'direct', url: directVideoUrl };

            return existingPreviewVideoUrl ? { type: 'direct', url: existingPreviewVideoUrl } : null;
        }

        function renderThumbnail() {
            if (!thumbnailPreview) return;
            const imageUrl = state.imageObjectUrl || existingImageUrl;

            if (imageUrl) {
                const image = document.createElement('img');
                image.src = imageUrl;
                image.alt = imageUrl === existingImageUrl ? existingImageAlt : 'Ảnh thumbnail khóa học';
                image.className = 'h-full w-full object-cover';

                const frame = document.createElement('div');
                frame.className = 'h-full w-full';
                frame.appendChild(image);

                thumbnailPreview.replaceChildren(frame);
                if (thumbnailBtnLabel) thumbnailBtnLabel.textContent = 'Thay ảnh';
            } else {
                thumbnailPreview.innerHTML = `
                    <div class="h-full w-full">
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-emerald-700 text-sm font-bold text-white">
                            Fea LMS
                        </div>
                    </div>
                `;
                if (thumbnailBtnLabel) thumbnailBtnLabel.textContent = 'Tải ảnh lên';
            }
        }

        function renderVideoPreview() {
            if (!videoWrapper || !videoPlayer) return;
            const videoSource = getVideoSource(videoInput.value);

            if (videoSource && !state.videoError) {
                videoWrapper.classList.remove('hidden');
                if (removePreviewButton) removePreviewButton.classList.remove('hidden');
                if (videoBtnLabel) videoBtnLabel.textContent = 'Thay video';

                const frame = document.createElement('div');
                frame.className = 'h-full w-full';

                if (videoSource.type === 'youtube') {
                    const iframe = document.createElement('iframe');
                    iframe.src = videoSource.url;
                    iframe.title = 'Video xem thử khóa học';
                    iframe.className = 'h-full w-full border-0 bg-black';
                    iframe.allowFullscreen = true;
                    iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                    frame.appendChild(iframe);
                } else {
                    const video = document.createElement('video');
                    video.src = videoSource.url;
                    video.controls = true;
                    video.playsInline = true;
                    video.preload = 'metadata';
                    video.className = 'h-full w-full object-contain bg-black';
                    video.addEventListener('error', () => {
                        state.videoError = true;
                        renderVideoPreview();
                    }, { once: true });
                    frame.appendChild(video);
                }

                videoPlayer.replaceChildren(frame);
            } else {
                videoPlayer.replaceChildren();
                videoWrapper.classList.add('hidden');
                if (removePreviewButton) removePreviewButton.classList.add('hidden');
                if (videoBtnLabel) videoBtnLabel.textContent = 'Tải video preview lên';
            }
        }

        function notifyPreviewChanged(isUploading = false, changed = true) {
            const form = videoInput.closest('form');
            if (!form) return;

            form.dataset.previewVideoUploading = isUploading ? 'true' : 'false';
            if (!isUploading && changed) {
                form.dataset.previewVideoChanged = 'true';
                form.dispatchEvent(new CustomEvent('course-preview-changed'));
            }
        }

        function setUploadStatus(message, type = 'info') {
            if (!previewUploadStatus) return;
            previewUploadStatus.textContent = message;
            previewUploadStatus.classList.remove('hidden', 'text-slate-600', 'text-emerald-600', 'text-rose-600');
            previewUploadStatus.classList.add(type === 'error' ? 'text-rose-600' : (type === 'success' ? 'text-emerald-600' : 'text-slate-600'));
        }

        thumbnailInput.addEventListener('change', () => {
            getSelectedImageUrl();
            updateThumbnailFileName();
            renderThumbnail();
        });

        if (videoFileInput && previewUpload && typeof S3MultipartUploader === 'function') {
            videoFileInput.addEventListener('change', async () => {
                const file = videoFileInput.files?.[0];
                if (!file) return;

                state.videoError = false;
                notifyPreviewChanged(true);
                setUploadStatus('Đang chuẩn bị tải video giới thiệu lên...');

                const uploader = new S3MultipartUploader({
                    courseId: Number(previewUpload.dataset.courseId),
                    mediaType: 'course_preview',
                    createUrl: previewUpload.dataset.createUrl,
                    signPartUrl: previewUpload.dataset.signUrl,
                    completeUrl: previewUpload.dataset.completeUrl,
                    abortUrl: previewUpload.dataset.abortUrl,
                    maxVideoBytes: Number(previewUpload.dataset.maxBytes),
                    maxDurationSeconds: Number(previewUpload.dataset.maxDurationSeconds),
                    onStatusChange: (_status, message) => setUploadStatus(message),
                    onSuccess: (result) => {
                        videoInput.value = result.key;
                        clearVideoObjectUrl();
                        state.videoObjectUrl = URL.createObjectURL(file);
                        videoFileInput.value = '';
                        setUploadStatus('Đã tải video giới thiệu thành công. Bấm “Lưu thay đổi” để áp dụng.', 'success');
                        renderVideoPreview();
                        notifyPreviewChanged(false);
                    },
                    onError: (error) => {
                        setUploadStatus(error?.message || 'Không thể tải video giới thiệu.', 'error');
                        notifyPreviewChanged(false, false);
                    },
                });

                try {
                    await uploader.upload(file);
                } catch (error) {
                    setUploadStatus(error?.message || 'Không thể tải video giới thiệu.', 'error');
                    notifyPreviewChanged(false, false);
                }
            });
        }

        removePreviewButton?.addEventListener('click', () => {
            videoInput.value = '';
            if (videoFileInput) videoFileInput.value = '';
            clearVideoObjectUrl();
            state.videoError = false;
            setUploadStatus('Video giới thiệu sẽ được gỡ khi bạn lưu thay đổi.', 'info');
            renderVideoPreview();
            notifyPreviewChanged(false);
        });

        window.addEventListener('pagehide', () => {
            clearImageObjectUrl();
            clearVideoObjectUrl();
        }, { once: true });

        updateThumbnailFileName();
        renderThumbnail();
        renderVideoPreview();
    })();

    (() => {
        const titleInput = document.getElementById('title');
        const titleCount = document.getElementById('title-count');

        if (!titleInput || !titleCount) return;

        const updateTitleCount = () => {
            titleCount.textContent = `${titleInput.value.length} / ${titleInput.maxLength}`;
        };

        titleInput.addEventListener('input', updateTitleCount);
        updateTitleCount();
    })();

    function formatPricePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;
        const val = parseFloat(input.value);
        if (isNaN(val) || val <= 0) {
            preview.textContent = val === 0 ? 'Miễn phí (0đ)' : '';
        } else {
            preview.textContent = '=> ' + new Intl.NumberFormat('vi-VN').format(val) + 'đ';
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        formatPricePreview('price', 'price-preview-txt');
        formatPricePreview('discount_price', 'discount-preview-txt');
    });

    @if($formId)
    (() => {
        const form = document.getElementById(@json($formId));
        if (!form) return;

        const readOnly = form.dataset.readOnly === 'true';
        const saveButtons = Array.from(document.querySelectorAll(`[data-course-save][form="${form.id}"]`));
        const controls = () => Array.from(form.elements).filter((control) => {
            if (!control.name || control.disabled) return false;
            if (['_token', '_method'].includes(control.name)) return false;
            return control.type !== 'hidden' && !['submit', 'button', 'reset'].includes(control.type);
        });
        const snapshot = () => JSON.stringify(controls().map((control) => {
            if (['checkbox', 'radio'].includes(control.type)) {
                return [control.name, control.checked, control.checked ? control.value : null];
            }
            if (control.type === 'file') {
                return [control.name, Array.from(control.files || []).map((file) => [file.name, file.size, file.lastModified])];
            }
            if (control instanceof HTMLSelectElement && control.multiple) {
                return [control.name, Array.from(control.selectedOptions).map((option) => option.value)];
            }

            return [control.name, control.value];
        }));

        if (readOnly) {
            Array.from(form.elements).forEach((control) => {
                if (!['_token', '_method'].includes(control.name)) control.disabled = true;
            });
        }

        const initialSnapshot = snapshot();
        const updateDirtyState = () => {
            const dirty = !readOnly && (
                form.dataset.previewVideoChanged === 'true'
                || snapshot() !== initialSnapshot
            );
            form.dataset.dirty = dirty ? 'true' : 'false';
            saveButtons.forEach((button) => {
                button.disabled = !dirty;
            });
        };

        form.addEventListener('input', updateDirtyState);
        form.addEventListener('change', updateDirtyState);
        form.addEventListener('course-preview-changed', updateDirtyState);
        form.addEventListener('submit', (event) => {
            if (form.dataset.previewVideoUploading === 'true') {
                event.preventDefault();
                return;
            }

            if (readOnly || (
                snapshot() === initialSnapshot
                && form.dataset.previewVideoChanged !== 'true'
            )) {
                event.preventDefault();
                updateDirtyState();
                return;
            }

            saveButtons.forEach((button) => {
                button.disabled = true;
                button.textContent = 'Đang lưu...';
            });
        });
        updateDirtyState();
    })();
    @endif
</script>
