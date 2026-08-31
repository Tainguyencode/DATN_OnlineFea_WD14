# Kiểm tra luồng demo: Client → Giảng viên → Admin

> Báo cáo dưới đây ghi nhận trạng thái trước sửa. Năm vấn đề đã được xử lý ở lượt tiếp theo; xem `SUA_5_LOI_LUONG_DEMO_2026-08-31.md` để biết cách sửa và phạm vi kiểm thử.

Ngày kiểm tra: 31/08/2026. Không sửa code ứng dụng trong lượt rà soát này.

## Phạm vi và bằng chứng

- Đọc route, controller, service, policy và giao diện liên quan mua/học khóa học, quiz, hỏi đáp, gửi/duyệt khóa học, rút tiền và hoàn tiền.
- Chạy nhóm test Authentication, CartCheckout, PayOSPaymentSecurity, MomoPayment, StudentOrderHistory, LearningProgress, QuizAttemptStartResume, QuizReview, RefundFlow, WithdrawalFlow, InstructorCourseCreate, CourseSubmissionUx, CourseReviewWorkflow, CourseApprovalInstructorApprovalWorkflow, DefenseTopTenRegression, StudentDashboardRefactor, CourseFeedbackReview.
- Kết quả: **225 tests, 1067 assertions đạt**. Log: `storage/app/demo-flow-tests.log`.
- Chạy phép tái hiện bổ sung trên database thử nghiệm riêng: **1 test, 6 assertions xác nhận hành vi lỗi hiện tại**, không phải chứng minh nghiệp vụ đúng. File: `storage/app/DemoFlowProbeTest.php`.
- Chạy mã JavaScript chat thực tế trong Node VM với DOM giả tối thiểu: mong đợi 2 tin, thực tế 1 tin. File: `storage/app/demo-chat-probe.cjs`.
- Kiểm tra syntax JavaScript chat và `git diff --check` đạt; Git có cảnh báo chuyển đổi CRLF/LF, không phải lỗi cú pháp.
- Không chạy giao dịch ngân hàng, không sửa dữ liệu thật; chưa kiểm tra tương tác bằng trình duyệt. Test thanh toán không chứng minh callback hoặc tài khoản cổng thanh toán trên máy demo đang hoạt động.

## 1. HIGH — Chat có thể ẩn câu trả lời nếu ID trùng tin mở đầu

- File: `resources/js/course-chat.js:12`, hàm `appendChatMessage`; dòng 34 kiểm tra trùng, dòng 41 gán ID. Đối chiếu `syncCourseChat` và `resources/views/components/learning/course-chat-drawer.blade.php` (Blade phân biệt `msg-disc-*` với `msg-reply-*`).
- Nguyên nhân: hàm append luôn dùng `msg-reply-${message.id}`, không phân biệt bản ghi Discussion với DiscussionReply. Hai bảng có dãy ID độc lập.
- Tình huống: học viên gửi câu hỏi mới; discussion có ID 7. Giảng viên trả lời với reply ID 7, trước khi học viên refresh.
- Hiện tại: câu hỏi bị gán `msg-reply-7`; câu trả lời bị coi là đã có nên không append. Polling cũng không cứu được vì kiểm tra cùng ID. Đây là lỗi hiển thị, không kết luận mất bản ghi DB.
- Bằng chứng: chạy `node storage/app/demo-chat-probe.cjs` thu được `expectedVisibleMessages: 2`, `actualVisibleMessages: 1`.
- Đúng: hiển thị cả câu hỏi và câu trả lời; thu hồi nhắm đúng loại tin.
- Đề xuất: đưa `kind` vào mọi payload (HTTP và broadcast), tạo ID theo loại tin ở append/sync/recall. Bổ sung test DOM cho tin mở đầu và câu trả lời trùng ID.
- Ảnh hưởng demo: trực tiếp ở bước Học viên hỏi → Giảng viên trả lời. Refresh có thể giúp vì Blade dựng đúng ID nhưng không phải cách sửa.

## 2. HIGH — Hoàn tiền không vô hiệu hóa khoản rút đang chờ bị mất nguồn thu

