<x-admin-layout title="Cấu hình Yêu cầu Hồ sơ Giảng viên theo Ngành" page-title="Cấu hình hồ sơ theo ngành" breadcrumb="Quản lý giảng viên / Yêu cầu hồ sơ theo ngành">
    <div class="space-y-6" x-data="{ createModal: false, editModal: false, editItem: {} }">
        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800 dark:border-rose-800/40 dark:bg-rose-900/20 dark:text-rose-300 shadow-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">Yêu cầu hồ sơ theo Ngành giảng dạy</h1>
                <p class="mt-1 text-sm text-slate-500">Thiết lập bộ hồ sơ & chứng chỉ bắt buộc đối với từng chuyên ngành khi giảng viên ứng tuyển</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.instructors.applications.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    ← Danh sách ứng tuyển
                </a>
                @if($selectedCategory)
                    <button type="button" @click="createModal = true" class="rounded-xl bg-[#0056D2] px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-[#00419e] flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Thêm yêu cầu cho ngành này</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- MAIN CONTENT: 2 CỘT --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            {{-- CỘT TRÁI: DANH SÁCH NGÀNH (CATEGORIES) --}}
            <div class="space-y-4 lg:col-span-1">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">Chọn Ngành / Lĩnh vực</h3>
                    <div class="space-y-1 max-h-[600px] overflow-y-auto pr-1">
                        @foreach($categories as $cat)
                            <a href="{{ route('admin.instructors.requirements.index', ['category_id' => $cat->id]) }}"
                               class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-xs font-bold transition {{ $selectedCategoryId === $cat->id ? 'bg-[#0056D2] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                                <span class="truncate">{{ $cat->name }}</span>
                                <span class="ml-2 rounded-full {{ $selectedCategoryId === $cat->id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }} px-2 py-0.5 text-[10px] font-black">
                                    {{ $cat->instructorDocumentRequirements()->count() }}
                                </span>
                            </a>

                            @if($cat->children->isNotEmpty())
                                <div class="pl-4 space-y-1 border-l-2 border-slate-100 dark:border-slate-800 my-1">
                                    @foreach($cat->children as $child)
                                        <a href="{{ route('admin.instructors.requirements.index', ['category_id' => $child->id]) }}"
                                           class="flex items-center justify-between rounded-lg px-3 py-1.5 text-[11px] font-semibold transition {{ $selectedCategoryId === $child->id ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                                            <span class="truncate">{{ $child->name }}</span>
                                            <span class="ml-1.5 text-[10px]">
                                                ({{ $child->instructorDocumentRequirements()->count() }})
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: BẢNG YÊU CẦU HỒ SƠ CỦA NGÀNH ĐƯỢC CHỌN --}}
            <div class="space-y-6 lg:col-span-3">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800 dark:bg-blue-950/50 dark:text-blue-300">
                                    {{ $selectedCategory?->parent ? ($selectedCategory->parent->name . ' → ' . $selectedCategory->name) : ($selectedCategory?->name ?? 'Chưa chọn') }}
                                </span>
                            </div>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white mt-1">Danh sách tài liệu yêu cầu ({{ $requirements->count() }})</h2>
                        </div>

                        <div class="text-xs text-slate-500">
                            Giảng viên chọn ngành này sẽ bắt buộc phải tải lên các tài liệu được đánh dấu [Bắt buộc]
                        </div>
                    </div>

                    @if($requirements->isEmpty())
                        <div class="my-12 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <h4 class="mt-4 text-base font-bold text-slate-800 dark:text-slate-200">Chưa có yêu cầu tài liệu nào cho ngành này</h4>
                            <p class="mt-1 text-xs text-slate-500">Bấm nút "Thêm yêu cầu cho ngành này" ở trên để thiết lập.</p>
                        </div>
                    @else
                        <div class="mt-6 space-y-4">
                            @foreach($requirements as $req)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border {{ $req->is_active ? ($req->is_required ? 'border-violet-200 bg-violet-50/30 dark:border-violet-900/40' : 'border-slate-200 bg-slate-50/50 dark:border-slate-800') : 'border-dashed border-slate-300 bg-slate-100/50 opacity-60' }} p-4.5 dark:bg-slate-900/60 transition">
                                    <div class="flex items-start gap-3.5 min-w-0">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $req->is_required ? 'bg-violet-600 text-white' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                            <span class="text-xs font-black">#{{ $req->sort_order ?: $loop->iteration }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="font-black text-sm text-slate-900 dark:text-white">{{ $req->document_title }}</h4>
                                                @if($req->is_required)
                                                    <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-black text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                                        Bắt buộc
                                                    </span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                        Tùy chọn
                                                    </span>
                                                @endif

                                                <span class="inline-flex rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                                                    Loại: {{ $req->documentTypeLabel() }}
                                                </span>

                                                @if(! $req->is_active)
                                                    <span class="inline-flex rounded-full bg-slate-300 px-2 py-0.5 text-[10px] font-bold text-slate-800">
                                                        Đã tắt
                                                    </span>
                                                @endif
                                            </div>

                                            @if($req->description)
                                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                                    {{ $req->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                        <button type="button"
                                                @click="editItem = {{ json_encode($req) }}; editModal = true"
                                                class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            Sửa
                                        </button>

                                        <form method="POST" action="{{ route('admin.instructors.requirements.toggle-status', $req) }}">
                                            @csrf
                                            <button type="submit" class="rounded-xl px-3 py-1.5 text-xs font-bold transition {{ $req->is_active ? 'bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                                {{ $req->is_active ? 'Tắt' : 'Bật' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.instructors.requirements.destroy', $req) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa yêu cầu tài liệu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/40 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- MODAL THÊM YÊU CẦU MỚI --}}
        <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800" @click.away="createModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Thêm yêu cầu hồ sơ cho ngành</h3>
                    <button type="button" @click="createModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.instructors.requirements.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="category_id" value="{{ $selectedCategoryId }}">

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Tên yêu cầu tài liệu *</label>
                        <input type="text" name="document_title" required placeholder="Ví dụ: Bằng Cử nhân CNTT, Chứng chỉ Digital Marketing..."
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Phân loại tài liệu *</label>
                            <select name="document_type" required class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                @foreach($documentTypes as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Thứ tự hiển thị</label>
                            <input type="number" name="sort_order" value="1" min="0"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Mô tả & Hướng dẫn cho Giảng viên</label>
                        <textarea name="description" rows="3" placeholder="Mô tả tiêu chuẩn của bằng cấp/chứng chỉ để giảng viên chuẩn bị đúng..."
                                  class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="create_is_required" name="is_required" value="1" checked
                               class="rounded border-slate-300 text-[#0056D2] focus:ring-[#0056D2]">
                        <label for="create_is_required" class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            Bắt buộc (Giảng viên phải nộp tài liệu này mới được duyệt hồ sơ)
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="createModal = false" class="rounded-xl px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300">
                            Hủy
                        </button>
                        <button type="submit" class="rounded-xl bg-[#0056D2] px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-[#00419e]">
                            Tạo yêu cầu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL CHỈNH SỬA YÊU CẦU --}}
        <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800" @click.away="editModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Cập nhật yêu cầu hồ sơ</h3>
                    <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" :action="`{{ url('admin/instructors/requirements') }}/${editItem.id}`" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Tên yêu cầu tài liệu *</label>
                        <input type="text" name="document_title" required x-model="editItem.document_title"
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Phân loại tài liệu *</label>
                            <select name="document_type" required x-model="editItem.document_type" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                @foreach($documentTypes as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Thứ tự hiển thị</label>
                            <input type="number" name="sort_order" min="0" x-model="editItem.sort_order"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Mô tả & Hướng dẫn</label>
                        <textarea name="description" rows="3" x-model="editItem.description"
                                  class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm text-slate-900 focus:border-[#0056D2] focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="edit_is_required" name="is_required" value="1" :checked="editItem.is_required"
                               class="rounded border-slate-300 text-[#0056D2] focus:ring-[#0056D2]">
                        <label for="edit_is_required" class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            Bắt buộc (Giảng viên phải nộp)
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="editModal = false" class="rounded-xl px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300">
                            Hủy
                        </button>
                        <button type="submit" class="rounded-xl bg-[#0056D2] px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-[#00419e]">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
