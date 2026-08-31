# Rà soát luồng Client → Giảng viên → Admin sau các thay đổi thanh toán

> Đã sửa ba điểm trong lượt tiếp theo. Xem `SUA_3_XUNG_DOT_LUONG_2026-08-31.md`; các phép tái hiện trong storage/app bên dưới là bằng chứng trước sửa, không phải regression suite hiện tại.

Ngày: 31/08/2026. Lượt này chỉ rà soát, chạy test và tạo bằng chứng; không sửa code nghiệp vụ.

## Kiểm chứng sâu bổ sung theo yêu cầu

Không sửa code ứng dụng. Chạy `storage/app/DetailedFlowConflictProbeTest.php` với 4 bộ dữ liệu độc lập, 36 assertions; mỗi trường hợp gọi endpoint Admin duyệt hoàn tiền thật trong ứng dụng trên DB test. Các lệnh ghi nhận thanh toán được gọi qua service, không chuyển tiền thật hoặc giả nhận rằng webhook qua Internet đã được thử.

| Thứ tự ghi nhận tiền | Đơn được hoàn | Kết quả đã quan sát |
|---|---|---|
| B rồi A | A | B vẫn paid nhưng enrollment bị cancelled |
| B rồi A | B | A vẫn paid nhưng enrollment bị cancelled |
| A rồi B | A | B vẫn paid nhưng enrollment bị cancelled |
| A rồi B | B | A vẫn paid nhưng enrollment bị cancelled |

Trong cả 4 trường hợp, `withLearningAccess()` không còn tìm được quyền học và enrollment_count của khóa học bị giảm 1. Vì vậy chỉ thêm `where('order_id', $refundOrderId)` là chưa đủ: khi hoàn chính đơn đang cấp enrollment, hệ thống còn phải tìm đơn paid khác để duy trì/chuyển nguồn quyền học. Các số lượng test ở đây là bằng chứng tái hiện lỗi, không phải kết quả chức năng đúng.

Chạy `storage/app/checkout-polling-probe.cjs`, trích nguyên hàm `checkStatus` từ Blade và thực thi trong Node VM với response API mô phỏng:

| Status API | Dừng timer | Chuyển trang |
|---|---|---|
| paid | Có | Trang thành công |
| cancelled | Không | Không |
| failed | Không | Không |
| refunded | Không | Không |
| not_found | Không | Không |

Đã xác nhận trang pay không có marker `data-refresh-on-history`. Đây là bằng chứng thực thi hàm JavaScript, chưa phải kiểm thử trình duyệt hai tab hoặc BFCache thực.

Đối chiếu lại banner dòng 130–131 với `Order::canCancel`: banner hướng dẫn liên hệ hỗ trợ khi cần hủy, còn backend cho hủy khi pending. Xác nhận đây là hướng dẫn chưa cập nhật, không phải lỗi chặn API hủy và không làm mất dữ liệu. Mức tác động thực tế của mục 3 thấp hơn hai mục đầu.

## Kết quả xác minh

- 237 tests PHP, 1154 assertions đạt. Nhóm test gồm xác thực, giỏ hàng, PayOS/MoMo, lịch sử đơn hàng, tiến độ, quiz, tạo/gửi/duyệt khóa học, đánh giá, hoàn tiền, rút tiền và các regression trước đó.
- 7 tests JavaScript đạt (chat, lịch sử điều hướng, an toàn hiển thị lời mời nhóm).
- Vite build thành công.
- Phép tái hiện riêng `storage/app/FinalFlowConflictProbeTest.php`: 1 test, 7 assertions xác nhận hành vi lỗi hiện tại ở mục 1. Test này cố ý assert trạng thái lỗi để làm bằng chứng, không phải chứng minh chức năng đúng.
- DB thử nghiệm riêng, không thay dữ liệu thật và không thực hiện giao dịch ngân hàng.
- Log: `storage/app/final-flow-review-tests.log`, `storage/app/final-flow-review-build.log`.
- Chưa chạy trực tiếp trình duyệt/PayOS thật, webhook qua Internet, hàng đợi HLS hoặc email/2FA thật. Những phần này **CẦN KIỂM TRA THÊM** trước khi demo.

## 1. HIGH — Hoàn tiền đơn cũ có thể thu hồi quyền học từ đơn mới vẫn paid

**File/function:** `app/Services/PaymentGatewayService.php:448` (`finalizePayment`), `:538` (`enrollStudent`), `:583` (`processRefund`), đặc biệt truy vấn Enrollment ở khoảng dòng 647.

**Cách tái hiện:**

1. Học viên hủy đơn A trong website. Link cổng thanh toán chưa chắc đã bị vô hiệu hóa.
2. Mua lại cùng khóa học bằng đơn B và thanh toán thành công; enrollment thuộc đơn B.
3. Tiền thực tế/callback hợp lệ về muộn cho A; A được đối soát thành paid.
4. Admin hoàn tiền đơn A bị thanh toán dư.

**Đã xác nhận trên DB test:** A thành refunded, B vẫn paid, nhưng enrollment thuộc B bị chuyển cancelled. Học viên mất quyền học dù còn đơn hợp lệ đã thanh toán.