- File: `app/Models/User.php:517` (`getTotalEarningsAttribute`), `:541` (`getAvailableBalanceAttribute`); `app/Services/PaymentGatewayService.php:476` (`processRefund`); `app/Http/Controllers/Web/Admin/WithdrawalController.php:52` (`approve`).
- Nguyên nhân: thu nhập tính từ đơn `paid`; hoàn tiền đổi đơn sang `refunded`. Yêu cầu rút đang chờ không được đối soát lại; approve chỉ khóa Withdrawal và kiểm tra pending. Không kiểm tra tổng thu nhập còn đủ để chi sau hoàn tiền. `max(0, ...)` còn che khoản thiếu hụt khỏi số dư hiển thị.
- Tình huống tái hiện: đơn 50.000đ đem lại thu nhập 40.000đ → tạo yêu cầu rút 40.000đ → hoàn tiền đơn → duyệt yêu cầu rút cũ.
- Hiện tại đã xác nhận: total_earnings = 0; withdrawal vẫn approved; total_withdrawn = 40.000; available_balance = 0.
- Đúng: phải có chính sách giữ tiền trong thời hạn hoàn, dự phòng hoặc ghi nhận công nợ; không chi tiếp mà không kiểm tra nguồn thu. Nếu đã trả tiền trước khi hoàn, cần ghi nhận khoản phải thu/điều chỉnh, không chỉ chặn số dư ở 0.
- Đề xuất: xác định tiền đủ điều kiện rút, giữ khoản liên quan refund; khóa và đối soát nhất quán ở cả refund/withdrawal; kiểm tra lại trước khi Admin thực sự chuyển tiền và xác nhận. Phân biệt tổng thu nhập với tiền được phép rút.
- Bằng chứng: `DemoFlowProbeTest::test_demo_refund_does_not_block_existing_withdrawal_approval` tái hiện trên DB test; không gọi ngân hàng.
- Ảnh hưởng demo: luồng đơn lẻ có thể chạy qua nhưng bị hỏi vặn khi kết hợp hoàn tiền và rút tiền.

## 3. HIGH — Duyệt rút tiền thiếu mã đối soát vẫn báo đã chuyển tiền

- File: `app/Http/Controllers/Web/Admin/WithdrawalController.php:52`, `approve`.
- Nguyên nhân: `transaction_ref` nullable; để trống thì tự tạo `FT` + thời gian + số ngẫu nhiên, đồng thời ghi trạng thái approved và thông báo đã chuyển khoản.
- Tình huống: Admin gọi POST approve với body rỗng cho yêu cầu pending.
- Hiện tại: được duyệt; mã tham chiếu tự sinh. Phép tái hiện ở mục 2 cũng xác nhận việc này.
- Đúng: với quy trình thủ công, bắt buộc nhập mã giao dịch/đối soát thực tế và xác nhận đã chuyển; mã do ứng dụng tạo chỉ được gọi là mã nội bộ. Mã do Admin nhập vẫn không phải bằng chứng tự động xác minh với ngân hàng.
- Đề xuất: validate reference bắt buộc, thêm bằng chứng/chốt xác nhận phù hợp, thống nhất với quy trình hoàn tiền vốn yêu cầu reference. Không giới thiệu nút này là chuyển khoản tự động.

## 4. MEDIUM — Giao diện vẫn mời hủy đơn đã khóa thanh toán

- File: `resources/views/student/dashboard/orders/show.blade.php:22`; `app/Http/Controllers/Web/Student/OrderController.php:75`, `cancel`.
- Nguyên nhân: view chỉ kiểm tra order pending; controller còn kiểm tra payment.gateway_order_code.
- Tình huống: tạo link/QR thanh toán → quay lại chi tiết đơn đang pending → bấm Hủy đơn hàng.
- Hiện tại: nút vẫn hiện, backend từ chối và thông báo phải chờ đối soát. Backend chặn là đúng để tránh bỏ sót tiền về muộn; điểm sai nằm ở giao diện không phản ánh điều kiện.
- Đúng: ẩn/vô hiệu hóa nút khi đã phát hành link, hiển thị giải thích và hướng dẫn hỗ trợ.
- Đề xuất: dùng chung quy tắc canCancel cho view và backend, vẫn kiểm tra dưới transaction khi thực thi.

## 5. MEDIUM — Lưu yêu cầu hoàn tiền xong mới gửi thông báo ngoài transaction

