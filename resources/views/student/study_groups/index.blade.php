@extends('layouts.app')

@section('title', 'Nhóm học tập')

@section('content')
<div class="ui-container py-8">
    <div class="mx-auto max-w-5xl space-y-8">
        {{-- Header area --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Nhóm học tập</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Cùng nhau học tập và chia sẻ kiến thức</p>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 dark:border-emerald-800/30 dark:bg-emerald-950/20 dark:text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800 dark:border-rose-800/30 dark:bg-rose-950/20 dark:text-rose-400">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-800/30 dark:bg-rose-950/20">
                <ul class="list-disc pl-5 text-sm font-semibold text-rose-800 dark:text-rose-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            {{-- Left side: Study Groups list --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Danh sách nhóm học tập</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tham gia trao đổi thảo luận về các khóa học bạn đang theo học.</p>

                    @if($studyGroups->isEmpty())
                        <div class="py-12 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/40">
                                <svg class="h-8 w-8 text-[#0056D2] dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <p class="font-medium text-slate-500 dark:text-slate-400">Chưa có nhóm học tập nào được lập.</p>
                        </div>
                    @else
                        <div class="mt-6 divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($studyGroups as $group)
                                <div class="py-5 first:pt-0 last:pb-0">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="space-y-1.5 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="font-bold text-slate-900 dark:text-white text-lg truncate">{{ $group->name }}</h3>
                                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-[#0056D2] ring-1 ring-inset ring-blue-700/10 dark:bg-blue-950/30 dark:text-blue-300">
                                                    {{ $group->course->title }}
                                                </span>
                                                @if($group->creator_id === Auth::id())
                                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/10 dark:bg-amber-950/30 dark:text-amber-400">
                                                        Trưởng nhóm
                                                    </span>
                                                @elseif($group->hasMember(Auth::id()))
                                                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/10 dark:bg-emerald-950/30 dark:text-emerald-400">
                                                        Đã tham gia
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                                                {{ $group->description ?? 'Không có mô tả.' }}
                                            </p>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                                                <span>Người tạo: <strong>{{ $group->creator->name }}</strong></span>
                                                <span>Thành viên: <strong>{{ $group->members_count }} / {{ $group->max_members }}</strong></span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            @if($group->creator_id === Auth::id() || Auth::user()->role === 'admin')
                                                {{-- Edit Trigger --}}
                                                <button onclick="toggleEditModal({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ addslashes($group->description) }}', {{ $group->max_members }})" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 cursor-pointer" title="Chỉnh sửa">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-2.036a5 5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                                {{-- Delete Form --}}
                                                <form action="{{ route('study-groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm này?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg p-2 text-rose-500 hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-950/20 cursor-pointer" title="Xóa nhóm">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($group->hasMember(Auth::id()))
                                                @if($group->creator_id !== Auth::id())
                                                    {{-- Leave Form --}}
                                                    <form action="{{ route('study-groups.leave', $group) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 cursor-pointer">
                                                            Rời nhóm
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                @if($group->members_count < $group->max_members)
                                                    {{-- Join Form --}}
                                                    <form action="{{ route('study-groups.join', $group) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-[#0056D2] px-4 text-sm font-bold text-white hover:bg-[#0046B8] dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                                                            Tham gia
                                                        </button>
                                                    </form>
                                                @else
                                                    <button disabled class="inline-flex h-9 items-center justify-center rounded-lg bg-slate-100 px-4 text-sm font-semibold text-slate-400 dark:bg-slate-800 dark:text-slate-600 cursor-not-allowed">
                                                        Đầy nhóm
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right side: Create Group Form --}}
            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Lập nhóm mới</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tạo nhóm học để cùng thảo luận.</p>

                    @if($availableCourses->isEmpty())
                        <div class="mt-6 rounded-lg bg-amber-50 p-4 dark:bg-amber-950/20">
                            <p class="text-sm text-amber-800 dark:text-amber-400">Bạn chưa đăng ký khóa học nào đang hoạt động. Hãy đăng ký khóa học để có thể lập nhóm học tập.</p>
                        </div>
                    @else
                        <form action="{{ route('study-groups.store') }}" method="POST" class="mt-6 space-y-4">
                            @csrf
                            <div>
                                <label for="course_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Khóa học áp dụng</label>
                                <select name="course_id" id="course_id" required class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                    <option value="">-- Chọn khóa học --</option>
                                    @foreach($availableCourses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Tên nhóm học tập</label>
                                <input type="text" name="name" id="name" required placeholder="Ví dụ: Nhóm tự học Laravel" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Mô tả nhóm</label>
                                <textarea name="description" id="description" rows="3" placeholder="Mục tiêu của nhóm, lịch sinh hoạt..." class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                            </div>

                            <div>
                                <label for="max_members" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Số lượng thành viên tối đa</label>
                                <input type="number" name="max_members" id="max_members" required min="2" max="100" value="10" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            </div>

                            <button type="submit" class="w-full inline-flex h-10 items-center justify-center rounded-xl bg-slate-950 text-sm font-bold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-slate-950 dark:hover:bg-indigo-200 cursor-pointer">
                                Tạo nhóm
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-xl max-w-md w-full border border-slate-200 dark:border-slate-800 p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Chỉnh sửa nhóm học tập</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label for="edit_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Tên nhóm</label>
                <input type="text" name="name" id="edit_name" required class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </div>

            <div>
                <label for="edit_description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Mô tả</label>
                <textarea name="description" id="edit_description" rows="3" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
            </div>

            <div>
                <label for="edit_max_members" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Thành viên tối đa</label>
                <input type="number" name="max_members" id="edit_max_members" required min="2" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 cursor-pointer">
                    Hủy
                </button>
                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#0056D2] px-4 text-sm font-bold text-white hover:bg-[#0046B8] dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleEditModal(id, name, description, maxMembers) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        form.action = `/study-groups/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_max_members').value = maxMembers;
        
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
