# Sửa ba xung đột của luồng demo

## Hoàn tiền và quyền học

`PaymentGatewayService::processRefund` khóa enrollment còn quyền học và tìm đơn paid khác của cùng học viên, cùng khóa học:

- Còn đơn paid: giữ quyền học, tiến độ, số bài hoàn thành và trạng thái hoàn thành. Nếu nguồn enrollment là đơn đang hoàn, chuyển order_id sang đơn paid còn lại.
- Không còn đơn paid: thu hồi quyền học và giảm enrollment_count đúng một lần.
- Không sửa enrollment đã bị hủy/thu hồi trước đó; không tự khôi phục quyền đã bị thu hồi sai trong dữ liệu lịch sử.
- Xử lý thanh toán và hoàn tiền dùng chung khóa giảng viên; đọc enrollment với lockForUpdate để tránh dùng snapshot cũ sau khi chờ khóa. Chưa stress-test bằng nhiều kết nối ngân hàng/concurrency thực.

Regression trong `DefenseTopTenRegressionTest`: bốn tổ hợp thứ tự tiền vào A/B và hoàn A/B, giữ nguyên tiến độ 40% và số bài hoàn thành, giữ số học viên; sau đó hoàn đơn cuối cùng và kiểm tra xử lý lặp không giảm thêm số học viên.

## Trang thanh toán

`resources/views/student/cart/pay.blade.php`:

- paid → trang thành công.
- cancelled/failed → trang kết quả tại checkout/failed, nơi controller phân biệt đúng trạng thái.
- refunded → chi tiết đơn; not_found → danh sách đơn.
- 401/419 → đăng nhập; pending tiếp tục chờ; lỗi mạng được thử lại.
- checkingStatus ngăn request chồng nhau; dừng timer khi nhận trạng thái kết thúc.
- Thêm data-refresh-on-history để Back/Forward đọc trạng thái mới từ server.
- Banner hướng dẫn hủy ở chi tiết đơn, nhắc không trả tiền vào QR cũ và liên hệ đối soát nếu đã chuyển tiền.

Test JavaScript thực thi hàm từ Blade với API giả lập: trạng thái kết thúc, mạng lỗi, pending, request chồng nhau và hết phiên.

## Kiểm tra

- Nhóm tập trung trước khi mở rộng kiểm tra đơn cuối: 24 tests / 166 assertions đạt.
- 10 tests JavaScript đạt; Vite build thành công.
- Log hồi quy toàn luồng sau sửa: `storage/app/three-conflicts-regression.log`.
- Kết quả hồi quy toàn luồng: **241 tests, 1214 assertions đạt**.
- PHP syntax và git diff --check các file liên quan đạt.
- Không thay schema, không sửa dữ liệu thật, không gọi chuyển khoản ngân hàng.
- Chưa thử trực tiếp PayOS thật hoặc trình duyệt hai tab trong lượt này; nên thử lại luồng hủy ở tab khác và quay lại trang thanh toán trước demo.
