<x-admin-layout title="Gửi thông báo" page-title="Gửi thông báo">

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]" x-data="{ audience: '{{ old('audience', 'students_instructors') }}' }">
    <form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        @csrf

        <div>
            <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">Tiêu đề</label>
            <input id="title" type="text" name="title" value="{{ old('title') }}" required
                   placeholder="Ví dụ: Lịch bảo trì hệ thống"
                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-rose-500">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="message" class="mb-1.5 block text-sm font-medium text-slate-700">Nội dung</label>
            <textarea id="message" name="message" rows="5" required
                      placeholder="Nhập nội dung thông báo gửi tới sinh viên và/hoặc giảng viên..."
                      class="w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-rose-500">{{ old('message') }}</textarea>
            @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="url" class="mb-1.5 block text-sm font-medium text-slate-700">Liên kết (tùy chọn)</label>
            <input id="url" type="text" name="url" value="{{ old('url') }}"
                   placeholder="/courses hoặc https://..."
                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-rose-500">
            @error('url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="audience" class="mb-1.5 block text-sm font-medium text-slate-700">Đối tượng nhận</label>
            <select id="audience" name="audience" required x-model="audience"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-rose-500">
                <option value="students_instructors" @selected(old('audience') === 'students_instructors')>Sinh viên + Giảng viên</option>
                <option value="students" @selected(old('audience') === 'students')>Chỉ sinh viên ({{ $stats['students'] }})</option>
                <option value="instructors" @selected(old('audience') === 'instructors')>Chỉ giảng viên ({{ $stats['instructors'] }})</option>
                <option value="course" @selected(old('audience') === 'course')>Theo khóa học</option>
                <option value="all" @selected(old('audience') === 'all')>Tất cả người dùng</option>
            </select>
            @error('audience')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div x-show="audience === 'course'">
            <label for="course_id" class="mb-1.5 block text-sm font-medium text-slate-700">Khóa học</label>
            <select id="course_id" name="course_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-rose-500">
                <option value="">-- Chọn khóa học --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected((string) old('course_id') === (string) $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
            @error('course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="rounded-xl bg-rose-600 px-6 py-2.5 font-semibold text-white transition hover:bg-rose-700">
            Gửi thông báo
        </button>
    </form>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Thống kê</h3>
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                    <span class="text-sm text-slate-600">Sinh viên đang hoạt động</span>
                    <span class="text-lg font-bold text-slate-900">{{ $stats['students'] }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                    <span class="text-sm text-slate-600">Giảng viên đang hoạt động</span>
                    <span class="text-lg font-bold text-slate-900">{{ $stats['instructors'] }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">Gửi gần đây</h3>
            @forelse($recentBroadcasts as $broadcast)
                <div class="border-b border-slate-100 py-3 last:border-b-0">
                    <p class="font-semibold text-slate-900">{{ $broadcast->title }}</p>
                    <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $broadcast->message }}</p>
                    <p class="mt-2 text-xs text-slate-400">
                        {{ $broadcast->recipient_count }} người nhận · {{ \Illuminate\Support\Carbon::parse($broadcast->created_at)->diffForHumans() }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-slate-500">Chưa có thông báo nào được gửi.</p>
            @endforelse
        </div>
    </div>
</div>

</x-admin-layout>
