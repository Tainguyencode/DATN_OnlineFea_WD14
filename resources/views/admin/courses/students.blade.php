<x-admin-layout :title="'Học viên - '.$course->title" page-title="Học viên khóa học" :breadcrumb="$course->title">

<div class="space-y-5">
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $course->category?->name ?? 'Chưa chọn danh mục' }}</span>
                    <span class="rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $enrollments->total() }} học viên</span>
                </div>
                <h2 class="mt-3 truncate text-2xl font-bold text-slate-950">{{ $course->title }}</h2>
                <p class="mt-2 text-sm text-slate-500">Giảng viên: {{ $course->instructor?->name ?? 'Chưa gán' }} · {{ $course->instructor?->email }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.courses.show', $course) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 cursor-pointer">Chi tiết khóa học</a>
                <a href="{{ route('admin.courses.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 cursor-pointer">Danh sách khóa học</a>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto p-3 sm:p-4">
            <table class="w-full min-w-[960px] text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="rounded-l-lg px-4 py-3 text-left font-semibold text-slate-600">Học viên</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Ngày đăng ký</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Trạng thái</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Tiến độ</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Hoàn thành</th>
                        <th class="rounded-r-lg px-4 py-3 text-right font-semibold text-slate-600">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($enrollments as $enrollment)
                        @php
                            $completedLessons = (int) ($completedLessonCounts[$enrollment->user_id] ?? 0);
                            $derivedProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 0;
                            $progress = max((float) ($enrollment->progress_percent ?? 0), $derivedProgress);
                            $statusClasses = [
                                'active' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100',
                                'completed' => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100',
                                'cancelled' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-100',
                            ];
                            $statusLabels = ['active' => 'Đang học', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
                        @endphp
                        <tr class="transition-colors duration-150 hover:bg-slate-50/80">
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        {{ strtoupper(substr($enrollment->user?->name ?? 'H', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="max-w-[220px] truncate font-bold text-slate-950">{{ $enrollment->user?->name ?? 'Không rõ học viên' }}</div>
                                        <div class="text-xs text-slate-500">ID #{{ $enrollment->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="max-w-[240px] truncate px-4 py-3 align-middle text-slate-600">{{ $enrollment->user?->email ?? 'Chưa có email' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle text-slate-600">{{ ($enrollment->enrolled_at ?? $enrollment->created_at)?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClasses[$enrollment->status] ?? 'bg-slate-50 text-slate-700 ring-1 ring-slate-200' }}">
                                    {{ $statusLabels[$enrollment->status] ?? $enrollment->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="min-w-44">
                                    <div class="flex items-center justify-between gap-2 text-xs font-bold text-slate-600">
                                        <span>{{ number_format($progress, $progress == (int) $progress ? 0 : 1) }}%</span>
                                        <span>{{ $totalLessons > 0 ? $completedLessons.'/'.$totalLessons.' bài' : 'Chưa có dữ liệu' }}</span>
                                    </div>
                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-rose-500" style="width: {{ min(100, $progress) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle text-slate-600">{{ $enrollment->completed_at?->format('d/m/Y H:i') ?? 'Chưa hoàn thành' }}</td>
                            <td class="px-4 py-3 align-middle text-right">
                                @if(Route::has('admin.users.show'))
                                    <a href="{{ route('admin.users.show', $enrollment->user) }}" class="inline-flex h-8 items-center rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 cursor-pointer">Xem user</a>
                                @else
                                    <a href="{{ route('admin.users', ['search' => $enrollment->user?->email]) }}" class="inline-flex h-8 items-center rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 cursor-pointer">Tìm user</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-14 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-lg bg-slate-50 text-slate-400">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a4 4 0 0 0-4-4h-1M9 20H4v-2a4 4 0 0 1 4-4h1m0-4a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 0a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
                                </div>
                                <h3 class="mt-4 text-base font-bold text-slate-950">Chưa có học viên đăng ký</h3>
                                <p class="mt-1 text-sm text-slate-500">Khi học viên đăng ký khóa học, danh sách sẽ xuất hiện tại đây.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 bg-slate-50/40 px-5 py-4">{{ $enrollments->links() }}</div>
    </section>
</div>

</x-admin-layout>
