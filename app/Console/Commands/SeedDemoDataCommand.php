<?php

namespace App\Console\Commands;

use Database\Seeders\Demo\DemoDataMasterSeeder;
use Illuminate\Console\Command;

class SeedDemoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:seed {--convert-hls=3 : Số lượng video chạy thử chuyển đổi HLS ngay sau khi seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nạp bộ dữ liệu demo/test lớn và sạch (Sinh viên, Giảng viên, Khóa học, Video MP4, Quizzes, HLS) cho OnlineFEA';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Lệnh này không được phép chạy trên môi trường Production!');
            return self::FAILURE;
        }

        $this->info('Đang nạp dữ liệu Demo qua DemoDataMasterSeeder...');
        $seeder = new DemoDataMasterSeeder();
        $seeder->run();

        $convertCount = (int) $this->option('convert-hls');
        if ($convertCount > 0) {
            $this->info("Đang kích hoạt chạy thử HLS Pipeline cho {$convertCount} video mẫu...");
            $this->call('demo:test-hls', ['--count' => $convertCount]);
        }

        return self::SUCCESS;
    }
}
