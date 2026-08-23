<x-instructor-layout title="Quản lý Lộ trình học tập - FEA Instructor">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">Quản lý Lộ trình học tập</h1>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Xây dựng lộ trình học tập theo các giai đoạn cho các khóa học do bạn giảng dạy</p>
            </div>
            <a href="{{ route('instructor.learning-paths.create') }}" class="ui-button-primary inline-flex items-center gap-2">
                <span>+ Tạo Lộ trình mới</span>
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-bold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                ✔ {{ session('success') }}
            </div>
        @endif

        {{-- Filter & Search --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-[#161615]">
            <form method="GET" action="{{ route('instructor.learning-paths.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm lộ trình của bạn..."
                        class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-3 pr-10 text-xs text-slate-900 focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                    <button type="submit" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                        🔍
                    </button>
                </div>

                @if(request('search'))
                    <a href="{{ route('instructor.learning-paths.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                        ✕ Xóa bộ lọc
                    </a>
                @endif
            </form>
        </div>

        {{-- Learning Paths Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-[#161615]">
            @if($learningPaths->isEmpty())
                <div class="p-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 font-bold text-lg dark:bg-indigo-950 dark:text-indigo-300">
                        🎯
                    </div>
                    <h3 class="mt-4 text-sm font-bold text-slate-800 dark:text-slate-200">Bạn chưa tạo Lộ trình học tập nào</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Hãy kết nối các khóa học của bạn thành một lộ trình bài bản giúp học viên dễ dàng định hướng phát triển sự nghiệp.</p>
                    <a href="{{ route('instructor.learning-paths.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-indigo-700">
                        + Tạo Lộ trình đầu tiên
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-400">
                            <tr>
                                <th class="px-5 py-3.5">Lộ trình học tập</th>
                                <th class="px-5 py-3.5">Trình độ</th>
                                <th class="px-5 py-3.5">Vị trí việc làm</th>
                                <th class="px-5 py-3.5 text-center">Môn học</th>
                                <th class="px-5 py-3.5 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($learningPaths as $path)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-extrabold text-sm text-slate-900 dark:text-white hover:text-indigo-600 transition">
                                            <a href="{{ route('learning-paths.show', $path->slug) }}" target="_blank">
                                                {{ $path->title }}
                                            </a>
                                        </div>
                                        <div class="mt-0.5 text-slate-500 dark:text-slate-400 truncate max-w-xs">
                                            {{ $path->description ? Str::limit($path->description, 60) : 'Chưa có mô tả' }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @php
                                            $levelBadge = match($path->level) {
                                                'beginner' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'intermediate' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'advanced' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                default => 'bg-slate-50 text-slate-700 border-slate-200'
                                            };
                                        @endphp
                                        <span class="inline-flex rounded-md border px-2.5 py-1 text-[11px] font-bold {{ $levelBadge }}">
                                            {{ ucfirst($path->level) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $path->target_role ?: 'Chưa thiết lập' }}</div>
                                    </td>

                                    <td class="px-5 py-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs">
                                            {{ $path->courses_count }} môn
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('learning-paths.show', $path->slug) }}" target="_blank" class="rounded-lg bg-slate-100 px-2.5 py-1.5 font-bold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300">
                                                Xem
                                            </a>
                                            <a href="{{ route('instructor.learning-paths.edit', $path) }}" class="rounded-lg bg-indigo-50 px-2.5 py-1.5 font-bold text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300">
                                                Sửa
                                            </a>
                                            <form method="POST" action="{{ route('instructor.learning-paths.destroy', $path) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa lộ trình học tập này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg bg-rose-50 px-2.5 py-1.5 font-bold text-rose-600 hover:bg-rose-100 dark:bg-rose-950 dark:text-rose-300">
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $learningPaths->links() }}
                </div>
            @endif
        </div>
    </div>
</x-instructor-layout>
