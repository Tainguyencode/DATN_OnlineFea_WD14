<x-admin-layout title="Nhật ký" page-title="Nhật ký hoạt động">

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Thời gian</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Người dùng</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Hành động</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($logs as $log)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-3">{{ $log->user?->name ?? '—' }}</td>
                    <td class="px-6 py-3"><span class="font-mono text-xs bg-slate-100 text-rose-600 px-2 py-1 rounded">{{ $log->action }}</span></td>
                    <td class="px-6 py-3 text-slate-400 text-xs">{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $logs->links() }}</div>
</div>

</x-admin-layout>
