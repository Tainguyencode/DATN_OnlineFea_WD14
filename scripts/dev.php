<?php

declare(strict_types=1);

/**
 * Cross-platform `composer dev` launcher.
 *
 * Laravel Pail requires the pcntl extension (unavailable on Windows PHP).
 * On Windows we run serve + queue + vite only, and never use --kill-others
 * so one process exit cannot tear down the whole stack.
 */

$hasPcntl = function_exists('pcntl_fork');

$names = ['server', 'queue'];
$commands = [
    'php artisan serve',
    'php artisan queue:listen --tries=1 --timeout=0',
];
$colors = ['#93c5fd', '#c4b5fd'];

if ($hasPcntl) {
    $names[] = 'logs';
    $commands[] = 'php artisan pail --timeout=0';
    $colors[] = '#fb7185';
}

$names[] = 'vite';
$commands[] = 'npm run dev';
$colors[] = '#fdba74';

$quotedCommands = array_map(
    static fn (string $command): string => '"'.str_replace('"', '\\"', $command).'"',
    $commands
);

$concurrently = sprintf(
    'npx concurrently -c "%s" %s --names=%s%s',
    implode(',', $colors),
    implode(' ', $quotedCommands),
    implode(',', $names),
    $hasPcntl ? ' --kill-others' : ''
);

if (! $hasPcntl) {
    fwrite(STDOUT, "[dev] pcntl unavailable — skipping `php artisan pail` (Windows-safe mode).\n");
}

passthru($concurrently, $exitCode);

exit($exitCode);
