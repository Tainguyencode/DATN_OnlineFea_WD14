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
                    <p class="text-sm text-slate-500 mt-1 mb-4">Cấp ngày: {{ $cert->issued_at->format('d/m/Y') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <a
                            href="{{ route('certificates.public', $cert->certificate_code) }}"
                            target="_blank"
                            class="inline-flex h-9 items-center justify-center rounded-xl bg-purple-600 px-4 text-xs font-bold text-white transition hover:bg-purple-700 shadow-sm"
                        >
                            Xem chứng chỉ
                        </a>
                        <a
                            href="{{ route('student.certificates.pdf', $cert) }}?download=1"
                            class="inline-flex h-9 items-center justify-center rounded-xl border border-purple-200 bg-white px-4 text-xs font-bold text-purple-700 transition hover:bg-purple-50 shadow-sm"
                        >
                            Tải PDF
                        </a>
                        {{-- Nút gửi email chứng chỉ --}}
                        <form method="GET" action="{{ route('student.certificates') }}">
                            <input type="hidden" name="send_email" value="1">
                            <input type="hidden" name="certificate_id" value="{{ $cert->id }}">
                            <button
                                type="submit"
                                class="inline-flex h-9 items-center justify-center rounded-xl border border-amber-300 bg-amber-50 px-4 text-xs font-bold text-amber-700 transition hover:bg-amber-100 shadow-sm gap-1"
                                onclick="this.disabled=true; this.textContent='Đang gửi...';"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Gửi về email
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

</x-student-layout>
