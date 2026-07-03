<x-admin-layout :title="'Duyệt - '.$course->title" page-title="Chi tiết duyệt khóa học" :breadcrumb="$course->title">

@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
    $price = $course->discount_price ?? $course->sale_price ?? $course->price;
    $levelLabels = ['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'];
    $typeLabels = ['video' => 'Video', 'document' => 'Tài liệu', 'quiz' => 'Quiz', 'assignment' => 'Bài tập'];
@endphp

<div class="space-y-6">
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-6 p-5 lg:grid-cols-[320px_minmax(0,1fr)_220px]">
            <div class="aspect-video overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-rose-700 text-sm font-bold text-white">Fea LMS</div>
                @endif
            </div>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">{{ $course->status === 'pending' ? 'Đang chờ duyệt' : $course->status }}</span>
                    <span class="text-xs font-semibold text-slate-500">{{ $course->category?->name ?? 'Chưa chọn danh mục' }}</span>
                </div>
                <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ $course->title }}</h2>
                <p class="mt-2 text-sm text-slate-500">Giảng viên: {{ $course->instructor?->name }} · {{ $course->instructor?->email }}</p>
                <p class="mt-4 text-sm leading-6 text-slate-600">{{ $course->short_description }}</p>

                <dl class="mt-5 grid gap-3 sm:grid-cols-4">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Giá</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-950">{{ $formatPrice($price) }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Trình độ</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-950">{{ $levelLabels[$course->level] ?? 'Chưa chọn' }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Chương</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-950">{{ $curriculumSections->count() }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Bài học</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-950">{{ $totalLessons }}</dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.courses.pending') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 cursor-pointer">
                    Quay lại
                </a>
                @if($course->status === 'pending')
                    <form method="POST" action="{{ route('admin.courses.approve', $course) }}" onsubmit="return confirm('Duyệt khóa học này?')">
                        @csrf
                        <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 cursor-pointer">
                            Duyệt khóa học
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h3 class="text-lg font-bold text-slate-950">Mô tả chi tiết</h3>
        <div class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $course->description }}</div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-rose-600">Curriculum preview</p>
                <h3 class="mt-1 text-lg font-bold text-slate-950">Chương và bài học</h3>
            </div>
            @if($course->preview_video)
                <a href="{{ $course->preview_video }}" target="_blank" class="text-sm font-bold text-indigo-600 hover:underline">Mở video giới thiệu</a>
            @endif
        </div>

        <div class="mt-5 space-y-4">
            @forelse($curriculumSections as $section)
                <article class="overflow-hidden rounded-lg border border-slate-200">
                    <div class="bg-slate-50 px-4 py-3">
                        <h4 class="font-bold text-slate-950">{{ $section->title }}</h4>
                        @if($section->description)
                            <p class="mt-1 text-sm text-slate-500">{{ $section->description }}</p>
                        @endif
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($section->lessons as $lesson)
                            <div class="p-4">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-700">{{ $typeLabels[$lesson->type] ?? $lesson->type }}</span>
                                            @if($lesson->is_preview)
                                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Xem thử</span>
                                            @endif
                                        </div>
                                        <h5 class="mt-2 font-bold text-slate-950">{{ $lesson->title }}</h5>
                                        @if($lesson->content)
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $lesson->content }}</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 flex-wrap gap-2">
                                        @if($lesson->video_url)
                                            <a href="{{ $lesson->video_url }}" target="_blank" class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-50">Xem video</a>
                                        @endif
                                        @if($lesson->document_file)
                                            <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="rounded-lg border border-sky-200 px-3 py-2 text-xs font-bold text-sky-700 hover:bg-sky-50">Xem tài liệu</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-sm text-slate-500">Chương này chưa có bài học.</div>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Khóa học chưa có chương học.</div>
            @endforelse
        </div>
    </section>

    @if($course->status === 'pending')
        <section class="rounded-lg border border-rose-200 bg-rose-50 p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-bold text-rose-900">Từ chối khóa học</h3>
            <form method="POST" action="{{ route('admin.courses.reject', $course) }}" class="mt-4 space-y-3" onsubmit="return confirm('Từ chối khóa học này?')">
                @csrf
                <label for="review-reason" class="block text-sm font-bold text-rose-900">Lý do từ chối</label>
                <textarea id="review-reason" name="reject_reason" rows="4" required maxlength="1000"
                          class="w-full resize-none rounded-lg border border-rose-200 bg-white px-3 py-2 text-sm outline-none focus:border-rose-500 focus-visible:ring-2 focus-visible:ring-rose-500/20"
                          placeholder="Nêu rõ phần cần giảng viên bổ sung hoặc chỉnh sửa..."></textarea>
                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-rose-600 px-5 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700 cursor-pointer">
                    Từ chối khóa học
                </button>
            </form>
        </section>
    @endif
</div>

</x-admin-layout>
