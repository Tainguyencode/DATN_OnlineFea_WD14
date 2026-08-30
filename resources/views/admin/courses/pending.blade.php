<x-admin-layout title="Duyệt khóa học" page-title="Duyệt khóa học" breadcrumb="{{ $courses->total() }} khóa học chờ duyệt">

@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
@endphp

@if($courses->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </div>
        <h3 class="mt-5 text-lg font-bold text-slate-950 dark:text-white">Không có khóa học chờ duyệt</h3>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Tất cả các khóa học đã được kiểm duyệt hoặc chưa có yêu cầu mới.</p>
    </div>
@else
    <div class="space-y-4" x-data="{
        confirmApproveModal: false,
        confirmRejectModal: false,
        selectedCourseId: null,
        selectedCourseTitle: '',
        approveActionUrl: '',
        rejectActionUrl: '',
        openApprove(id, title, url) {
            this.selectedCourseId = id;
            this.selectedCourseTitle = title;
            this.approveActionUrl = url;
            this.confirmApproveModal = true;
        },
        openReject(id, title, url) {
            this.selectedCourseId = id;
            this.selectedCourseTitle = title;
            this.rejectActionUrl = url;
            this.confirmRejectModal = true;
        }
    }">
        @foreach($courses as $course)
            @php
                $sections = $course->courseSections->isNotEmpty() ? $course->courseSections : $course->chapters;
                $lessonCount = $sections->sum(fn ($section) => $section->lessons->count());
                $price = $course->discount_price ?? $course->sale_price ?? $course->price;
                $durationMinutes = $course->totalVideoDurationMinutes();
                $approveUrl = route('admin.courses.approve', $course);
                $rejectUrl = route('admin.courses.reject', $course);
            @endphp

            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-200 dark:border-slate-800 dark:bg-slate-900">
                <div class="grid gap-5 p-5 lg:grid-cols-[160px_minmax(0,1fr)_220px] items-center">
                    {{-- Thumbnail --}}
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100 aspect-video lg:aspect-auto lg:h-32 lg:w-40 shrink-0 dark:border-slate-800 dark:bg-slate-950">
                        <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}" class="h-full w-full object-cover" loading="lazy">
                    </div>

                    {{-- Course Information --}}
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 dark:border-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Chờ duyệt
                            </span>
                            @if($course->category)
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $course->category->name }}
                                </span>
                            @endif
                            @if($course->submitted_at)
                                <span class="text-xs text-slate-400">Gửi lúc {{ $course->submitted_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>

                        <h3 class="mt-2 text-lg font-extrabold text-slate-950 dark:text-white line-clamp-1" title="{{ $course->title }}">
                            {{ $course->title }}
                        </h3>

                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Giảng viên: <strong class="text-slate-700 dark:text-slate-300">{{ $course->instructor?->name ?? 'Chưa gán' }}</strong> ({{ $course->instructor?->email }})
                        </p>

                        <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                            {{ $course->short_description ?: Str::limit($course->description, 150) }}
                        </p>

                        <div class="mt-3.5 flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-600 dark:text-slate-400">
                            <span class="rounded-lg bg-slate-50 px-2.5 py-1 dark:bg-slate-800">💰 {{ $formatPrice($price) }}</span>
                            <span class="rounded-lg bg-slate-50 px-2.5 py-1 dark:bg-slate-800">📂 {{ $sections->count() }} chương</span>
                            <span class="rounded-lg bg-slate-50 px-2.5 py-1 dark:bg-slate-800">▶ {{ $lessonCount }} bài học</span>
                            <span class="rounded-lg bg-slate-50 px-2.5 py-1 dark:bg-slate-800">⏱ {{ $durationMinutes }} phút</span>
                        </div>
                    </div>

                    {{-- Actions (View, Approve, Reject with accessible colors & confirmation modal) (ADM-FE-01) --}}
                    <div class="flex flex-col gap-2 border-t border-slate-100 pt-4 lg:border-t-0 lg:pt-0 shrink-0">
                        {{-- View & Review --}}
                        <a href="{{ route('admin.courses.review', $course) }}"
                           title="Xem chi tiết nội dung khóa học để kiểm duyệt"
                           class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Kiểm duyệt chi tiết</span>
                        </a>

                        <div class="grid grid-cols-2 gap-2">
                            {{-- Quick Approve with Modal --}}
                            <button
                                type="button"
                                @click="openApprove('{{ $course->id }}', '{{ addslashes($course->title) }}', '{{ $approveUrl }}')"
                                title="Duyệt khóa học này"
                                class="inline-flex h-9 items-center justify-center gap-1 rounded-xl bg-emerald-600 px-3 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Duyệt</span>
                            </button>

                            {{-- Reject with Modal --}}
                            <button
                                type="button"
                                @click="openReject('{{ $course->id }}', '{{ addslashes($course->title) }}', '{{ $rejectUrl }}')"
                                title="Từ chối duyệt khóa học này"
                                class="inline-flex h-9 items-center justify-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 text-xs font-bold text-rose-700 transition hover:bg-rose-100 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Từ chối</span>
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach

        {{-- Modal Xác nhận Duyệt khóa học --}}
        <div x-show="confirmApproveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div @click.away="confirmApproveModal = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:border dark:border-slate-800 dark:bg-slate-900 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-950 dark:text-white">Xác nhận duyệt khóa học</h4>
                        <p class="text-xs text-slate-500">Khóa học sẽ được kích hoạt xuất bản tới học viên.</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 bg-slate-50 p-3 rounded-xl dark:bg-slate-800/60">
                    Khóa học: <strong class="text-slate-900 dark:text-white" x-text="selectedCourseTitle"></strong>
                </p>
                <form :action="approveActionUrl" method="POST" class="flex justify-end gap-2 pt-2">
                    @csrf
                    <button type="button" @click="confirmApproveModal = false" class="h-9 rounded-xl border border-slate-200 px-4 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300">Hủy</button>
                    <button type="submit" class="h-9 rounded-xl bg-emerald-600 px-5 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm">Xác nhận duyệt</button>
                </form>
            </div>
        </div>

        {{-- Modal Xác nhận Từ chối khóa học --}}
        <div x-show="confirmRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div @click.away="confirmRejectModal = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:border dark:border-slate-800 dark:bg-slate-900 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-950 dark:text-white">Xác nhận từ chối khóa học</h4>
                        <p class="text-xs text-slate-500">Khóa học sẽ chuyển về trạng thái bị từ chối và thông báo tới giảng viên.</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 bg-slate-50 p-3 rounded-xl dark:bg-slate-800/60">
                    Khóa học: <strong class="text-slate-900 dark:text-white" x-text="selectedCourseTitle"></strong>
                </p>
                <form :action="rejectActionUrl" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lý do từ chối / Ghi chú phản hồi (*)</label>
                        <textarea name="admin_note" rows="3" required placeholder="Nêu rõ nội dung cần giảng viên chỉnh sửa..." class="w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-900 outline-none focus:border-rose-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="confirmRejectModal = false" class="h-9 rounded-xl border border-slate-200 px-4 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300">Hủy</button>
                        <button type="submit" class="h-9 rounded-xl bg-rose-600 px-5 text-xs font-bold text-white hover:bg-rose-700 shadow-sm">Xác nhận từ chối</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-6">{{ $courses->links() }}</div>
@endif

</x-admin-layout>
