<?php

namespace App\Services;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service Quản lý Rút tiền Giảng viên & Danh sách Ngân hàng VietQR (PayoutService)
 * 
 * Chức năng chính:
 * 1. Lấy danh sách 50+ Ngân hàng thương mại tại Việt Nam (MBBank, Vietcombank, Techcombank, Agribank, VPBank...) từ VietQR API v2.
 * 2. Lưu Cache 24 giờ (`vietnam_banks_list`) tránh quá tải API bên thứ ba.
 * 3. Sinh đường dẫn ảnh mã QR Chuyển khoản nhanh Napas247 (VietQR Compact2) động cho Admin chuyển khoản rút tiền.
 */
class PayoutService
{
    /**
     * Danh sách 50+ Ngân hàng Việt Nam phổ biến làm fallback nếu API VietQR tạm offline
     */
    protected static array $fallbackBanks = [
        ['code' => 'MB', 'shortName' => 'MBBank', 'name' => 'Ngân hàng TMCP Quân Đội', 'bin' => '970422', 'logo' => 'https://api.vietqr.io/img/MB.png'],
        ['code' => 'VCB', 'shortName' => 'Vietcombank', 'name' => 'Ngân hàng TMCP Ngoại Thương Việt Nam', 'bin' => '970436', 'logo' => 'https://api.vietqr.io/img/VCB.png'],
        ['code' => 'TCB', 'shortName' => 'Techcombank', 'name' => 'Ngân hàng TMCP Kỹ Thương Việt Nam', 'bin' => '970407', 'logo' => 'https://api.vietqr.io/img/TCB.png'],
        ['code' => 'VPB', 'shortName' => 'VPBank', 'name' => 'Ngân hàng TMCP Việt Nam Thịnh Vượng', 'bin' => '970432', 'logo' => 'https://api.vietqr.io/img/VPB.png'],
        ['code' => 'ICB', 'shortName' => 'VietinBank', 'name' => 'Ngân hàng TMCP Công Thương Việt Nam', 'bin' => '970415', 'logo' => 'https://api.vietqr.io/img/ICB.png'],
        ['code' => 'BIDV', 'shortName' => 'BIDV', 'name' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam', 'bin' => '970418', 'logo' => 'https://api.vietqr.io/img/BIDV.png'],
        ['code' => 'ACB', 'shortName' => 'ACB', 'name' => 'Ngân hàng TMCP Á Châu', 'bin' => '970416', 'logo' => 'https://api.vietqr.io/img/ACB.png'],
        ['code' => 'TPB', 'shortName' => 'TPBank', 'name' => 'Ngân hàng TMCP Tiên Phong', 'bin' => '970423', 'logo' => 'https://api.vietqr.io/img/TPB.png'],
        ['code' => 'STB', 'shortName' => 'Sacombank', 'name' => 'Ngân hàng TMCP Sài Gòn Thương Tín', 'bin' => '970403', 'logo' => 'https://api.vietqr.io/img/STB.png'],
        ['code' => 'VAB', 'shortName' => 'VietABank', 'name' => 'Ngân hàng TMCP Việt Á', 'bin' => '970427', 'logo' => 'https://api.vietqr.io/img/VAB.png'],
        ['code' => 'VAB', 'shortName' => 'Agribank', 'name' => 'Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam', 'bin' => '970405', 'logo' => 'https://api.vietqr.io/img/VBA.png'],
        ['code' => 'HDB', 'shortName' => 'HDBank', 'name' => 'Ngân hàng TMCP Phát triển TP. HCM', 'bin' => '970437', 'logo' => 'https://api.vietqr.io/img/HDB.png'],
        ['code' => 'MSB', 'shortName' => 'MSB', 'name' => 'Ngân hàng TMCP Hàng Hải Việt Nam', 'bin' => '970426', 'logo' => 'https://api.vietqr.io/img/MSB.png'],
        ['code' => 'SHB', 'shortName' => 'SHB', 'name' => 'Ngân hàng TMCP Sài Gòn - Hà Nội', 'bin' => '970443', 'logo' => 'https://api.vietqr.io/img/SHB.png'],
        ['code' => 'EIB', 'shortName' => 'Eximbank', 'name' => 'Ngân hàng TMCP Xuất Nhập Khẩu Việt Nam', 'bin' => '970431', 'logo' => 'https://api.vietqr.io/img/EIB.png'],
        ['code' => 'OCB', 'shortName' => 'OCB', 'name' => 'Ngân hàng TMCP Phương Đông', 'bin' => '970448', 'logo' => 'https://api.vietqr.io/img/OCB.png'],
        ['code' => 'LPB', 'shortName' => 'LPBank', 'name' => 'Ngân hàng TMCP Lộc Phát Việt Nam', 'bin' => '970449', 'logo' => 'https://api.vietqr.io/img/LPB.png'],
        ['code' => 'VIB', 'shortName' => 'VIB', 'name' => 'Ngân hàng TMCP Quốc tế Việt Nam', 'bin' => '970441', 'logo' => 'https://api.vietqr.io/img/VIB.png'],
        ['code' => 'SCB', 'shortName' => 'SCB', 'name' => 'Ngân hàng TMCP Sài Gòn', 'bin' => '970429', 'logo' => 'https://api.vietqr.io/img/SCB.png'],
        ['code' => 'BVB', 'shortName' => 'BaoVietBank', 'name' => 'Ngân hàng TMCP Bảo Việt', 'bin' => '970438', 'logo' => 'https://api.vietqr.io/img/BVB.png'],
    ];

    /**
     * Lấy danh sách ngân hàng Việt Nam từ VietQR API có caching 24 giờ.
     * 
     * @return array Danh sách ngân hàng [code, shortName, name, bin, logo]
     */
    public function getVietNamBanks(): array
    {
        return Cache::remember('vietnam_banks_list', 86400, function () {
            try {
                $response = Http::timeout(5)->withoutVerifying()->get('https://api.vietqr.io/v2/banks');
                if ($response->successful() && $response->json('code') === '00') {
                    $banks = $response->json('data', []);
                    if (! empty($banks)) {
                        return collect($banks)->map(function ($bank) {
                            return [
                                'code' => $bank['code'] ?? $bank['shortName'],
                                'shortName' => $bank['shortName'] ?? $bank['name'],
                                'name' => $bank['name'],
                                'bin' => $bank['bin'] ?? '',
                                'logo' => $bank['logo'] ?? '',
                            ];
                        })->toArray();
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Không thể kết nối VietQR Bank API, dùng danh sách ngân hàng mặc định: ' . $e->getMessage());
            }

            return static::$fallbackBanks;
        });
    }

    /**
     * Sinh URL ảnh mã QR Chuyển khoản VietQR (Napas247) động chứa sẵn số tiền, nội dung rút tiền và tên tài khoản.
     * 
     * @param Withdrawal $withdrawal Yêu cầu rút tiền
     * @return string URL ảnh QR Code VietQR
     */
    public function generateVietQrUrl(Withdrawal $withdrawal): string
    {
        $bankCode = urlencode($withdrawal->bank_code ?? 'MB');
        $accNo = urlencode($withdrawal->bank_account_number);
        $amount = (int) $withdrawal->amount;
        $accountName = urlencode($withdrawal->bank_account_name);
        $addInfo = urlencode("RUT TIEN MAGV " . $withdrawal->user_id . " REQ" . $withdrawal->id);

        return "https://img.vietqr.io/image/{$bankCode}-{$accNo}-compact2.png?amount={$amount}&addInfo={$addInfo}&accountName={$accountName}";
    }

    /**
     * Tự động chi tiền cho Giảng viên qua PayOS Payout API
     * 
     * @param Withdrawal $withdrawal Yêu cầu rút tiền
     * @return array Kết quả trả về từ PayOS
     */
    public function processAutoPayout(Withdrawal $withdrawal): array
    {
        $clientId = env('PAYOS_PAYOUT_CLIENT_ID', env('PAYOS_CLIENT_ID'));
        $apiKey = env('PAYOS_PAYOUT_API_KEY', env('PAYOS_API_KEY'));
        $checksumKey = env('PAYOS_PAYOUT_CHECKSUM_KEY', env('PAYOS_CHECKSUM_KEY'));

        if (empty($clientId) || empty($apiKey) || empty($checksumKey)) {
            throw new \Exception('Chưa cấu hình API Keys Chi hộ (PayOS Payout) trong file .env');
        }

        $banks = $this->getVietNamBanks();
        $bankInfo = collect($banks)->first(function ($b) use ($withdrawal) {
            $code = strtolower($withdrawal->bank_code ?? '');
            return strtolower($b['code'] ?? '') === $code || strtolower($b['shortName'] ?? '') === $code;
        });

        $bin = $bankInfo['bin'] ?? '970422'; // Mặc định BIN MBBank nếu không khớp

        $amount = (int) $withdrawal->amount;
        $description = 'RUT TIEN REQ ' . $withdrawal->id;
        $description = preg_replace('/[^a-zA-Z0-9 ]/', '', $description);
        $description = substr($description, 0, 25);

        $referenceId = 'PO' . $withdrawal->id . 'T' . time();

        $params = [
            'amount' => $amount,
            'description' => $description,
            'referenceId' => $referenceId,
            'toAccountNumber' => (string) $withdrawal->bank_account_number,
            'toBin' => (string) $bin,
        ];

        // Sắp xếp các tham số theo bảng chữ cái và mã hóa URI (rawurlencode) theo chuẩn PayOS SDK
        ksort($params);
        $stringToSign = collect($params)
            ->map(fn($v, $k) => rawurlencode((string) $k) . '=' . rawurlencode((string) $v))
            ->implode('&');

        $signature = hash_hmac('sha256', $stringToSign, $checksumKey);

        $params['signature'] = $signature;

        $idempotencyKey = 'PO-REQ-' . $withdrawal->id . '-' . time();

        $response = Http::withoutVerifying()->withHeaders([
            'x-client-id' => $clientId,
            'x-api-key' => $apiKey,
            'x-idempotency-key' => $idempotencyKey,
            'x-signature' => $signature,
            'Content-Type' => 'application/json',
        ])->post('https://api-merchant.payos.vn/v1/payouts/', $params);

        $resData = $response->json();
        $code = (string) ($resData['code'] ?? '');

        if ($response->failed() || ($code !== '' && $code !== '00' && $code !== '0')) {
            $msg = $resData['desc'] ?? $resData['message'] ?? $response->body();
            throw new \Exception('Lỗi từ PayOS Payout API (' . ($code ?: 'HTTP ' . $response->status()) . '): ' . $msg);
        }

        return $resData['data'] ?? $resData;
    }
}
