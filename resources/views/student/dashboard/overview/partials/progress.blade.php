<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-slate-800 dark:bg-slate-900" aria-labelledby="learning-progress-title">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div><h2 id="learning-progress-title" class="text-lg font-extrabold text-slate-950 dark:text-white">Tiến độ học tập</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tiến độ trung bình của các khóa học bạn có quyền truy cập</p></div>
        <strong class="text-3xl font-extrabold text-[#0056D2] dark:text-blue-300">{{ number_format($avgProgress) }}%</strong>
    </div>
    <progress class="mt-4 h-3 w-full overflow-hidden rounded-full [&::-moz-progress-bar]:bg-[#0056D2] [&::-webkit-progress-bar]:bg-slate-100 [&::-webkit-progress-value]:bg-[#0056D2] dark:[&::-webkit-progress-bar]:bg-slate-800" max="100" value="{{ min(100, $avgProgress) }}">{{ number_format($avgProgress) }}%</progress>
</section>
