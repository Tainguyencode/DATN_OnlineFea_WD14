<x-admin-layout title="Duyệt khóa học" page-title="Duyệt khóa học" breadcrumb="{{ $courses->total() }} khóa học chờ duyệt">

@if($courses->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center text-slate-500">Không có khóa học chờ duyệt.</div>
@else
    <div class="space-y-6">
        @foreach($courses as $course)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-full font-medium">Chờ duyệt</span>
                            <span class="text-xs text-slate-500">{{ $course->category?->name }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">{{ $course->title }}</h3>
                        <p class="text-sm text-slate-500 mt-1">Giảng viên: {{ $course->instructor?->name }} ({{ $course->instructor?->email }})</p>
                        <p class="text-sm text-slate-600 mt-3 line-clamp-3">{{ $course->description }}</p>
                        <p class="text-sm text-slate-500 mt-2">{{ $course->chapters->count() }} chương · {{ number_format($course->price, 0, ',', '.') }}đ</p>
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-emerald-700 transition">✓ Duyệt</button>
                        </form>
                        <form method="POST" action="{{ route('admin.courses.reject', $course) }}" class="space-y-2">
                            @csrf
                            <input type="text" name="reason" placeholder="Lý do từ chối..." required
                                   class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm">
                            <button type="submit" class="w-full bg-red-100 text-red-700 px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-200 transition">✕ Từ chối</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $courses->links() }}</div>
@endif

</x-admin-layout>
