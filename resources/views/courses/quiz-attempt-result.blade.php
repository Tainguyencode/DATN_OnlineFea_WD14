@extends('layouts.app')

@section('title', 'Ket qua quiz - ' . $result['version']->title)

@section('content')
<section class="bg-slate-50 py-8 dark:bg-[#0a0a0a]">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <a href="{{ route('courses.show', $course->slug) }}" class="text-sm font-bold text-indigo-600 hover:underline dark:text-indigo-300">Quay lai khoa hoc</a>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-3xl">Ket qua Quiz - Phien ban V{{ $result['version']->version }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $result['version']->title }}</p>
            </div>
            <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $result['attempt']->passed ? 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30' : 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/30' }}">{{ $result['attempt']->passed ? 'Dat' : 'Chua dat' }}</span>
        </div>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-[#161615] sm:p-6">
            <div class="grid gap-3 sm:grid-cols-4">
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/70"><span class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Diem</span><strong class="mt-1 block text-2xl text-slate-950 dark:text-white">{{ $result['attempt']->score }}</strong></div>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/70"><span class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tong diem</span><strong class="mt-1 block text-2xl text-slate-950 dark:text-white">{{ $result['attempt']->total_score }}</strong></div>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/70"><span class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Phan tram</span><strong class="mt-1 block text-2xl text-slate-950 dark:text-white">{{ number_format((float) $result['attempt']->percent, 2) }}%</strong></div>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/70"><span class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Diem dat</span><strong class="mt-1 block text-2xl text-slate-950 dark:text-white">{{ $result['version']->pass_score }}%</strong></div>
            </div>
        </article>

        <div class="mt-6 space-y-5">
            @foreach($result['questions'] as $question)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-[#161615] sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $question['is_correct'] ? 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30' : 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/30' }}">{{ $question['is_correct'] ? 'Dung' : 'Sai' }}</span>
                            <h2 class="mt-3 text-base font-extrabold text-slate-950 dark:text-white">Cau {{ $question['number'] }}. <span data-math-content>{{ $question['question'] }}</span></h2>
                        </div>
                        <span class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $question['points'] }} diem</span>
                    </div>

                    @if($question['is_unanswered'])
                        <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">Chua tra loi.</p>
                    @elseif($question['has_missing_selection'])
                        <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">Dap an truoc day khong con kha dung.</p>
                    @endif

                    <div class="mt-4 space-y-2">
                        @foreach($question['options'] as $option)
                            @php
                                $optionClass = $option['is_correct']
                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100'
                                    : ($option['is_selected'] ? 'border-rose-200 bg-rose-50 text-rose-900 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-100' : 'border-slate-200 text-slate-700 dark:border-slate-800 dark:text-slate-200');
                            @endphp
                            <div class="flex items-start justify-between gap-3 rounded-xl border p-3 text-sm {{ $optionClass }}">
                                <span class="leading-6" data-math-content>{{ $option['text'] }}</span>
                                <div class="flex shrink-0 flex-wrap justify-end gap-2">
                                    @if($option['is_selected'])<span class="rounded-full bg-white/70 px-2 py-0.5 text-xs font-bold text-slate-700 dark:bg-black/20 dark:text-white">Ban chon</span>@endif
                                    @if($option['is_correct'])<span class="rounded-full bg-white/70 px-2 py-0.5 text-xs font-bold text-emerald-700 dark:bg-black/20 dark:text-emerald-100">Dap an dung</span>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($question['explanation'])
                        <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600 dark:bg-slate-900/70 dark:text-slate-300"><strong class="text-slate-900 dark:text-white">Giai thich:</strong> <span data-math-content>{{ $question['explanation'] }}</span></div>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
