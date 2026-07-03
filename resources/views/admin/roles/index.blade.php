<x-admin-layout title="Vai trò" page-title="Role & Permission" breadcrumb="Ma trận quyền quản trị tương thích hệ thống hiện tại">
@if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[380px_1fr]">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h2 class="text-xl font-black text-slate-900">Tạo role mới</h2>
        <p class="mt-1 text-sm text-slate-500">Role tùy chỉnh có thể được gắn permission mà không phá enum role hiện tại.</p>

        <form method="POST" action="{{ route('admin.roles.store') }}" class="mt-6 space-y-4">
            @csrf
            <input name="name" required placeholder="Tên role" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-rose-400 focus:ring-4 focus:ring-rose-500/10">
            <input name="slug" placeholder="slug-tu-chon" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-rose-400 focus:ring-4 focus:ring-rose-500/10">
            <textarea name="description" rows="3" placeholder="Mô tả" class="w-full resize-none rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-rose-400 focus:ring-4 focus:ring-rose-500/10"></textarea>

            <div class="max-h-72 space-y-4 overflow-y-auto rounded-2xl border border-slate-100 bg-slate-50 p-4">
                @foreach($permissions as $group => $items)
                    <div>
                        <div class="mb-2 text-xs font-black uppercase tracking-wider text-slate-500">{{ $group }}</div>
                        <div class="space-y-2">
                            @foreach($items as $permission)
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-slate-300 text-rose-600">
                                    {{ $permission->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="w-full rounded-2xl bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-rose-500/20">Tạo role</button>
        </form>
    </div>

    <div class="space-y-5">
        @foreach($roles as $role)
            <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <button type="button" x-on:click="open = !open" class="flex w-full items-center justify-between gap-4 p-6 text-left">
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-black text-slate-900">{{ $role->name }}</h3>
                            @if($role->is_system)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">System</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-slate-500">{{ $role->description ?: 'Chưa có mô tả' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-black text-slate-950">{{ $role->permissions->count() }}</div>
                        <div class="text-xs font-bold text-slate-400">permissions</div>
                    </div>
                </button>

                <div x-show="open" class="border-t border-slate-100 p-6" x-cloak>
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 md:grid-cols-2">
                            <input name="name" value="{{ $role->name }}" required class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-rose-400 focus:ring-4 focus:ring-rose-500/10">
                            <input value="{{ $role->slug }}" disabled class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                        </div>
                        <textarea name="description" rows="2" class="w-full resize-none rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-rose-400 focus:ring-4 focus:ring-rose-500/10">{{ $role->description }}</textarea>

                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($permissions as $group => $items)
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                    <div class="mb-3 text-xs font-black uppercase tracking-wider text-slate-500">{{ $group }}</div>
                                    <div class="space-y-2">
                                        @foreach($items as $permission)
                                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked($role->permissions->contains($permission)) class="rounded border-slate-300 text-rose-600">
                                                {{ $permission->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <button class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white">Lưu permission</button>
                            <span class="text-xs font-semibold text-slate-400">{{ $role->is_system ? 'Role hệ thống không thể xóa.' : $role->users_count.' người dùng đang gắn role này.' }}</span>
                        </div>
                    </form>

                    @if(! $role->is_system)
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Xóa role này?')" class="mt-3 text-right">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white">Xóa role</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
</x-admin-layout>
