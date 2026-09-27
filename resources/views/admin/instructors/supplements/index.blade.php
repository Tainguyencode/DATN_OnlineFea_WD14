<x-admin-layout title="Tài liệu bổ sung giảng viên" page-title="Tài liệu bổ sung" breadcrumb="Quản lý giảng viên / Tài liệu bổ sung">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Tài liệu bổ sung đang chờ duyệt</h1>
            <p class="mt-1 text-sm text-slate-500">Chỉ gồm tài liệu mới của ngành đã được phê duyệt; không trộn với hồ sơ global hoặc yêu cầu duyệt ngành mới.</p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-5 py-3">Giảng viên</th>
                            <th class="px-5 py-3">Ngành</th>
                            <th class="px-5 py-3">Tài liệu</th>
                            <th class="px-5 py-3">Gửi lúc</th>
                            <th class="px-5 py-3 text-right">Xử lý</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($documents as $document)
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $document->user?->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $document->user?->email }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-700 dark:text-slate-300">{{ $document->teachingField?->category?->full_name ?? $document->teachingField?->category?->name }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $document->title ?: $document->original_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $document->requirement?->document_title }}</div>
                                    @if($document->diploma_number || $document->book_reg_number || $document->lookup_url || $document->credential_url || $document->supplementary_file_path)
                                        <div class="mt-1 text-[11px] text-blue-700 dark:text-blue-300 space-y-0.5">
                                            @if($document->diploma_number)
                                                <div>Số hiệu: <strong>{{ $document->diploma_number }}</strong></div>
                                            @endif
                                            @if($document->book_reg_number)
                                                <div>Số vào sổ: <strong>{{ $document->book_reg_number }}</strong></div>
                                            @endif
                                            @if($document->lookup_url)
                                                <div><a href="{{ $document->lookup_url }}" target="_blank" class="font-bold text-[#0056D2] hover:underline">↗ Cổng tra cứu trường / trung tâm</a></div>
                                            @endif
                                            @if($document->credential_url)
                                                <div><a href="{{ $document->credential_url }}" target="_blank" class="font-bold text-purple-700 hover:underline">↗ Link xác thực (Credly / Coursera / Github)</a></div>
                                            @endif
                                            @if($document->supplementary_file_path)
                                                <div><a href="{{ route('admin.instructors.applications.certificates.supplementary', $document) }}" target="_blank" class="font-bold text-emerald-700 hover:underline">↗ Tệp minh chứng phụ đính kèm</a></div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500">{{ $document->uploaded_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700" target="_blank" href="{{ route('admin.instructors.applications.certificates.view', $document) }}">Xem</a>
                                        <form method="POST" action="{{ route('admin.instructors.applications.documents.review', [$document->user, $document]) }}">@csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white">Duyệt</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.instructors.applications.documents.review', [$document->user, $document]) }}" class="flex gap-2">@csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <input required name="rejection_reason" maxlength="1000" placeholder="Lý do" class="w-36 rounded-lg border px-2 py-1 text-xs">
                                            <button class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-bold text-white">Từ chối</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">Không có tài liệu bổ sung đang chờ duyệt.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $documents->links() }}
    </div>
</x-admin-layout>
