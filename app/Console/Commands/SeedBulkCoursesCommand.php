<?php

namespace App\Console\Commands;

use Database\Seeders\BulkCourseSeeder;
use Illuminate\Console\Command;

class SeedBulkCoursesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:seed-bulk 
                            {--count=20000 : Số lượng khóa học cần nạp (mặc định 20000)}
                            {--clean : Xóa các khóa học nạp bulk trước đó}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nạp khóa học quy mô lớn (20.000+) kèm ảnh đại diện tương ứng theo chuyên đề';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = (int)$this->option('count');
        if ($count <= 0) {
            $count = 20000;
        }

        $clean = (bool)$this->option('clean');

        $this->info("Bắt đầu thực thi nạp $count khóa học...");

        $seeder = new BulkCourseSeeder();
        $seeder->run($count, function (string $message) {
            $this->line($message);
        }, $clean);

        return self::SUCCESS;
    }
}
