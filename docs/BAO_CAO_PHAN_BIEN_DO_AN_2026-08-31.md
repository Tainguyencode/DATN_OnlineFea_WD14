# BÁO CÁO PHẢN BIỆN SOURCE CODE — ONLINE FEA

> Bản ghi **trước sửa**. Sau yêu cầu sửa TOP 10, xem [nhật ký sửa và kiểm thử](SUA_TOP_10_TRUOC_BAO_VE_2026-08-31.md) để biết trạng thái code mới; các nhận định “hiện tại” bên dưới mô tả lúc audit ban đầu.

Ngày rà soát: 31/08/2026. Vai trò: giảng viên kiểm tra nghiệp vụ, tính toàn vẹn dữ liệu và khả năng bảo vệ thiết kế.

**Kết luận:** Project có nhiều cơ chế được đầu tư đúng hướng: FormRequest, policy, transaction, khóa hàng, version quiz, batch import, chữ ký webhook và unique constraint. Tuy nhiên, các lớp bảo vệ chưa nhất quán giữa những đường API cùng thực hiện một nghiệp vụ. Những điểm dễ bị bắt lỗi nhất là tải bài tập khác khóa, giả tiến độ video, thanh toán và coupon, nộp quiz quá giờ, thay video vượt quy trình duyệt và XSS ở màn hình mời nhóm.

## 1. Phạm vi và mức độ xác minh

- Đã lập danh mục 833 file trong app, routes, resources, database và tests; đọc route, middleware, các service/controller/model/migration và frontend liên quan các luồng chính; tìm kiếm xuyên source các điểm SQL raw, HTML raw, upload, request input, quyền và xử lý trạng thái.
- Đây là **rà soát tĩnh theo luồng kết hợp kiểm tra môi trường**, không phải xác nhận đã chạy mọi dòng trong 833 file. vendor/node_modules không được audit từng dòng; chưa kiểm toán lịch sử Git hoặc CVE của dependencies.
- Không sửa source ứng dụng, migration, cấu hình hoặc test. Chỉ thêm báo cáo này.
- PHP CLI: 8.3.16; artisan báo Laravel Framework 13.29.0.
- TestingDatabaseIsolationTest: **1 test, 3 assertions đạt**. Test này kiểm tra tên database cấu hình; không chứng minh kết nối MySQL hoạt động.
- CartCheckoutTest chạy với stop-on-error: **1 test, 0 assertions, 1 error**, SQLSTATE[HY000][2002], MySQL 127.0.0.1:3306 từ chối kết nối đến web_onlinefea_test. Không phải kết quả fail assertion của checkout.
- Đã dừng lượt chạy toàn bộ PHPUnit vì môi trường DB không khả dụng; không có kết luận toàn bộ test pass/fail.
- Chưa chạy browser end-to-end, giao dịch thật, S3/FFmpeg/Reverb/SMTP thật, tải đồng thời hoặc fault injection vào DB. Các kết quả “hiện tại” bên dưới là hành vi suy ra từ đường code đã dẫn, trừ kết quả môi trường nêu trên.
- Không công bố khóa API, mật khẩu hoặc nội dung nhạy cảm trong .env. APP_ENV=local và APP_DEBUG=true được dùng để đánh giá rủi ro đưa bản local ra internet, không tự kết luận production bị lộ.

**Cách đọc mức độ:**

- 🔴 CRITICAL: có thể phá nghiệp vụ chính, mất quyền kiểm soát dữ liệu hoặc nhận tiền nhưng không cấp dịch vụ.
- 🟠 HIGH: cần sửa trước bảo vệ.
- 🟡 MEDIUM: chất lượng, mở rộng, đồng thời hoặc thiết kế cần giải thích rõ.
- 🟢 LOW: dọn dẹp và tài liệu.
- “CẦN KIỂM TRA THÊM” là phần chưa đủ bằng chứng môi trường hoặc chính sách để kết luận, không phải khẳng định có khai thác thành công.

## 2. Bản đồ chức năng thực sự có trong source

| Nhóm | Thành phần chính | Nội dung cần bảo vệ |
|---|---|---|
| Tài khoản | AuthService, AuthController, SocialAuthService, middleware | Email/username, đăng ký student/instructor, xác minh email, reset password, 2FA, single-session |
| Khóa học | CourseController, CurriculumLessonService, CourseReviewService, ContentUpdateService | Catalog, chapter/section/lesson, duyệt khóa và cập nhật nội dung |
| Import | LessonImport*, FullCourseImport* | Preview, xác nhận batch, validate workbook, tạo đồ thị dữ liệu |
| Bán khóa học | CartController, PaymentGatewayService, MomoService | Giỏ hàng, coupon, PayOS/MoMo, đơn miễn phí, callback, hủy đơn |
| Tài chính | RefundController, WalletController, WithdrawalController | Hoàn tiền, hoa hồng, thu nhập và rút tiền giảng viên |
| Học tập | LearningProgressService, CourseCompletionService | Video/document/assignment/quiz, tiến độ, điểm XP, chứng chỉ |
| Quiz | QuizAttemptService, QuizVersioningService, QuizService | Attempt, resume, thời gian, version, chấm lại, giám sát phía client |
| Tương tác | DiscussionController, StudyGroupController, ReviewService | Chat khóa học, nhóm học, lời mời, đánh giá, bình luận |
| Hỗ trợ/AI | SupportTicketService, Ai\LessonAiService, Ai\LearningPathAiService | Ticket, file đính kèm, trợ lý bài học/lộ trình |
| Vận hành | Reverb, queue jobs, console commands | HLS, mail, bảng xếp hạng, nhắc học và tác vụ nền |

## 3. Phát hiện cụ thể

### F01 — 🔴 CRITICAL — Checkout chấp nhận payos nhưng ghi giá trị không thuộc enum database

- **File/hàm/vị trí:** [CartController::checkout](D:/DATN/app/Http/Controllers/Web/Student/CartController.php:223), dòng 338 và 385; [migration payments](D:/DATN/database/migrations/2026_06_23_052454_create_payments_table.php:23); migration 2026_08_30_000003_remove_sepay_gateway_from_payments_table.php cũng chỉ giữ momo, bank_transfer.
- **Nguyên nhân:** validate cho phép payment_method=payos; cả nhánh đơn miễn phí và đơn có tiền gán gateway trực tiếp từ payment_method. Việc ánh xạ payos sang bank_transfer chỉ có ở processPayment, quá muộn với checkout ban đầu.
- **Giảng viên test:** giỏ có khóa hợp lệ, POST checkout với payment_method=payos; làm lại với coupon 100%.
- **Hiện tại:** theo schema migration và MySQL strict=true, insert Payment không hợp lệ, transaction rollback và request lỗi; CẦN KIỂM TRA THÊM schema DB thực tế nếu đã sửa tay ngoài migration.
- **Đúng/cách sửa:** một ánh xạ gateway dùng chung cho mọi nhánh; persist bank_transfer hoặc thay schema có chủ đích. Kiểm thử cả miễn phí/có phí và không chỉ thao tác qua radio button.

### F02 — 🔴 CRITICAL — IDOR tại tải tài liệu và làm lại bài tập

- **File/hàm/vị trí:** [AssignmentController::download](D:/DATN/app/Http/Controllers/Web/Student/AssignmentController.php:65), ::retry dòng 130; [route](D:/DATN/routes/web.php:149).
- **Nguyên nhân:** kiểm tra enrollment theo Course trên URL nhưng dùng Lesson được bind độc lập. Không xác nhận lesson thuộc course và đúng type trong download/retry. download còn tự tạo Assignment nếu chưa có.
- **Giảng viên test:** mua khóa A; thay lesson trên URL download thành ID bài tập B của khóa chưa mua. Sau đó thử một lesson không phải assignment.
- **Hiện tại:** vượt được kiểm tra enrollment nhờ A, dùng tài liệu/assignment của B, có thể tạo submission hoặc tạo Assignment cho lesson sai loại. Không có scopeBindings bảo vệ cặp route trong cấu hình đã đọc.
- **Đúng/cách sửa:** dùng cùng kiểm tra course–lesson–assignment như submit; policy tập trung hoặc scoped binding phù hợp; không tạo nội dung khóa học từ request tải của học viên. Quyền đối tượng phải được kiểm tra trước bất kỳ ghi DB nào.

### F03 — 🔴 CRITICAL — Có thể hoàn thành video bằng request giả

- **File/hàm/vị trí:** [LearningProgressService::recordVideoProgress](D:/DATN/app/Services/LearningProgressService.php:142), nhánh completed dòng 168 và lastPosition dòng 218; [CourseController::updateLessonProgress](D:/DATN/app/Http/Controllers/Web/CourseController.php:432); [UpdateLessonProgressRequest](D:/DATN/app/Http/Requests/Learning/UpdateLessonProgressRequest.php:22).
- **Nguyên nhân:** completed=true được client gửi và server lập tức ghi hoàn thành. Ngoài ra, gửi last_position_seconds đến cuối video cũng ép watched_seconds bằng toàn bộ duration, kể cả played_seconds=0.
- **Giảng viên test:** học viên đã mua khóa, POST /courses/{course}/lessons/{lesson}/progress với {"completed":true}. Thử API video progress với vị trí cuối video nhưng chưa xem.
- **Hiện tại:** nhận tiến độ 100%, có thể được cộng điểm bài học; khi các điều kiện khác đủ, được hoàn thành khóa/chứng chỉ. Đây không phải bypass enrollment mà là bypass điều kiện học.
- **Đúng/cách sửa:** server chỉ dùng sự kiện ended/completed như tín hiệu tham khảo; tính thời lượng hợp lệ bằng heartbeat có giới hạn server và kiểm tra ngưỡng. Không cho vị trí seek thay thế thời gian thực đã xem.

### F04 — 🟠 HIGH — Học viên sửa được thời lượng bài học dùng chung

