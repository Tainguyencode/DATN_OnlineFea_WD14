<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ExpandedSampleDataSeeder;

class RunExpandedSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seeder:expanded';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the expanded sample data seeder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('db:seed', [
            '--class' => 'ExpandedSampleDataSeeder',
        ]);

        $this->info('✓ Expanded sample data seeder completed!');
    }
}
