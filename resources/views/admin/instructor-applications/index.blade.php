<x-admin-layout title="Duyệt giảng viên" page-title="Đơn đăng ký giảng viên" breadcrumb="Quản lý hồ sơ ứng viên">
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach(['pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối'] as $key => $label)
            <a href="{{ route('admin.instructor-applications.index', ['status' => $key]) }}"
               class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $status === $key ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Ứng viên</th>
                        <th class="px-4 py-3 font-semibold">Chuyên môn</th>
                        <th class="px-4 py-3 font-semibold">Ngày gửi</th>
                        <th class="px-4 py-3 font-semibold">Trạng thái</th>
                        <th class="px-4 py-3 font-semibold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $application)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $application->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $application->user->email ?? $application->user->phone }}</div>
                            </td>
                            <td class="px-4 py-4">{{ $application->expertise }}</td>
                            <td class="px-4 py-4">{{ $application->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                    @if($application->status === 'pending') bg-amber-50 text-amber-700
                                    @elseif($application->status === 'approved') bg-emerald-50 text-emerald-700
                                    @else bg-rose-50 text-rose-700 @endif">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                @if($application->isPending())
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.instructor-applications.approve', $application) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Duyệt</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.instructor-applications.reject', $application) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">Từ chối</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">Đã xử lý</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">Không có đơn nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $applications->links() }}</div>
        @endif
    </div>
</x-admin-layout>
