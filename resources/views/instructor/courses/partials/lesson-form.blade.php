@php
    $selectedType = old('type', $lesson->type ?? 'video');
    $selectedStatus = old('status', $lesson->status ?? 'draft');
    $formatVideoSize = function ($bytes) {
        if (! $bytes) {
            return null;
        }

        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 2).' MB'
            : number_format($bytes / 1024, 1).' KB';
    };
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        <label class="block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">Tên bài học</span>
            <input type="text" name="title" value="{{ old('title', $lesson->title ?? '') }}" required maxlength="255"
                   class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
        </label>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Loại</span>
                <select name="type" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 cursor-pointer">
                    @foreach($lessonTypes as $value => $label)
                        <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Thời lượng</span>
                <input type="number" name="duration" value="{{ old('duration', $lesson->duration ?? $lesson->duration_seconds ?? '') }}" min="0"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Thứ tự</span>
                <input type="number" name="sort_order" value="{{ old('sort_order', $lesson->sort_order ?? $nextSortOrder ?? '') }}" min="0"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Trạng thái</span>
                <select name="status" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 cursor-pointer">
                    @foreach($lessonStatuses as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>

    <label class="block">
        <span class="mb-1.5 block text-sm font-bold text-slate-700">Video URL</span>
        <input type="text" name="video_url" value="{{ old('video_url', $lesson->video_url ?? '') }}" placeholder="https://..."
               class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
    </label>

    <label class="block">
        <span class="mb-1.5 block text-sm font-bold text-slate-700">Video bài giảng</span>
        <input type="file" name="video_file" accept=".mp4,.mov,.avi,.webm,video/mp4,video/quicktime,video/x-msvideo,video/webm"
               class="block w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-indigo-700 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-indigo-800">
        <span class="mt-1 block text-xs font-medium text-slate-500">MP4, MOV, AVI hoặc WEBM. Tối đa 200MB.</span>
    </label>

    @if($lesson?->video_path)
        <div class="rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-2 text-sm text-indigo-900">
            <div class="font-bold">
                Video hiện tại: {{ $lesson->video_original_name ?: basename($lesson->video_path) }}
                @if($formatVideoSize($lesson->video_size))
                    <span class="font-semibold text-indigo-700">({{ $formatVideoSize($lesson->video_size) }})</span>
                @endif
            </div>
            <p class="mt-1 text-xs font-medium text-indigo-700">Upload video mới để thay thế file hiện tại.</p>
        </div>
    @endif

    <label class="block">
        <span class="mb-1.5 block text-sm font-bold text-slate-700">Nội dung dạng text</span>
        <textarea name="content" rows="4" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">{{ old('content', $lesson->content ?? '') }}</textarea>
    </label>

    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_180px] lg:items-end">
        <label class="block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">Tệp tài liệu</span>
            <input type="file" name="document_file"
                   class="block w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-900 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-slate-800">
            @if($lesson?->document_file)
                <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="mt-1 inline-block text-xs font-semibold text-sky-600 hover:underline">Tệp hiện tại</a>
            @endif
        </label>

        <label class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700">
            <input type="checkbox" name="is_preview" value="1" @checked(old('is_preview', $lesson->is_preview ?? false)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            Bài xem thử
        </label>
    </div>

    <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
        {{ $submitLabel }}
    </button>
</form>
