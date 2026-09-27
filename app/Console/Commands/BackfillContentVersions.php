<?php

namespace App\Console\Commands;

use App\Services\ContentVersionBackfillService;
use Illuminate\Console\Command;

class BackfillContentVersions extends Command
{
    protected $signature = 'content-versions:backfill {--dry-run : Report snapshots without writing them}';

    protected $description = 'Create idempotent V1 snapshots for published legacy course content.';

    public function handle(ContentVersionBackfillService $backfill): int
    {
        $result = $backfill->backfillPublished((bool) $this->option('dry-run'));
        $this->table(['Entity', 'Created', 'Skipped'], [
            ['Courses', $result['courses_created'], $result['courses_skipped']],
            ['Sections', $result['sections_created'], $result['sections_skipped']],
            ['Lessons', $result['lessons_created'], $result['lessons_skipped']],
            ['Assignments', $result['assignments_created'], $result['assignments_skipped']],
        ]);

        return self::SUCCESS;
    }
}
