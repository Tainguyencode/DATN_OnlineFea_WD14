<div x-cloak x-show="studentSidebarOpen" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu học viên">
    <button type="button" x-show="studentSidebarOpen" x-transition.opacity x-on:click="$dispatch('close-student-sidebar')" class="absolute inset-0 bg-slate-950/50" aria-label="Đóng menu"></button>
    <aside id="student-mobile-sidebar" x-show="studentSidebarOpen"
           x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
           class="relative h-full w-[min(88vw,20rem)] bg-white shadow-2xl dark:bg-slate-900">
        <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4 dark:border-slate-800">
            <strong class="text-sm">Menu học viên</strong>
            <button type="button" x-on:click="$dispatch('close-student-sidebar')" class="inline-flex h-10 w-10 items-center justify-center rounded-xl hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:hover:bg-slate-800" aria-label="Đóng menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <div class="h-[calc(100%-4rem)]">
            @include('student.dashboard.partials.sidebar', ['mobile' => true])
        </div>
    </aside>
</div>
