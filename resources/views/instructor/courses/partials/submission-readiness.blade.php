@php
    $canSubmit = $course->canBeSubmittedForReview();
    $isReady = $submissionCheck->passes();
@endphp

<section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-amber-600">Điều kiện gửi duyệt</p>
            <h2 class="mt-1 text-lg font-bold text-slate-950">Kiểm tra trước khi gửi</h2>
            <p class="mt-1 text-sm text-slate-500">
                Hoàn thành tất cả mục bên dưới để có thể gửi khóa học cho admin duyệt.
            </p>
        </div>

        @if ($canSubmit)
            <span @class([
                'inline-flex shrink-0 rounded-full border px-3 py-1 text-xs font-bold',
                'border-emerald-200 bg-emerald-50 text-emerald-700' => $isReady,
                'border-amber-200 bg-amber-50 text-amber-700' => ! $isReady,
            ])>
                {{ $isReady ? 'Sẵn sàng gửi duyệt' : 'Chưa đủ điều kiện' }}
            </span>
        @endif
    </div>

    <ul class="mt-5 space-y-2">
        @foreach ($submissionCheck->items() as $item)
            <li @class([
                'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm',
                'border-emerald-100 bg-emerald-50/60' => $item['passed'],
                'border-rose-100 bg-rose-50/60' => ! $item['passed'],
            ])>
                <span @class([
                    'mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                    'bg-emerald-600 text-white' => $item['passed'],
                    'bg-rose-500 text-white' => ! $item['passed'],
                ])>
                    @if ($item['passed'])
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        !
                    @endif
                </span>
                <div class="min-w-0">
                    <p class="font-bold text-slate-900">{{ $item['label'] }}</p>
                    @if (! $item['passed'] && $item['message'])
                        <p class="mt-0.5 text-slate-600">{{ $item['message'] }}</p>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>

    @if ($canSubmit && $isReady)
        <form method="POST" action="{{ route('instructor.courses.submit', $course) }}" class="mt-5"
              onsubmit="return confirm('Gửi khóa học này cho admin duyệt?')">
            @csrf
            <button type="submit"
                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-amber-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 cursor-pointer">
                {{ in_array($course->status, ['need_revision', 'rejected'], true) ? 'Gửi duyệt lại' : 'Gửi duyệt' }}
            </button>
        </form>
    @elseif ($canSubmit)
        <p class="mt-5 text-sm font-semibold text-amber-700">
            Vui lòng hoàn thiện các mục chưa đạt trước khi gửi duyệt.
        </p>
    @endif
</section>
