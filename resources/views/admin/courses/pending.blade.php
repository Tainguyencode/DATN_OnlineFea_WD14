<x-admin-layout title="Duyệt khóa học" page-title="Duyệt khóa học" breadcrumb="{{ $courses->total() }} khóa học chờ duyệt">

@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
@endphp

@if($courses->isEmpty())
    <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </div>
        <h3 class="mt-5 text-lg font-bold text-slate-950">Không có khóa học chờ duyệt</h3>
        <p class="mt-2 text-sm text-slate-500">Các khóa học được giảng viên gửi duyệt sẽ xuất hiện tại đây.</p>
    </div>
@else
    <div class="space-y-5">
        @foreach($courses as $course)
            @php
                $sections = $course->courseSections->isNotEmpty() ? $course->courseSections : $course->chapters;
                $lessonCount = $sections->sum(fn ($section) => $section->lessons->count());
                $price = $course->discount_price ?? $course->sale_price ?? $course->price;
            @endphp

            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="grid gap-5 p-5 lg:grid-cols-[180px_minmax(0,1fr)_220px]">
                    <div class="aspect-video overflow-hidden rounded-lg border border-slate-200 bg-slate-100 lg:aspect-[4/3]">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-rose-700 text-sm font-bold text-white">Fea LMS</div>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Đang chờ duyệt</span>
                            <span class="text-xs font-semibold text-slate-500">{{ $course->category?->name ?? 'Chưa chọn danh mục' }}</span>
                        </div>
                        <h3 class="mt-2 text-xl font-bold text-slate-950">{{ $course->title }}</h3>
                        <p class="mt-1 text-sm text-slate-500">Giảng viên: {{ $course->instructor?->name }} · {{ $course->instructor?->email }}</p>
                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $course->short_description ?: $course->description }}</p>

                        <div class="mt-4 grid gap-3 sm:grid-cols-4">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <span class="block text-xs font-bold uppercase tracking-wide text-slate-500">Giá</span>
                                <strong class="mt-1 block text-sm text-slate-950">{{ $formatPrice($price) }}</strong>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <span class="block text-xs font-bold uppercase tracking-wide text-slate-500">Chương</span>
                                <strong class="mt-1 block text-sm text-slate-950">{{ $sections->count() }}</strong>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <span class="block text-xs font-bold uppercase tracking-wide text-slate-500">Bài học</span>
                                <strong class="mt-1 block text-sm text-slate-950">{{ $lessonCount }}</strong>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <span class="block text-xs font-bold uppercase tracking-wide text-slate-500">Gửi duyệt</span>
                                <strong class="mt-1 block text-sm text-slate-950">{{ $course->submitted_at?->format('d/m/Y H:i') ?? 'Chưa rõ' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <a href="{{ route('admin.courses.review', $course) }}"
                           class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 cursor-pointer">
                            Xem chi tiết
                        </a>
                        <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
                                Duyệt
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.courses.reject', $course) }}" class="space-y-2">
                            @csrf
                            <label class="sr-only" for="reason-{{ $course->id }}">Lý do từ chối</label>
                            <textarea id="reason-{{ $course->id }}" name="reason" rows="3" required maxlength="1000"
                                      placeholder="Lý do từ chối..."
                                      class="w-full resize-none rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none transition-colors duration-200 focus:border-rose-500 focus-visible:ring-2 focus-visible:ring-rose-500/20"></textarea>
                            <button type="submit"
                                    class="inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 cursor-pointer">
                                Từ chối
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-6">{{ $courses->links() }}</div>
@endif

</x-admin-layout>
