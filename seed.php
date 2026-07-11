<?php
/**
 * Quick script to run expanded seeder
 * Run: php seed.php
 */

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "\n========== RUNNING EXPANDED SEEDER ==========\n\n";

try {
    $kernel->call('db:seed', ['--class' => 'ExpandedSampleDataSeeder']);
    
    echo "\n\n========== COMPLETED SUCCESSFULLY ==========\n";
    echo "\n✓ Expanded sample data has been seeded!\n\n";
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n\n";
}
