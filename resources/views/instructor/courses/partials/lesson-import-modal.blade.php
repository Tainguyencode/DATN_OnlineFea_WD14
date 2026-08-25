<div
    id="lesson-import-dialog"
    data-lesson-import
    class="fixed inset-0 z-[70] hidden overflow-y-auto p-2 sm:p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="lesson-import-title"
    aria-describedby="lesson-import-subtitle"
>
    <div class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm" data-lesson-import-backdrop aria-hidden="true"></div>

    <div class="relative flex min-h-full items-center justify-center">
        <section
            data-lesson-import-panel
            class="relative flex max-h-[calc(100dvh-1rem)] w-full max-w-2xl flex-col overflow-hidden rounded-xl border border-slate-200 bg-white text-left shadow-2xl transition-[max-width] duration-200 sm:max-h-[calc(100dvh-2rem)]"
        >
            <header class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-200 bg-white px-4 py-4 sm:px-6">
                <div class="min-w-0">
                    <h2 id="lesson-import-title" data-lesson-import-heading tabindex="-1" class="text-lg font-bold text-slate-950">
                        Import bài học
                    </h2>
                    <p id="lesson-import-subtitle" data-lesson-import-subtitle class="mt-1 text-sm leading-5 text-slate-600">
                        Nhập nhiều bài học vào một chương bằng file Excel mẫu.
                    </p>
                </div>
                <button
                    type="button"
                    data-lesson-import-close
                    class="inline-flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-lg text-slate-500 transition-colors duration-200 hover:bg-slate-100 hover:text-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                    aria-label="Đóng cửa sổ import bài học"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div data-lesson-import-step="select" class="min-h-0 flex-1 overflow-y-auto">
                <div class="space-y-5 px-4 py-5 sm:px-6">
                    @if($course->courseSections->isEmpty())
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4" role="status">
                            <p class="text-sm font-bold text-amber-900">Bạn cần tạo ít nhất một chương trước khi nhập bài học.</p>
                            <p class="mt-1 text-xs leading-5 text-amber-800">Đóng cửa sổ này và dùng nút “+ Thêm chương” ở phía trên.</p>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <label class="block" for="lesson-import-section">
                            <span class="mb-1.5 block text-sm font-bold text-slate-700">Chương đích <span class="text-rose-600" aria-hidden="true">*</span></span>
                            <select
                                id="lesson-import-section"
                                data-lesson-import-section
                                class="w-full cursor-pointer rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                                @disabled($course->courseSections->isEmpty())
                            >
                                <option value="">Chọn chương sẽ nhận bài học</option>
                                @foreach($course->courseSections as $section)
                                    @php
                                        $chapterNumber = $loop->iteration;
                                        $chapterFallback = 'Chương '.$chapterNumber;
                                        $chapterTitle = trim((string) $section->title);
                                        $normalizedChapterTitle = preg_replace('/\s+/u', ' ', mb_strtolower($chapterTitle));
                                        $normalizedFallback = mb_strtolower($chapterFallback);
                                        $chapterLabel = $chapterTitle === '' || $normalizedChapterTitle === $normalizedFallback
                                            ? $chapterFallback
                                            : $chapterFallback.' — '.$chapterTitle;
                                    @endphp
                                    <option
                                        value="{{ $section->id }}"
                                        data-preview-url="{{ route('instructor.courses.lessons.import.preview', [$course, $section]) }}"
                                    >
                                        {{ $chapterLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <div>
                            <span class="mb-1.5 block text-sm font-bold text-slate-700">File Excel <span class="text-rose-600" aria-hidden="true">*</span></span>
                            <input
                                id="lesson-import-file"
                                data-lesson-import-file
                                type="file"
                                accept=".xlsx"
                                class="sr-only"
                                tabindex="-1"
                                aria-describedby="lesson-import-file-help lesson-import-filename"
                                @disabled($course->courseSections->isEmpty())
                            >
                            <div class="flex min-h-11 flex-col gap-2 rounded-lg border border-slate-300 bg-white p-2 sm:flex-row sm:items-center">
                                <button
                                    type="button"
                                    data-lesson-import-file-trigger
                                    aria-controls="lesson-import-file"
                                    class="inline-flex min-h-9 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-md bg-slate-900 px-3 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-300"
                                    @disabled($course->courseSections->isEmpty())
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L8 8m4-4 4 4M5 20h14" />
                                    </svg>
                                    <span data-lesson-import-file-trigger-label>Chọn file Excel</span>
                                </button>
                                <span id="lesson-import-filename" data-lesson-import-filename class="min-w-0 break-all px-1 text-sm font-semibold text-slate-600" aria-live="polite">Chưa chọn file</span>
                            </div>
                            <span id="lesson-import-file-help" class="mt-1.5 block text-xs text-slate-500">.xlsx • tối đa 5MB • tối đa 100 bài học</span>
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                        <p class="font-semibold">File mẫu hỗ trợ Video, Tài liệu, Quiz và Bài tập.</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">Video và tệp tài liệu sẽ được bổ sung sau khi import.</p>
                        <details class="mt-3">
                            <summary class="cursor-pointer text-xs font-bold text-slate-700 hover:text-slate-950">Quy định file</summary>
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-xs leading-5 text-slate-600">
                                <li>Không đổi tên hoặc thứ tự các cột.</li>
                                <li>Không dùng công thức Excel.</li>
                                <li>Tối đa 100 bài học và chỉ dùng định dạng .xlsx.</li>
                            </ul>
                        </details>
                    </div>

                    <div data-lesson-import-error class="hidden rounded-lg border border-rose-200 bg-rose-50 p-4" role="alert">
                        <p class="text-sm font-bold text-rose-900">Không thể kiểm tra file</p>
                        <p data-lesson-import-error-message class="mt-1 text-sm leading-5 text-rose-800"></p>
                    </div>
                </div>

                <footer class="sticky bottom-0 flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <a
                        href="{{ route('instructor.courses.lessons.import.template', $course) }}"
                        download
                        class="inline-flex min-h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                        </svg>
                        Tải file mẫu
                    </a>
                    <div class="flex flex-col-reverse gap-2 sm:flex-row">
                        <button type="button" data-lesson-import-close class="inline-flex min-h-10 cursor-pointer items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                            Hủy
                        </button>
                        <button
                            type="button"
                            data-lesson-import-submit
                            class="inline-flex min-h-10 cursor-pointer items-center justify-center rounded-lg bg-emerald-600 px-5 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:cursor-not-allowed disabled:bg-emerald-300"
                            disabled
                        >
                            <span data-lesson-import-submit-label>Kiểm tra file</span>
                        </button>
                    </div>
                </footer>
            </div>

            <div data-lesson-import-step="preview" class="hidden min-h-0 flex-1 overflow-y-auto">
                <div class="space-y-5 px-4 py-5 sm:px-6">
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3" aria-label="Tóm tắt kết quả kiểm tra">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <span class="text-xs font-bold text-slate-600">Tổng số</span>
                            <strong data-lesson-import-count="row_count" class="mt-1 block text-xl text-slate-950">0</strong>
                        </div>
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                            <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-800"><span aria-hidden="true">✓</span> Hợp lệ</span>
                            <strong data-lesson-import-count="valid_count" class="mt-1 block text-xl text-emerald-900">0</strong>
                        </div>
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                            <span class="flex items-center gap-1.5 text-xs font-bold text-amber-800"><span aria-hidden="true">!</span> Cảnh báo</span>
                            <strong data-lesson-import-count="warning_count" class="mt-1 block text-xl text-amber-900">0</strong>
                        </div>
                        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3">
                            <span class="flex items-center gap-1.5 text-xs font-bold text-rose-800"><span aria-hidden="true">×</span> Lỗi</span>
                            <strong data-lesson-import-count="error_count" class="mt-1 block text-xl text-rose-900">0</strong>
                        </div>
                    </div>

                    <div data-lesson-import-error-guidance class="hidden rounded-lg border border-rose-200 bg-rose-50 p-4" role="status">
                        <p class="text-sm font-bold text-rose-900">File còn dòng lỗi và chưa sẵn sàng để import.</p>
                        <p class="mt-1 text-sm text-rose-800">Vui lòng sửa các dòng lỗi trong file Excel và kiểm tra lại.</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap gap-2" aria-label="Lọc trạng thái bài học">
                            @foreach([
                                'all' => 'Tất cả',
                                'valid' => 'Hợp lệ',
                                'warning' => 'Cảnh báo',
                                'error' => 'Lỗi',
                            ] as $filterValue => $filterLabel)
                                <button
                                    type="button"
                                    data-lesson-import-filter="{{ $filterValue }}"
                                    class="inline-flex min-h-9 cursor-pointer items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 aria-pressed:border-slate-900 aria-pressed:bg-slate-900 aria-pressed:text-white"
                                    aria-pressed="{{ $filterValue === 'all' ? 'true' : 'false' }}"
                                >
                                    {{ $filterLabel }}
                                    <span data-lesson-import-filter-count="{{ $filterValue }}" class="rounded bg-black/5 px-1.5 py-0.5 tabular-nums aria-pressed:bg-white/15">0</span>
                                </button>
                            @endforeach
                        </div>
                        <p data-lesson-import-filter-summary class="text-xs font-semibold text-slate-500" aria-live="polite"></p>
                    </div>

                    <div data-lesson-import-empty class="hidden rounded-lg border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center">
                        <p class="text-sm font-bold text-slate-800">File Excel chưa có bài học nào.</p>
                    </div>

                    <div data-lesson-import-table-wrap class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-[820px] w-full border-collapse text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600">
                                <tr>
                                    <th scope="col" class="w-16 px-3 py-3 font-bold">Dòng</th>
                                    <th scope="col" class="w-36 px-3 py-3 font-bold">Mã</th>
                                    <th scope="col" class="min-w-64 px-3 py-3 font-bold">Tên bài học</th>
                                    <th scope="col" class="w-28 px-3 py-3 font-bold">Loại</th>
                                    <th scope="col" class="w-32 px-3 py-3 font-bold">Thời lượng</th>
                                    <th scope="col" class="w-44 px-3 py-3 font-bold">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody data-lesson-import-rows class="divide-y divide-slate-200 bg-white"></tbody>
                        </table>
                    </div>
                </div>

                <footer class="sticky bottom-0 flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-6">
                    <button type="button" data-lesson-import-choose-another class="inline-flex min-h-10 cursor-pointer items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                        Chọn file khác
                    </button>
                    <button type="button" data-lesson-import-close class="inline-flex min-h-10 cursor-pointer items-center justify-center rounded-lg bg-slate-900 px-5 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500">
                        Đóng
                    </button>
                </footer>
            </div>

            <p data-lesson-import-live class="sr-only" aria-live="polite"></p>
        </section>
    </div>
</div>