- File: `app/Http/Controllers/Web/Student/RefundController.php:95` và `:123`, hàm `store`.
- Nguyên nhân: transaction tạo Refund kết thúc trước vòng gửi NotificationService::send cho Admin; không có cơ chế retry/outbox tại đây.
- Tình huống cần kiểm tra fault-injection: thao tác lưu thông báo lỗi sau khi Refund đã commit.
- Kết luận từ code: có thể trả lỗi cho học viên dù yêu cầu đã lưu; Admin có thể không nhận đủ thông báo. Gửi lại bị kiểm tra yêu cầu tồn tại, không tự bù notice. Chưa tái hiện bằng fault-injection trong lượt này.
- Đúng: trạng thái phản hồi nhất quán với kết quả lưu yêu cầu và có cơ chế đảm bảo thông báo được tạo/gửi lại.
- Đề xuất: nếu chỉ insert notice nội bộ, cùng transaction; nếu gửi ngoài hệ thống, dùng outbox/job sau commit có retry và khóa chống trùng.

## Những phần đã có bằng chứng tích cực

| Bước demo | Đánh giá trong phạm vi test/code |
|---|---|
| Đăng nhập | Nhóm test đạt; hash không hợp lệ được xử lý thay vì văng BCrypt RuntimeException. Không có nghĩa tài khoản dữ liệu thật đã được đặt lại mật khẩu. |
| Giỏ hàng, checkout, payOS/MoMo | Các test chọn chạy đạt: kiểm tra quyền, trạng thái, xử lý lặp và callback theo dữ liệu test. |
| Học ngay | Regression test kiểm tra link vào bài học và trường hợp nhiều khóa học đạt. |
| Học và tiến độ | Test tiến độ đạt; hoàn thành không chỉ dựa vào client tự báo hoàn tất. Chưa xác minh phát video trên trình duyệt máy demo. |
| Quiz | Test start/resume/deadline/review đạt; cần chọn bài đã mở khóa, câu hỏi đã xuất bản và đáp ứng điều kiện vào bài. |
| Hỏi đáp | Policy kiểm tra học viên sở hữu thảo luận/còn quyền học và giảng viên sở hữu khóa học; test API chat đạt nhưng còn lỗi DOM ở mục 1. |
| Gửi khóa học | Controller kiểm tra sở hữu, trạng thái, bản quyền, HLS và submissionCheck; test gửi duyệt đạt. |
| Admin duyệt | Service kiểm tra checklist; tự xuất bản còn phụ thuộc hồ sơ giảng viên được duyệt, không bị khóa và lựa chọn publish. Test workflow đạt. |
| Gửi rút tiền | Test ngân hàng/số tiền/khả dụng/gửi lặp/thông báo/rollback đạt; không bao phủ vấn đề liên nghiệp vụ ở mục 2. |
| Hoàn tiền | Test đơn miễn phí/quá hạn/tiến độ cao/dữ liệu ngân hàng/duyệt thủ công đạt; không tự động xác minh chuyển khoản ngân hàng. |

## Lưu ý thao tác dễ nhầm, không coi là lỗi chức năng

- Chuông và badge sidebar Admin hiện render theo request, cần refresh. Chat có polling riêng 1,5 giây; không đồng nghĩa chuông/sidebar cũng realtime.
- Trang chi tiết hoàn tiền Admin hiện chỉ xem thông tin. Nút xử lý nằm ở trang danh sách `admin/refunds`; quay lại danh sách để mở modal duyệt/từ chối.
- Dùng khóa học đã xuất bản cho phần học viên; dùng khóa học nháp khác cho phần giảng viên gửi duyệt. Đây là hệ quả thứ tự Client → Giảng viên → Admin.
- Dùng đơn khác cho hoàn tiền, tránh đơn vừa demo học có tiến độ >= 50%.
- Chưa kiểm tra frontend end-to-end, callback thật qua Internet, email/2FA thật, xử lý HLS/queue và số dư/dữ liệu tài khoản demo hiện tại: **CẦN KIỂM TRA THÊM**.

## Ưu tiên trước demo

1. Sửa ID tin nhắn chat.
2. Xử lý chính sách tiền khả dụng khi hoàn tiền và khoản rút đang chờ.
3. Không tự sinh mã ngân hàng thay cho mã đối soát thật.
4. Đồng bộ nút hủy đơn với điều kiện backend.
5. Làm nhất quán việc lưu yêu cầu hoàn tiền và thông báo.

Đạt 225 test không đồng nghĩa toàn bộ website không lỗi; hai phép tái hiện bổ sung đã cho thấy khoảng trống của bộ test hiện tại.