- **File/hàm/vị trí:** [LearningProgressService::durationSeconds](D:/DATN/app/Services/LearningProgressService.php:474); [VideoPlayerController::updateProgress](D:/DATN/app/Http/Controllers/Web/Student/VideoPlayerController.php:276).
- **Nguyên nhân:** video_duration_seconds từ client được dùng update trực tiếp lessons.duration_seconds và duration nếu lệch quá 3 giây; validation không giới hạn theo metadata video tin cậy.
- **Giảng viên test:** video 600 giây, gửi video_duration_seconds=1, last_position_seconds=1. Kiểm tra lại thời lượng bài của học viên khác và trang quản trị.
- **Hiện tại:** metadata chung của lesson thành 1 giây; dữ liệu tiến độ và tổng thời lượng có thể sai. Số cực lớn cũng có nguy cơ vượt kiểu cột.
- **Đúng/cách sửa:** duration chỉ do FFprobe/job xử lý video hoặc giảng viên qua workflow có quyền cập nhật. Thông tin player báo về chỉ lưu telemetry, không sửa bài học.

### F05 — 🟠 HIGH — Nộp quiz quá giờ vẫn được chấm nếu gọi API trực tiếp

- **File/hàm/vị trí:** [QuizAttemptService::submit](D:/DATN/app/Services/QuizAttemptService.php:77); ::findInProgress dòng 252; [QuizController::gradeBoundAttempt](D:/DATN/app/Http/Controllers/Web/Student/QuizController.php:309).
- **Nguyên nhân:** findInProgress chuyển expired khi hết giờ, nhưng submit chỉ yêu cầu status=in_progress trước khi grade; không chặn theo started_at + time_limit_minutes. startOrResume cũng trả attempt đang mở trước khi kiểm tra hết giờ.
- **Giảng viên test:** bắt đầu quiz 1 phút; không reload trang hoặc gọi luồng tìm attempt; chặn JS tự nộp, sau hơn 1 phút POST submit với attempt_id và đáp án.
- **Hiện tại:** nếu attempt vẫn in_progress, service chấm và ghi completed; remainingTime chỉ dùng để ghi số giây còn lại, không từ chối bài.
- **Đúng/cách sửa:** kiểm tra deadline server trong cùng lock tại start/resume/save/submit; hết hạn phải chuyển trạng thái theo chính sách thống nhất trước khi chấm. Grace period nếu có phải định nghĩa rõ.

### F06 — 🟠 HIGH — Coupon riêng không được ràng buộc người được cấp

- **File/hàm/vị trí:** [Coupon::scopeAvailableToUser](D:/DATN/app/Models/Coupon.php:50), ::isValid dòng 124, ::isUsedByUser dòng 145; CartController::checkout, ::applyCoupon và ::applyCouponToOrder.
- **Nguyên nhân:** có is_private và UserCoupon nhưng scope không lọc quyền sở hữu coupon riêng; các đường áp dụng chỉ kiểm tra hiệu lực, đã dùng và khóa học đủ điều kiện.
- **Giảng viên test:** cấp mã private cho A, dùng B xem danh sách mã rồi nhập mã đó ở checkout.
- **Hiện tại:** B không bị từ chối chỉ vì không được cấp mã; scope còn có thể đưa mã riêng vào danh sách khả dụng.
- **Đúng/cách sửa:** một hàm canBeUsedBy(user) kiểm tra private + grant chưa dùng/hợp lệ, áp dụng cả listing, preview, checkout và finalize trong transaction.

### F07 — 🔴 CRITICAL — Đã trả tiền nhưng đơn failed vì coupon hết lượt/hết hạn

- **File/hàm/vị trí:** [PaymentGatewayService::finalizePayment](D:/DATN/app/Services/PaymentGatewayService.php:382), ::lockAndValidateCoupon dòng 438; [PaymentController::payosIpn](D:/DATN/app/Http/Controllers/Web/PaymentController.php:22).
- **Nguyên nhân:** chưa giữ lượt coupon khi tạo đơn. Sau khi ngân hàng xác nhận tiền, finalize mới kiểm tra lại isValid/isUsedByUser; nếu không hợp lệ thì đổi order/payment thành failed. payosIpn không dùng kết quả bool này và vẫn trả success=true.
- **Giảng viên test:** hai người tạo link với mã chỉ còn 1 lượt; cả hai trả tiền. Hoặc tạo link trước thời điểm mã hết hạn rồi trả sau.
- **Hiện tại:** một giao dịch có tiền thật nhưng không enrollment, webhook đã được ACK, không có nhánh bù tiền trong đoạn xử lý này.
- **Đúng/cách sửa:** reservation coupon có TTL ở bước tạo đơn, hoặc giữ cam kết giá snapshot khi đã nhận tiền; dùng trạng thái paid_needs_reconciliation/refund_required khi có xung đột. Không biểu diễn “không nhận được tiền” cho giao dịch đã nhận tiền.

### F08 — 🔴 CRITICAL — Sửa tiền/cổng của order pending làm mất liên kết giao dịch đã phát hành

- **File/hàm/vị trí:** [CartController::applyCouponToOrder](D:/DATN/app/Http/Controllers/Web/Student/CartController.php:528), ::removeCouponFromOrder dòng 632, ::processPayment dòng 688; [PaymentGatewayService::createPayOSUrl](D:/DATN/app/Services/PaymentGatewayService.php:39).
- **Nguyên nhân:** có thể sửa order.total_amount và payment.amount sau khi link thanh toán đã tồn tại; không hủy/version hóa link cũ. Trạng thái pending được đọc ngoài transaction và các hàm sửa coupon không lock/recheck order. Đổi cổng ghi đè gateway của một Payment duy nhất.
- **Giảng viên test:** mở QR số tiền X ở tab 1; tab 2 áp dụng/gỡ coupon thành Y; thanh toán QR cũ. Thử PayOS → MoMo rồi thanh toán link PayOS cũ. Cho callback chạy xen giữa đọc pending và ghi coupon.
- **Hiện tại:** webhook có thể bị báo sai số tiền hoặc không tìm thấy gateway tương ứng; race còn có thể đổi số tiền của order vừa paid.
- **Đúng/cách sửa:** payment attempt bất biến gồm gateway, reference, amount, currency; đơn đã có attempt hoạt động không tùy tiện sửa. Khi đổi phải tạo attempt mới và vẫn lưu, đối soát được attempt cũ. Lock order và kiểm tra trạng thái lại trước commit.

### F09 — 🟠 HIGH — Hủy đơn nội bộ trước callback thành công làm mất ghi nhận tiền

- **File/hàm/vị trí:** [OrderController::cancel](D:/DATN/app/Http/Controllers/Web/Student/OrderController.php:87); PaymentGatewayService::finalizePayment dòng 389; PaymentController::payosIpn.
- **Nguyên nhân:** cancel chỉ đổi DB, chưa phối hợp trạng thái gateway. finalize bỏ qua mọi order không pending/paid; PayOS vẫn ACK success sau khi service trả false.
- **Giảng viên test:** đã chuyển tiền nhưng webhook chậm; học viên hủy đơn trước khi callback đến. Hoặc thanh toán một link cũ còn hiệu lực sau hủy.
- **Hiện tại:** order cancelled/payment failed, callback không cấp khóa, chưa có dòng đối soát/bù tương ứng trong luồng này.
- **Đúng/cách sửa:** tách cancel_requested và cancelled_confirmed; query/cancel gateway khi phù hợp; luôn ghi sự kiện tiền đến, xử lý cấp quyền hoặc hoàn tiền theo state machine.

### F10 — 🟠 HIGH — Stored XSS qua tên người dùng khi mời nhóm

- **File/hàm/vị trí:** [_show_content.blade.php::searchUsersForInvite](D:/DATN/resources/views/student/study_groups/_show_content.blade.php:1146); ::sendInvite dòng 1194/1199; escapeHtml dòng 662; RegisterRequest cho name là string|max.
- **Nguyên nhân:** escapeHtml dùng cho một chuỗi JavaScript bên trong thuộc tính onclick. Browser giải mã &#039; thành dấu nháy trước khi chạy handler, nên HTML escaping không bảo vệ ngữ cảnh JavaScript.
- **Giảng viên test an toàn:** tài khoản thử có tên x');alert(1);// ; chủ nhóm tìm người đó và bấm Mời. Tên bình thường O'Connor cũng là test lỗi cú pháp hữu ích.
- **Hiện tại theo source:** handler ghép tên có thể bị đóng chuỗi và chạy lệnh JavaScript; nhánh retry còn chèn userName không escape. CẦN KIỂM TRA THÊM trên browser và CSP của môi trường; chưa thực thi payload trong lượt rà soát.
- **Đúng/cách sửa:** tạo DOM bằng textContent; gắn addEventListener với closure nhận u.id/u.name; không đưa dữ liệu vào inline event handler. Không chỉ thêm một lần escape HTML nữa.

### F11 — 🟠 HIGH — 2FA/verified/active không được áp dụng đồng nhất cho API học và nhóm

- **File/hàm/vị trí:** [routes/api.php](D:/DATN/routes/api.php:16); [routes/web.php](D:/DATN/routes/web.php:143), nhóm study-groups dòng 113; [AuthController::login](D:/DATN/app/Http/Controllers/Web/AuthController.php:53); EnsureTwoFactorIsVerified.
- **Nguyên nhân:** Auth::attempt đã tạo phiên trước challenge. Nhiều route chỉ auth hoặc web+auth, trong khi dashboard có 2fa. Ví dụ StudyGroupController::join không kiểm tra two_factor_passed_at.
- **Giảng viên test:** bật 2FA, đăng nhập đúng mật khẩu nhưng chưa nhập OTP; POST /api/study-groups/{id}/join ở khóa đã mua, hoặc gọi API tiến độ.
- **Hiện tại:** các route này không bắt buộc bước 2FA; quyền course/enrollment riêng vẫn có thể được kiểm tra. Không được diễn giải thành “student vào được admin”: admin routes có role:admin và 2fa.
- **Đúng/cách sửa:** thiết kế tập middleware protected thống nhất hoặc trạng thái pre-auth riêng. Với tài khoản bị khóa, mức khai thác còn phụ thuộc session invalidation; CẦN KIỂM TRA THÊM đường khóa tài khoản và phiên đã tồn tại.

### F12 — 🟠 HIGH — Tiêu chí cấp chứng chỉ bỏ sót document bắt buộc

