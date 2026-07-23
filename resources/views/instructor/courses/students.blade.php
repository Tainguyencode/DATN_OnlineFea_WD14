<x-instructor-layout :title="$course->title" page-title="Danh sách học viên" :breadcrumb="$course->title">

    <div class="space-y-6">
        {{-- Header Section with Back link --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('instructor.courses.index') }}" 
               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại danh sách khóa học
            </a>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-inset ring-slate-600/10">
                    Mã khóa học: #{{ $course->id }}
                </span>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950 truncate max-w-xl">{{ $course->title }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Quản lý và theo dõi học tập của học viên</p>
                </div>
                <div class="shrink-0 mt-3 sm:mt-0 bg-emerald-50 text-emerald-700 font-bold text-xs px-3 py-1.5 rounded-xl border border-emerald-100 inline-flex items-center gap-1.5 self-start sm:self-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>{{ $enrollments->total() }} học viên đã đăng ký</span>
                </div>
            </div>

            @if($enrollments->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Chưa có học viên đăng ký khóa học này</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm">
                        Khi học viên đăng ký và thanh toán thành công khóa học của bạn, thông tin chi tiết của họ sẽ xuất hiện tại đây.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Học viên</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Ngày đăng ký</th>
                                <th class="px-6 py-4">Trạng thái học</th>
                                <th class="px-6 py-4">Tiến độ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($enrollments as $enrollment)
                                <tr class="hover:bg-slate-50/80 transition align-middle">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="shrink-0">
                                                @if($enrollment->user->avatar)
                                                    <img src="{{ $enrollment->user->avatarUrl() }}" 
                                                         class="w-10 h-10 object-cover rounded-xl shadow-sm border border-slate-200" 
                                                         alt="{{ $enrollment->user->name }}">
                                                @else
                                                    <div class="w-10 h-10 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center text-sm font-bold border border-emerald-100/50">
                                                        {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-950 text-sm">{{ $enrollment->user->name }}</p>
                                                <p class="text-xs text-slate-400">ID: #{{ $enrollment->user->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">
                                        {{ $enrollment->user->email }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 text-xs font-semibold">
                                        {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d/m/Y H:i') : $enrollment->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($enrollment->status === 'completed')
                                            <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                Đã hoàn thành
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-[#0056D2] ring-1 ring-inset ring-blue-700/10">
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                                Đang học
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-300" 
                                                     style="width: {{ $enrollment->progress_percent }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-700">
                                                {{ number_format($enrollment->progress_percent, 0) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>

</x-instructor-layout>
