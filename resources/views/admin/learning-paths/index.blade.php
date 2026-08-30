<x-admin-layout title="Quản lý Lộ trình học tập" page-title="Quản lý Lộ trình học tập" breadcrumb="Danh sách lộ trình học tập trên hệ thống">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.learning-paths.index') }}" class="flex items-center gap-2 max-w-md w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm lộ trình..." class="ui-input flex-1">
                <button type="submit" class="ui-button-secondary py-2.5">Tìm</button>
            </form>

            <a href="{{ route('admin.learning-paths.create') }}" class="ui-button-primary justify-center sm:w-auto">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm Lộ trình mới
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-black">Lộ trình</th>
                            <th class="px-6 py-4 font-black">Cấp độ</th>
                            <th class="px-6 py-4 font-black">Số môn học</th>
                            <th class="px-6 py-4 font-black">Ngày tạo</th>
                            <th class="px-6 py-4 font-black text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($learningPaths as $path)
                            @php
                                $levelBadge = match($path->level) {
                                    'beginner' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
                                    'intermediate' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
                                    'advanced' => 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300',
                                    default => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300'
                                };
                                $levelLabel = match($path->level) {
                                    'beginner' => 'Cơ bản',
                                    'intermediate' => 'Trung cấp',
                                    'advanced' => 'Nâng cao',
                                    default => 'Mọi trình độ'
                                };
                            @endphp
                            <tr class="transition duration-150 hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-base">
                                        {{ $path->title }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-1 line-clamp-1 max-w-md">
                                        {{ $path->description ?? 'Chưa có mô tả' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $levelBadge }}">
                                        {{ $levelLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $path->courses_count }}</span> môn học
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                    {{ $path->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex flex-row flex-nowrap items-center justify-end gap-2">
                                        <a href="{{ route('learning-paths.show', $path->slug) }}" target="_blank" title="Xem"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.learning-paths.edit', $path) }}" title="Sửa"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] hover:bg-blue-100 dark:bg-blue-950 dark:text-blue-300">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.learning-paths.destroy', $path) }}" class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Bạn chắc chắn muốn xóa lộ trình này?')" title="Xóa"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950 dark:text-rose-300">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    Chưa có lộ trình học tập nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($learningPaths->hasPages())
                <div class="border-t border-slate-200 p-4 dark:border-slate-800">
                    {{ $learningPaths->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