- **File/hàm/vị trí:** [CourseCompletionService::check](D:/DATN/app/Services/CourseCompletionService.php:36), các vòng lặp dòng 55/64/76, eligible dòng 98; LearningProgressService::requiredLessonIds.
- **Nguyên nhân:** danh sách allLessons có bài bắt buộc nhưng chỉ kiểm tra video, quiz, assignment. Không kiểm tra document; quiz/assignment thiếu bản ghi con thì continue thay vì báo thiếu.
- **Giảng viên test:** khóa có video và nhiều document bắt buộc; hoàn thành các video/quiz/assignment nhưng để lại một document chưa học, kích hoạt kiểm tra completion.
- **Hiện tại:** missing có thể rỗng, enrollment completed và chứng chỉ được cấp dù tỷ lệ tiến độ chưa 100%. Với khóa không có bài bắt buộc, hai service còn khác nhau về fallback.
- **Đúng/cách sửa:** một specification hoàn thành thống nhất theo tất cả loại bài bắt buộc; thiếu quiz/assignment là dữ liệu không hợp lệ. Không dùng phần trăm hiển thị làm thay thế duy nhất cho điều kiện nghiệp vụ.

### F13 — 🟠 HIGH — Checkout không kiểm tra lại khóa còn bán và người mua đã sở hữu

- **File/hàm/vị trí:** [CartController::checkout](D:/DATN/app/Http/Controllers/Web/Student/CartController.php:223), đoạn nạp cart sau validate; ::add dòng 183 có kiểm tra nhưng checkout không lặp lại; PaymentGatewayService::enrollStudent.
- **Nguyên nhân:** cart là dữ liệu lâu sống; source tin điều kiện ở lúc add. Idempotency key nullable và chỉ gộp request cùng key, không gộp các đơn khác key cho cùng khóa.
- **Giảng viên test:** thêm khóa vào cart rồi admin archive; tiếp tục checkout. Tạo hai order pending khác key cùng khóa khi cart chưa bị xóa, rồi trả cả hai.
- **Hiện tại:** vẫn có thể tạo đơn khóa đã ngừng bán; hai đơn có thể cùng paid trong khi enrollment unique chỉ cho một quyền học, không ngăn việc thu tiền hai lần.
- **Đúng/cách sửa:** validate lại từng course và quyền sở hữu khi tạo attempt; quản lý đơn pending trùng khóa và tình huống tiền đến từ hai attempt. Unique enrollment không thay thế quy tắc chống bán trùng.

### F14 — 🟠 HIGH — Gửi lại submit bài tập sau 6 giờ làm đổi bài đã nộp/chấm thành expired

- **File/hàm/vị trí:** [AssignmentController::submit](D:/DATN/app/Http/Controllers/Web/Student/AssignmentController.php:228), kiểm tra submitted/graded nằm sau kiểm tra deadline.
- **Nguyên nhân:** quá deadline thì update status=expired, result=fail trước khi nhận diện bài đã nộp hoặc đã graded.
- **Giảng viên test:** nộp đúng hạn, giảng viên chấm PASS; sau 6 giờ tính từ started_at gửi lại POST submit, kể cả request thiếu content/file.
- **Hiện tại:** bài đang submitted hoặc graded có thể bị chuyển expired/fail, trong khi score/feedback/progress cũ chưa chắc được đồng bộ. Tác động xảy ra trước validation nội dung.
- **Đúng/cách sửa:** khóa submission, trả kết quả idempotent hoặc từ chối nếu đã submitted/graded trước mọi kiểm tra có ghi dữ liệu; deadline chỉ áp dụng attempt đang làm.

### F15 — 🟠 HIGH — API multipart có thể thay video live vượt quy trình duyệt

- **File/hàm/vị trí:** [S3MultipartUploadController::complete](D:/DATN/app/Http/Controllers/Web/Instructor/S3MultipartUploadController.php:174), update Lesson dòng 221; authorizeCourse cuối file chỉ kiểm tra owner; [ConvertVideoToHLS::handle](D:/DATN/app/Jobs/ConvertVideoToHLS.php:40), publish dòng 83.
- **Nguyên nhân:** có lesson_id hợp lệ thì cập nhật original_video_key của Lesson thật và dispatch job; không phân biệt course đã published cần ContentUpdate. Việc cập nhật ContentUpdate phía sau không ngăn ghi vào Lesson thật.
- **Giảng viên test:** giảng viên sở hữu khóa published, gọi multipart create/complete trực tiếp với lesson_id của bài live và video mới, chưa gửi admin duyệt.
- **Hiện tại:** source cho phép sửa Lesson thật; job chuyển HLS và đặt lesson published. CẦN KIỂM TRA THÊM S3/queue thật để đo thời điểm nội dung mới được phục vụ.
- **Đúng/cách sửa:** khóa published chỉ upload vào candidate có quyền sở hữu rõ; job candidate không được ghi tài nguyên live. Admin approve mới kích hoạt version/manifest mới.

### F16 — 🟠 HIGH — Thu nhập rút được ngay nhưng refund có thể xóa nguồn thu sau đó

- **File/hàm/vị trí:** [User::getTotalEarningsAttribute](D:/DATN/app/Models/User.php:517), ::getAvailableBalanceAttribute dòng 541; WalletController::requestWithdrawal dòng 95; PaymentGatewayService::processRefund dòng 523.
- **Nguyên nhân:** tổng earnings lấy mọi order paid, không chờ hết cửa sổ hoàn tiền 7 ngày. Refund đổi order sang refunded làm earnings biến mất; available_balance dùng max(0, ...), che số âm.
- **Giảng viên test:** giảng viên rút toàn bộ doanh thu đơn mới; học viên xin và được duyệt hoàn tiền trong 7 ngày. Thử yêu cầu withdrawal pending rồi refund trước khi admin duyệt rút.
- **Hiện tại:** tiền rút đã approved không bị bù trừ; withdrawal pending có thể được duyệt mà không đối soát lại nguồn thu. Không có ledger nợ phải thu trong phần code này.
- **Đúng/cách sửa:** tách pending/available earnings, hold qua cửa sổ refund hoặc reserve; ledger ghi nợ có thể âm, cơ chế clawback. Admin approve phải tái kiểm tra điều kiện tài chính. Đây là lỗi mô hình nghiệp vụ, không chỉ thiếu transaction.

### F17 — 🟡 MEDIUM — Hai người có thể chiếm cùng suất cuối trong nhóm

- **File/hàm/vị trí:** [StudyGroupController::join](D:/DATN/app/Http/Controllers/Api/StudyGroupController.php:669), ::acceptInvitation dòng 1108; migration study_group_members có unique(group,user).
- **Nguyên nhân:** isFull, hasMember và attach chạy tách rời, không lock nhóm hoặc transaction bao trọn.
- **Giảng viên test:** nhóm còn một chỗ; hai tài khoản gửi join đồng thời. Gửi hai lần accept cùng invitation.
- **Hiện tại theo interleaving:** hai user khác nhau đều có thể được attach vượt max_members; cùng user có thể đụng unique và trả 500. Unique không bảo vệ tổng sức chứa.
- **Đúng/cách sửa:** lock StudyGroup, kiểm tra sức chứa và membership trong transaction, attach rồi cập nhật invitation nguyên tử; trả 409/422 có nghĩa khi hết chỗ.

### F18 — 🟡 MEDIUM — Assignment thiếu khóa cho start/retry/submit và file nằm trên public disk

- **File/hàm/vị trí:** [AssignmentController](D:/DATN/app/Http/Controllers/Web/Student/AssignmentController.php:65), ::retry dòng 130, ::submit dòng 197; migration 2026_08_28_000003_add_attempt_tracking_to_submissions_table.php.
- **Nguyên nhân:** đọc latest rồi create/update không lock. File được store trên public trước khi update DB; không có bù xóa file khi DB thất bại.
- **Giảng viên test:** double-click retry; gửi hai submit khác nội dung đồng thời; giả lập DB lỗi sau upload trên bản test.
- **Hiện tại:** retry cùng attempt_number bị unique chặn nhưng không bắt lỗi thân thiện; hai submit có thể ghi đè nhau, file mồ côi. Nếu public storage được expose, ai biết URL có thể đọc file ngoài policy.
- **Đúng/cách sửa:** transaction+lock/state transition cho submission, request idempotency; file private và download có policy; cleanup/outbox cho file. CẦN KIỂM TRA THÊM web server/storage symlink để khẳng định file public truy cập được ngoài đăng nhập.

### F19 — 🟡 MEDIUM — Token video không bị thu hồi theo session; signed URL sống lâu hơn token

- **File/hàm/vị trí:** [VideoTokenService::verifyToken](D:/DATN/app/Services/VideoTokenService.php:37); [VideoPlayerController::playlist](D:/DATN/app/Http/Controllers/Web/Student/VideoPlayerController.php:73), signed URLs 12 giờ và cache theo lesson.
- **Nguyên nhân:** token cache có user_id nhưng verify chỉ so lesson_id. Không tái kiểm tra phiên, trạng thái tài khoản/enrollment. Các segment S3 dùng URL ký 12 giờ, trong khi token chỉ 10 phút.
- **Giảng viên test:** lấy token/segment URL hợp lệ rồi logout hoặc hoàn tiền; mở URL đó ở trình duyệt khác.
- **Hiện tại:** bearer URL còn sống vẫn dùng được theo thời hạn của nó; single-session không đồng nghĩa thu hồi video tức thời. Đây không phải bằng chứng video chưa mua có thể tự lấy token.
- **Đúng/cách sửa:** định nghĩa rõ threat model; TTL ngắn và revocation/version, gateway/edge authorization khi cần. CẦN KIỂM TRA THÊM HLS encryption/CloudFront/cache thực tế. Không tuyên bố HLS là DRM hay chống tải tuyệt đối.

### F20 — 🟡 MEDIUM — N+1 và kiểm tra hoàn thành toàn khóa trên mỗi heartbeat

- **File/hàm/vị trí:** [CourseCompletionService::check](D:/DATN/app/Services/CourseCompletionService.php:36); LearningProgressService::refreshCourseProgress; [ProgressController::getUserEnrollments](D:/DATN/app/Http/Controllers/Api/ProgressController.php:197).
- **Nguyên nhân:** mỗi video lesson query progress riêng; quiz/assignment có lazy load và query kết quả riêng. Eager load course.lessons ở đầu không áp dụng cho collection allLessons query mới. getUserEnrollments count từng course và lazy load instructor.
- **Giảng viên test:** khóa 100–300 bài, học video với heartbeat 10 giây; đo query count và p95 thay vì chỉ thử một học viên.
- **Hiện tại theo cấu trúc query:** chi phí mỗi heartbeat tăng tuyến tính theo số bài, cùng nhiều ghi DB và lock enrollment. Chưa benchmark nên không đưa số ms giả.
- **Đúng/cách sửa:** nạp progress keyed by lesson, eager load trên đúng collection, aggregate kết quả quiz/assignment; chỉ đánh giá completion khi có chuyển trạng thái liên quan; phân trang enrollment.

