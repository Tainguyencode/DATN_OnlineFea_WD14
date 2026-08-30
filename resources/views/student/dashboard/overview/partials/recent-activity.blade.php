@if($recentActivities->isNotEmpty())
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900" aria-labelledby="recent-activity-title">
    <h2 id="recent-activity-title" class="text-lg font-extrabold text-slate-950 dark:text-white">Hoạt động gần đây</h2>
    <ol class="mt-4 space-y-4">
        @foreach($recentActivities as $activity)
            @php
                $label = $activity->description ?: match($activity->action) {
                    'enroll_course' => 'Đăng ký khóa học',
                    'complete_lesson' => 'Hoàn thành bài học',
                    'complete_course' => 'Hoàn thành khóa học',
                    'certificate_issued' => 'Nhận chứng chỉ',
                    'payment_success' => 'Thanh toán đơn hàng thành công',
                    default => 'Tiếp tục học',
                };
            @endphp
            <li class="flex gap-3">
                <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-[#0056D2] ring-4 ring-blue-50 dark:ring-blue-950/40" aria-hidden="true"></span>
                <div class="min-w-0"><p class="text-sm font-semibold leading-5 text-slate-700 dark:text-slate-200">{{ $label }}</p><time class="mt-0.5 block text-xs text-slate-500" datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->diffForHumans() }}</time></div>
            </li>
        @endforeach
    </ol>
</section>
@endif
