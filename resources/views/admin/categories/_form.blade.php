@php
    $isEdit = $category->exists;
    $selectedParent = old('parent_id', $category->parent_id);
@endphp

@if ($errors->any())
    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
        <p class="font-bold">Vui lòng kiểm tra lại thông tin danh mục.</p>
        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-bold text-slate-700">Tên danh mục <span class="text-rose-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" maxlength="255"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                @error('name') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="parent_id" class="mb-1.5 block text-sm font-bold text-slate-700">Danh mục cha</label>
                <select id="parent_id" name="parent_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    <option value="">Không chọn - tạo danh mục cha</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" @selected((string) $selectedParent === (string) $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
                @error('parent_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="mb-1.5 block text-sm font-bold text-slate-700">Slug</label>
                <input id="slug" type="text" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="255"
                       placeholder="Tự tạo từ tên nếu để trống"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                @error('slug') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="icon" class="mb-1.5 block text-sm font-bold text-slate-700">Icon</label>
                    <input id="icon" type="text" name="icon" value="{{ old('icon', $category->icon) }}" maxlength="100"
                           placeholder="code, briefcase..."
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    @error('icon') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sort_order" class="mb-1.5 block text-sm font-bold text-slate-700">Thứ tự hiển thị</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    @error('sort_order') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="lg:col-span-2">
                <label for="description" class="mb-1.5 block text-sm font-bold text-slate-700">Mô tả</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">{{ old('description', $category->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="lg:col-span-2">
                <input type="hidden" name="status" value="0">
                <label class="inline-flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                    <input type="checkbox" name="status" value="1" @checked(old('status', $category->status ?? true))
                           class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    Bật danh mục
                </label>
            </div>
        </div>
    </section>

    <div class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">Danh mục cha chỉ dùng để nhóm. Khóa học chỉ được chọn danh mục con đang bật.</p>
        <div class="flex gap-2">
            <a href="{{ route('admin.categories.index') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                Hủy
            </a>
            <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-rose-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700">
                {{ $submitLabel }}
            </button>
        </div>
    </div>
</form>
