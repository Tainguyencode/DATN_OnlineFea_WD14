<x-student-layout title="Chứng chỉ" page-title="Chứng chỉ của tôi">

@if($certificates->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <p class="text-slate-600">Hoàn thành khóa học để nhận chứng chỉ.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($certificates as $cert)
            <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-900/60 rounded-xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-200/30 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative">
                    <div class="text-amber-600 text-sm font-bold uppercase tracking-wider mb-2">Chứng chỉ hoàn thành</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $cert->course->title }}</h3>
                    <p class="text-sm text-slate-500">Mã: <span class="font-mono font-medium">{{ $cert->certificate_code }}</span></p>
                    <p class="text-sm text-slate-500 mt-1">Cấp ngày: {{ $cert->issued_at->format('d/m/Y') }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif

</x-student-layout>
