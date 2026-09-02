<?php

namespace App\Console\Commands;

use App\Services\EnrollmentReleaseBackfillService;
use Illuminate\Console\Command;

class BackfillEnrollmentReleases extends Command
{
    protected $signature = 'enrollments:backfill-releases {--dry-run : Report only (default)} {--execute : Write proven pins only} {--course= : Inspect a single course}';

    protected $description = 'Report or backfill enrollment releases using proven publication history';

    public function handle(EnrollmentReleaseBackfillService $service): int
    {
        if ($this->option('dry-run') && $this->option('execute')) {
            $this->error('Choose either --dry-run or --execute.');

            return self::INVALID;
        }
        $course = $this->option('course');
        if ($course !== null && (! ctype_digit((string) $course) || (int) $course < 1)) {
            $this->error('--course must be a positive course ID.');

            return self::INVALID;
        }
        $execute = (bool) $this->option('execute');
        $this->info($execute ? 'EXECUTE: proven pins only' : 'DRY RUN: no database writes');
        $this->line('course | enrollment | selected version | reason | unresolved reason');
        $totals = $service->run($execute, fn ($row) => $this->line(implode(' | ', array_map(fn ($value) => $value ?? '-', $row))), $course === null ? null : (int) $course);
        $this->line(json_encode($totals, JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }
}
