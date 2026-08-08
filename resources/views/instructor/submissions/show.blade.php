<x-instructor-layout title="Chi tiết bài nộp" pageTitle="Chi tiết bài nộp" breadcrumb="Giảng viên / Bài tập đã nộp / Chi tiết">
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Thông tin chi tiết bài nộp -->
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-950 dark:text-white">{{ $submission->assignment->title }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Khóa học: {{ $submission->assignment->lesson->course->title }}</p>
                    </div>
                    <a href="{{ route('instructor.submissions.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center gap-1">
                        &larr; Quay lại danh sách
                    </a>
                </div>

                <!-- Thông tin học viên -->
                <div class="flex items-center gap-3 py-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 rounded-full flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($submission->user->name ?? 'H', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $submission->user->name }}</p>
                        <p class="text-sm text-slate-500">{{ $submission->user->email }}</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <!-- Thời gian nộp -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block uppercase">Thời gian nộp thực tế</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                {{ $submission->submitted_at?->format('d/m/Y H:i') ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <!-- Nội dung bài làm tự luận -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-2">Nội dung nhận xét bài làm</h3>
                        <div class="rounded-xl border border-slate-200 p-4 bg-white dark:border-slate-800 dark:bg-slate-950 min-h-[100px] text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">
                            {{ $submission->content ?: 'Không có văn bản mô tả đi kèm.' }}
                        </div>
                    </div>

                    <!-- Tệp đính kèm -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-2">Tệp bài làm đính kèm</h3>
                        @if($submission->file_path)
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 p-4 bg-slate-50 dark:border-slate-800 dark:bg-slate-950">
                                <div class="flex items-center gap-3">
                                    <svg class="w-8 h-8 text-[#0056D2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">File bài làm</p>
                                        <p class="text-xs text-slate-500">Bấm nút bên phải để tải tệp bài làm của học viên</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#0046B8]">
                                    Tải tệp xuống
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-slate-400 italic">Học viên không tải lên tệp đính kèm nào.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form chấm điểm -->
        <div class="space-y-6">
            @if($submission->grading_history && count($submission->grading_history) > 0)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-sm font-extrabold text-slate-950 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 uppercase tracking-wider">Lịch sử các lần chấm</h3>
                    <div class="mt-4 space-y-4">
                        @foreach(array_reverse($submission->grading_history) as $history)
                            <div class="border-l-2 border-slate-200 dark:border-slate-800 pl-4 py-1 text-xs space-y-1">
                                <div class="flex items-center justify-between text-slate-500">
                                    <span>Người chấm: <strong>{{ $history['graded_by'] ?? 'Giảng viên' }}</strong></span>
                                    <span>{{ isset($history['graded_at']) ? \Carbon\Carbon::parse($history['graded_at'])->format('d/m/Y H:i') : '' }}</span>
                                </div>
                                <div class="font-semibold text-slate-900 dark:text-white">
                                    Điểm: <span class="text-emerald-600 dark:text-emerald-400">{{ $history['score'] }}đ</span>
                                    - Trạng thái: 
                                    @if(($history['status'] ?? '') === 'graded')
                                        <span class="text-emerald-500">Đã chấm điểm</span>
                                    @else
                                        <span class="text-rose-500">Yêu cầu nộp lại</span>
                                    @endif
                                </div>
                                @if(!empty($history['feedback']))
                                    <p class="text-slate-600 dark:text-slate-400 italic">"{{ $history['feedback'] }}"</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-lg font-extrabold text-slate-950 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4">Chấm điểm & Nhận xét</h3>
                
                @if(session('success'))
                    <div class="mt-4 rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('instructor.submissions.grade', $submission) }}" class="mt-5 space-y-4">
                    @csrf
                    
                    <!-- Điểm -->
                    <div>
                        <label for="score" class="block text-sm font-bold text-slate-800 dark:text-slate-100">Điểm số</label>
                        <div class="relative mt-2 rounded-xl shadow-sm">
                            <input type="number" name="score" id="score" min="0" max="{{ $submission->assignment->max_score ?? 100 }}" value="{{ old('score', $submission->score ?? '') }}" required class="w-full rounded-xl border-slate-300 pr-20 text-sm dark:border-slate-700 dark:bg-slate-950" placeholder="Nhập điểm...">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-sm font-semibold text-slate-400">/ {{ $submission->assignment->max_score ?? 100 }}</span>
                            </div>
                        </div>
                        @error('score')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phản hồi -->
                    <div>
                        <label for="feedback" class="block text-sm font-bold text-slate-800 dark:text-slate-100">Nhận xét & Phản hồi</label>
                        <textarea name="feedback" id="feedback" rows="5" class="mt-2 w-full rounded-xl border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950" placeholder="Viết phản hồi chi tiết cho học viên...">{{ old('feedback', $submission->feedback) }}</textarea>
                        @error('feedback')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Trạng thái -->
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-100 mb-2">Trạng thái bài nộp</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-800 p-3 hover:bg-slate-50 dark:hover:bg-slate-950 cursor-pointer">
                                <input type="radio" name="status" value="graded" @checked(old('status', $submission->status) === 'graded' || $submission->status === 'submitted') class="text-[#0056D2] focus:ring-[#0056D2] border-slate-300">
                                <div>
                                    <span class="block text-sm font-bold text-slate-900 dark:text-white">Đã chấm điểm (Graded)</span>
                                    <span class="block text-xs text-slate-500">Lưu điểm số và chuyển trạng thái hoàn thành bài tập.</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-800 p-3 hover:bg-slate-50 dark:hover:bg-slate-950 cursor-pointer">
                                <input type="radio" name="status" value="returned" @checked(old('status', $submission->status) === 'returned' || old('status', $submission->status) === 'resubmit_required') class="text-[#0056D2] focus:ring-[#0056D2] border-slate-300">
                                <div>
                                    <span class="block text-sm font-bold text-slate-900 dark:text-white">Yêu cầu nộp lại (Returned)</span>
                                    <span class="block text-xs text-slate-500">Trả bài và yêu cầu học viên chỉnh sửa, nộp lại bài làm.</span>
                                </div>
                            </label>
                        </div>
                        @error('status')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full cursor-pointer rounded-xl bg-emerald-600 py-3 text-sm font-bold text-white transition-colors hover:bg-emerald-700 mt-2 shadow-sm">
                        Lưu điểm & Trả kết quả
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-instructor-layout>
