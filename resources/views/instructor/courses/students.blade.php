<x-instructor-layout :title="$course->title" page-title="Học viên" :breadcrumb="$course->title">

<div id="student-toast" class="fixed top-20 right-4 z-[60] hidden max-w-sm rounded-xl border px-4 py-3 text-sm font-semibold shadow-lg transition-all duration-300" role="status" aria-live="polite"></div>

<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Instructor course studio</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">{{ $course->title }}</h2>
            <p class="mt-2 text-sm text-slate-500">{{ $enrollments->total() }} học viên đã ghi danh</p>
        </div>
        <a href="{{ route('instructor.courses.index') }}"
           class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
            ← Quay lại khóa học
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('instructor.courses.students', $course) }}"
              class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <label class="block flex-1">
                <span class="mb-1.5 block text-sm font-semibold text-slate-700">Tìm kiếm học viên</span>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                    </svg>
                    <input type="search" name="search" value="{{ $search }}"
                           placeholder="Tên hoặc email học viên..."
                           class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
                </div>
            </label>
            <div class="flex flex-wrap gap-2">
                <button type="submit"
                        class="inline-flex min-h-10 items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800">
                    Tìm kiếm
                </button>
                @if ($search !== '')
                    <a href="{{ route('instructor.courses.students', $course) }}"
                       class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                        Xóa lọc
                    </a>
                @endif
                <a href="{{ route('instructor.courses.students.export', array_filter(['course' => $course, 'search' => $search ?: null])) }}"
                   class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-700 transition-colors duration-200 hover:bg-emerald-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Xuất danh sách (CSV)
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[960px] text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Học viên</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Ngày đăng ký</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Trạng thái</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Tiến độ</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Bài học gần nhất</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Quiz &amp; Thực hành</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-600">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($enrollments as $enrollment)
                        @php
                            $user = $enrollment->user;
                            $userId = $enrollment->user_id;
                            $progressRecord = $latestProgress->get($userId);
                            $quizAvg = $quizStats->get($userId);
                            $lab = $labStats->get($userId);
                            $enrolledAt = $enrollment->enrolled_at ?? $enrollment->created_at;
                            $isCompleted = $enrollment->status === \App\Models\Enrollment::STATUS_COMPLETED
                                || $enrollment->isCourseCompleted()
                                || (float) $enrollment->progress_percent >= 100;
                            $progressValue = min(100, max(0, (float) $enrollment->progress_percent));
                        @endphp
                        <tr class="transition-colors duration-150 hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($user->avatar)
                                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                                             class="h-9 w-9 rounded-full object-cover ring-2 ring-emerald-100">
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-900">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                {{ $enrolledAt?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($isCompleted)
                                    <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                        Hoàn thành
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700">
                                        Đang học
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex min-w-[120px] items-center gap-2">
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                                             style="width: {{ $progressValue }}%"></div>
                                    </div>
                                    <span class="w-10 text-right text-xs font-bold text-slate-700">{{ number_format($progressValue, 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-[180px] truncate text-slate-700" title="{{ $progressRecord?->lesson?->title }}">
                                    {{ $progressRecord?->lesson?->title ?? 'Chưa học bài nào' }}
                                </p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-600">
                                @if ($quizAvg !== null || $lab !== null)
                                    <span class="font-semibold text-slate-800">
                                        Quiz: {{ $quizAvg !== null ? number_format($quizAvg, 0).'% (Avg)' : '—' }}
                                    </span>
                                    <span class="mx-1 text-slate-300">|</span>
                                    <span class="font-semibold text-slate-800">
                                        Lab: {{ $lab !== null ? $lab['score'].'/'.$lab['max'] : '—' }}
                                    </span>
                                @else
                                    <span class="text-slate-400">Chưa có điểm</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button"
                                        class="notify-btn inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition-colors duration-200 hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
                                        data-student-id="{{ $userId }}"
                                        data-student-name="{{ $user->name }}">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9" />
                                    </svg>
                                    Gửi thông báo
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                                @if ($search !== '')
                                    Không tìm thấy học viên phù hợp với từ khóa "{{ $search }}".
                                @else
                                    Chưa có học viên nào ghi danh khóa học này.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($enrollments->hasPages())
            <div class="border-t border-slate-100 p-4">{{ $enrollments->links() }}</div>
        @endif
    </div>
</div>

{{-- Modal gửi thông báo --}}
<div id="notifyModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="notify-modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        <div id="notifyModalBackdrop" class="fixed inset-0 bg-slate-900/50 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block w-full max-w-lg transform overflow-hidden rounded-2xl border border-slate-200 bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:align-middle">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="text-base font-bold text-slate-900" id="notify-modal-title">Gửi thông báo</h3>
                <p class="mt-1 text-sm text-slate-500">Gửi đến: <span id="notifyStudentName" class="font-semibold text-slate-700"></span></p>
            </div>
            <form id="notifyForm" class="space-y-4 px-6 py-5">
                @csrf
                <label class="block">
                    <span class="mb-1.5 block text-sm font-semibold text-slate-700">Tiêu đề</span>
                    <input type="text" name="title" id="notifyTitle" required maxlength="255"
                           placeholder="Nhập tiêu đề thông báo..."
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-semibold text-slate-700">Nội dung</span>
                    <textarea name="message" id="notifyMessage" required maxlength="2000" rows="4"
                              placeholder="Nhập nội dung thông báo..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20"></textarea>
                </label>
                <p id="notifyError" class="hidden text-sm font-semibold text-rose-600"></p>
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" id="notifyCancelBtn"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        Hủy
                    </button>
                    <button type="submit" id="notifySubmitBtn"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                        Gửi thông báo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('notifyModal');
    const backdrop = document.getElementById('notifyModalBackdrop');
    const form = document.getElementById('notifyForm');
    const toast = document.getElementById('student-toast');
    const studentNameEl = document.getElementById('notifyStudentName');
    const errorEl = document.getElementById('notifyError');
    const submitBtn = document.getElementById('notifySubmitBtn');
    const cancelBtn = document.getElementById('notifyCancelBtn');
    const notifyUrlTemplate = @json(route('instructor.courses.students.notify', ['course' => $course, 'student' => '__STUDENT__']));
    let activeStudentId = null;
    let toastTimer = null;

    function showToast(message, type = 'success') {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800', 'border-rose-200', 'bg-rose-50', 'text-rose-800');
        if (type === 'success') {
            toast.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-800');
        } else {
            toast.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
        }
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.add('hidden'), 4000);
    }

    function openModal(studentId, studentName) {
        activeStudentId = studentId;
        studentNameEl.textContent = studentName;
        form.reset();
        errorEl.classList.add('hidden');
        errorEl.textContent = '';
        modal.classList.remove('hidden');
        document.getElementById('notifyTitle').focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        activeStudentId = null;
    }

    document.querySelectorAll('.notify-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            openModal(btn.dataset.studentId, btn.dataset.studentName);
        });
    });

    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!activeStudentId) return;

        const url = notifyUrlTemplate.replace('__STUDENT__', activeStudentId);
        submitBtn.disabled = true;
        errorEl.classList.add('hidden');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        || form.querySelector('input[name="_token"]')?.value,
                },
                body: JSON.stringify({
                    title: document.getElementById('notifyTitle').value.trim(),
                    message: document.getElementById('notifyMessage').value.trim(),
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                const message = data.message
                    || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Không thể gửi thông báo.');
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
                showToast(message, 'error');
                return;
            }

            closeModal();
            showToast(data.message || 'Đã gửi thông báo thành công.');
        } catch (error) {
            errorEl.textContent = 'Có lỗi xảy ra. Vui lòng thử lại.';
            errorEl.classList.remove('hidden');
            showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        } finally {
            submitBtn.disabled = false;
        }
    });
})();
</script>

</x-instructor-layout>
