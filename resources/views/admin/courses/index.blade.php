<x-admin-layout title="Quản lý khóa học" page-title="Quản lý khóa học" :breadcrumb="$courses->total().' khóa học'">

@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
    $sortLabels = ['newest' => 'Mới nhất', 'oldest' => 'Cũ nhất', 'students' => 'Nhiều học viên nhất'];
@endphp

<div class="space-y-5">
    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach($statusLabels as $status => $label)
            <a href="{{ route('admin.courses.index', ['status' => $status]) }}"
               class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-colors duration-200 hover:border-rose-200 hover:bg-rose-50/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-200 cursor-pointer">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</span>
                <strong class="mt-2 block text-2xl font-bold text-slate-950">{{ number_format((int) ($statusCounts[$status] ?? 0)) }}</strong>
            </a>
        @endforeach
    </section>

    <form method="GET" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 lg:grid-cols-[minmax(220px,1.2fr)_repeat(5,minmax(150px,1fr))_auto]">
            <label class="sr-only" for="course-search">Tìm khóa học</label>
            <input id="course-search" type="text" name="search" value="{{ $filters['search'] }}"
                   placeholder="Tìm theo tên hoặc slug..."
                   class="h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100">

            <label class="sr-only" for="course-instructor">Giảng viên</label>
            <select id="course-instructor" name="instructor" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                <option value="">Tất cả giảng viên</option>
                @foreach($instructors as $instructor)
                    <option value="{{ $instructor->id }}" @selected((string) $filters['instructorId'] === (string) $instructor->id)>{{ $instructor->name }}</option>
                @endforeach
            </select>

            <label class="sr-only" for="course-status">Trạng thái</label>
            <select id="course-status" name="status" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                <option value="">Tất cả trạng thái</option>
                @foreach($statusLabels as $status => $label)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $label }}</option>
                @endforeach
            </select>

            <label class="sr-only" for="course-category">Danh mục</label>
            <select id="course-category" name="category" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $filters['categoryId'] === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <label class="sr-only" for="course-pricing">Hình thức phí</label>
            <select id="course-pricing" name="pricing" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                <option value="">Miễn phí/trả phí</option>
                <option value="free" @selected($filters['pricing'] === 'free')>Miễn phí</option>
                <option value="paid" @selected($filters['pricing'] === 'paid')>Trả phí</option>
            </select>

            <label class="sr-only" for="course-sort">Sắp xếp</label>
            <select id="course-sort" name="sort" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                @foreach($sortLabels as $value => $label)
                    <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-rose-200 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .8 1.6L14 13.667V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 8 17v-3.333L3.2 4.6A1 1 0 0 1 3 4Z"/></svg>
                    Lọc
                </button>
                <a href="{{ route('admin.courses.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-200 px-3 text-sm font-bold text-slate-600 transition-colors duration-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 cursor-pointer">Xóa</a>
            </div>
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto p-3 sm:p-4">
            <table class="w-full min-w-[1280px] text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="rounded-l-lg px-4 py-3 text-left font-semibold text-slate-600">Thumbnail</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Tên khóa học</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Giảng viên</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Danh mục</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Giá</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Học viên</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Chương</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Bài học</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Trạng thái</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Ngày tạo</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Xuất bản</th>
                        <th class="rounded-r-lg px-4 py-3 text-right font-semibold text-slate-600">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($courses as $course)
                        @php
                            $price = $course->discount_price ?? $course->sale_price ?? $course->price;
                            $curriculumSections = $course->courseSections->isNotEmpty() ? $course->courseSections : $course->chapters;
                            $sectionsCount = $curriculumSections->count();
                            $lessonCount = $curriculumSections->sum(fn ($section) => $section->lessons->count());
                            $statusClass = $statusBadgeClasses[$course->status] ?? 'bg-slate-50 text-slate-700 ring-1 ring-slate-200';
                        @endphp
                        <tr class="transition-colors duration-150 hover:bg-slate-50/80">
                            <td class="px-4 py-3 align-middle">
                                <div class="h-14 w-24 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-rose-700 text-xs font-bold text-white">EP</div>
                                    @endif
                                </div>
                            </td>
                            <td class="max-w-xs px-4 py-3 align-middle">
                                <div class="truncate font-bold text-slate-950">{{ $course->title }}</div>
                                <div class="mt-1 truncate text-xs text-slate-500">{{ $course->slug }}</div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="max-w-[180px] truncate font-semibold text-slate-800">{{ $course->instructor?->name ?? 'Chưa gán' }}</div>
                                <div class="max-w-[180px] truncate text-xs text-slate-500">{{ $course->instructor?->email }}</div>
                            </td>
                            <td class="px-4 py-3 align-middle text-slate-600">{{ $course->category?->name ?? 'Chưa chọn' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle font-semibold text-slate-900">{{ $formatPrice($price) }}</td>
                            <td class="px-4 py-3 text-center align-middle font-semibold text-slate-900">{{ number_format((int) $course->active_enrollments_count) }}</td>
                            <td class="px-4 py-3 text-center align-middle text-slate-700">{{ $sectionsCount }}</td>
                            <td class="px-4 py-3 text-center align-middle text-slate-700">{{ $lessonCount }}</td>
                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabels[$course->status] ?? $course->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle text-slate-500">{{ $course->created_at?->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle text-slate-500">{{ $course->published_at?->format('d/m/Y') ?? 'Chưa có' }}</td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('admin.courses.show', $course) }}" class="inline-flex h-8 items-center rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 cursor-pointer">Chi tiết</a>
                                    <a href="{{ route('admin.courses.students', $course) }}" class="inline-flex h-8 items-center rounded-lg border border-indigo-100 bg-indigo-50 px-3 text-xs font-bold text-indigo-700 transition-colors duration-200 hover:bg-indigo-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-200 cursor-pointer">Học viên</a>

                                    {{-- Submitted: nút đi vào trang kiểm duyệt đầy đủ --}}
                                    @if($course->status === \App\Models\Course::STATUS_SUBMITTED)
                                        <a href="{{ route('admin.courses.review', $course) }}"
                                           class="inline-flex h-8 items-center rounded-lg bg-amber-500 px-3 text-xs font-bold text-white transition-colors duration-200 hover:bg-amber-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-300 cursor-pointer">
                                            Kiểm duyệt
                                        </a>
                                    @endif

                                    {{-- Approved: nút Xuất bản --}}
                                    @if($course->status === \App\Models\Course::STATUS_APPROVED)
                                        <form method="POST" action="{{ route('admin.courses.publish', $course) }}" class="inline-flex" onsubmit="return confirm('Xuất bản khóa học này? Học viên sẽ thấy ngay.')">
                                            @csrf
                                            <button type="submit" class="inline-flex h-8 items-center rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 cursor-pointer">
                                                Xuất bản
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Published: nút Ẩn --}}
                                    @if($course->status === \App\Models\Course::STATUS_PUBLISHED)
                                        <form method="POST" action="{{ route('admin.courses.archive', $course) }}" class="inline-flex" onsubmit="return confirm('Ẩn/lưu trữ khóa học này?')">
                                            @csrf
                                            <button type="submit" class="inline-flex h-8 items-center rounded-lg border border-amber-100 bg-amber-50 px-3 text-xs font-bold text-amber-700 transition-colors duration-200 hover:bg-amber-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-200 cursor-pointer">Ẩn</button>
                                        </form>
                                    @endif

                                    {{-- Archived: nút Khôi phục --}}
                                    @if($course->status === \App\Models\Course::STATUS_ARCHIVED)
                                        <form method="POST" action="{{ route('admin.courses.restore', $course) }}" class="inline-flex" onsubmit="return confirm('Khôi phục khóa học này?')">
                                            @csrf
                                            <button type="submit" class="inline-flex h-8 items-center rounded-lg border border-emerald-100 bg-emerald-50 px-3 text-xs font-bold text-emerald-700 transition-colors duration-200 hover:bg-emerald-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-200 cursor-pointer">Khôi phục</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-14 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-lg bg-slate-50 text-slate-400">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <h3 class="mt-4 text-base font-bold text-slate-950">Không tìm thấy khóa học</h3>
                                <p class="mt-1 text-sm text-slate-500">Thử đổi bộ lọc hoặc xóa điều kiện tìm kiếm.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 bg-slate-50/40 px-5 py-4">{{ $courses->links() }}</div>
    </section>
</div>

</x-admin-layout>
