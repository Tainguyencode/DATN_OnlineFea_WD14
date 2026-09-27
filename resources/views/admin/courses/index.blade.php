<x-admin-layout title="Quản lý khóa học" page-title="Quản lý khóa học" :breadcrumb="$courses->total().' khóa học'">

@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
    $sortLabels = ['newest' => 'Mới nhất', 'oldest' => 'Cũ nhất', 'students' => 'Nhiều học viên nhất'];
@endphp

<div class="min-w-0 space-y-5">
    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach($statusLabels as $status => $label)
            <a href="{{ route('admin.courses.index', ['status' => $status]) }}"
               class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-colors duration-200 hover:border-rose-200 hover:bg-rose-50/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-200 cursor-pointer">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</span>
                <strong class="mt-2 block text-2xl font-bold text-slate-950">{{ number_format((int) ($statusCounts[$status] ?? 0)) }}</strong>
            </a>
        @endforeach
    </section>

    <form method="GET" class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid min-w-0 gap-3 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-[minmax(220px,1.2fr)_repeat(5,minmax(150px,1fr))_auto]">
            <label class="sr-only" for="course-search">Tìm khóa học</label>
            <input id="course-search" type="text" name="search" value="{{ $filters['search'] }}"
                   placeholder="Tìm kiếm theo tên khóa học hoặc mã slug..."
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
                        <th class="rounded-l-lg px-4 py-2.5 text-left font-semibold text-slate-600">Thumbnail</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-slate-600">Tên khóa học</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-slate-600">Giảng viên</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-slate-600">Danh mục</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-slate-600">Giá</th>
                        <th class="px-4 py-2.5 text-center font-semibold text-slate-600">Học viên</th>
                        <th class="px-4 py-2.5 text-center font-semibold text-slate-600">Chương</th>
                        <th class="px-4 py-2.5 text-center font-semibold text-slate-600">Bài học</th>
                        <th class="px-4 py-2.5 text-center font-semibold text-slate-600 whitespace-nowrap">Trạng thái</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-slate-600">Ngày tạo</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-slate-600">Xuất bản</th>
                        <th class="rounded-r-lg px-4 py-2.5 text-right font-semibold text-slate-600 w-[64px] min-w-[64px] whitespace-nowrap">Thao tác</th>
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
                            <td class="px-4 py-2 align-middle">
                                <div class="h-10 w-16 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-rose-700 text-[10px] font-bold text-white">EP</div>
                                    @endif
                                </div>
                            </td>
                            <td class="max-w-xs px-4 py-2 align-middle">
                                <div class="truncate font-bold text-slate-950">{{ $course->title }}</div>
                                <div class="mt-0.5 truncate text-[11px] text-slate-500">{{ $course->slug }}</div>
                            </td>
                            <td class="px-4 py-2 align-middle">
                                <div class="max-w-[150px] truncate font-semibold text-slate-800">{{ $course->instructor?->name ?? 'Chưa gán' }}</div>
                                <div class="max-w-[150px] truncate text-[11px] text-slate-500">{{ $course->instructor?->email }}</div>
                            </td>
                            <td class="px-4 py-2 align-middle text-slate-600 max-w-[150px] truncate" title="{{ $course->category?->full_name ?? 'Chưa chọn' }}">{{ $course->category?->full_name ?? 'Chưa chọn' }}</td>
                            <td class="whitespace-nowrap px-4 py-2 align-middle font-semibold text-slate-900">{{ $formatPrice($price) }}</td>
                            <td class="px-4 py-2 text-center align-middle font-semibold text-slate-900">{{ number_format((int) $course->active_enrollments_count) }}</td>
                            <td class="px-4 py-2 text-center align-middle text-slate-700">{{ $sectionsCount }}</td>
                            <td class="px-4 py-2 text-center align-middle text-slate-700">{{ $lessonCount }}</td>
                            <td class="px-4 py-2 align-middle text-center whitespace-nowrap">
                                @php
                                    $mappedStatus = match($course->status) {
                                        'draft' => 'status-inactive',
                                        'submitted' => 'status-pending',
                                        'approved' => 'status-active',
                                        'published' => 'status-active',
                                        'rejected' => 'status-danger',
                                        'archived' => 'status-disabled',
                                        default => 'status-info'
                                    };
                                @endphp
                                <span class="status-badge {{ $mappedStatus }}">{{ $statusLabels[$course->status] ?? $course->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-2 align-middle text-slate-500">{{ $course->created_at?->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-2 align-middle text-slate-500">{{ $course->published_at?->format('d/m/Y') ?? 'Chưa có' }}</td>
                            <td class="px-4 py-2 align-middle text-right w-[64px] min-w-[64px] whitespace-nowrap">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" @click.outside="open = false" type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none cursor-pointer">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 z-50 mt-1 w-44 origin-top-right rounded-xl border border-slate-200 bg-white p-1 shadow-lg ring-1 ring-black/5 focus:outline-none">
                                         
                                         <!-- Chi tiết -->
                                         <a href="{{ route('admin.courses.show', $course) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                                             <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                             </svg>
                                             Chi tiết
                                         </a>
                                         
                                         <!-- Học viên -->
                                         <a href="{{ route('admin.courses.students', $course) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                                             <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                             </svg>
                                             Học viên
                                         </a>
                                         
                                         {{-- Submitted: Kiểm duyệt --}}
                                         @if($course->status === \App\Models\Course::STATUS_SUBMITTED)
                                             <a href="{{ route('admin.courses.review', $course) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50 transition cursor-pointer">
                                                 <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                 </svg>
                                                 Kiểm duyệt
                                             </a>
                                         @endif
                                         
                                         {{-- Approved: Xuất bản --}}
                                         @if($course->status === \App\Models\Course::STATUS_APPROVED)
                                             <button type="button" onclick="if(confirm('Xuất bản khóa học này? Học viên sẽ thấy ngay.')) { document.getElementById('publish-form-{{ $course->id }}').submit(); }" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50 transition text-left cursor-pointer">
                                                 <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                 </svg>
                                                 Xuất bản
                                             </button>
                                         @endif
                                         
                                         {{-- Published: Ẩn --}}
                                         @if($course->status === \App\Models\Course::STATUS_PUBLISHED)
                                             <button type="button" onclick="if(confirm('Ẩn/lưu trữ khóa học này?')) { document.getElementById('archive-form-{{ $course->id }}').submit(); }" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50 transition text-left cursor-pointer">
                                                 <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                 </svg>
                                                 Ẩn khóa học
                                             </button>
                                         @endif
                                         
                                         {{-- Archived: Khôi phục --}}
                                         @if($course->status === \App\Models\Course::STATUS_ARCHIVED)
                                             <button type="button" onclick="if(confirm('Khôi phục khóa học này?')) { document.getElementById('restore-form-{{ $course->id }}').submit(); }" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50 transition text-left cursor-pointer">
                                                 <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7H19"/>
                                                 </svg>
                                                 Khôi phục
                                             </button>
                                         @endif
                                         
                                         {{-- Nổi bật --}}
                                         <button type="button" onclick="document.getElementById('featured-form-{{ $course->id }}').submit();" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition text-left cursor-pointer">
                                             @if($course->is_featured)
                                                 <span class="text-amber-500">⭐</span> Tắt nổi bật
                                             @else
                                                 <span class="text-slate-400">☆</span> Bật nổi bật
                                             @endif
                                         </button>
                                     </div>
                                     
                                     {{-- Hidden forms for action --}}
                                     @if($course->status === \App\Models\Course::STATUS_APPROVED)
                                         <form id="publish-form-{{ $course->id }}" method="POST" action="{{ route('admin.courses.publish', $course) }}" class="hidden">
                                             @csrf
                                         </form>
                                     @endif
                                     @if($course->status === \App\Models\Course::STATUS_PUBLISHED)
                                         <form id="archive-form-{{ $course->id }}" method="POST" action="{{ route('admin.courses.archive', $course) }}" class="hidden">
                                             @csrf
                                         </form>
                                     @endif
                                     @if($course->status === \App\Models\Course::STATUS_ARCHIVED)
                                         <form id="restore-form-{{ $course->id }}" method="POST" action="{{ route('admin.courses.restore', $course) }}" class="hidden">
                                             @csrf
                                         </form>
                                     @endif
                                     <form id="featured-form-{{ $course->id }}" method="POST" action="{{ route('admin.courses.toggle-featured', $course) }}" class="hidden">
                                         @csrf
                                     </form>
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
