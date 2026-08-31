# Sửa TOP 10 trước bảo vệ — 31/08/2026

Tài liệu này ghi lại đợt sửa theo yêu cầu sau báo cáo phản biện. Báo cáo ban đầu giữ nguyên như bản ghi tình trạng trước sửa; không nên dùng các kết luận cũ để mô tả code đã sửa.

## Những gì đã thay đổi

| Ưu tiên / mã báo cáo | Sửa trong code | Hành vi sau sửa |
|---|---|---|
| 1 — F02 | `AssignmentController::download/retry`, helper kiểm tra quan hệ | Lesson phải thuộc course và có loại assignment; Assignment cũng phải thuộc course. Không tạo Assignment từ yêu cầu tải xuống. Sửa ID sang khóa khác nhận 404. |
| 2 — F03/F04 | `LearningProgressService::recordVideoProgress`, player HTML5/YouTube | Không tin `completed`, vị trí cuối video hoặc duration của học viên. Chỉ cộng số giây xem bị giới hạn bởi thời gian server giữa các heartbeat, tối đa 30 giây/request. Heartbeat đầu không cấp thời gian xem. Duration của lesson không bị client sửa. |
| 3 — F01 | `CartController::checkout` | `payos` được ánh xạ sang gateway `bank_transfer` hợp lệ với schema ở cả đơn miễn phí và đơn có tiền. |
| 4 — F07 | `PaymentGatewayService::finalizePayment` | Thanh toán thật đã xác nhận đúng số tiền được hưởng giá đã chốt, dù coupon vừa hết hạn/hết lượt. Ghi nhận paid, enrollment, coupon trong transaction; callback lặp không cộng lại. Mock/miễn phí vẫn kiểm tra hiệu lực coupon. |
| 5 — F08/F09 | `Order::prepareGatewayPayment/assertPaymentEditable`, các gateway, Cart/Order/PaymentController | Khóa hàng để chốt mã giao dịch trước gọi mạng. Retry giữ nguyên mã; không cho đổi cổng, đổi coupon/số tiền hoặc hủy cục bộ sau khi có mã. Callback thật hợp lệ đến muộn có thể đối soát đơn cancelled/failed cũ. IPN chưa hoàn tất được trả lỗi thay vì báo đã xử lý thành công. |
| 6 — F10 | Blade nhóm học `renderInviteButton/sendInvite` | Tạo nút bằng DOM, gắn event bằng closure; tên người dùng không chèn vào JavaScript inline. Cả nhánh lỗi và thử lại dùng cách này. |
| 7 — F05 | `QuizAttemptService::submit/terminate/saveProgress/startOrResume` | Kiểm tra deadline server theo phiên bản quiz gắn với attempt. Quá giờ chuyển expired, không chấm đáp án mới. Client không thể gia hạn bằng `remaining_seconds`. |
| 8 — F15 | `S3MultipartUploadController::complete` | Khóa course/lesson và kiểm tra lại trạng thái. Video của khóa đã duyệt đi vào ContentUpdate draft và job riêng; lesson live giữ nguyên cho đến quy trình duyệt. Job dispatch sau commit. |
| 9 — F06 | `Coupon::availableToUser/canBeUsedBy`, các đường checkout/apply | Coupon private chỉ xuất hiện và dùng được nếu có UserCoupon chưa dùng cấp cho đúng học viên. Kiểm tra tại server, không phụ thuộc giao diện. |
| 10 — F14 | `AssignmentController::submit` | Chặn replay submitted/graded trước kiểm tra deadline. Cập nhật có điều kiện theo status để request cũ không ghi đè bài đã được xử lý; tệp mới của request thua được xóa. |

Sửa liên quan: bỏ route quiz submit trùng ghi đè handler AJAX, để phản hồi có `lesson_completed/course_progress`; bỏ player video cũ không còn dùng; trang thanh toán hiển thị lý do khóa và vô hiệu hóa tùy chọn không hợp lệ.

## Chính sách và giới hạn cần hiểu khi bảo vệ

- **Thanh toán:** hiện vẫn một Payment/Order, chưa triển khai bảng nhiều payment attempt hay tác vụ đối soát tự động. Mã giao dịch được giữ nguyên kể cả khi gọi gateway timeout vì request có thể đã tới gateway. Liên kết hết hạn/hủy ở gateway cần hỗ trợ đối soát; không tự phát hành mã khác. Đây là đánh đổi có chủ đích để tránh mất mapping hoặc trả tiền hai lần.
- **Coupon:** ưu tiên không làm mất đơn đã nhận tiền. Những đơn đã chốt giá có thể làm `used_count` vượt `max_uses` nếu thanh toán cùng lúc. Muốn giới hạn cứng ngân sách/lượt cần bổ sung cơ chế giữ lượt và hết hạn giữ lượt; bản sửa này không tuyên bố đã có cơ chế đó.
- **Video:** heartbeat giới hạn việc nhảy tiến độ tức thời; không chứng minh học viên thật sự tập trung xem. Video phải có duration đúng do giảng viên/pipeline cung cấp. Bản sửa không tự suy đoán duration còn thiếu và không xóa tiến độ lịch sử.
- **Quiz:** hết deadline server thì không nhận đáp án mới, kể cả trình duyệt bị treo hoặc nộp chậm do mạng. Cần giải thích chính sách này cho học viên; không dựa riêng timer JavaScript.
- **Upload:** DB transaction không rollback được object đã hoàn tất bên S3. Tệp mồ côi hoặc job lỗi vẫn cần cơ chế dọn/retry hiện có; chưa kiểm thử S3 và FFmpeg thật trong đợt này.
- Không thay `.env`, không migrate database đang dùng, không chỉnh các đơn/điểm học tập cũ và không deploy. Database test mới độc lập tại cổng 3307, tên `web_onlinefea_test`.