### F21 — 🟡 MEDIUM — Frontend có thể mất thời gian xem phát sinh trong lúc request đang chạy

- **File/hàm/vị trí:** [learning-player.js::sendProgress](D:/DATN/resources/js/learning-player.js:254), dòng 297; sendBeaconProgress và listener pagehide/beforeunload.
- **Nguyên nhân:** payload chụp unsavedPlayedSeconds lúc gửi, nhưng response thành công lại gán toàn bộ bộ đếm về 0. Trong thời gian chờ, notePlayedSegment có thể đã cộng thêm giây mới.
- **Giảng viên test:** throttle network để API mất 5–10 giây, tiếp tục xem/pause trong lúc request chạy; so thời gian xem và DB. Thử rời trang khi request đang bay.
- **Hiện tại theo interleaving:** phần phát sinh sau snapshot có thể bị xóa cùng phần đã ACK. Hai lifecycle events còn có thể gửi delta trùng; mức cộng trùng phụ thuộc thời điểm server xử lý.
- **Đúng/cách sửa:** chỉ trừ delta đã được ACK; dùng sequence/request ID hoặc tổng tích lũy đơn điệu server deduplicate; xác định một chiến lược flush khi rời trang.

### F22 — 🟡 MEDIUM — AI lộ trình public chưa có quota tại ứng dụng và history tải toàn bộ

- **File/hàm/vị trí:** [routes/web.php](D:/DATN/routes/web.php:106); [LearningPathAiController::chat/getConversation](D:/DATN/app/Http/Controllers/Web/LearningPathAiController.php:35); Ai\LearningPathAiService::processMessage.
- **Nguyên nhân:** route public không throttle; message có max:2000 nhưng onboarding chỉ array, chưa schema/max cho nội dung con. getConversation dùng messages()->get() toàn bộ; mỗi request chat có thể gọi Gemini.
- **Giảng viên test:** gửi liên tiếp chat hợp lệ bằng nhiều session; lịch sử hàng nghìn tin; onboarding có dữ liệu con sai hoặc quá lớn.
- **Hiện tại:** source không giới hạn chi phí theo IP/user/ngày, DB hội thoại có thể tăng; không kết luận WAF ngoài app không tồn tại.
- **Đúng/cách sửa:** quota, rate limit, kích thước request và nested validation; pagination/cursor history, retention. CẦN KIỂM TRA THÊM giới hạn reverse proxy, quota provider và chi phí thực tế.

### F23 — 🟡 MEDIUM — Migration thêm unique và rollback lịch sử attempt chưa xử lý dữ liệu tồn tại

- **File/hàm/vị trí:** [2026_08_28_000001_harden_idempotent_writes.php](D:/DATN/database/migrations/2026_08_28_000001_harden_idempotent_writes.php:12); [2026_08_28_000003_add_attempt_tracking_to_submissions_table.php::down](D:/DATN/database/migrations/2026_08_28_000003_add_attempt_tracking_to_submissions_table.php:43).
- **Nguyên nhân:** thêm unique refunds.order_id/user_points không kiểm tra hoặc hợp nhất bản ghi trùng cũ. Rollback submissions khôi phục unique(assignment,user) trong khi đã cho phép nhiều attempt.
- **Giảng viên test:** trên bản sao DB trước migration, tạo dữ liệu trùng mà schema cũ cho phép rồi migrate; với hai attempt cùng assignment/user, rollback migration attempt.
- **Hiện tại:** nếu dữ liệu tiền đề tồn tại, DDL unique thất bại; MySQL DDL không được bảo vệ như transaction nghiệp vụ thường. CẦN KIỂM TRA THÊM dữ liệu DB đang dùng.
- **Đúng/cách sửa:** preflight/backfill rõ ràng, giữ lịch sử; migration down cần chiến lược không mất dữ liệu hoặc tuyên bố không rollback sau khi phát sinh attempt thay vì hứa rollback an toàn.

### F24 — 🟠 HIGH có điều kiện — Đưa local qua tunnel làm lộ đường đăng nhập không mật khẩu

- **File/hàm/vị trí:** [routes/web.php](D:/DATN/routes/web.php:567), /dev/login-as-admin và /dev/login-as-student; .env hiện APP_ENV=local, APP_DEBUG=true.
- **Nguyên nhân:** route chỉ được chặn bằng environment local, không bằng địa chỉ truy cập hoặc credential. Local qua tunnel vẫn là local.
- **Giảng viên test:** trên đúng bản demo được expose, thử /dev/login-as-admin khi chưa đăng nhập.
- **Hiện tại:** khi route tồn tại và có admin, auth()->login được gọi không mật khẩu. Dashboard còn có thể yêu cầu 2FA nếu tài khoản bật; không được khẳng định vượt 2FA ở đây.
- **Đúng/cách sửa:** không đăng ký dev-login ở môi trường demo công khai, tắt debug, quản lý tài khoản demo rõ ràng; kiểm tra route cache. CẦN KIỂM TRA THÊM việc website có đang expose ra ngoài hay không.

### F25 — 🟢 LOW — Code hỗ trợ cũ, dependency lớp nghiệp vụ vào HTTP và tài liệu chưa theo project

- **File/hàm/vị trí:** [PaymentGatewayService::payOSResponseSummary](D:/DATN/app/Services/PaymentGatewayService.php:209), helper private không thấy call site trong source; [CourseReviewService::submitForReview](D:/DATN/app/Services/CourseReviewService.php:16) đọc request() trong service; [README.md](D:/DATN/README.md:1) chủ yếu scaffold Laravel.
- **Nguyên nhân:** nhiều vòng phát triển để lại helper chưa dùng, service phụ thuộc global request và tài liệu khung.
- **Giảng viên test/hỏi:** gọi service duyệt từ CLI/job hoặc yêu cầu sinh viên chỉ ra hàm được gọi ở đâu, hướng dẫn dựng môi trường từ README.
- **Hiện tại:** khó giải thích ranh giới controller/service, dễ bỏ sót validation khi tái sử dụng; README chính chưa là mô tả đồ án dù docs có tài liệu bổ sung.
- **Đúng/cách sửa:** xác minh call site rồi dọn helper; truyền dữ liệu cam kết bản quyền qua tham số/DTO; README dẫn setup, kiến trúc, migration, worker và giới hạn demo. Không bắt buộc thêm Repository nếu Eloquent/service hiện tại đã đủ.

### F26 — 🟡 MEDIUM — CSRF exception cho multipart rộng hơn webhook

