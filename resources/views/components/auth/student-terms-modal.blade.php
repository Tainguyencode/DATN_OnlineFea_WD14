<template x-teleport="body">
    <div
        id="student-terms-modal"
        data-student-terms-modal
        x-show="termsModalOpen"
        x-cloak
        style="display: none;"
        x-init="$watch('termsModalOpen', value => document.body.classList.toggle('overflow-hidden', value))"
        x-on:keydown.escape.window="termsModalOpen = false"
        x-on:click.self="termsModalOpen = false"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-3 sm:p-4 backdrop-blur-sm overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="student-terms-title"
        aria-describedby="student-terms-description"
    >
        <section
            x-show="termsModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-3 scale-95 opacity-0"
            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
            x-transition:leave-end="translate-y-3 scale-95 opacity-0"
            class="flex max-h-[94vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
        >
            <header class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#0056D2] dark:text-blue-300">Đăng ký học viên FEA</p>
                    <h2 id="student-terms-title" class="mt-1 text-xl font-bold text-slate-900 dark:text-white">Chi tiết điều khoản đăng ký</h2>
                    <p id="student-terms-description" class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Nội dung chính thức từ tài liệu PDF Điều khoản đăng ký Online FEA.
                    </p>
                </div>
                <button
                    type="button"
                    x-ref="termsCloseButton"
                    x-on:click="termsModalOpen = false"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white"
                    aria-label="Đóng chi tiết điều khoản"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </header>

            <div class="min-h-0 flex-1 bg-slate-100 dark:bg-slate-950">
                <object
                    data-student-terms-pdf
                    data="{{ route('legal.registration-terms') }}#toolbar=1&navpanes=0"
                    type="application/pdf"
                    class="h-[68vh] min-h-[28rem] w-full"
                    aria-label="Tài liệu PDF Điều khoản đăng ký Online FEA"
                >
                    <div class="flex h-full min-h-[28rem] flex-col items-center justify-center gap-4 p-6 text-center text-slate-600 dark:text-slate-300">
                        <p>Trình duyệt của bạn không hỗ trợ xem PDF trực tiếp.</p>
                        <a
                            href="{{ route('legal.registration-terms') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-semibold text-[#0056D2] underline underline-offset-4 dark:text-blue-300"
                        >
                            Mở tài liệu PDF trong tab mới
                        </a>
                    </div>
                </object>
            </div>

            <footer class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/50 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500 dark:text-slate-400">
                    Việc mở tài liệu không tự động đánh dấu ô đồng ý.
                </p>
                <div class="flex flex-wrap items-center justify-end gap-3">
                    <a
                        data-student-terms-open-pdf
                        href="{{ route('legal.registration-terms') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center rounded-lg border border-[#0056D2] px-4 py-2.5 text-sm font-semibold text-[#0056D2] transition hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] focus-visible:ring-offset-2 dark:border-blue-300 dark:text-blue-300 dark:hover:bg-blue-500/10 dark:focus-visible:ring-offset-slate-900"
                    >
                        Mở PDF trong tab mới
                    </a>
                    <button
                        type="button"
                        x-on:click="termsModalOpen = false"
                        class="inline-flex items-center justify-center rounded-lg bg-[#0056D2] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0046B8] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                    >
                        Đóng
                    </button>
                </div>
            </footer>
        </section>
    </div>
</template>
