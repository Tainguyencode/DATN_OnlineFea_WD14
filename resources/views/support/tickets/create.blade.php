@php
    $layout = auth()->user()?->role === 'instructor' ? 'instructor-layout' : 'student-layout';
@endphp

<x-dynamic-component :component="$layout" title="Gửi Ticket mới" page-title="Gửi Ticket mới">
    <div class="support-ticket-page w-full rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 xl:p-7">
        <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">Gửi Ticket hỗ trợ</h2>
        <p class="mt-1 text-sm text-slate-500">Mô tả rõ vấn đề để Ban hỗ trợ xử lý nhanh hơn.</p>

        <form method="POST" action="{{ route('support.tickets.store') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
            @csrf
            <div>
                <label for="ticket-subject" class="mb-2 block text-sm font-semibold text-slate-800">Tiêu đề</label>
                <input id="ticket-subject" type="text" name="subject" value="{{ old('subject') }}" placeholder="Nhập tiêu đề ticket..." class="support-ticket-control" required maxlength="255">
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="ticket-category" class="mb-2 block text-sm font-semibold text-slate-800">Loại vấn đề</label>
                    <select id="ticket-category" name="category" class="support-ticket-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->label() }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="ticket-priority" class="mb-2 block text-sm font-semibold text-slate-800">Mức ưu tiên</label>
                    <select id="ticket-priority" name="priority" class="support-ticket-control">
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->value }}" @selected(old('priority', 'medium') === $priority->value)>{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                    @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="ticket-message" class="mb-2 block text-sm font-semibold text-slate-800">Nội dung</label>
                <textarea id="ticket-message" name="message" rows="8" placeholder="Mô tả chi tiết vấn đề bạn đang gặp..." class="support-ticket-control" required maxlength="5000">{{ old('message') }}</textarea>
                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="ticket-attachments" class="mb-2 block text-sm font-semibold text-slate-800">Đính kèm (tối đa 5 file, mỗi file ≤ 5MB)</label>
                <input id="ticket-attachments" type="file" name="attachments[]" multiple class="support-ticket-control">
                @error('attachments.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-1">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Gửi Ticket</button>
                <a href="{{ route('support.tickets.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-500">Hủy</a>
            </div>
        </form>
    </div>
</x-dynamic-component>
