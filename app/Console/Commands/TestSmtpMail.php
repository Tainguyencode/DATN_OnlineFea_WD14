<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestSmtpMail extends Command
{
    protected $signature = 'mail:test-smtp {--to=0387043899ax@gmail.com : Recipient email address}';

    protected $description = 'Send a test SMTP email (local environment only)';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Lệnh này chỉ dùng trong môi trường local.');

            return self::FAILURE;
        }

        $to = (string) $this->option('to');
        $mailer = (string) config('mail.default');
        $host = (string) config('mail.mailers.smtp.host');
        $port = (string) config('mail.mailers.smtp.port');
        $scheme = (string) (config('mail.mailers.smtp.scheme') ?? 'null');
        $username = (string) (config('mail.mailers.smtp.username') ?? '');

        $this->info("Mailer: {$mailer}");
        $this->info("Host: {$host}:{$port}");
        $this->info("Scheme: {$scheme}");
        $this->info('Username: '.($username !== '' ? $username : '(trống)'));

        if ($mailer !== 'smtp') {
            $this->error('MAIL_MAILER hiện không phải smtp.');

            return self::FAILURE;
        }

        if ($username === '' || ! config('mail.mailers.smtp.password')) {
            $this->error('MAIL_USERNAME hoặc MAIL_PASSWORD chưa được cấu hình trong .env.');

            return self::FAILURE;
        }

        try {
            Mail::raw('Email SMTP của OnlineFEA đã hoạt động.', function ($message) use ($to): void {
                $message->to($to)->subject('Kiểm tra SMTP OnlineFEA');
            });

            $this->info("Đã gửi email kiểm tra tới {$to}.");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Gửi email thất bại: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
