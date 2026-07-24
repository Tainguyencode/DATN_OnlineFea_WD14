@php
    $layout = auth()->user()?->role === 'instructor' ? 'instructor-layout' : 'student-layout';
@endphp

<x-dynamic-component :component="$layout" title="Gửi Ticket mới" page-title="Gửi Ticket mới">
    <div class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-xl font-bold text-slate-900">Gửi Ticket hỗ trợ</h2>
        <p class="mt-1 text-sm text-slate-500">Mô tả rõ vấn đề để Ban hỗ trợ xử lý nhanh hơn.</p>

        <form method="POST" action="{{ route('support.tickets.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Tiêu đề</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="w-full rounded-xl border-slate-300" required maxlength="255">
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Loại vấn đề</label>
                    <select name="category" class="w-full rounded-xl border-slate-300" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->label() }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Mức ưu tiên</label>
                    <select name="priority" class="w-full rounded-xl border-slate-300">
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->value }}" @selected(old('priority', 'medium') === $priority->value)>{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Nội dung</label>
                <textarea name="message" rows="6" class="w-full rounded-xl border-slate-300" required maxlength="5000">{{ old('message') }}</textarea>
                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Đính kèm (tối đa 5 file, mỗi file ≤ 5MB)</label>
                <input type="file" name="attachments[]" multiple class="w-full text-sm">
                @error('attachments.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700">Gửi Ticket</button>
                <a href="{{ route('support.tickets.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700">Hủy</a>
            </div>
        </form>
    </div>
</x-dynamic-component>
