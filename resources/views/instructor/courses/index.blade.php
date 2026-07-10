<x-instructor-layout title="Khóa học của tôi" page-title="Khóa học của tôi"
    breadcrumb="Quản lý danh mục khóa học, bản nháp và trạng thái duyệt">

    @php
        $statusStyles = [
            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
            'submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
            'need_revision' => 'bg-orange-50 text-orange-700 border-orange-200',
            'approved' => 'bg-sky-50 text-sky-700 border-sky-200',
            'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
            'archived' => 'bg-zinc-100 text-zinc-700 border-zinc-200',
        ];

        $levelLabels = [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
        ];

        $formatPrice = fn($value) => (float) $value <= 0
            ? 'Miễn phí'
            : number_format((float) $value, 0, ',', '.') . 'đ';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Instructor course studio</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Khóa học của tôi</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Tạo bản nháp, cập nhật thông tin bán hàng, theo dõi trạng thái duyệt và quản lý nội dung học tập.
                </p>
            </div>

            <a href="{{ route('instructor.courses.create') }}"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tạo khóa học
            </a>
        </div>

        <form method="GET" action="{{ route('instructor.courses.index') }}"
            class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px_auto]">
                <label class="block">
                    <span class="mb-1.5 block text-sm font-semibold text-slate-700">Tìm kiếm khóa học</span>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>
                        <input type="search" name="search" value="{{ $search }}"
                            placeholder="Nhập tên khóa học, mô tả..."
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
                    </div>
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-sm font-semibold text-slate-700">Trạng thái</span>
                    <select name="status"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20 cursor-pointer">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="inline-flex min-h-10 items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 focus-visible:ring-offset-2 cursor-pointer">
                        Lọc
                    </button>
                    <a href="{{ route('instructor.courses.index') }}"
                        class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 cursor-pointer">
                        Xóa lọc
                    </a>
                </div>
            </div>
        </form>

        @if ($errors->has('submission'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-bold">Không thể gửi duyệt khóa học</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->get('submission') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($courses->isEmpty())
            <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-950">Chưa có khóa học nào</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    Bắt đầu bằng một bản nháp khóa học. Bạn có thể bổ sung thumbnail, giá bán và nội dung bài học trước
                    khi gửi duyệt.
                </p>
                <a href="{{ route('instructor.courses.create') }}"
                    class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tạo khóa học đầu tiên
                </a>
            </div>
        @else
            <div class="hidden overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead
                            class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-4">Khóa học</th>
                                <th class="px-5 py-4">Trạng thái</th>
                                <th class="px-5 py-4">Giá</th>
                                <th class="px-5 py-4">Học viên</th>
                                <th class="px-5 py-4">Ngày tạo</th>
                                <th class="px-5 py-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($courses as $course)
                                @php
                                    $statusClass = $statusStyles[$course->status] ?? $statusStyles['draft'];
                                    $discountPrice = $course->discount_price ?? $course->sale_price;
                                    $check = $submissionChecks[$course->id] ?? null;
                                    $canSubmit = $course->canBeSubmittedForReview();
                                    $isReady = $check?->passes() ?? false;
                                @endphp
                                <tr class="align-middle transition-colors duration-200 hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="h-16 w-24 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                                                @if ($course->thumbnail)
                                                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                                        alt="{{ $course->title }}" class="h-full w-full object-cover">
                                                @else
                                                    <div
                                                        class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-800 to-emerald-700 text-xs font-bold text-white">
                                                        Fea</div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate font-bold text-slate-950">{{ $course->title }}</p>
                                                <p class="mt-1 truncate text-xs text-slate-500">
                                                    {{ $course->category?->name ?? 'Chưa chọn danh mục' }} ·
                                                    {{ $levelLabels[$course->level] ?? 'Chưa chọn trình độ' }}</p>
                                                @if (in_array($course->status, ['rejected', 'need_revision'], true) && $course->rejectionReasonText())
                                                    <p class="mt-2 line-clamp-2 text-xs font-semibold text-rose-600">Lý
                                                        do: {{ $course->rejectionReasonText() }}</p>
                                                @endif
                                                @if (in_array($course->status, ['rejected', 'need_revision'], true))
                                                    <a href="{{ route('instructor.courses.edit', $course) }}#ai-moderation-results"
                                                       class="mt-1 inline-flex text-xs font-bold text-indigo-700 transition-colors hover:text-indigo-900">
                                                        Xem kết quả kiểm duyệt AI →
                                                    </a>
                                                @endif
                                                @if ($canSubmit && $check && ! $isReady)
                                                    <ul class="mt-2 space-y-0.5 text-xs text-amber-700">
                                                        @foreach ($check->errorMessages() as $message)
                                                            <li>• {{ $message }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass }}">
                                            {{ $statusOptions[$course->status] ?? $course->status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900">
                                            {{ $formatPrice($discountPrice ?? $course->price) }}</div>
                                        @if ($discountPrice)
                                            <div class="text-xs text-slate-400 line-through">
                                                {{ $formatPrice($course->price) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $course->enrollments_count ?? 0 }}</td>
                                    <td class="px-5 py-4 text-slate-500">{{ $course->created_at?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('instructor.courses.edit', $course) }}"
                                                class="rounded-lg px-3 py-2 text-xs font-bold text-emerald-700 transition-colors duration-200 hover:bg-emerald-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">Sửa</a>
                                            <a href="{{ route('instructor.courses.curriculum', $course) }}"
                                                class="rounded-lg px-3 py-2 text-xs font-bold text-indigo-700 transition-colors duration-200 hover:bg-indigo-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 cursor-pointer">Quản
                                                lý nội dung</a>
                                            @if ($course->status === 'published')
                                                <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
                                                    class="rounded-lg px-3 py-2 text-xs font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 cursor-pointer">Xem
                                                    trước</a>
                                            @else
                                                <span class="rounded-lg px-3 py-2 text-xs font-bold text-slate-400"
                                                    title="Chỉ xem trước công khai sau khi khóa học được xuất bản">Xem
                                                    trước</span>
                                            @endif
                                            @if ($canSubmit && $isReady)
                                                <form method="POST"
                                                    action="{{ route('instructor.courses.submit', $course) }}"
                                                    onsubmit="return confirm('Gửi khóa học này cho admin duyệt?')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="rounded-lg px-3 py-2 text-xs font-bold text-amber-700 transition-colors duration-200 hover:bg-amber-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 cursor-pointer">
                                                        {{ in_array($course->status, ['need_revision', 'rejected'], true) ? 'Gửi lại duyệt' : 'Gửi duyệt' }}
                                                    </button>
                                                </form>
                                            @elseif ($canSubmit)
                                                <span class="rounded-lg px-3 py-2 text-xs font-bold text-slate-400"
                                                    title="Hoàn thiện điều kiện gửi duyệt trước">Chưa đủ điều kiện</span>
                                            @endif
                                            @if ($course->status === 'published')
                                                <form method="POST"
                                                    action="{{ route('instructor.courses.archive', $course) }}"
                                                    onsubmit="return confirm('Ẩn khóa học này khỏi trang học viên?')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="rounded-lg px-3 py-2 text-xs font-bold text-zinc-700 transition-colors duration-200 hover:bg-zinc-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-500 cursor-pointer">
                                                        Ẩn khóa học
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST"
                                                action="{{ route('instructor.courses.destroy', $course) }}"
                                                onsubmit="return confirm('Bạn chắc chắn muốn xóa hoặc lưu trữ khóa học này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg px-3 py-2 text-xs font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 cursor-pointer">
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
            </div>

            <div class="space-y-4 md:hidden">
                @foreach ($courses as $course)
                    @php
                        $statusClass = $statusStyles[$course->status] ?? $statusStyles['draft'];
                        $discountPrice = $course->discount_price ?? $course->sale_price;
                        $check = $submissionChecks[$course->id] ?? null;
                        $canSubmit = $course->canBeSubmittedForReview();
                        $isReady = $check?->passes() ?? false;
                    @endphp
                    <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div class="aspect-video bg-slate-100">
                            @if ($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-800 to-emerald-700 text-sm font-bold text-white">
                                    Fea LMS</div>
                            @endif
                        </div>
                        <div class="space-y-4 p-4">
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-bold leading-6 text-slate-950">{{ $course->title }}</h3>
                                    <span
                                        class="shrink-0 rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass }}">
                                        {{ $statusOptions[$course->status] ?? $course->status }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $course->category?->name ?? 'Chưa chọn danh mục' }} ·
                                    {{ $course->created_at?->format('d/m/Y') }}</p>
                                @if (in_array($course->status, ['rejected', 'need_revision'], true) && $course->rejectionReasonText())
                                    <p
                                        class="mt-2 rounded-lg bg-rose-50 p-3 text-xs font-semibold leading-5 text-rose-700">
                                        Lý do: {{ $course->rejectionReasonText() }}</p>
                                @endif
                                @if (in_array($course->status, ['rejected', 'need_revision'], true))
                                    <a href="{{ route('instructor.courses.edit', $course) }}#ai-moderation-results"
                                       class="mt-2 inline-flex text-xs font-bold text-indigo-700 transition-colors hover:text-indigo-900">
                                        Xem kết quả kiểm duyệt AI →
                                    </a>
                                @endif
                                @if ($canSubmit && $check && ! $isReady)
                                    <ul class="mt-2 space-y-1 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">
                                        @foreach ($check->errorMessages() as $message)
                                            <li>• {{ $message }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 rounded-lg bg-slate-50 p-3 text-sm">
                                <div>
                                    <span class="block text-xs font-semibold text-slate-500">Giá</span>
                                    <strong
                                        class="text-slate-950">{{ $formatPrice($discountPrice ?? $course->price) }}</strong>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-slate-500">Học viên</span>
                                    <strong class="text-slate-950">{{ $course->enrollments_count ?? 0 }}</strong>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('instructor.courses.edit', $course) }}"
                                    class="rounded-lg bg-emerald-600 px-3 py-2 text-center text-xs font-bold text-white">Sửa</a>
                                <a href="{{ route('instructor.courses.curriculum', $course) }}"
                                    class="rounded-lg bg-indigo-600 px-3 py-2 text-center text-xs font-bold text-white">Nội
                                    dung</a>
                                @if ($course->status === 'published')
                                    <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
                                        class="rounded-lg border border-slate-300 px-3 py-2 text-center text-xs font-bold text-slate-700">Xem
                                        trước</a>
                                @else
                                    <span
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-400">Xem
                                        trước</span>
                                @endif
                                @if ($canSubmit && $isReady)
                                    <form method="POST" action="{{ route('instructor.courses.submit', $course) }}"
                                        onsubmit="return confirm('Gửi khóa học này cho admin duyệt?')">
                                        @csrf
                                        <button type="submit"
                                            class="w-full rounded-lg border border-amber-200 px-3 py-2 text-center text-xs font-bold text-amber-700">
                                            {{ in_array($course->status, ['need_revision', 'rejected'], true) ? 'Gửi lại duyệt' : 'Gửi duyệt' }}
                                        </button>
                                    </form>
                                @elseif ($canSubmit)
                                    <span
                                        class="block w-full rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-400">
                                        Chưa đủ điều kiện
                                    </span>
                                @endif
                                @if ($course->status === 'published')
                                    <form method="POST" action="{{ route('instructor.courses.archive', $course) }}"
                                        onsubmit="return confirm('Ẩn khóa học này khỏi trang học viên?')">
                                        @csrf
                                        <button type="submit"
                                            class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-center text-xs font-bold text-zinc-700">Ẩn
                                            khóa học</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('instructor.courses.destroy', $course) }}"
                                    onsubmit="return confirm('Bạn chắc chắn muốn xóa hoặc lưu trữ khóa học này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full rounded-lg border border-rose-200 px-3 py-2 text-center text-xs font-bold text-rose-700">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="pt-2">
                {{ $courses->links() }}
            </div>
        @endif
    </div>

</x-instructor-layout>