## Kiểm thử

- PHP syntax: tất cả file PHP thay đổi đều qua kiểm tra; `git diff --check` không có lỗi whitespace.
- JavaScript: 2/2 test payload XSS qua, gồm HTTP thất bại và mất kết nối rồi thử lại. `node --check` và `npm run build` thành công.
- Toàn bộ PHPUnit: **700 tests, 5.952 assertions, 7 failures, 1 risky**; không phải toàn bộ suite đã xanh.
- Sáu failures thuộc `DemoDataIntegrityTest`: DB mới không chứa sẵn tài khoản/khóa học/lộ trình/video/chứng chỉ demo mà các test này yêu cầu. Test instructor trong cùng lớp không có assertion vì tập dữ liệu rỗng. Không seed dữ liệu lớn hay lấy dữ liệu thật để che các kết quả này.
- Một failure thuộc `FavoritesTest::test_header_favorite_badge_disappears_after_unfavorite`: test yêu cầu HTML không có `data-favorite-badge`, trong khi `resources/views/components/public/header.blade.php:129` luôn render badge và dùng `x-show="count > 0"` để ẩn. Test và template này không nằm trong các bản sửa. Cần kiểm thử DOM trình duyệt hoặc thống nhất lại hợp đồng HTML trong một đợt riêng.
- Bộ hồi quy tập trung TOP 10: **119/119 tests đạt, 473 assertions**, chạy lại sau toàn bộ thay đổi cuối. Log tại `storage/app/top10-regression-final.log`; log toàn suite tại `storage/app/top10-full-suite.log`.

Các kiểm thử mới nằm trong `tests/Feature/DefenseTopTenRegressionTest.php`, các ca deadline trong `tests/Feature/QuizAttemptStartResumeTest.php`, và `tests/js/study-group-invite-security.test.mjs`. Test PayOS cũ đòi đổi mã giao dịch khi link terminal đã được cập nhật theo chính sách giữ mã để đối soát.

Lệnh chạy lại (chỉ dùng DB test riêng, cấu hình kết nối bằng biến môi trường phù hợp):

```powershell
php vendor/bin/phpunit --filter 'DefenseTopTenRegressionTest|CartCheckoutTest|PayOSPaymentSecurityTest|MomoPaymentTest|LearningProgressTest|S3MultipartUploadTest|StudentAssignmentLibraryTest|RefundFlowTest|StudentOrderHistoryTest|QuizAttemptStartResumeTest' --do-not-cache-result
node --test tests/js/study-group-invite-security.test.mjs
npm run build
```

## Tự chạy trước buổi bảo vệ

1. Học viên A tải/thử lại bài tập của course B bằng cách sửa ID: 404, không tạo dữ liệu.
2. Gửi `completed=true`, seek cuối video, duration giả: không hoàn thành và không đổi duration lesson. Xem liên tục, pause/resume, refresh: tiến độ tăng theo heartbeat hợp lệ.
3. Xem video HTML5 và YouTube thật đến ngưỡng; tắt mạng rồi nối lại; kiểm tra trạng thái và vị trí resume. Đây vẫn là bước browser cần làm.
4. Checkout bằng giá trị API `payos` với đơn có tiền và coupon 100%: không lỗi enum.
5. Chốt link giảm giá, cho coupon hết hạn rồi mô phỏng callback hợp lệ: paid và enrollment; gửi lại callback: không tăng lượt lần nữa.
6. Mở link thanh toán, back về trang, thử đổi cổng/áp/gỡ coupon/hủy: bị chặn, số tiền và mã gateway giữ nguyên.
7. Gửi callback hợp lệ đến muộn cho đơn cancelled cũ: đối soát paid; chữ ký hoặc số tiền sai vẫn bị từ chối.
8. Đặt tên chứa dấu nháy và chuỗi JavaScript, tìm kiếm/mời/thử lại sau lỗi: không thực thi script.
9. Quiz quá giờ, sửa timer và POST submit/terminate/save trực tiếp: expired, không có điểm mới. Quiz đúng giờ vẫn chấm và cập nhật tiến độ.
10. Thay video của khóa published: học viên vẫn thấy video cũ; giảng viên có candidate để gửi duyệt; duyệt rồi mới đổi live.
11. Nhập coupon private không được cấp: từ chối; cấp cho đúng tài khoản: áp dụng được.
12. Bài tập PASS, đợi quá sáu giờ rồi replay submit: status/result/score giữ nguyên.

Chưa xác nhận browser end-to-end, thanh toán với nhà cung cấp thật, upload S3/FFmpeg thật hoặc stress test đa tiến trình. HTTP/queue trong regression được fake/mock để không phát sinh giao dịch hoặc upload thật.
