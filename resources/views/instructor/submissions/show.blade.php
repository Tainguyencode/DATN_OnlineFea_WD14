<x-instructor-layout title="Chi tiết bài nộp" pageTitle="Chi tiết bài nộp" breadcrumb="Giảng viên / Bài tập đã nộp / Chi tiết">
    <div class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- CỘT TRÁI (65-70%): Thông tin bài nộp, Nội dung bài làm, Tệp đính kèm -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Thông tin bài nộp -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="rounded-full bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 px-2.5 py-0.5 text-[11px] font-bold">
                                    BÀI TẬP THỰC HÀNH
                                </span>
                                <span class="rounded-full bg-indigo-100 dark:bg-indigo-950/40 text-indigo-800 dark:text-indigo-300 px-2.5 py-0.5 text-[11px] font-bold">
                                    Lần làm {{ $submission->attempt_number }}/{{ $submission->allowed_attempts ?? 2 }}
                                </span>
                                @if($submission->granter)
                                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 px-2.5 py-0.5 text-[11px] font-medium" title="Lý do: {{ $submission->grant_reason ?: 'Không có' }}">
                                        Cấp bởi GV: {{ $submission->granter->name }} ({{ $submission->granted_at?->format('d/m/Y H:i') }})
                                    </span>
                                @endif
                            </div>
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

                    <!-- Thời gian làm bài & nộp bài của attempt này -->
                    <div class="mt-5 grid gap-4 sm:grid-cols-3 bg-slate-50 dark:bg-slate-950 p-4 rounded-xl text-xs">
                        <div>
                            <span class="font-semibold text-slate-400 block uppercase mb-0.5">Bắt đầu tải tài liệu</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                {{ $submission->started_at?->format('d/m/Y H:i') ?? 'Chưa bắt đầu' }}
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-400 block uppercase mb-0.5">Hạn nộp (6 Giờ)</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                {{ $submission->getDeadline()?->format('d/m/Y H:i') ?? '6 giờ kể từ khi tải' }}
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-400 block uppercase mb-0.5">Ngày giờ nộp</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                {{ $submission->submitted_at?->format('d/m/Y H:i') ?? 'Chưa nộp' }}
                            </span>
                            @if($submission->isExpired())
                                <span class="ml-1 inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700 dark:bg-rose-950/20 dark:text-rose-400">
                                    Quá hạn (FAIL)
                                </span>
                            @elseif($submission->submitted_at)
                                <span class="ml-1 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400">
                                    Đúng hạn
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Nội dung bài làm -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3">Nội dung bài làm (Lần {{ $submission->attempt_number }})</h3>
                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950 min-h-[100px] text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                        {{ $submission->content ?: 'Học viên không gửi nội dung văn bản ghi chú nào.' }}
                    </div>
                </div>

                <!-- Tệp đính kèm -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3">Tệp bài làm đính kèm (Lần {{ $submission->attempt_number }})</h3>
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
                        <p class="text-sm text-slate-400 italic">Lần làm này học viên không tải lên tệp đính kèm nào.</p>
                    @endif
                </div>
            </div>

            <!-- CỘT PHẢI (30-35%): Đánh giá PASS/FAIL & Cấp thêm lượt làm lại -->
            <div class="space-y-6">
                <!-- Đánh giá & Nhận xét -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-950 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 uppercase tracking-wider">
                            Đánh giá Lần {{ $submission->attempt_number }}
                        </h3>
                        
                        <!-- Kết quả hiện tại -->
                        @if($submission->result !== null || $submission->status === 'graded')
                            <div class="mt-4 space-y-4">
                                <div>
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Kết quả hiện tại</span>
                                    <div class="mt-2">
                                        @if($submission->result === 'pass')
                                            <span class="inline-flex items-center gap-1.5 text-sm font-extrabold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                PASS (Đạt)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-sm font-extrabold text-rose-700 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200 dark:bg-rose-950/30 dark:text-rose-300">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                FAIL (Không đạt)
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

                    <!-- Form đánh giá PASS / FAIL -->
                    <form method="POST" action="{{ route('instructor.submissions.grade', $submission) }}" class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        @csrf
                        
                        <!-- Lựa chọn PASS / FAIL -->
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Đánh giá kết quả (Lần {{ $submission->attempt_number }})</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center gap-2 rounded-xl border-2 p-3 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 cursor-pointer transition-all {{ old('result', $submission->result) === 'pass' ? 'border-emerald-500 bg-emerald-50/30 text-emerald-700 dark:text-emerald-300 font-black' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300' }}">
                                    <input type="radio" name="result" value="pass" @checked(old('result', $submission->result) === 'pass') required class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-sm font-bold">PASS (Đạt)</span>
                                </label>
                                <label class="flex items-center justify-center gap-2 rounded-xl border-2 p-3 hover:bg-rose-50/50 dark:hover:bg-rose-950/30 cursor-pointer transition-all {{ old('result', $submission->result) === 'fail' ? 'border-rose-500 bg-rose-50/30 text-rose-700 dark:text-rose-300 font-black' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300' }}">
                                    <input type="radio" name="result" value="fail" @checked(old('result', $submission->result) === 'fail') required class="text-rose-600 focus:ring-rose-500">
                                    <span class="text-sm font-bold">FAIL (Không đạt)</span>
                                </label>
                            </div>
                            @error('result')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phản hồi input -->
                        <div>
                            <label for="feedback" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Nhận xét & Phản hồi</label>
                            <textarea name="feedback" id="feedback" rows="4" class="mt-2 w-full rounded-lg border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-950 focus:border-[#0056D2] focus:ring-[#0056D2]" placeholder="Viết nhận xét giải thích lý do PASS hoặc FAIL cho học viên...">{{ old('feedback', $submission->feedback) }}</textarea>
                            @error('feedback')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full cursor-pointer rounded-lg bg-[#0056D2] py-2.5 text-sm font-bold text-white transition-colors hover:bg-[#0046B8] mt-2 shadow-sm">
                            Lưu đánh giá (Lần {{ $submission->attempt_number }})
                        </button>
                    </form>
                </div>

                <!-- CẤP THÊM LƯỢT LÀM BÀI CHO HỌC VIÊN -->
                @php
                    $isPassedOverall = $allSubmissions->contains(fn($s) => $s->isPassed());
                    $latestAttempt = $allSubmissions->last();
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-950 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 uppercase tracking-wider">
                        Cấp thêm lượt làm bài
                    </h3>
                    
                    <div class="text-xs text-slate-600 dark:text-slate-400 space-y-1">
                        <p>• Tổng số lượt hiện tại: <strong class="text-slate-900 dark:text-white">{{ $latestAttempt->allowed_attempts ?? 2 }} lượt</strong></p>
                        <p>• Đã thực hiện: <strong class="text-slate-900 dark:text-white">{{ $allSubmissions->count() }} lần</strong></p>
                    </div>

                    @if($isPassedOverall)
                        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 p-3 text-xs text-emerald-800 dark:text-emerald-300 font-medium">
                            ✓ Học viên đã đạt (PASS) bài tập này, không cần cấp thêm lượt.
                        </div>
                    @else
                        <form method="POST" action="{{ route('instructor.submissions.grant-retry', $submission) }}" onsubmit="return confirm('Bạn có chắc muốn cấp thêm 1 lượt làm bài cho học viên này? (Học viên sẽ có lần làm tiếp theo)')" class="space-y-3 pt-2">
                            @csrf
                            <div>
                                <label for="reason" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Lý do cấp thêm (tùy chọn)</label>
                                <input type="text" name="reason" id="reason" class="w-full rounded-lg border-slate-300 text-xs dark:border-slate-700 dark:bg-slate-950" placeholder="VD: Cho phép làm lại để hoàn thiện phần 2...">
                            </div>

                            <button type="submit" class="w-full cursor-pointer rounded-lg bg-emerald-600 py-2.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700 shadow-sm flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Cho phép làm lại (+1 lượt)
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- BẢNG LỊCH SỬ TẤT CẢ CÁC LẦN LÀM CỦA HỌC VIÊN -->
        @if(isset($allSubmissions) && $allSubmissions->count() > 0)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-sm font-extrabold text-slate-950 dark:text-white uppercase tracking-wider">
                        Lịch sử toàn bộ các lần làm bài ({{ $allSubmissions->count() }} lần)
                    </h3>
                    <span class="text-xs text-slate-500 font-medium">Học viên: {{ $submission->user->name }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Lần làm</th>
                                <th class="px-4 py-3">Nguồn cấp</th>
                                <th class="px-4 py-3">Bắt đầu / Hạn nộp (6h)</th>
                                <th class="px-4 py-3">Ngày nộp</th>
                                <th class="px-4 py-3">Bài làm</th>
                                <th class="px-4 py-3">Trạng thái / Kết quả</th>
                                <th class="px-4 py-3">Nhận xét của GV</th>
                                <th class="px-4 py-3 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($allSubmissions as $attempt)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-950/40 {{ $attempt->id === $submission->id ? 'bg-indigo-50/40 dark:bg-indigo-950/20 font-semibold' : '' }}">
                                    <td class="px-4 py-3.5">
                                        <span class="font-bold text-slate-900 dark:text-white">Lần {{ $attempt->attempt_number }}/{{ $attempt->allowed_attempts ?? 2 }}</span>
                                        @if($attempt->id === $submission->id)
                                            <span class="block text-[10px] text-indigo-600 dark:text-indigo-400 font-bold">(Đang xem)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($attempt->granter)
                                            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-medium">GV {{ $attempt->granter->name }}</span>
                                            <span class="block text-[10px] text-slate-400">{{ $attempt->granted_at?->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="text-slate-500">Mặc định</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-[11px]">
                                        @if($attempt->started_at)
                                            <span class="block text-slate-800 dark:text-slate-200">{{ $attempt->started_at->format('d/m/Y H:i') }}</span>
                                            <span class="block text-amber-600 font-medium">Hạn: {{ $attempt->getDeadline()?->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="text-slate-400">Chưa bắt đầu</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 font-mono">
                                        {{ $attempt->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($attempt->file_path)
                                            <a href="{{ Storage::url($attempt->file_path) }}" target="_blank" class="text-[#0056D2] hover:underline font-bold inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                File Lần {{ $attempt->attempt_number }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">Không có file</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($attempt->result === 'pass')
                                            <span class="inline-flex items-center gap-1 text-xs font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-950/30 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                                                ✓ PASS
                                            </span>
                                        @elseif($attempt->result === 'fail' || $attempt->isExpired())
                                            <span class="inline-flex items-center gap-1 text-xs font-black text-rose-600 bg-rose-50 dark:bg-rose-950/30 px-2 py-0.5 rounded border border-rose-200 dark:border-rose-800">
                                                ✕ FAIL {{ $attempt->isExpired() ? '(Quá hạn)' : '' }}
                                            </span>
                                        @elseif($attempt->status === 'submitted')
                                            <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 dark:bg-amber-950/30 px-2 py-0.5 rounded border border-amber-200 dark:border-amber-800">
                                                Chờ chấm
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">Đang làm</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 max-w-[200px] truncate text-slate-600 dark:text-slate-400 italic">
                                        {{ $attempt->feedback ?: '—' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if($attempt->id !== $submission->id)
                                            <a href="{{ route('instructor.submissions.show', $attempt) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                                Xem lần này
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">Đang xem</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-instructor-layout>
