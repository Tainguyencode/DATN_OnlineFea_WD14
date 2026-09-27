# Sửa 5 vấn đề trong luồng demo — 31/08/2026

> Cập nhật yêu cầu hủy đơn: cho phép chủ đơn hủy mọi đơn còn pending, kể cả đã phát hành link. Hiển thị toast thành công và trạng thái đã hủy; giữ reference để đối soát tiền về muộn. Hủy trong ứng dụng không đồng nghĩa hủy link ở cổng thanh toán. Mục 4 bên dưới mô tả quy tắc trước thay đổi này.

> Cập nhật theo yêu cầu tiếp theo: tạm ẩn ô mã giao dịch và cho phép duyệt rút tiền không có mã. Khi bỏ trống, lưu NULL, không tự sinh mã; kiểm tra nguồn tiền và chống duyệt trùng vẫn giữ nguyên. Mục 3 bên dưới mô tả bản sửa trước thay đổi này.

## Thay đổi

1. **Chat:** `DiscussionController::chatMessagePayload` trả `kind` cho cả HTTP/broadcast. `course-chat.js` tạo ID DOM theo loại tin (`msg-disc-*`, `msg-reply-*`), tránh va chạm ID giữa hai bảng. Test kiểm tra hiển thị đủ hai tin cùng ID số, không lặp khi nhận lại và thu hồi đúng tin.
2. **Rút tiền và hoàn tiền:** `User::refund_reserve` giữ phần thu nhập giảng viên của các đơn có refund pending/processing; không giữ toàn bộ giá trị đơn khi chỉ một phần là thu nhập giảng viên. Số dư khả dụng trừ khoản giữ này. Từ chối refund giải phóng khoản giữ; refund thành công loại thu nhập khỏi tổng earned. Admin khóa user và kiểm tra lại nguồn tiền trước khi approve, không chỉ kiểm tra trạng thái Withdrawal. Trang Admin vô hiệu hóa nút mở VietQR nếu nguồn tiền không đủ cho các yêu cầu đang chờ. `InstructorFinanceService` khóa các giảng viên của đơn theo thứ tự ID trước khi tạo/xử lý refund, dùng cùng khóa user với thao tác rút tiền.
3. **Mã giao dịch:** bỏ tự sinh FT ở cả JavaScript và backend. Form hiển thị ô bắt buộc nhập mã ngân hàng; backend yêu cầu chuỗi 4–100 ký tự. Đây vẫn là quy trình đối soát thủ công, không phải xác minh mã hoặc chuyển khoản tự động.
4. **Hủy đơn:** thêm `Order::canCancel`, dùng chung ở view và controller trong transaction. Đơn đã phát hành gateway_order_code không hiện form hủy; thay bằng hướng dẫn chờ đối soát/liên hệ hỗ trợ. Gọi API trực tiếp vẫn bị chặn.
5. **Thông báo hoàn tiền:** tạo thông báo cho Admin đang hoạt động ngay trong transaction tạo/cập nhật Refund. Nếu lưu thông báo lỗi, yêu cầu rollback. Gửi lại sau lỗi có thể thành công; gửi lại yêu cầu đã tồn tại không tạo thêm thông báo.

## Khoản đã chi trước khi hoàn tiền

Không sửa lịch sử đã chi và không tự chuyển/thu hồi tiền thật. `settlement_deficit = max(total_withdrawn - total_earnings, 0)` hiển thị riêng trong ví giảng viên, tránh che thiếu hụt bằng available_balance = 0. Thu nhập mới tự bù trừ số đã chi trong công thức số dư. Đây là số cần đối soát được tính từ dữ liệu hiện có, không phải một sổ công nợ kế toán độc lập hay tác vụ tự thu hồi tiền ngân hàng.

Khoản rút thiếu nguồn tiền vẫn pending để Admin đối soát/từ chối; không tự xóa yêu cầu. Hệ thống kiểm tra đủ nguồn cho tổng các khoản đang chờ của giảng viên, nên Admin cần từ chối các khoản không còn hợp lệ trước khi chi khoản còn lại.

## Kiểm tra

- Nhóm tập trung: 28 tests / 149 assertions đạt.
- Nhóm hồi quy luồng demo: 230 tests / 1108 assertions đạt (`storage/app/five-fixes-demo-regression.log`).
- Hai test JavaScript chat đạt; frontend Vite build thành công (`storage/app/five-fixes-build.log`).
- PHP syntax các file nghiệp vụ thay đổi và git diff --check đạt. Không thay route hoặc schema; không cần migration.
- Thêm 5 test PHP (WithdrawalFlowTest: 3, RefundFlowTest: 1, StudentOrderHistoryTest: 1) và 2 test JS.
- Dùng database thử nghiệm riêng, không gửi tiền, không thay `.env`, không đặt lại mật khẩu hoặc chỉnh dữ liệu thật.

## Giới hạn và cách kiểm tra thủ công

- Chưa thực hiện giao dịch ngân hàng và chưa kiểm thử trình duyệt end-to-end/concurrency bằng nhiều kết nối thực. Mã giao dịch do Admin nhập không được tự kiểm chứng với ngân hàng.
- Trước chuyển tiền thủ công, tải lại danh sách và kiểm tra nguồn tiền. Thao tác ở app ngân hàng là bên ngoài transaction; nếu có thay đổi trạng thái trong khoảng chuyển thật đến lúc xác nhận, cần đối soát thủ công, không chuyển lại chỉ vì app báo lỗi.
- Chuông/sidebar vẫn cần refresh; không thêm cơ chế realtime cho phần này.
- Test nhanh: gửi câu hỏi mới và trả lời không refresh; gửi refund rồi thử rút/duyệt; từ chối refund và kiểm tra tiền được giải phóng; duyệt refund và xác nhận khoản rút cũ bị chặn; để trống mã ngân hàng phải bị báo lỗi; mở đơn đã có QR không được thấy nút hủy.
- Các script `storage/app/demo-chat-probe.cjs` và `storage/app/DemoFlowProbeTest.php` là bằng chứng trạng thái lỗi trước sửa, không phải regression suite sau sửa. Dùng các test trong `tests/` để kiểm tra bản hiện tại.