- **File/hàm/vị trí:** [bootstrap/app.php](D:/DATN/bootstrap/app.php:29), except instructor/courses/*/s3/multipart/*.
- **Nguyên nhân:** các thao tác do trình duyệt giảng viên gửi, xác thực bằng session, bị loại khỏi kiểm tra CSRF như webhook; controller owner check không thay thế CSRF.
- **Giảng viên test:** từ origin khác hoặc origin cùng site phù hợp, gửi form multipart-create/abort trong phiên giảng viên, không có _token.
- **Hiện tại:** app chủ động bỏ CSRF check; khai thác cross-site thực tế phụ thuộc SameSite cookie, Fetch Metadata/origin protection của framework và deployment.
- **Đúng/cách sửa:** bỏ exception cho browser routes, frontend gửi token; chỉ exempt webhook thực sự có chữ ký. **CẦN KIỂM TRA THÊM**, chưa kết luận CSRF cross-site đã thành công.

### F27 — 🟡 MEDIUM — Giới hạn upload multipart dựa trên kích thước tự khai

- **File/hàm/vị trí:** [S3MultipartUploadController::create](D:/DATN/app/Http/Controllers/Web/Instructor/S3MultipartUploadController.php:27), ::complete dòng 174; [AwsS3UploadService::completeMultipartUpload](D:/DATN/app/Services/AwsS3UploadService.php:113).
- **Nguyên nhân:** file_size được validate lúc create nhưng không persist đối chiếu; part ký upload không ràng buộc tổng byte; complete không HeadObject xác minh kích thước/MIME thực.
- **Giảng viên test:** khai file_size nhỏ hợp lệ, upload dữ liệu lớn hơn cấu hình qua presigned part rồi complete; thử nội dung không phải video với đuôi mp4.
- **Hiện tại:** giới hạn application không được chứng minh bằng object thật; FFmpeg có thể báo lỗi sau khi tài nguyên đã được nhận. Không kết luận đây là RCE.
- **Đúng/cách sửa:** lưu upload session server, quota, HeadObject sau complete, kiểm tra media bằng công cụ phù hợp, quarantine/cleanup trước publish; lifecycle cho multipart bỏ dở. CẦN KIỂM TRA THÊM quota/bucket policy bên AWS.

### F28 — 🟡 MEDIUM — Lưu nháp quiz chưa ràng buộc attempt với quiz trên URL

- **File/hàm/vị trí:** [QuizAttemptService::saveProgress](D:/DATN/app/Services/QuizAttemptService.php:234); [QuizController::saveProgress](D:/DATN/app/Http/Controllers/Web/Student/QuizController.php:72).
- **Nguyên nhân:** assertAccess kiểm tra course/lesson của URL, nhưng sau đó attempt chỉ được kiểm tra user_id và status, không kiểm tra attempt.quiz_id thuộc lesson đó. Controller cũng không validate answers bằng quy tắc array trước khi gọi service có tham số array.
- **Giảng viên test:** A có attempt Q1 đang làm và truy cập được Q2; gọi save-progress của Q2 với attempt_id Q1. Thử answers là số hoặc chuỗi JSON scalar.
- **Hiện tại:** có thể ghi answers/remaining_seconds vào Q1 qua URL Q2 của cùng user; input sai kiểu có thể gây TypeError/500 thay vì 422. Chưa có bằng chứng sửa attempt của user khác vì owner check vẫn tồn tại.
- **Đúng/cách sửa:** xác nhận attempt.quiz_id khớp quiz của lesson trong cùng transaction; validate kiểu và cấu trúc answer IDs theo version của attempt, không chỉ parse JSON rồi chuyển tiếp.

## 4. Database: những gì đúng và những ràng buộc chưa đủ

| Quan hệ/ràng buộc thấy trong migration | Đánh giá |
|---|---|
| enrollments unique(user_id, course_id), FK user/course/order | Đúng cho một quyền học hiện hành; không ngăn hai order cùng thu tiền (F13). |
| lesson_progress unique(user_id, lesson_id) | Đúng để chống hai dòng tiến độ; không kiểm tra thời gian xem là thật (F03/F04). |
| order_items unique(order_id, course_id) | Đúng để không lặp item trong một order; không liên quan trùng giữa hai order. |
| payments unique(order_id), transaction_id, gateway_order_code | Đúng cho mô hình một payment/order và chống reference trùng; mô hình thiếu lịch sử nhiều payment attempt khi đổi cổng (F08). |
| orders.order_code unique; idempotency theo user+UUID nullable | Đúng với request có cùng key; NULL/khác key không chống trùng nghiệp vụ. |
| study_group_members unique(group,user) | Chống thành viên trùng, không chống vượt sức chứa (F17). |
| submissions unique(assignment,user,attempt_number) | Giữ nhiều lần làm; controller cần xử lý race và rollback schema cần giữ lịch sử (F18/F23). |
| user_points unique(user,source,reference) | Cơ sở tốt cho thưởng một lần; cần kiểm tra mọi nguồn có reference và backfill không trùng. |
| payment amount decimal(12,2), nhưng code dùng float khi phân bổ | Schema đúng hơn float cho tiền; CẦN KIỂM TRA THÊM invariant tổng item sau làm tròn, coupon phần trăm và commission nhiều khóa. |
| Nullable reviewer, grant và reference tùy vòng đời | Không thể kết luận nullable là lỗi chỉ vì cho NULL; cần phù hợp từng trạng thái. |

**Điểm phải giải thích:** FK đơn lẻ không đảm bảo các cột course_id, section_id, chapter_id trên cùng Lesson chỉ về cùng một khóa. Source có nhiều fallback quan hệ; cần invariant tại service và test dữ liệu lệch. Chưa đọc DB thực tế nên không kết luận đang có dòng mồ côi hoặc sai quan hệ.

**Transaction không bảo vệ mọi thứ:** khóa/rollback DB không rollback chuyển tiền ngân hàng, email đã gửi hoặc file đã lưu. F07–F09/F15/F18 là các ví dụ cần state machine, idempotency, outbox và thao tác bù, không chỉ bọc thêm DB::transaction.

## 5. Các cơ chế đang làm đúng — và giới hạn của lời khẳng định

1. **Phân quyền admin ở route:** auth, active, verified, 2fa, role:admin bảo vệ nhóm admin. Student sửa URL đơn thuần không đủ vào nhóm này. Không đồng nghĩa mọi API khác đều được bảo vệ giống vậy (F11).
2. **Đơn hàng của từng người:** OrderController::show/cancel gọi authorizeOwner; nhiều trang checkout lọc user_id trước firstOrFail. Đây là kiểm tra server, không chỉ giấu nút.
3. **Password và session:** User có cast password=hashed; AuthService dùng Auth::attempt, regenerate session rồi đăng ký active session. Logout invalidate phiên và regenerate CSRF token. Reset password có cơ chế vô hiệu session DB.
4. **PayOS IPN:** kiểm tra checksum key, payload, hash_equals HMAC, mapping gateway order code và số tiền trước finalize. Chữ ký sai không đủ để đánh dấu paid. Vấn đề còn lại là state machine sau xác thực.
5. **Finalize thanh toán cùng một order:** lock order/payment trong transaction, order đã paid trả lại thành công, có unique transaction ID và cập nhật enrollment cùng transaction. Đây là nền tảng tốt cho webhook lặp cùng giao dịch.
6. **Rút tiền đồng thời:** WalletController khóa User rồi tính lại available_balance trước tạo withdrawal. Đúng với hai yêu cầu rút đồng thời; không xử lý đầy đủ rủi ro refund về sau (F16).
7. **Quiz version và ownership ở submit:** attempt gắn quiz_version_id; kiểm tra owner, quiz của lesson, version thuộc quiz; attempt finalized trả kết quả cũ. Đây là cách bảo vệ lịch sử khi giảng viên sửa câu hỏi. Thiếu deadline vẫn phải sửa (F05).
8. **Import toàn khóa:** FullCourseImportConfirmService khóa batch, kiểm tra actor/user_id, trạng thái, hạn dùng, error_count và validate lại canonical payload; completed trả course cũ. Các record tạo trong transaction. Đúng với double confirm tuần tự và rollback DB khi một bước lỗi.
9. **Review:** ReviewService khóa enrollment, chặn review gốc trùng kể cả soft-deleted, tính lại rating trong transaction và chuyển lỗi trùng sang validation. Không kết luận mọi query review không có race nếu chưa test đồng thời.
10. **Ghi chú và ticket:** LessonNotePolicy kiểm tra chủ sở hữu và quyền học hiện tại; SupportTicketPolicy kiểm tra ticket owner và attachment thuộc ticket. Ticket attachment lưu local, không như assignment public.
11. **Realtime:** routes/channels.php dùng private channel, nhóm học yêu cầu member, thảo luận khóa yêu cầu chủ cuộc thảo luận/instructor/admin. CẦN KIỂM TRA THÊM connection đã subscribe trước khi bị kick, token expiration và reconnect.
12. **Frontend không hoàn toàn bỏ ngỏ lỗi:** learning-player có in-flight flag, pending save, xử lý 409 stale, toast khi API lỗi; AI chat có AbortController. F21 là lỗi delta cụ thể, không phải nhận xét “không có loading/error”.
13. **Duyệt khóa có readiness checklist:** CourseSubmissionValidator kiểm tra thumbnail/title/description/objectives/category, số bài, tổng thời lượng, nguồn video, quiz readiness và HLS. Tuy nhiên API upload riêng phải tuân thủ cùng workflow (F15).

## 6. Security: kết luận theo từng nhóm được yêu cầu

| Nhóm | Kết luận từ lượt rà soát |
|---|---|
| SQL Injection | Chưa thấy điểm khai thác cụ thể trong các query đã truy vết. Ví dụ CourseController dùng placeholders cho min/max price; raw CASE/aggregate là biểu thức cố định. Không gán nhãn SQLi chỉ vì có DB::raw. |
| XSS | Có đường dữ liệu user name → HTML onclick ở F10; cần browser xác nhận. Blade escape thông thường không sửa được lỗi ghép ngữ cảnh JS. |
| CSRF | Web routes có bảo vệ framework; IPN exempt là hợp lý khi verify signature. Exception multipart cần kiểm tra/sửa theo F26. GET download bài tập lại có side effect ghi DB (F02/F18). |
| Mass assignment | Có fillable không tự động là lỗ hổng. Các luồng đọc sâu phần lớn truyền mảng trường rõ ràng; chưa xác nhận được request->all trực tiếp update User để nâng role. |
| Broken Access Control/IDOR | F02; thiếu 2FA nhất quán F11; S3 vượt duyệt F15. API dùng tài nguyên đúng owner vẫn có thể sai quyền trạng thái. |
| Upload | Có validation loại file/size ở nhiều nơi; multipart kiểm chứng byte thật chưa đủ F27; assignment public cần xác minh exposure F18. Chưa có bằng chứng thực thi mã upload. |
| Sensitive data | Không thấy .env được Git theo dõi ở working tree hiện tại; .env.testing và docs/TEST_ACCOUNTS.md được track. CẦN KIỂM TRA THÊM lịch sử Git, tài khoản demo còn hiệu lực, log và cấu hình public; không in secrets. |
| Password/hash | User cast hashed là đúng; 2FA code lưu thẳng trong bảng TwoFactorCode, nên cần cân nhắc hash/attempt budget/retention. Chưa kết luận brute force thành công: verify route có throttle. |
| API public | AI lộ trình public có chủ đích nhưng thiếu quota ở app F22. Webhook public có chữ ký không đồng nghĩa “API không bảo vệ”. |
| Client-only validation | F03/F04/F05 thể hiện server nhận dữ liệu client hoặc bỏ deadline, dù có validation kiểu dữ liệu. Validation kiểu không thay thế validation nghiệp vụ. |

## 7. Những mục phải kiểm tra thêm trước khi tự nhận “sẵn sàng bảo vệ”

- Chạy migrations từ DB sạch và upgrade từ bản sao DB đang dùng; không chạy migrate:fresh trên DB đồ án.
- Chạy PHPUnit đầy đủ khi MySQL test sẵn sàng; thêm regression cho F01–F18 trước khi sửa rồi kiểm chứng sau sửa.
- Chạy browser với student A/B, instructor A/B và admin; OTP, phiên bị thay, back/forward, bfcache, refresh, nhiều tab.
- Test gateway sandbox: webhook lặp, lệch thứ tự, timeout trước/sau commit, thành công nhưng callback mất, đổi cổng, hủy/hoàn tiền. Không dùng tiền thật cho fault injection.
- Kiểm tra Reverb bị dừng, reconnect, kick member khi đang subscribe, request send và broadcast trùng.
- Kiểm tra FFmpeg thiếu/crash, S3 timeout/AccessDenied, job retry, upload dở và xóa nội dung.
- OAuth: callback/state/verified email theo từng provider và linking tài khoản có email trùng; lượt này chưa xác nhận provider nào bảo đảm verified email.
- Chứng chỉ đã cấp khi hoàn tiền/khóa tài khoản/nội dung bắt buộc thay đổi: chính sách giữ hay thu hồi phải được chốt, chưa tự coi một lựa chọn là lỗi.
- Admin withdrawal hiện có thể tự sinh transaction_ref và mặc định lời nhắn đã chuyển khoản; cần giải thích đó là xác nhận chuyển tiền thủ công, không phải bằng chứng ngân hàng tự động.
- Dynamic Role/Permission và role chuỗi dùng song song: làm rõ permission nào thực sự được Gate kiểm tra, không hứa rằng mọi quyền tùy chỉnh đều chi phối route role cố định.

## A. 🚨 TOP 10 LỖI PHẢI SỬA TRƯỚC KHI BẢO VỆ

Thứ tự ưu tiên theo khả năng tái hiện và mức phá vỡ nghiệp vụ. Chi tiết file/hàm/nguyên nhân/test/sửa nằm ở F tương ứng.

| Ưu tiên | Phát hiện | Vì sao cần sửa trước |
|---|---|---|
| 1 | F02 — IDOR tải bài tập khác khóa | Chỉ sửa ID trên URL có thể truy cập nội dung chưa mua và ghi dữ liệu sai. |
| 2 | F03 + F04 — Giả hoàn thành và sửa duration từ client | Làm mất giá trị tiến độ, XP và điều kiện chứng chỉ; còn sửa metadata dùng chung. |
| 3 | F01 — gateway=payos trái enum | API checkout hợp lệ theo validation nhưng có thể lỗi DB ngay. |
| 4 | F07 — Trả tiền xong coupon hết lượt khiến order failed | Có tiền thật nhưng không cấp quyền học, webhook vẫn ACK. |
| 5 | F08 + F09 — Đổi/hủy đơn khi link đang hoạt động | Mất mapping hoặc từ chối callback có tiền; thiếu trạng thái đối soát. |
| 6 | F10 — XSS trong nút mời nhóm | Dữ liệu tên user có thể thành JavaScript; tên có dấu nháy cũng dễ làm hỏng chức năng. |
| 7 | F05 — API nộp quiz không chặn quá giờ | Giảng viên có thể bỏ qua JS và làm lộ giới hạn chống gian lận. |
| 8 | F15 — Thay video live qua multipart | Quy trình admin duyệt bị bỏ qua bằng API riêng. |
| 9 | F06 — Coupon private dùng được bởi người không được cấp | Chức năng cấp mã riêng sai ngay ở điều kiện cốt lõi. |
| 10 | F14 — Nộp lại quá 6 giờ sửa bài đã PASS thành FAIL | Lịch sử chấm bài có thể bị phá bởi request gửi lại. |

**Không bỏ qua ngay sau TOP 10:** F11 (2FA), F12 (document/chứng chỉ), F16 (rút tiền/refund). Nếu demo public với APP_ENV=local, xử lý F24 trước khi chia sẻ URL.

## B. 🧪 DANH SÁCH TEST CASE GIẢNG VIÊN CÓ THỂ DÙNG ĐỂ TEST WEBSITE

**Chuẩn bị:** DB test hoặc bản sao có thể phục hồi; student A/B, instructor I/J và admin; khóa K có video 600 giây, document bắt buộc, quiz giới hạn 1 phút, assignment; khóa L mà A chưa mua; coupon public/private/100%/còn 1 lượt; nhóm còn một suất. Dùng sandbox và tài khoản tự tạo. Không chạy payload hoặc chuyển tiền trên dữ liệu người khác.

Mọi test bên dưới **chưa được chạy browser trong lượt rà soát này**. Cột “chuẩn đạt” là kết quả cần có, không phải tuyên bố code hiện đã đạt. Những test gắn F là test hồi quy ưu tiên cho lỗi đã tìm thấy.

| ID | Thao tác/tình huống | Chuẩn đạt và dữ liệu cần đối chiếu | Liên quan |
|---|---|---|---|
| T01 | Login sai password nhiều lần; thử email và username | Không login; throttle có thông báo; không lộ password/log nhạy cảm | Auth |
| T02 | Register trống, email sai, username trùng, role=admin qua API | 422/không tạo user; không tự nâng quyền | Auth/validation |
| T03 | Gửi lại form đăng ký hai lần | Không có hai user cùng email/username; lỗi dễ hiểu | Unique/race |
| T04 | Bật 2FA, chỉ nhập password; gọi API join/progress | Chưa được thực hiện trước OTP | F11 |
| T05 | OTP sai, hết hạn, đã dùng và resend liên tục | Không chấp nhận OTP cũ; throttle đúng | 2FA |
| T06 | Reset password, dùng lại link reset và session cũ | Link không dùng lại; phiên cũ không tiếp tục quyền được bảo vệ | Auth/session |
| T07 | Login ở hai trình duyệt; gọi API từ phiên cũ | Phiên cũ bị chặn; thông báo rõ; test ngoài APP_ENV=testing | SingleSession |
| T08 | Student truy cập /admin và gọi API quản trị | Không thực thi tác vụ; JSON 403 hoặc redirect hợp lệ | Role |
| T09 | Admin khóa user khi user đang mở trang | Request tiếp theo bị từ chối; không chỉ ẩn giao diện | Active |
| T10 | Xem/sửa/hủy order, note, ticket của user khác bằng ID | 403/404; DB không đổi; attachment thuộc đúng ticket | Policy/IDOR |
| T11 | Dùng course đã mua nhưng lesson của khóa khác ở download/retry | 403/404; không tải file, không tạo Assignment/Submission | F02 |
| T12 | Thêm khóa cart rồi admin archive, tiếp tục checkout | Không bán khóa không khả dụng, hoặc theo chính sách đã chốt | F13 |
| T13 | Checkout không chọn khóa, ID không có trong cart, ID trùng | Thông báo hợp lệ, không tạo đơn rỗng/giá sai | Cart |
| T14 | Checkout trực tiếp payment_method=payos | Tạo payment hợp lệ theo enum, không 500 | F01 |
| T15 | Cùng T14 với coupon 100% | Paid/enrollment một lần, không gọi gateway thu tiền | F01 |
| T16 | Coupon private chỉ cấp A nhưng B dùng | B bị từ chối ở listing/preview/checkout/finalize | F06 |
| T17 | Coupon hết hạn/chưa bắt đầu/sai khóa/chưa đạt giá tối thiểu | Server từ chối, giá UI và server khớp | Coupon |
| T18 | Hai người trả tiền với coupon còn một lượt | Không có tiền bị “rơi”; reservation/đối soát rõ ràng | F07 |
| T19 | Bấm checkout liên tục cùng UUID; refresh/back | Chỉ một order cho key; trả lại kết quả hiện có | Idempotency |
| T20 | Tạo hai order khác UUID cùng khóa rồi trả cả hai | Không thu trùng không xử lý; kiểm tra enrollment và refund | F13 |
| T21 | QR X, tab khác áp coupon thành Y, trả QR X | Không từ chối im lặng tiền nhận; đối soát attempt cũ | F08 |
| T22 | Tạo PayOS rồi chuyển MoMo, callback PayOS đến sau | Còn nhận diện giao dịch cũ và xử lý đúng | F08 |
| T23 | Hủy order lúc ngân hàng đã thu tiền, callback đến muộn | Ghi nhận tiền; trạng thái cấp dịch vụ/hoàn tiền rõ | F09 |
| T24 | Giả webhook chữ ký sai, amount sai, orderCode sai | Không paid/enroll; status code phù hợp | Webhook |
| T25 | Gửi cùng webhook đúng nhiều lần và đồng thời | Một thanh toán, enrollment/XP/count không lặp | Finalize |
| T26 | Thanh toán thành công nhưng chặn IPN; mở return URL | Đối soát server/gateway; không tin query success đơn thuần | Payment |
| T27 | Gateway timeout tạo link, sau đó retry | Không tạo nhiều attempt không kiểm soát; lỗi khôi phục được | Payment |
| T28 | Hoàn tiền đơn 0đ, quá 7 ngày, đã học ≥50% | Bị từ chối; dưới ngưỡng xử lý theo chính sách | Refund |
| T29 | Hai lần refund cùng order; admin approve hai lần | Một yêu cầu hoạt động/một khoản hoàn, trạng thái ổn định | Refund |
| T30 | GV đã rút hoặc đang chờ rút rồi student refund | Ledger/reserve phản ánh thiếu hụt, không che nợ bằng số 0 | F16 |
| T31 | Hai yêu cầu rút đồng thời vượt tổng số dư | Tổng tiền giữ không vượt available balance | Wallet lock |
| T32 | API video completed=true khi chưa xem | Không tự hoàn thành | F03 |
| T33 | API video vị trí cuối, played_seconds=0 | Không coi seek là xem đủ | F03 |
| T34 | API video duration=1 hoặc rất lớn | Không sửa metadata Lesson chung | F04 |
| T35 | Gửi client_updated_at tương lai/rất cũ | Không nhận thời gian xem giả hoặc khóa cập nhật về sau | F03/F04 |
| T36 | Mạng chậm khi xem, pause, resume, refresh/back | Không mất/cộng trùng delta; resume đúng | F21 |
| T37 | Hoàn thành video nhưng bỏ document bắt buộc | Chưa cấp chứng chỉ/hoàn thành khóa | F12 |
| T38 | Quiz hết giờ nhưng chặn JS rồi POST đáp án | Server xử lý expired, không chấm như bài đúng hạn | F05 |
| T39 | Quiz refresh, back/forward và mở hai tab | Một attempt đang làm, thứ tự đề/đáp án ổn định, deadline không reset | Quiz |
| T40 | Gửi attempt_id của người khác hoặc quiz khác | 403/404, không đổi answers/score | Quiz binding |
| T41 | Giảng viên sửa câu hỏi khi A đang làm | A giữ version cũ; người bắt đầu sau nhận version đúng | Versioning |
| T42 | Nộp quiz hai lần, sửa answers ở lần gửi sau | Kết quả finalized không bị ghi đè hoặc cộng điểm lại | Quiz |
| T43 | Assignment nộp trước tải, trống, sai MIME, quá size | Validation server từ chối, không ghi trạng thái lỗi | Assignment |
| T44 | Assignment PASS rồi gửi lại submit sau 6 giờ | PASS/submitted không bị đổi expired | F14 |
| T45 | Assignment double retry và hai submit đồng thời | Không 500/ghi đè; một transition đúng và file không mồ côi | F18 |
| T46 | GV thay video published bằng multipart API trực tiếp | Chỉ tạo candidate, không đổi video live trước approve | F15 |
| T47 | Multipart khai size nhỏ nhưng object thật lớn; file giả mp4 | Kiểm tra nội dung thật/quota, không publish, cleanup | F27 |
| T48 | Multipart không CSRF token từ origin khác | Bị từ chối khi request dùng cookie phiên | F26 |
| T49 | Hai user join suất cuối; accept invitation đồng thời | Chỉ một người được thêm, không vượt giới hạn | F17 |
| T50 | Tên O'Connor và payload thử x');alert(1);// khi mời nhóm | Không lỗi JS, không alert; tên chỉ là dữ liệu | F10 |
| T51 | Chat send khi Reverb tắt, reconnect sau mất mạng | Tin gửi được hoặc báo lỗi rõ; không trùng khi broadcast về | Realtime |
| T52 | Kick member khi đang subscribe rồi nhận tin mới | Không tiếp tục nhận nội dung không còn quyền theo chính sách | Realtime |
| T53 | Lấy video URL rồi logout/refund, mở ngoài phiên | Hành vi đúng TTL/revocation được công bố | F19 |
| T54 | Import workbook sai sheet/cột/quiz/định dạng | Preview chỉ rõ hàng/cột lỗi, chưa tạo course nửa vời | Import |
| T55 | Confirm import hai lần/của người khác/hết hạn | Course cũ hoặc 403/410, không tạo bản sao | Import |
| T56 | DB lỗi giữa tạo order/payment/items hoặc import | DB rollback toàn bộ; trạng thái trả về rõ; file/email có xử lý riêng | Transaction |
| T57 | AI gửi nhanh, provider 429/timeout, reset khi request chưa xong | Quota/error rõ, không ghép phản hồi vào hội thoại sai | F22 |
| T58 | Khóa 300 bài + nhiều enrollment/tin chat | Đo số query, payload, p95; có pagination và giới hạn | F20 |
| T59 | Migration trên dữ liệu có nhiều attempt và duplicate cũ | Không mất lịch sử; migration/rollback có kế hoạch rõ | F23 |
| T60 | Mở URL demo public: /dev/login-as-admin, lỗi 500 | Không login miễn phí; không stack trace/secret | F24 |

**Cách ghi biên bản test:** lưu ID, môi trường/commit, account, input, HTTP status, response, screenshot, các row DB trước/sau và kết luận. Với race, gửi request thực sự đồng thời; bấm nhanh hai lần nhưng server xử lý tuần tự chưa chứng minh được tính đúng dưới đồng thời.

## C. 🎓 TOP 30 CÂU HỎI VẤN ĐÁP CÓ KHẢ NĂNG ĐƯỢC HỎI NHẤT

Các câu trả lời dưới đây phân biệt “code đang làm” và “cần sửa”. Không học thuộc lời hứa bảo mật mà source chưa thực hiện.

### 1. Em mô tả kiến trúc hệ thống và vì sao dùng Service?

**Trả lời ngắn:** Đây là Laravel với Blade và JavaScript. Controller nhận HTTP, nhiều FormRequest validate, middleware/policy kiểm tra quyền; service xử lý nghiệp vụ như thanh toán, quiz, import, completion; Eloquent quản lý dữ liệu; queue xử lý video và tác vụ nền.

**Hỏi vặn:** Tại sao CartController vẫn tính commission, CourseReviewService lại gọi request()?

**Điểm yếu phải biết:** Ranh giới chưa nhất quán; F25. Không nên nói project đã tách sạch Controller/Service/Repository. Repository không bắt buộc nếu chưa đem lại lợi ích rõ.

### 2. Đăng nhập bảo vệ mật khẩu và session như thế nào?

**Trả lời ngắn:** AuthService dùng Auth::attempt; User cast password=hashed; sau login regenerate session rồi registerActiveSession. Logout invalidate session và đổi CSRF token.

**Hỏi vặn:** Vì sao đăng ký active session sau regenerate? Remember-me và reset password ảnh hưởng phiên nào?

**Điểm yếu phải biết:** ID phiên thay đổi; lưu ID trước sẽ khiến phiên mới không khớp. SingleSessionMiddleware bỏ qua khi testing và Tests\TestCase cũng tắt middleware này, nên PHPUnit hiện không chứng minh đầy đủ luồng phiên thật.

### 3. 2FA có bảo vệ mọi API không?

**Trả lời ngắn:** Chưa. Dashboard/admin có middleware 2fa, nhưng một số API học và nhóm chỉ auth. Phiên đã tồn tại sau password nên có thể dùng các API đó trước OTP.

**Hỏi vặn:** Vậy 2FA đang bảo vệ tài khoản hay chỉ bảo vệ một số màn hình?

**Điểm yếu phải biết:** F11. Cần bảo vệ toàn bộ tác vụ yêu cầu xác thực hoàn chỉnh hoặc tách phiên pre-auth.

### 4. Student sửa URL admin có làm được thao tác quản trị không?

**Trả lời ngắn:** Nhóm admin được chặn bằng role:admin cùng auth/active/verified/2fa. Tuy nhiên quyền role không thay thế kiểm tra quyền đối tượng ở các API khác.

**Hỏi vặn:** Role lưu dạng chuỗi và Permission/Gate có khác nhau không?

**Điểm yếu phải biết:** Route CheckRole kiểm tra role chuỗi; không được nói mọi permission động đều tác động đến mọi route. Rà đúng call site Gate trước khi khẳng định.

### 5. Route model binding có tự chống IDOR không?

**Trả lời ngắn:** Không. Binding chỉ tìm model theo ID; phải kiểm tra ownership và quan hệ cha–con. download/retry assignment hiện thiếu liên kết lesson với course.

**Hỏi vặn:** Tôi giữ course đã mua rồi thay lesson chưa mua thì sao?

**Điểm yếu phải biết:** F02. So sánh với submit đã kiểm tra quan hệ để giải thích vì sao hai endpoint đang không nhất quán.

### 6. Giá checkout có lấy từ trình duyệt không?

**Trả lời ngắn:** Giá được tính lại từ Course trên server, ưu tiên discount_price rồi sale_price rồi price; course IDs phải thuộc cart khi tạo order. Nhưng checkout chưa kiểm tra lại trạng thái bán và ownership tại thời điểm mua.

**Hỏi vặn:** Nếu admin archive khóa sau khi tôi thêm cart? Nếu tôi trả hai đơn cùng khóa?

**Điểm yếu phải biết:** F13. Server tính giá không đồng nghĩa toàn bộ nghiệp vụ checkout đã đúng.

### 7. Tại sao PayOS lại lưu gateway bank_transfer?

**Trả lời ngắn:** Schema hiện chỉ cho momo và bank_transfer; processPayment có ánh xạ payos → bank_transfer. checkout ban đầu chưa dùng ánh xạ đó nên có lỗi enum.

**Hỏi vặn:** Vì sao UI chạy được nhưng gọi API payos lại lỗi?

**Điểm yếu phải biết:** F01. Giá trị gửi từ UI có thể là bank_transfer, nhưng server đã công khai chấp nhận payos thì phải xử lý đúng cả giá trị đó.

### 8. Làm sao tin được callback thanh toán?

**Trả lời ngắn:** PayOS IPN kiểm tra HMAC bằng checksum key và hash_equals, tìm payment bằng gateway_order_code, đối chiếu amount rồi mới finalize. MoMo cũng có verifyResult và kiểm tra amount.

**Hỏi vặn:** Có chữ ký đúng nhưng coupon đã hết lượt thì sao? Return URL có đáng tin như webhook không?

**Điểm yếu phải biết:** Chữ ký chỉ xác nhận dữ liệu nguồn gửi; F07/F08/F09 là lỗi xử lý nghiệp vụ sau xác thực. Không tin query success từ browser.

### 9. Callback gửi hai lần có ghi danh hai lần không?

**Trả lời ngắn:** Cùng một order, finalize khóa hàng và nếu đã paid thì trả lại thành công; unique trên payment reference và enrollment giúp chống trùng.

**Hỏi vặn:** Hai order khác nhau cho cùng khóa có được bảo vệ như vậy không?

**Điểm yếu phải biết:** Không tương đương. F13 vẫn có thể thu hai lần; idempotency theo request/order không phải tính duy nhất của giao dịch kinh doanh.

### 10. Khách đã trả tiền nhưng callback mất thì sao?

**Trả lời ngắn:** PayOS có checkAndUpdatePayOSStatus gọi gateway để đối soát; MoMo có đường query ở return khi đủ điều kiện. Nhưng cần kiểm tra retry và trường hợp không quay lại website.

**Hỏi vặn:** Có worker đối soát định kỳ hay chỉ khi user mở trang? Nếu mã/cổng đã bị đổi?

**Điểm yếu phải biết:** Không được khẳng định có reconciler nền nếu chưa thấy tác vụ đó. F08/F09 làm đối soát khó hơn vì mapping bị ghi đè hoặc trạng thái bị chặn.

### 11. Coupon còn một lượt và hai người cùng mua thì sao?

**Trả lời ngắn:** Finalize có lock coupon nên lượt sử dụng không tăng tùy ý, nhưng chưa reserve trước thanh toán. Người trả sau có thể bị failed dù tiền đã nhận.

**Hỏi vặn:** Vậy khóa hàng đã đủ chưa? Ai hoàn tiền cho khách?

**Điểm yếu phải biết:** F07. Cần giữ lượt hoặc quy trình bù/đối soát, không chỉ thêm lock.

### 12. Coupon private khác coupon public ở điểm nào?

**Trả lời ngắn:** Có cột is_private và bảng UserCoupon để cấp mã, nhưng đường sử dụng hiện chưa kiểm tra grant bắt buộc; phải sửa trước khi tuyên bố mã chỉ dành cho người nhận.

**Hỏi vặn:** Biết mã của người khác thì dùng được không? API có khác UI không?

**Điểm yếu phải biết:** F06; cả listing và các đường apply/finalize phải dùng cùng điều kiện.

### 13. Có thể sửa coupon hoặc đổi cổng khi đã mở QR không?

**Trả lời ngắn:** Code hiện cho đổi order pending và cập nhật payment. Cách này không an toàn vì link cũ vẫn mang số tiền/reference trước đó.

**Hỏi vặn:** Callback link cũ đến sau thì tìm bằng dữ liệu nào?

**Điểm yếu phải biết:** F08. Nên lưu payment attempt bất biến và lịch sử, không chỉ một payment bị ghi đè.

### 14. Transaction có đảm bảo ngân hàng và DB cùng thành công không?

**Trả lời ngắn:** Không. Transaction chỉ nguyên tử trong DB cùng kết nối. Chuyển tiền, gửi mail, ghi S3 là side effect ngoài DB, cần idempotency, trạng thái trung gian, retry và bù.

**Hỏi vặn:** Ngân hàng chuyển tiền xong rồi commit DB lỗi thì làm sao tránh chuyển lại?

**Điểm yếu phải biết:** processRefund dùng giai đoạn processing và reference, nhưng vẫn phải kiểm chứng idempotency của payout provider và khả năng query lại. Không tự khẳng định provider trả cùng kết quả khi retry.

### 15. Hoàn tiền có điều kiện gì, ảnh hưởng giảng viên thế nào?

**Trả lời ngắn:** Request refund yêu cầu order paid, có tiền, trong 7 ngày từ paid_at và không có enrollment của item đạt từ 50%. Duyệt sẽ đổi order refunded và hủy quyền học liên quan. Ví giảng viên hiện thiếu khoản giữ tiền qua cửa sổ refund.

**Hỏi vặn:** Giảng viên rút hết trước khi refund thì ai chịu?

**Điểm yếu phải biết:** F16. max(0,balance) chỉ che số âm, không giải quyết khoản thiếu hụt.

### 16. Bấm duyệt rút tiền có thực sự chuyển tiền qua ngân hàng không?

**Trả lời ngắn:** WithdrawalController::approve đang ghi nhận admin đã chuyển thủ công, có thể tự sinh mã FT khi thiếu transaction_ref; bản thân hàm này không gọi ngân hàng để chuyển.

**Hỏi vặn:** Mã FT là chứng từ ngân hàng hay mã nội bộ? Nếu admin chưa chuyển mà bấm duyệt?

**Điểm yếu phải biết:** Không nói đây là thanh toán tự động. Nên bắt buộc chứng từ/reference thật và phân biệt yêu cầu, đã chuyển, đã đối soát.

### 17. Làm sao biết học viên thật sự xem đủ video?

**Trả lời ngắn:** Code có heartbeat, played_seconds và giới hạn elapsed, nhưng hiện vẫn tin completed và vị trí cuối video nên chưa đảm bảo. Cần sửa hai đường ép hoàn thành.

**Hỏi vặn:** Tôi POST completed=true hoặc seek về cuối thì sao?

**Điểm yếu phải biết:** F03. Ngay cả sau sửa, heartbeat chỉ là bằng chứng tương tác, không chứng minh người dùng thực sự tập trung xem.

### 18. Ai được cập nhật thời lượng video?

**Trả lời ngắn:** Nên là pipeline media dựa vào metadata tin cậy. Hiện durationSeconds còn update Lesson từ số client gửi, tạo quyền sửa dữ liệu chung cho học viên.

**Hỏi vặn:** Player báo 1 giây trong khi file 10 phút thì DB lưu gì?

**Điểm yếu phải biết:** F04; FFmpeg job có cập nhật duration thật nhưng không ngăn request học viên ghi lại sau đó.

### 19. Điều kiện cấp chứng chỉ là gì?

**Trả lời ngắn:** CourseCompletionService kiểm tra video bắt buộc hoàn thành, quiz có attempt đạt và assignment đạt; cần enrollment có quyền học và certificate_enabled để cấp chứng chỉ. Document bắt buộc hiện bị bỏ sót.

**Hỏi vặn:** Tại sao progress chưa 100% nhưng có chứng chỉ? Nếu quiz thiếu record thì sao?

**Điểm yếu phải biết:** F12. Không trả lời “mọi bài bắt buộc đều được kiểm tra” khi code chưa làm.

### 20. Quiz version giải quyết vấn đề gì?

**Trả lời ngắn:** Attempt gắn quiz_version_id; câu hỏi/đáp án được lấy theo version đó, để thay đổi đề mới không tự sửa dữ liệu lịch sử của bài đang làm hoặc đã nộp.

**Hỏi vặn:** Giảng viên sửa đáp án sai và muốn chấm lại lịch sử thì sao?

**Điểm yếu phải biết:** Có QuizQuestionInvalidation/Regrade service và job riêng; cần phân biệt cập nhật đề tương lai với chấm lại lịch sử có kiểm soát, không update trực tiếp đáp án cũ.

### 21. Đồng hồ quiz do client hay server quyết định?

**Trả lời ngắn:** Có remainingTime dựa started_at và time_limit_minutes ở server, nhưng submit chưa dùng deadline để chặn. Vì vậy hiện chưa thể nói server cưỡng chế đầy đủ thời gian.

**Hỏi vặn:** Tắt JS, đợi hết giờ rồi gọi submit trực tiếp?

**Điểm yếu phải biết:** F05. Chống chuyển tab/fullscreen phía client cũng có thể bị bỏ qua, không phải cơ chế chống gian lận tuyệt đối.

### 22. Refresh khi đang làm quiz có bị đổi đề hoặc thêm lượt không?

**Trả lời ngắn:** startOrResume tìm attempt in_progress dưới lock quiz; attempt lưu question_ids và presentation_order, nên có nền tảng resume cùng bài. findInProgress còn xử lý hết giờ.

**Hỏi vặn:** saveProgress có kiểm tra attempt thuộc đúng quiz trên URL không?

**Điểm yếu phải biết:** saveProgress hiện kiểm tra owner/status nhưng không đối chiếu attempt.quiz_id với lesson như submit; có thể ghi nhầm attempt khác của chính user. CẦN KIỂM TRA THÊM UI nhiều tab và validation answers sai kiểu.

### 23. Bài tập thực hành tính 6 giờ từ lúc nào?

**Trả lời ngắn:** download tạo hoặc cập nhật started_at; Submission::getDeadline cộng 6 giờ. Retry tạo attempt mới với started_at=null, timer bắt đầu khi tải.

**Hỏi vặn:** Gửi lại bài đã PASS sau 6 giờ có sao không? File tải thất bại thì timer đã bắt đầu chưa?

**Điểm yếu phải biết:** F14; download ghi thời điểm trước bước trả file. Cần giải thích chính sách khi file không có/tải lỗi, không chỉ dựa timer UI.

### 24. Upload video lớn bảo vệ những gì?

**Trả lời ngắn:** Có owner check, prefix S3 theo course, allowlist extension, giới hạn file_size khai báo, part number và ETag; conversion chạy queue. Nhưng chưa kiểm chứng tổng byte thật và API complete có thể sửa Lesson live.

**Hỏi vặn:** Tôi gọi complete trực tiếp cho khóa published hoặc khai size nhỏ thì sao?

**Điểm yếu phải biết:** F15/F27. Chặn nút upload trên UI không đủ.

### 25. Vì sao HLS/token không bảo đảm chống tải video?

**Trả lời ngắn:** HLS chia video thành segment; token/signed URL giới hạn truy cập trong thời gian, không ngăn người đã được cấp quyền lưu dữ liệu. Token hiện chỉ kiểm tra lesson, segment S3 có TTL dài hơn.

**Hỏi vặn:** Logout hoặc refund rồi URL có chết ngay không?

**Điểm yếu phải biết:** F19. Không hứa thu hồi tức thì hay DRM; cần giải thích TTL và chính sách revocation.

### 26. Realtime có bảo đảm chỉ thành viên đọc chat không?

**Trả lời ngắn:** Channel authorization kiểm tra member đối với study-group và chủ thể được phép đối với discussion. Controller vẫn cần quyền mỗi lần gửi/đọc; WebSocket không thay thế API authorization.

**Hỏi vặn:** Kick người đang subscribe thì connection cũ có bị ngắt? Hai người vào suất cuối thì sao?

**Điểm yếu phải biết:** Thu hồi kết nối cần test thêm; sức chứa nhóm có race F17. Không suy từ private channel ra mọi trạng thái đều an toàn.

### 27. Em escape dữ liệu rồi, vì sao còn XSS?

**Trả lời ngắn:** Escape phải đúng ngữ cảnh. HTML escaping không bảo vệ một chuỗi JavaScript nằm trong onclick vì browser giải mã entity trước khi chạy handler.

**Hỏi vặn:** Có cách nào đơn giản hơn việc tự escape nhiều lớp?

**Điểm yếu phải biết:** F10. Dùng textContent và addEventListener, tách dữ liệu khỏi mã.

### 28. Import lỗi giữa chừng hoặc bấm xác nhận hai lần thì sao?

**Trả lời ngắn:** FullCourseImportConfirmService khóa batch, kiểm tra owner/trạng thái/hạn, validate payload server lưu và tạo dữ liệu trong transaction. Batch đã completed trả course đã tạo.

**Hỏi vặn:** Client sửa payload sau preview có được tin không? Course đã tạo bị xóa rồi confirm lại?

**Điểm yếu phải biết:** Service dùng canonical_payload persist, không tin lại workbook/client; completed course mất thì báo completed_course_missing. Đừng tuyên bố transaction rollback cả file/ngoại dịch vụ nếu chưa có xử lý đó.

### 29. Một học viên xem video thì server chạy bao nhiêu query? AI có thể tốn vô hạn không?

**Trả lời ngắn:** Chưa đo thực tế. Source cho thấy completion query theo từng lesson trong heartbeat và enrollment listing có N+1; AI lộ trình public chưa throttle ở app, history get toàn bộ.

**Hỏi vặn:** 1.000 người học cùng lúc thì p95, query count, quota là bao nhiêu?

**Điểm yếu phải biết:** F20/F22; trả lời bằng kế hoạch đo và số đã đo, không bịa năng lực chịu tải.

### 30. Em chứng minh đồ án ổn bằng bộ test như thế nào?

**Trả lời ngắn:** Project có nhiều feature test theo nghiệp vụ. Lượt rà soát này test cấu hình database đạt 1 test/3 assertions, nhưng checkout test không chạy được do MySQL từ chối kết nối; chưa có kết quả full suite.

**Hỏi vặn:** Vậy đã chứng minh được gì? Test có chạy SingleSession và API trực tiếp không?

**Điểm yếu phải biết:** Test cấu hình không chứng minh tích hợp, TestCase tắt SingleSession, các lỗi F cần regression riêng. Phải chạy lại trên DB test, bổ sung test xung đột/race/gateway và browser trước khi nói “đã kiểm thử đầy đủ”.

---

**Cách dùng báo cáo khi luyện bảo vệ:** Với mỗi câu, chỉ vào đúng file và giải thích invariant cần giữ, sau đó mô tả test làm invariant bị phá. Đừng nói “Laravel tự bảo mật hết”, “có transaction là không mất dữ liệu” hoặc “đã validate frontend nên user không sửa được”. Những câu đó không đúng với các đường code đã nêu.
