<x-admin-layout :title="'Kiểm duyệt: '.$course->title">
    <div class="mb-6">
        <a href="{{ route('admin.course-reviews.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Danh sách kiểm duyệt</a>
        <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $course->title }}</h1>
        <p class="mt-1 text-sm text-slate-500">Giảng viên: {{ $course->instructor?->name }} · {{ $totalLessons }} bài · {{ gmdate('H:i:s', $totalDuration) }}</p>
    </div>

    @if(session('success'))<div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>@endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h2 class="font-bold text-slate-900">Thông tin khóa học</h2>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Mô tả ngắn</dt><dd class="text-slate-800">{{ $course->short_description }}</dd></div>
                    <div><dt class="text-slate-500">Giá</dt><dd class="text-slate-800">{{ number_format($course->effective_price, 0, ',', '.') }}đ</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Mô tả</dt><dd class="mt-1 whitespace-pre-line text-slate-700">{{ $course->description }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Mục tiêu</dt><dd class="mt-1 text-slate-700">{{ $course->objectives }}</dd></div>
                </dl>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h2 class="font-bold text-slate-900">Nội dung khóa học</h2>
                <div class="mt-4 space-y-4">
                    @foreach($curriculumSections as $section)
                        <div class="rounded-lg border border-slate-100 p-4">
                            <h3 class="font-semibold text-slate-900">{{ $section->title }}</h3>
                            <ul class="mt-2 space-y-1 text-sm text-slate-600">
                                @foreach($section->lessons as $lesson)
                                    <li>• {{ $lesson->title }} <span class="text-xs text-slate-400">({{ $lesson->type }})</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($reviewHistory->isNotEmpty())
                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h2 class="font-bold text-slate-900">Lịch sử kiểm duyệt</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($reviewHistory as $review)
                            <div class="rounded-lg border border-slate-100 p-4 text-sm">
                                <div class="font-semibold text-slate-900">Lần {{ $review->submission_number }}: {{ $review->status->label() }}</div>
                                @if($review->comment)<p class="mt-1 text-slate-600">Lý do: {{ $review->comment }}</p>@endif
                                @if($review->reviewer)<p class="mt-1 text-slate-500">Người duyệt: {{ $review->reviewer->name }}</p>@endif
                                <p class="text-xs text-slate-400">{{ $review->reviewed_at?->format('d/m/Y H:i') ?? $review->submitted_at?->format('d/m/Y H:i') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start">
            @if($course->status === 'pending_review')
                <form method="POST" action="{{ route('admin.course-reviews.approve', $course) }}" class="rounded-xl border border-slate-200 bg-white p-6">
                    @csrf
                    <h3 class="font-bold text-slate-900">Checklist duyệt</h3>
                    <div class="mt-4 space-y-2">
                        @foreach($checklistItems as $key => $label)
                            <label class="flex items-start gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="checklist[{{ $key }}]" value="1" required class="mt-0.5 rounded border-slate-300 text-blue-600">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <label class="mt-4 flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="publish_immediately" value="1" checked class="rounded border-slate-300 text-blue-600">
                        Xuất bản ngay sau khi duyệt
                    </label>
                    <button type="submit" class="mt-4 w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Duyệt khóa học</button>
                </form>

                <form method="POST" action="{{ route('admin.course-reviews.reject', $course) }}" class="rounded-xl border border-red-200 bg-white p-6">
                    @csrf
                    <h3 class="font-bold text-red-700">Từ chối</h3>
                    <textarea name="comment" rows="4" required minlength="10" class="mt-3 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Nhập lý do từ chối (tối thiểu 10 ký tự)"></textarea>
                    @error('comment')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="mt-3 w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Từ chối</button>
                </form>
            @else
                <div class="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-600">
                    Trạng thái hiện tại: <strong>{{ $course->status }}</strong>
                </div>
            @endif
        </aside>
    </div>
</x-admin-layout>
