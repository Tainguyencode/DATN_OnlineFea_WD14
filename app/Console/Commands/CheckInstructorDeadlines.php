<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckInstructorDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instructors:check-profile-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra thời hạn 7 ngày hoàn thiện hồ sơ giảng viên và thời gian chờ cấp lại quyền.';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Bắt đầu kiểm tra thời hạn hồ sơ giảng viên...');

        $now = now();

        // 1. Kiểm tra giảng viên chưa hoàn thiện hồ sơ
        $activeInstructors = User::query()
            ->where('role', 'instructor')
            ->where('account_status', 'active')
            ->where('instructor_status', '!=', 'approved')
            ->whereNull('submitted_for_review_at')
            ->whereNotNull('email_verified_at')
            ->get();

        $lockedCount = 0;
        $notifiedCount = 0;

        foreach ($activeInstructors as $user) {
            $deadline = $user->instructor_deadline_at;
            if (! $deadline) {
                continue;
            }

            if ($now->greaterThanOrEqualTo($deadline)) {
                // Quá hạn 7 ngày -> Khóa tài khoản
                $user->lockDueToProfileDeadline('Chưa hoàn thiện hồ sơ chứng chỉ trong thời hạn 7 ngày.');
                $lockedCount++;

                ActivityLogService::log($user->id, 'instructor_account_locked_deadline_expired', User::class, $user->id, [
                    'deadline' => $deadline,
                    'locked_at' => $now,
                ]);

                try {
                    $notificationService->send(
                        $user,
                        '🔒 Tài khoản giảng viên đã bị tạm khóa',
                        'Tài khoản giảng viên của bạn đã bị tạm khóa do quá hạn 7 ngày hoàn thiện hồ sơ chứng chỉ. Bạn có thể xem chi tiết và chuẩn bị gửi yêu cầu cấp lại tại trang Hồ sơ.',
                        'instructor_account_locked',
                        route('instructor.profile')
                    );
                } catch (\Throwable $e) {
                    Log::error("Gửi thông báo khóa tài khoản cho user {$user->id} thất bại: " . $e->getMessage());
                }

                $this->warn("Đã khóa tài khoản giảng viên: {$user->email} (ID: {$user->id}) do quá hạn 7 ngày.");
            } else {
                $daysRemaining = $user->instructor_deadline_days_remaining;

                // Gửi thông báo nhắc nhở vào các mốc 7, 3, 1 ngày
                if (in_array($daysRemaining, [7, 3, 1], true)) {
                    $msg = match ($daysRemaining) {
                        7 => 'Bạn có 7 ngày để hoàn thiện và gửi hồ sơ chứng chỉ giảng viên.',
                        3 => '⚠️ Bạn còn 3 ngày để hoàn thiện hồ sơ chứng chỉ giảng viên.',
                        1 => '🚨 Bạn chỉ còn 1 ngày để hoàn thiện hồ sơ chứng chỉ giảng viên trước khi tài khoản bị khóa.',
                    };

                    try {
                        $notificationService->send(
                            $user,
                            "Nhắc nhở hoàn thiện hồ sơ giảng viên ({$daysRemaining} ngày còn lại)",
                            $msg,
                            'instructor_deadline_reminder',
                            route('instructor.profile')
                        );
                        $notifiedCount++;
                    } catch (\Throwable $e) {
                        Log::error("Gửi nhắc nhở deadline cho user {$user->id} thất bại: " . $e->getMessage());
                    }
                }
            }
        }

        // 2. Kiểm tra giảng viên bị khóa đã đủ cooldown 10-15 ngày (14 ngày)
        $lockedInstructors = User::query()
            ->where('role', 'instructor')
            ->where('account_status', 'locked')
            ->where('reactivation_status', 'none')
            ->whereNotNull('locked_at')
            ->get();

        $cooldownNotifiedCount = 0;
        foreach ($lockedInstructors as $user) {
            if ($user->canRequestReactivation()) {
                try {
                    $notificationService->send(
                        $user,
                        'Đã đủ điều kiện gửi yêu cầu cấp lại quyền Giảng viên',
                        'Bạn đã đủ thời gian chờ quy định để gửi đơn yêu cầu mở khóa và cấp lại quyền giảng viên. Vui lòng vào trang Hồ sơ để thực hiện.',
                        'instructor_reactivation_available',
                        route('instructor.profile')
                    );
                    $cooldownNotifiedCount++;
                } catch (\Throwable $e) {
                    Log::error("Gửi thông báo mở đơn cấp lại cho user {$user->id} thất bại: " . $e->getMessage());
                }
            }
        }

        $this->info("Hoàn tất: Khóa {$lockedCount} tài khoản, gửi {$notifiedCount} nhắc nhở deadline, gửi {$cooldownNotifiedCount} thông báo mở đơn cấp lại.");

        return Command::SUCCESS;
    }
}