**Nguyên nhân:** `processRefund` thu hồi tất cả enrollment theo user_id + course_id, không xét đơn cấp quyền và không kiểm tra còn nguồn thanh toán hợp lệ khác. `enrollStudent` giữ enrollment đang active nên callback A về muộn không thay order_id của enrollment B.

**Kết quả đúng:** ghi nhận cả hai khoản tiền, xử lý khoản dư và duy trì quyền học nếu còn đơn hợp lệ. Chỉ thu hồi khi không còn nguồn cấp quyền học.

**Hướng sửa:** đối soát quyền học theo các đơn paid còn lại của học viên/khóa học; nếu enrollment đang trỏ vào đơn hoàn nhưng có đơn khác hợp lệ, chuyển nguồn cấp quyền; nếu đang trỏ đơn khác thì không thu hồi. Số lượng enrollment chỉ giảm khi quyền học thực sự bị thu hồi. Không chỉ thêm điều kiện order_id rồi bỏ qua trường hợp phải chuyển nguồn quyền học. Cần test cả thứ tự A/B thanh toán và A/B hoàn tiền.

## 2. MEDIUM — Trang thanh toán còn giữ trạng thái cũ khi đơn bị hủy ở tab khác

**File/function:** `resources/views/student/cart/pay.blade.php:32` (`initPolling`), `:37` (`checkStatus`); `app/Http/Controllers/Web/Student/CartController.php:944` (`checkStatus`).

**Cách kiểm tra:** mở trang thanh toán ở tab A, sang tab B hủy đơn trong chi tiết đơn hàng, rồi quay lại tab A.

**Kết luận từ code:** API trả cancelled nhưng JavaScript chỉ xử lý paid. Trang vẫn giữ form thanh toán và timer kiểm tra mỗi 3 giây. Bấm tiếp thanh toán thì backend chặn do đơn không còn pending. Đây là lệch trạng thái giao diện, không kết luận backend cho thanh toán lại đơn đã hủy.

**Back/Forward:** chỉ danh sách đơn, chi tiết đơn và trang kết quả hủy có `data-refresh-on-history`; trang thanh toán không có marker nên cơ chế mới chưa xử lý trang này.

**Hướng sửa:** xử lý cancelled/failed/refunded/not_found theo trạng thái, dừng polling và chuyển về trang kết quả phù hợp; refresh/revalidate trang thanh toán khi khôi phục lịch sử; tránh tạo polling chồng nhau. Cần thử trình duyệt hai tab để xác nhận trải nghiệm thực tế.

## 3. MEDIUM — Hướng dẫn hủy trên trang thanh toán trái với quy tắc hiện tại

**File:** `resources/views/student/cart/pay.blade.php:129–131`, đối chiếu `app/Models/Order.php:14` (`canCancel`).

**Hiện tại:** banner vẫn hướng dẫn liên hệ hỗ trợ nếu cần hủy, nhưng chi tiết đơn cho phép chủ đơn tự hủy khi pending, dù đã có reference.

**Kết quả đúng:** hướng dẫn nhất quán: có thể hủy đơn tại chi tiết đơn; không tiếp tục chuyển tiền theo QR cũ; tiền về muộn vẫn được đối soát.

**Hướng sửa:** cập nhật nội dung và thêm link tới chi tiết đơn. Không bỏ khóa sửa số tiền/mã giảm giá của liên kết đã phát hành vì đó là quy tắc khác với quyền hủy.

## Các thay đổi đang nhất quán trong phạm vi đã kiểm tra

- Học ngay dẫn đến khóa học/bài học đã mua theo regression test.
- Hủy đơn nội bộ: chủ đơn pending được hủy, trạng thái cancelled và thông báo thành công; giữ reference để đối soát.
- Hủy ở PayOS: xác nhận từ API trước khi ghi cancelled; tham số cancel trên URL không tự ghi đè trạng thái. Đơn paid chuyển trang thành công.
- Quay lại từ chi tiết đơn dùng link danh sách rõ ràng; các trang đơn có xử lý history cache, thông báo cũ được loại bỏ. Test JS và HTTP đạt nhưng chưa thao tác trình duyệt thật trong lượt này.
- Chat tách ID câu hỏi/câu trả lời; test chống trùng ID và thu hồi đúng tin đạt.
- Gửi và duyệt khóa học: test điều kiện nội dung/quyền/checklist đạt.
- Refund và thông báo Admin cùng transaction; test rollback/retry đạt.
- Rút tiền: giữ thu nhập liên quan refund pending/processing, kiểm tra nguồn tiền trước approve; test thiếu nguồn, hoàn trước/sau rút và giải phóng sau từ chối đạt.
- Mã ngân hàng cho phép bỏ trống theo yêu cầu người dùng: không coi là regression; đây vẫn là giới hạn đối soát thủ công cần trình bày trung thực.

## Ưu tiên trước demo

1. Sửa mục 1 trước khi demo chuỗi hủy → mua lại → hoàn tiền cùng khóa học.
2. Đồng bộ trạng thái trang thanh toán với thao tác hủy và Back/Forward.
3. Sửa banner hướng dẫn.

Chưa thể kết luận toàn bộ website không còn lỗi chỉ vì nhóm test hiện có đạt; phép tái hiện bổ sung đã xác nhận xung đột liên nghiệp vụ chưa được suite bao phủ.
