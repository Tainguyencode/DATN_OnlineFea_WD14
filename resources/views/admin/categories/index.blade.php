<x-admin-layout title="Danh mục khóa học" page-title="Danh mục khóa học" :breadcrumb="$stats['total'].' danh mục'">

<div class="space-y-5">
    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Tổng danh mục</span>
            <strong class="mt-2 block text-2xl font-bold text-slate-950">{{ number_format($stats['total']) }}</strong>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Danh mục cha</span>
            <strong class="mt-2 block text-2xl font-bold text-slate-950">{{ number_format($stats['parents']) }}</strong>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Danh mục con</span>
            <strong class="mt-2 block text-2xl font-bold text-slate-950">{{ number_format($stats['children']) }}</strong>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Đang bật</span>
            <strong class="mt-2 block text-2xl font-bold text-slate-950">{{ number_format($stats['active']) }}</strong>
        </div>
    </section>

    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" class="flex-1 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 lg:grid-cols-[minmax(220px,1.2fr)_minmax(160px,.7fr)_minmax(160px,.7fr)_auto]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm theo tên hoặc slug..."
                       class="h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100">

                <select name="parent" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                    <option value="">Tất cả cấp</option>
                    <option value="root" @selected($parent === 'root')>Chỉ danh mục cha</option>
                    @foreach($parents as $parentOption)
                        <option value="{{ $parentOption->id }}" @selected((string) $parent === (string) $parentOption->id)>{{ $parentOption->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected($status === 'active')>Đang bật</option>
                    <option value="inactive" @selected($status === 'inactive')>Đang tắt</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-rose-600 px-4 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700">Lọc</button>
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-200 px-3 text-sm font-bold text-slate-600 transition-colors duration-200 hover:bg-slate-50">Xóa</a>
                </div>
            </div>
        </form>

        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex min-h-11 items-center justify-center rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800">
            Thêm danh mục
        </a>
    </div>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto p-3 sm:p-4">
            <table class="w-full min-w-[960px] text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="rounded-l-lg px-4 py-3 text-left font-semibold text-slate-600">Danh mục</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Slug</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Cấp</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Danh mục con</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Khóa học</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Thứ tự</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Trạng thái</th>
                        <th class="rounded-r-lg px-4 py-3 text-right font-semibold text-slate-600">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="transition-colors duration-150 hover:bg-slate-50/80">
                            <td class="px-4 py-3 align-middle">
                                <div class="font-bold text-slate-950">
                                    @if($category->parent_id)
                                        <span class="mr-1 text-slate-400">↳</span>
                                    @endif
                                    {{ $category->name }}
                                </div>
                                @if($category->parent)
                                    <div class="mt-1 text-xs text-slate-500">{{ $category->parent->name }} -> {{ $category->name }}</div>
                                @elseif($category->description)
                                    <div class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $category->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle text-slate-600">{{ $category->slug }}</td>
                            <td class="px-4 py-3 align-middle">
                                <span class="inline-block whitespace-nowrap rounded-full border border-slate-200 px-2.5 py-1 text-xs font-bold text-slate-600">
                                    {{ $category->parent_id ? 'Danh mục con' : 'Danh mục cha' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center align-middle font-semibold text-slate-900">{{ number_format((int) $category->children_count) }}</td>
                            <td class="px-4 py-3 text-center align-middle font-semibold text-slate-900">{{ number_format((int) $category->courses_count) }}</td>
                            <td class="px-4 py-3 text-center align-middle text-slate-700">{{ $category->sort_order }}</td>
                            <td class="px-4 py-3 align-middle">
                                @if($category->status)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">Đang bật</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">Đang tắt</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex h-8 items-center rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">Sửa</a>
                                    <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}" class="inline-flex">
                                        @csrf
                                        <button type="submit" class="inline-flex h-8 items-center rounded-lg border border-amber-100 bg-amber-50 px-3 text-xs font-bold text-amber-700 transition-colors duration-200 hover:bg-amber-100">
                                            {{ $category->status ? 'Tắt' : 'Bật' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline-flex" onsubmit="return confirm('Xóa danh mục này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 items-center rounded-lg border border-rose-100 bg-rose-50 px-3 text-xs font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-100">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-14 text-center">
                                <h3 class="text-base font-bold text-slate-950">Chưa có danh mục phù hợp</h3>
                                <p class="mt-1 text-sm text-slate-500">Thêm danh mục cha và danh mục con để giảng viên chọn khi tạo khóa học.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 bg-slate-50/40 px-5 py-4">{{ $categories->links() }}</div>
    </section>
</div>

</x-admin-layout>
