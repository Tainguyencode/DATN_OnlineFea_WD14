<x-instructor-layout title="Chi tiết bài nộp" pageTitle="Chi tiết bài nộp" breadcrumb="Giảng viên / Bài tập đã nộp / Chi tiết">
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- CỘT TRÁI (65-70%): Thông tin bài nộp, Nội dung bài làm, Tệp đính kèm -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Thông tin bài nộp -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg font-extrabold text-slate-950 dark:text-white break-words">{{ $submission->assignment->title }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Khóa học: {{ $submission->assignment->lesson->course->title }}</p>
                    </div>
                    <a href="{{ route('instructor.submissions.index') }}" class="text-xs font-bold text-[#0056D2] hover:underline flex items-center gap-1 shrink-0">
                        &larr; Quay lại danh sách
                    </a>
                </div>

                <!-- Thông tin học viên -->
                <div class="flex items-center gap-3 py-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 rounded-full flex items-center justify-center font-bold text-lg shrink-0">
                        {{ strtoupper(substr($submission->user->name ?? 'H', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-900 dark:text-white truncate" title="{{ $submission->user->name }}">{{ $submission->user->name }}</p>
                        <p class="text-sm text-slate-500 break-all">{{ $submission->user->email }}</p>
                    </div>
                </div>

                <!-- Thời gian nộp & hạn nộp -->
                <div class="mt-5 grid gap-4 sm:grid-cols-2 bg-slate-50 dark:bg-slate-950 p-4 rounded-xl">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase">Hạn nộp bài</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                            {{ $submission->assignment?->due_date?->format('d/m/Y H:i') ?? 'Không giới hạn' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase">Ngày giờ nộp</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                            {{ $submission->submitted_at?->format('d/m/Y H:i') ?? 'N/A' }}
                        </span>
                        @if($submission->assignment?->due_date)
                            @if($submission->isLate())
                                <span class="ml-2 inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-600/10 dark:bg-rose-950/20 dark:text-rose-400">
                                    Nộp trễ
                                </span>
                            @else
                                <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/10 dark:bg-emerald-950/20 dark:text-emerald-400">
                                    Đúng hạn
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Nội dung bài làm -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3">Nội dung bài làm</h3>
                <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950 min-h-[100px] text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                    {{ $submission->content ?: 'Học viên không tải lên nội dung văn bản nào.' }}
                </div>
            </div>

            <!-- Tệp đính kèm -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3">Tệp bài làm đính kèm</h3>
                @if($submission->file_path)
                    @php
                        $fileName = basename($submission->file_path);
                        $fileExtension = strtoupper(pathinfo($submission->file_path, PATHINFO_EXTENSION));
                    @endphp
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 p-4 bg-slate-50 dark:border-slate-800 dark:bg-slate-950 gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-2xl shrink-0">📄</span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 dark:text-white truncate" title="{{ $fileName }}">{{ $fileName }}</p>
                                @if($fileExtension)
                                    <p class="text-[10px] text-slate-400">Định dạng: {{ $fileExtension }}</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#0046B8] shrink-0 shadow-sm">
                            Xem / Tải xuống
                        </a>
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Học viên không tải lên tệp đính kèm nào.</p>
                @endif
            </div>
        </div>

        <!-- CỘT PHẢI (30-35%): Chấm điểm & Nhận xét, Lịch sử các lần chấm -->
        <div class="space-y-6">
            <!-- Chấm điểm & Nhận xét -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-slate-950 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 uppercase tracking-wider">Chấm điểm & Nhận xét</h3>
                    
                    @if(session('success'))
                        <div class="mt-4 rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400 mb-4 shadow-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Điểm số hiện tại -->
                    @if($submission->score !== null)
                        <div class="mt-4 space-y-4">
                            <div>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Điểm số</span>
                                <div class="flex items-baseline text-slate-900 dark:text-white whitespace-nowrap mt-1">
                                    <span class="text-3xl font-black">{{ floatval($submission->score) }}</span>
                                    <span class="text-sm font-bold text-slate-400 ml-1">/ {{ $submission->assignment->max_score ?? 100 }}</span>
                                </div>
                                <div class="mt-2">
                                    @if($submission->score >= ($submission->assignment?->passing_score ?? 70))
                                        <span class="inline-flex items-center text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                                            ✓ Đạt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-xs font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-100">
                                            ✕ Chưa đạt
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Nhận xét của giảng viên -->
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Nhận xét của giảng viên</span>
                                <p class="text-sm text-slate-700 dark:text-slate-300 italic whitespace-pre-wrap leading-relaxed bg-slate-50 dark:bg-slate-950 p-3 rounded-lg border border-slate-150 dark:border-slate-800">
                                    {{ $submission->feedback ?: 'Chưa có nhận xét.' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Form chấm điểm / Chỉnh sửa -->
                <form method="POST" action="{{ route('instructor.submissions.grade', $submission) }}" class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    @csrf
                    
                    <!-- Điểm input -->
                    <div>
                        <label for="score" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Điểm số</label>
                        <div class="relative mt-2 rounded-lg shadow-sm">
                            <input type="number" name="score" id="score" step="any" min="0" max="{{ $submission->assignment->max_score ?? 100 }}" value="{{ old('score', $submission->score !== null ? floatval($submission->score) : '') }}" required class="w-full rounded-lg border-slate-300 pr-16 text-sm dark:border-slate-700 dark:bg-slate-950 focus:border-[#0056D2] focus:ring-[#0056D2]" placeholder="Nhập điểm...">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs font-semibold text-slate-400">/ {{ $submission->assignment->max_score ?? 100 }}</span>
                            </div>
                        </div>
                        @error('score')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phản hồi input -->
                    <div>
                        <label for="feedback" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Nhận xét & Phản hồi</label>
                        <textarea name="feedback" id="feedback" rows="4" class="mt-2 w-full rounded-lg border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950 focus:border-[#0056D2] focus:ring-[#0056D2]" placeholder="Viết phản hồi cho học viên...">{{ old('feedback', $submission->feedback) }}</textarea>
                        @error('feedback')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Trạng thái -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Trạng thái bài nộp</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 rounded-lg border border-slate-200 dark:border-slate-800 p-2.5 hover:bg-slate-50 dark:hover:bg-slate-950 cursor-pointer">
                                <input type="radio" name="status" value="graded" @checked(old('status', $submission->status) === 'graded' || $submission->status === 'submitted') class="text-[#0056D2] focus:ring-[#0056D2] border-slate-300">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white">Đã chấm điểm</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 rounded-lg border border-slate-200 dark:border-slate-800 p-2.5 hover:bg-slate-50 dark:hover:bg-slate-950 cursor-pointer">
                                <input type="radio" name="status" value="returned" @checked(old('status', $submission->status) === 'returned' || old('status', $submission->status) === 'resubmit_required') class="text-[#0056D2] focus:ring-[#0056D2] border-slate-300">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white">Yêu cầu nộp lại</span>
                                </div>
                            </label>
                        </div>
                        @error('status')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full cursor-pointer rounded-lg bg-[#0056D2] py-2.5 text-sm font-bold text-white transition-colors hover:bg-[#0046B8] mt-2 shadow-sm">
                        Lưu đánh giá
                    </button>
                </form>
            </div>

            <!-- Lịch sử các lần chấm -->
            @if($submission->grading_history && count($submission->grading_history) > 0)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-sm font-extrabold text-slate-950 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 uppercase tracking-wider">Lịch sử các lần chấm</h3>
                    <div class="mt-4 space-y-4">
                        @foreach(array_reverse($submission->grading_history) as $history)
                            <div class="flex flex-col gap-1.5 {{ !$loop->first ? 'pt-3 border-t border-slate-100 dark:border-slate-800' : '' }}">
                                <p class="text-xs text-slate-500 font-medium">
                                    Người chấm: <strong>{{ $history['graded_by'] ?? 'Giảng viên' }}</strong>
                                </p>
                                <p class="text-xs text-slate-550 dark:text-slate-400">
                                    <span class="font-bold text-slate-800 dark:text-white">{{ floatval($history['score'] ?? 0) }}/{{ $submission->assignment->max_score ?? 100 }}</span> · 
                                    @if(($history['status'] ?? '') === 'graded')
                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">Đã chấm</span>
                                    @else
                                        <span class="font-semibold text-rose-600 dark:text-rose-400">Yêu cầu nộp lại</span>
                                    @endif
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    {{ isset($history['graded_at']) ? \Carbon\Carbon::parse($history['graded_at'])->format('d/m/Y H:i') : '' }}
                                </p>
                                @if(!empty($history['feedback']))
                                    <p class="text-xs text-slate-500 italic bg-slate-50 dark:bg-slate-950 p-2.5 rounded-lg border border-slate-150 dark:border-slate-800 leading-normal">
                                        "{{ $history['feedback'] }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-instructor-layout>
