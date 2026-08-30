@php
    $routePrefix = $isAdmin ? 'admin.courses.versions' : 'instructor.courses.versions';
    $filters = ['' => 'Tất cả', 'course' => 'Khóa học', 'chapter' => 'Chương', 'lesson' => 'Bài học', 'assignment' => 'Bài tập', 'quiz' => 'Quiz'];
@endphp

<div class="mx-auto max-w-6xl space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Lịch sử phiên bản — {{ $course->title }}</h1>
            <p class="mt-1 text-sm text-slate-600">Mỗi phiên bản là một snapshot bất biến. Học viên luôn nhận phiên bản đang xuất bản.</p>
        </div>
        <a href="{{ $isAdmin ? route('admin.courses.review', $course) : route('instructor.courses.curriculum', $course) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700">Quay lại khóa học</a>
    </div>

    <nav class="flex flex-wrap gap-2" aria-label="Lọc lịch sử phiên bản">
        @foreach($filters as $value => $label)
            <a href="{{ route($routePrefix.'.index', array_filter([$course, 'type' => $value])) }}" class="rounded-full px-3 py-1.5 text-sm font-bold {{ ($filter ?? '') === $value ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $label }}</a>
        @endforeach
    </nav>

    <div class="space-y-3">
        @forelse($timeline as $item)
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold uppercase text-indigo-700">{{ $item['type_label'] }}</span>
                            <strong class="text-lg text-slate-950">V{{ $item['version_number'] }}</strong>
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $item['is_current'] ? 'bg-emerald-100 text-emerald-800' : ($item['status'] === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700') }}">{{ $item['is_current'] ? 'Đang xuất bản' : $item['status_label'] }}</span>
                            @if($item['is_archived'])<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">Đã lưu trữ</span>@endif
                        </div>
                        <h2 class="mt-2 font-bold text-slate-900">{{ $item['entity_label'] }}</h2>
                        <p class="mt-1 text-sm text-slate-600">Nguồn: {{ $item['origin'] }}@if($item['source_version_number']) · từ V{{ $item['source_version_number'] }}@endif</p>
                        <p class="mt-1 text-xs text-slate-500">Tạo bởi {{ $item['creator_name'] }} · {{ optional($item['created_at'])->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route($routePrefix.'.show', [$course, $item['type'], $item['version']->id]) }}" class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white">Xem snapshot</a>
                        <a href="{{ route($routePrefix.'.compare', [$course, $item['type'], $item['version']->id]) }}" class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-bold text-indigo-700">So sánh hiện tại</a>
                        @if(!$isAdmin && $item['rollback_eligible'])
                            <a href="{{ route('instructor.courses.versions.rollback.confirm', [$course, $item['type'], $item['version']->id]) }}" class="rounded-lg bg-amber-600 px-3 py-2 text-xs font-bold text-white">Khôi phục từ phiên bản này</a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">Chưa có phiên bản phù hợp bộ lọc.</div>
        @endforelse
    </div>

    {{ $timeline->links() }}
</div>
