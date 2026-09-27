<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">Duyệt ngành giảng dạy</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kiểm tra từng yêu cầu và tài liệu thuộc đúng ngành trước khi phê duyệt.</p>
            </div>
            <nav aria-label="Lọc trạng thái ngành giảng dạy" class="flex flex-wrap gap-2 text-xs font-bold">
                @foreach(['pending' => 'Chờ duyệt', 'rejected' => 'Bị từ chối', 'approved' => 'Đã duyệt'] as $key => $label)
                    <a href="{{ route('admin.instructors.teaching-fields.index', ['status' => $key]) }}" class="cursor-pointer rounded-xl px-3 py-2 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950 {{ $status === $key ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">{{ $label }}</a>
                @endforeach
            </nav>
        </div>

        @forelse($fields as $field)
            @php
                $reviewData = $field->requirement_data;
                $reviewSummary = $reviewData['summary'];
                $canApprove = $reviewSummary['can_approve'];
            @endphp
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <header class="border-b border-slate-200 p-5 dark:border-slate-800">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-lg font-black text-slate-900 dark:text-white">{{ $field->category?->full_name ?? $field->category?->name }}</h2>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                {{ $field->profile?->user?->name }}
                                <span aria-hidden="true">·</span>
                                {{ $field->profile?->user?->email }}
                            </p>
                            @if($field->replacedField)
                                <p class="mt-2 inline-flex rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                                    Thay thế ngành {{ $field->replacedField->category?->name }} bằng {{ $field->category?->name }}
                                </p>
                            @else
                                <p class="mt-2 inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">Đăng ký thêm ngành</p>
                            @endif
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $field->approval_status === 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300' : ($field->approval_status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300') }}">
                            {{ strtoupper($field->approval_status) }}
                        </span>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                        <span class="font-bold text-slate-800 dark:text-slate-100">
                            {{ $reviewSummary['fulfilled_count'] }}/{{ $reviewSummary['required_count'] }} tài liệu bắt buộc hợp lệ
                        </span>
                        @if($canApprove)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">Đủ điều kiện phê duyệt</span>
                        @else
                            <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-800 dark:bg-rose-950/50 dark:text-rose-300">Thiếu {{ $reviewSummary['missing_count'] }} tài liệu bắt buộc</span>
                        @endif
                    </div>
                </header>

                @if($field->approval_status === 'pending')
                    <section aria-label="Hồ sơ tài liệu đang xét duyệt" class="space-y-4 p-5">
                        @forelse($reviewData['requirements'] as $index => $item)
                            @php
                                $requirement = $item['requirement'];
                                $requirementStatusLabel = match ($item['status']) {
                                    'approved' => 'Đã duyệt',
                                    'pending' => 'Chờ duyệt',
                                    default => 'Chưa nộp',
                                };
                                $requirementStatusClass = match ($item['status']) {
                                    'approved' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300',
                                    'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                };
                            @endphp
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-bold text-slate-900 dark:text-white">{{ $index + 1 }}. {{ $requirement->document_title }}</h3>
                                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $requirement->is_required ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-950/50 dark:text-sky-300' }}">
                                                {{ $requirement->is_required ? 'Bắt buộc' : 'Không bắt buộc' }}
                                            </span>
                                        </div>
                                        @if(filled($requirement->description))
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $requirement->description }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold {{ $requirementStatusClass }}">{{ $requirementStatusLabel }}</span>
                                </div>

                                <div class="mt-4 space-y-2">
                                    @forelse($item['documents'] as $document)
                                        <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800/70 border border-slate-100 dark:border-slate-700/60 space-y-2">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                <div class="min-w-0">
                                                    <p class="break-words text-sm font-bold text-slate-800 dark:text-slate-100">{{ $document->title ?: $document->original_name ?: 'Tài liệu minh chứng' }}</p>
                                                    @if($document->original_name && $document->original_name !== $document->title)
                                                        <p class="mt-1 break-all text-xs text-slate-600 dark:text-slate-300">Tên tệp: {{ $document->original_name }}</p>
                                                    @endif
                                                    <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                                                        <span>Trạng thái: {{ $document->status === 'approved' ? 'Đã duyệt' : 'Chờ duyệt' }}</span>
                                                        @if($document->uploaded_at)
                                                            <span>Đã tải lên: {{ $document->uploaded_at->format('d/m/Y H:i') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <a href="{{ route('admin.instructors.applications.certificates.view', $document) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-bold text-blue-700 transition-colors duration-200 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-800 dark:bg-slate-900 dark:text-blue-300 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900">
                                                    {{ $document->isUrlSource() ? 'Xem tài liệu' : ($document->isVideo() ? 'Xem video' : 'Xem tệp') }}
                                                </a>
                                            </div>

                                            {{-- THÔNG TIN XÁC THỰC PHÒNG CHỐNG GIẢ MẠO (ANTI-FORGERY) --}}
                                            @if($document->diploma_number || $document->book_reg_number || $document->lookup_url || $document->credential_url || $document->supplementary_file_path || $document->supplementary_proof_type)
                                                <div class="mt-2 rounded-xl border border-blue-100 bg-blue-50/60 p-3 text-xs text-slate-700 dark:border-blue-900/40 dark:bg-slate-900/60 dark:text-slate-300 space-y-1.5">
                                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                                        <div class="flex items-center gap-1.5 font-bold text-blue-700 dark:text-blue-300">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                            <span>Dữ liệu xác thực đối chiếu (Anti-Forgery):</span>
                                                        </div>
                                                        @if($document->isDomesticCertificate())
                                                            <span class="rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-black text-purple-800 dark:bg-purple-950/60 dark:text-purple-300">
                                                                🏫 Chứng chỉ Trong nước
                                                            </span>
                                                        @elseif($document->verification_method === 'credential_url')
                                                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-black text-blue-800 dark:bg-blue-950/60 dark:text-blue-300">
                                                                🌐 Chứng chỉ Quốc tế
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($document->diploma_number || $document->book_reg_number)
                                                        <div class="flex flex-wrap gap-4 text-[11px]">
                                                            @if($document->diploma_number)
                                                                <span><strong>{{ $document->isDomesticCertificate() ? 'Mã chứng chỉ trung tâm' : 'Số hiệu văn bằng' }}:</strong> <code class="rounded bg-white px-1.5 py-0.5 font-mono font-bold text-slate-900 border border-slate-200 dark:bg-slate-800 dark:text-white dark:border-slate-700">{{ $document->diploma_number }}</code></span>
                                                            @endif
                                                            @if($document->book_reg_number)
                                                                <span><strong>Số vào sổ cấp bằng:</strong> <code class="rounded bg-white px-1.5 py-0.5 font-mono font-bold text-slate-900 border border-slate-200 dark:bg-slate-800 dark:text-white dark:border-slate-700">{{ $document->book_reg_number }}</code></span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if($document->lookup_url)
                                                        <div class="pt-0.5">
                                                            <a href="{{ $document->lookup_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-bold text-[#0056D2] hover:underline dark:text-blue-400">
                                                                <span>{{ $document->isDomesticCertificate() ? 'Mở cổng tra cứu của trung tâm đào tạo ↗' : 'Mở cổng tra cứu văn bằng của trường ↗' }}</span>
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if($document->credential_url)
                                                        <div class="pt-0.5">
                                                            <a href="{{ $document->credential_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-bold {{ str_contains($document->credential_url, 'github.com') ? 'text-slate-800 dark:text-slate-200' : 'text-purple-700 dark:text-purple-400' }} hover:underline">
                                                                @if(str_contains($document->credential_url, 'github.com'))
                                                                    <span>📂 Xem sản phẩm / Kho mã nguồn thực tế (Github) ↗</span>
                                                                @else
                                                                    <span>Kiểm tra link xác thực chứng chỉ số (Credly / Coursera Verify) ↗</span>
                                                                @endif
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if($document->supplementary_proof_type || $document->supplementary_file_path)
                                                        <div class="flex items-center gap-2 pt-0.5 text-[11px] flex-wrap">
                                                            <span class="rounded bg-slate-200 px-2 py-0.5 font-bold text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                                {{ $document->supplementaryProofTypeLabel() ?: 'Minh chứng đính kèm' }}
                                                            </span>
                                                            @if($document->supplementary_file_path)
                                                                <a href="{{ route('admin.instructors.applications.certificates.supplementary', $document) }}" target="_blank" class="font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                                                                    [Xem tệp minh chứng phụ ↗]
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-4 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-300">Chưa nộp</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-300">Ngành này không có yêu cầu tài liệu đang hoạt động.</div>
                        @endforelse
                    </section>

                    <footer class="border-t border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/40">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                            <form method="POST" action="{{ route('admin.instructors.teaching-fields.approve', $field) }}">
                                @csrf
                                <button type="submit" @disabled(!$canApprove) class="min-h-10 rounded-xl px-4 py-2 text-xs font-bold text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 {{ $canApprove ? 'cursor-pointer bg-emerald-600 transition-colors duration-200 hover:bg-emerald-700' : 'cursor-not-allowed bg-slate-400' }}">Phê duyệt</button>
                            </form>
                            <form method="POST" action="{{ route('admin.instructors.teaching-fields.reject', $field) }}" class="flex w-full flex-col gap-2 sm:flex-row sm:items-end lg:max-w-2xl">
                                @csrf
                                <div class="min-w-0 flex-1">
                                    <label for="rejection_reason_{{ $field->id }}" class="mb-1 block text-xs font-bold text-slate-700 dark:text-slate-200">Lý do từ chối</label>
                                    <input id="rejection_reason_{{ $field->id }}" required minlength="10" maxlength="2000" name="rejection_reason" placeholder="Nhập lý do từ chối" class="min-h-10 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500/30 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </div>
                                <button type="submit" class="min-h-10 cursor-pointer rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition-colors duration-200 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">Từ chối</button>
                            </form>
                        </div>
                    </footer>
                @elseif($field->rejection_reason)
                    <p class="p-5 text-sm text-rose-600 dark:text-rose-300">Lý do: {{ $field->rejection_reason }}</p>
                @endif
            </article>
        @empty
            <div class="rounded-2xl bg-white p-8 text-center text-sm text-slate-500 shadow-sm dark:bg-slate-900 dark:text-slate-400">Không có yêu cầu ngành phù hợp.</div>
        @endforelse

        {{ $fields->links() }}
    </div>
</x-admin-layout>
