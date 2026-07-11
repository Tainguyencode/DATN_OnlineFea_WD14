#!/usr/bin/env php
<?php
/**
 * Script to run expanded sample data seeder
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

exit($kernel->call('db:seed', ['--class' => 'ExpandedSampleDataSeeder']));
