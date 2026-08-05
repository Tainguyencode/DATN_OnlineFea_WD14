<?php

namespace App\Console\Commands;

use App\Services\EngagementService;
use Illuminate\Console\Command;

class ProcessLearningReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'engagement:process-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xử lý gửi nhắc nhở học tập qua Email và Push Notification cho học viên gián đoạn học tập';

    /**
     * Execute the console command.
     */
    public function handle(EngagementService $engagementService): int
    {
        $this->info('Bắt đầu xử lý nhắc nhở học tập...');

        $count = $engagementService->processReminders();

        $this->info("Đã gửi thành công {$count} nhắc nhở học tập.");

        return Command::SUCCESS;
    }
}
