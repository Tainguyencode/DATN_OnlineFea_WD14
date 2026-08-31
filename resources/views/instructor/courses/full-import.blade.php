<x-instructor-layout title="Nhập toàn bộ khóa học" page-title="Nhập toàn bộ khóa học từ Excel" breadcrumb="Tải mẫu, chọn file và xem trước dữ liệu trước khi tạo khóa học" :back-url="route('instructor.courses.index')">
    <div class="mx-auto w-full max-w-5xl space-y-6" data-full-course-import>
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Bước 1: Tải lên workbook v3</h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Chỉ nhận .xlsx, tối đa 5 MB. Dữ liệu chỉ được kiểm tra và lưu bản xem trước; chưa tạo khóa học.</p>
                </div>
                <a href="{{ route('instructor.courses.full-import.template') }}" class="inline-flex shrink-0 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">Tải mẫu Excel v3</a>
            </div>
            <form class="mt-6 space-y-4" data-full-course-import-form novalidate data-preview-url="{{ route('instructor.courses.full-import.preview') }}" data-confirm-url="{{ route('instructor.courses.full-import.confirm') }}">
                @csrf
                <div>
                    <label for="full-course-import-file" class="block text-sm font-semibold text-slate-800 dark:text-slate-100">Chọn file Excel <span class="text-rose-500">*</span></label>
                    <input id="full-course-import-file" name="file" type="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
                    <p class="mt-1.5 hidden text-sm font-medium text-rose-600 dark:text-rose-400" data-full-course-file-error></p>
                    <p class="mt-1 hidden text-sm text-slate-600 dark:text-slate-300" data-full-course-file-name></p>
                </div>
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Xem trước</button>
            </form>
        </section>

        <section class="hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900" data-full-course-preview>
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div><h2 class="text-lg font-bold text-slate-900 dark:text-white" data-full-course-title>Khóa học</h2><p class="mt-1 text-sm text-slate-600 dark:text-slate-300" data-full-course-meta></p></div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300" data-full-course-confirm-state></p>
            </div>
            <dl class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4" data-full-course-summary></dl>
            <div class="mt-6 border-b border-slate-200 dark:border-slate-700" role="tablist" aria-label="Nội dung xem trước" data-full-course-tabs></div>
            <div class="mt-4 overflow-x-auto" data-full-course-panel></div>
            <div class="mt-6" data-full-course-issues></div>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <button type="button" data-full-course-confirm class="inline-flex items-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-emerald-600">Xác nhận tạo khóa học</button>
                <p class="text-sm text-slate-600 dark:text-slate-300">Video sẽ được tạo dưới dạng shell; hãy tải nguồn video lên sau khi mở curriculum.</p>
            </div>
        </section>
        <p class="sr-only" aria-live="polite" data-full-course-live></p>
    </div>
</x-instructor-layout>
