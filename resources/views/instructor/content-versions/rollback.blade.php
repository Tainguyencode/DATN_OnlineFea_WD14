<x-instructor-layout title="Khôi phục phiên bản" page-title="Khôi phục phiên bản" :breadcrumb="$course->title">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a class="text-sm font-bold text-indigo-700" href="{{ route('instructor.courses.versions.show', [$course, $type, $resolved->id]) }}">← Chi tiết V{{ $detail['version_number'] }}</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-950">Khôi phục từ V{{ $detail['version_number'] }}</h1>
        </div>

        <section class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-950">
            <p class="font-bold">Thao tác này không thay đổi nội dung đang xuất bản ngay lập tức.</p>
            <ul class="mt-3 list-disc space-y-1 pl-5">
                <li>Một ContentUpdate nháp mới sẽ được tạo từ snapshot V{{ $detail['version_number'] }}.</li>
                <li>Khi gửi duyệt, hệ thống tạo một candidate có số phiên bản mới.</li>
                <li>Admin phải duyệt trước khi candidate trở thành phiên bản đang xuất bản.</li>
                <li>V{{ $detail['version_number'] }} vẫn là lịch sử bất biến và không bị tái kích hoạt.</li>
                <li>Rollback chỉ áp dụng cho {{ strtolower($detail['type_label']) }} này; không đệ quy sang nội dung con hoặc hợp đồng Assignment khác.</li>
            </ul>
        </section>

        <form method="POST" action="{{ route('instructor.courses.versions.rollback.store', [$course, $type, $resolved->id]) }}" class="rounded-xl border border-slate-200 bg-white p-5">
            @csrf
            <label class="block text-sm font-bold text-slate-800">Lý do khôi phục
                <textarea name="reason" required minlength="5" maxlength="1000" rows="5" class="mt-2 w-full rounded-lg border border-slate-300 p-3" placeholder="Mô tả lý do cần khôi phục phiên bản này">{{ old('reason') }}</textarea>
            </label>
            @error('reason')<p class="mt-2 text-sm font-semibold text-rose-700">{{ $message }}</p>@enderror
            <div class="mt-4 flex gap-3">
                <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-bold text-white">Tạo yêu cầu khôi phục nháp</button>
                <a href="{{ route('instructor.courses.versions.show', [$course, $type, $resolved->id]) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700">Hủy</a>
            </div>
        </form>
    </div>
</x-instructor-layout>
