<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'id' => 1,
                'question' => 'Làm sao để đăng ký khóa học?',
                'answer' => 'Bạn chỉ cần chọn khóa học yêu thích, bấm "Thêm vào giỏ hàng" hoặc "Mua ngay", sau đó tiến hành chọn phương thức thanh toán Momo, VNPay hoặc Chuyển khoản ngân hàng để hoàn thành.',
                'category' => 'Khóa học',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'question' => 'Tôi có được học thử miễn phí không?',
                'answer' => 'Có, hầu hết các khóa học trên hệ thống của chúng tôi đều cung cấp từ 1 đến 3 bài học xem trước (Preview) miễn phí để bạn có trải nghiệm tốt nhất trước khi quyết định mua.',
                'category' => 'Khóa học',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'question' => 'Làm thế nào để tôi được nhận chứng chỉ hoàn thành?',
                'answer' => 'Sau khi bạn hoàn thành 100% tất cả các bài giảng, bài tập thực hành và bài kiểm tra trắc nghiệm trong khóa học, hệ thống sẽ tự động kích hoạt tính năng nhận chứng chỉ hoàn thành (dạng PDF) để bạn tải về.',
                'category' => 'Chứng chỉ',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'question' => 'Tôi có thể thanh toán qua những hình thức nào?',
                'answer' => 'Hệ thống hỗ trợ thanh toán đa dạng qua ví điện tử MoMo, cổng thanh toán VNPay (hỗ trợ thẻ ATM nội địa, QR Code ngân hàng) và chuyển khoản trực tiếp qua ngân hàng.',
                'category' => 'Thanh toán',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'question' => 'Làm thế nào để đổi mật khẩu tài khoản?',
                'answer' => 'Bạn truy cập vào "Trang cá nhân", chọn mục "Cài đặt tài khoản" rồi chọn "Đổi mật khẩu". Nhập mật khẩu hiện tại và mật khẩu mới để cập nhật.',
                'category' => 'Tài khoản',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('faqs')->insert($faqs);
    }
}
