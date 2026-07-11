<?php

namespace App\Support;

use Throwable;

class MailErrorFormatter
{
    public static function verificationSendFailure(Throwable $exception): string
    {
        $message = $exception->getMessage();

        if (str_contains($message, 'scheme is not supported') && str_contains($message, 'tls')) {
            return 'Cấu hình MAIL_SCHEME không hợp lệ. Với Gmail cổng 587, hãy để trống MAIL_SCHEME hoặc đặt MAIL_SCHEME=smtp. Không dùng tls.';
        }

        if (
            str_contains($message, '535')
            || str_contains($message, 'BadCredentials')
            || str_contains($message, 'Username and Password not accepted')
        ) {
            return 'Xác thực Gmail SMTP thất bại. Hãy đặt MAIL_PASSWORD là Google App Password 16 ký tự (không dùng mật khẩu đăng nhập Gmail thông thường).';
        }

        if (
            str_contains($message, 'Connection could not be established')
            || str_contains($message, 'Connection timed out')
        ) {
            return 'Không kết nối được máy chủ SMTP. Kiểm tra MAIL_HOST, MAIL_PORT và kết nối mạng.';
        }

        return 'Không thể gửi mã xác thực. Vui lòng kiểm tra cấu hình SMTP và thử lại sau.';
    }
}
