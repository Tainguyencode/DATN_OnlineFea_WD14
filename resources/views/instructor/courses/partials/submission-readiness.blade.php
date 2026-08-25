@php
    $items = $submissionCheck->items();
    $totalItems = count($items);
    $passedItems = count(array_filter($items, fn ($item) => $item['passed']));
    $progress = $totalItems ? (int) round(($passedItems / $totalItems) * 100) : 0;
@endphp

<section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm lg:p-5">
    <div class="flex items-start gap-3">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5.25H7.5A2.25 2.25 0 0 0 5.25 7.5v10.25A2.25 2.25 0 0 0 7.5 20h9A2.25 2.25 0 0 0 18.75 17.75V7.5a2.25 2.25 0 0 0-2.25-2.25H15M9 5.25A2.25 2.25 0 0 1 11.25 3h1.5A2.25 2.25 0 0 1 15 5.25M9 5.25A2.25 2.25 0 0 0 11.25 7.5h1.5A2.25 2.25 0 0 0 15 5.25M9 12h6m-6 3h4" />
            </svg>
        </span>
        <div class="min-w-0 flex-1">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-950">Kiểm tra trước khi gửi duyệt</h2>
                    <p class="mt-0.5 text-xs leading-5 text-slate-500">Hoàn tất các mục bên dưới để dễ dàng chờ admin duyệt khóa học.</p>
                </div>
                <span class="shrink-0 text-sm font-bold {{ $progress === 100 ? 'text-emerald-600' : 'text-slate-700' }}">{{ $passedItems }}/{{ $totalItems }}</span>
            </div>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-200">
                <div class="h-full rounded-full bg-emerald-600 transition-[width] duration-200" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    <ul class="mt-3 grid gap-x-6 md:grid-cols-2">
        @foreach ($items as $item)
            <li class="flex min-w-0 items-center gap-2 border-b border-dotted border-slate-200 py-1.5 text-xs">
                <span @class([
                    'inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white',
                    'bg-emerald-600' => $item['passed'],
                    'bg-rose-500' => ! $item['passed'],
                ])>
                    @if ($item['passed'])
                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        !
                    @endif
                </span>
                <span class="min-w-0 truncate font-semibold text-slate-700">{{ $item['label'] }}</span>
                @if (! $item['passed'])
                    <span class="ml-auto shrink-0 text-right font-semibold text-rose-600">{{ $item['message'] ?: 'Chưa có' }}</span>
                @endif
            </li>
        @endforeach
    </ul>
</section>
