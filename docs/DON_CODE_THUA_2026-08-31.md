# Rà soát và dọn code thừa — 31/08/2026

## Phạm vi và cách xác minh

Đợt này chỉ dọn code thừa; không gộp các bản sửa bảo mật/thanh toán/đăng nhập trước đó vào kết quả. Đã lưu snapshot source trước khi dọn để đối chiếu riêng thay vì dùng toàn bộ git diff đang có sẵn.

- Lập danh mục file dự án; quét nội dung 858 file thuộc app, routes, resources, database, config, bootstrap, tests, scripts và public/js: 627 PHP, 218 Blade, 10 JS, 1 MJS, 2 CSS.
- Kiểm tra Composer/PSR-4, bootstrap/provider, middleware aliases, route API/web, console scheduler, channel/event, Vite entry/import và Blade/component động.
- Dùng parser của Pint với **duy nhất** rule `no_unused_imports`; không áp dụng định dạng toàn project. Đối chiếu diff để xác nhận chỉ xóa import không tham chiếu, không sửa thân seeder hay migration.
- Các tìm kiếm tên/function/biến chỉ tạo danh sách nghi ngờ. Kiểm tra thêm string reference, compact, dependency injection, event auto-discovery và phạm vi khai báo trước khi quyết định.
- Không audit từng dòng vendor/node_modules, không xóa tài sản public/build hoặc storage, không đọc/đổi bí mật trong .env, không chạy seeder vào DB đang sử dụng. Tệp ảnh/font/binary và file cấu hình Git không phải đối tượng xóa dựa vào reference source.
- Không có bằng chứng đủ mạnh để xóa nguyên file/class. Không khẳng định đã loại bỏ mọi code thừa hoặc chứng minh mọi hành vi trình duyệt không đổi.

## Đã xóa

**Không xóa nguyên file nào. Không xóa class nào.** Tổng cộng chỉnh 22 file source/test; xóa 59 dòng so với snapshot đầu đợt.

### 33 import không sử dụng — 15 file

Bằng chứng: parser không tìm thấy việc sử dụng alias trong file, bao gồm type hint/static call và annotation mà rule hỗ trợ; diff chỉ chứa lệnh `use`. Import PHP không tự đăng ký class vào container/listener hoặc chạy side effect. Không xóa class được import, không xóa seeder, không đổi autoload.

| File chỉnh sửa | Import đã xóa |
|---|---|
| `app/Http/Controllers/Web/Student/AssignmentController.php` | `Illuminate\Support\Facades\DB` |
| `app/Console/Commands/RecoverPendingHlsVideosCommand.php` | `Illuminate\Support\Facades\DB` |
| `database/seeders/CourseSeeder.php` | `Illuminate\Support\Facades\DB` |
| `database/seeders/LargeScaleUserSeeder.php` | `App\Models\Certificate` |
| `database/seeders/RevenueOrderSeeder.php` | `App\Models\Order`, `App\Models\OrderItem`, `App\Models\Payment` |
| `database/seeders/StudentReviewSeeder.php` | `App\Models\LessonComment` |
| `database/seeders/UserSeeder.php` | `Illuminate\Support\Facades\DB` |
| `database/seeders/Demo/DemoCourseSeeder.php` | `App\Models\User`, `Illuminate\Support\Facades\DB` |
| `database/seeders/Demo/DemoDataMasterSeeder.php` | `App\Models\Course` |
| `database/seeders/Demo/DemoInteractionSeeder.php` | `App\Models\Course`, `App\Models\User`, `Illuminate\Support\Facades\DB` |
| `database/seeders/Demo/DemoLearningPathSeeder.php` | `Illuminate\Support\Facades\DB`, `Illuminate\Support\Str` |
| `database/seeders/Demo/DemoUserSeeder.php` | `App\Enums\UserRole`, `App\Enums\UserStatus`, `Illuminate\Support\Facades\DB` |
| `database/seeders/Demo/DemoVideoGenerator.php` | `Illuminate\Support\Facades\Log` |
| `tests/Feature/DemoDataIntegrityTest.php` | `App\Models\LessonProgress`, `Illuminate\Support\Facades\DB` |
| `tests/Unit/QuizProctoringDomainTest.php` | `App\Models\Category`, `App\Models\Course`, `App\Models\CourseSection`, `App\Models\Enrollment`, `App\Models\Lesson`, `App\Models\QuizVersion`, `App\Models\User`, `App\Services\QuizAttemptService`, `App\Services\QuizContentService`, `App\Services\QuizVersioningService` |

### Hàm, biến, log và route — 7 file khác

| File chỉnh sửa | Đã xóa | Bằng chứng và phần giữ nguyên |
|---|---|---|
| `app/Providers/AppServiceProvider.php` | `$isStudentDashboardHeader`, `$isStudentPublicHeader` | Hai giá trị chỉ được gán, không đọc hay truyền qua compact/view. Giữ nguyên composer, gate, listener và logic đếm wishlist/cart đang sử dụng. |
| `app/Services/InstructorRequirementService.php` | `$uniqueKey` và comment cũ nói chống trùng ID | Chuỗi ghép ID không được dùng vào map/filter/result. Giữ nguyên truy vấn và logic kiểm tra yêu cầu; comment mô tả một hiệu ứng mà phép gán này không thực hiện. |
| `app/Console/Commands/TestHlsPipelineCommand.php` | `$masterPath` | Chỉ ghép đường dẫn, không đọc/ghi file tại đường dẫn đó. Lệnh Artisan, job, kiểm tra playlist và segments giữ nguyên. |
| `resources/js/learning-player.js` | `capturedCurrentTime` | Biến cục bộ chỉ khởi tạo false, không có phép đọc/ghi hay closure sử dụng. Không thay đổi chức năng note/timestamp/video. |
| `resources/views/instructor/courses/edit.blade.php` | `handleReadinessSubmit(event)` | Không có call, binding onsubmit/addEventListener hoặc string reference. Form `readinessSubmitForm` mà hàm nhắm tới không còn trong giao diện. Luồng hiện tại dùng `copyrightSubmitForm`; cảnh báo HLS và hàm submit hiện tại giữ nguyên. |
| `resources/views/admin/courses/review.blade.php` | 3 `console.log`, biến `seekableInfo` chỉ phục vụ log | Không ai dùng kết quả console; không ảnh hưởng currentTime/pause/play/listener/timer. Giữ nguyên `console.warn` khi fallback để hỗ trợ chẩn đoán lỗi thực. |
| `routes/web.php` | Khai báo thứ hai của GET `/courses/{course}/lessons/{lesson}/quiz/attempts/{attempt}` | Hai khai báo cùng ngoài route group, cùng `StudentQuizController::reviewAttempt`, middleware auth và tên route. Giữ khai báo đầu; endpoint không bị xóa. Đã so sánh route runtime trước/sau. Các `/profile` giống nhau trên source nhưng khác prefix/group không bị xóa. |

## Nghi ngờ nhưng KHÔNG xóa

| Vị trí | Vì sao giữ lại |
|---|---|
| `app/Listeners/HandleUserLogin.php` | Tìm kiếm trực tiếp dễ cho kết quả “không dùng”, nhưng `artisan event:list` xác nhận Laravel auto-discovery đăng ký `HandleUserLogin@handle` cho Login. Đây là code đang hoạt động. |
| `app/Console/Commands/*`, `app/Policies/*`, `database/factories/*` | Laravel tự phát hiện theo convention; không dùng tần suất tên class để xóa. Các thuộc tính `$signature`, `$tries`, `$timeout`, `$fillable`, `$casts`, `$appends` cũng không phải biến thừa. |
| `AppServiceProvider` đăng ký `AwardLoginPoints` và event discovery | Danh sách event có cả đăng ký explicit và dạng `@handle`. Không tự gộp/xóa vì thuộc luồng đăng nhập/điểm thưởng; cần kiểm chứng idempotency và thứ tự xử lý riêng. |
| `app/Services/PaymentGatewayService.php::payOSResponseSummary` | Không thấy lời gọi trong source được quét, nhưng giữ theo giới hạn không dọn tùy tiện phần thanh toán/callback trong yêu cầu. |
| `resources/views/components/learning/course-chat-drawer.blade.php::submitCourseChatForm` | Có dấu hiệu handler cũ đã được module `course-chat.js` thay thế. Giữ vì cùng tồn tại renderer/recall/validation cũ; chưa chạy đầy đủ browser realtime để kết luận có thể tháo toàn bộ cụm. |
| `resources/views/instructor/discussions/show.blade.php::validateInstructorReplyForm` | Có dấu hiệu validation cũ, trong khi form dùng `data-course-chat-send`. Giữ cùng cụm chat do yêu cầu thận trọng với realtime. |
| `resources/views/welcome.blade.php`, `resources/css/welcome.css` | Chưa tìm thấy controller render trực tiếp trong rà soát; CSS vẫn là entry Vite và có trong manifest fixture. Chưa xác nhận giao diện dự phòng đã ngừng dùng nên không xóa file hoặc entry. |
| `resources/views/student/dashboard/partials/header.blade.php` | Có dấu hiệu header cũ song song với component public.header. Chưa chứng minh mọi luồng chọn layout/include động không cần nó. |
| Layout/component dùng `<x-dynamic-component :component="$layout">` | Không có tên component cố định trên HTML không có nghĩa component chết. Giữ nguyên các layout admin/instructor/student/support/notification. |
| `routes/api.php`, endpoint video/progress, `routes/channels.php`, jobs/webhooks | Client bên ngoài hoặc websocket không thể suy ra đầy đủ từ frontend repository. Không xóa endpoint vì thiếu fetch/axios trực tiếp. |
| CSS utility, trạng thái dark/responsive, class ghép trong JS | Static grep không chứng minh unused; Vite/Tailwind và JS tạo class động. Không purge CSS hoặc bỏ dependency theo suy đoán. |
| Các query và biến truyền qua `compact(...)` | Nhiều biến trông chỉ gán một lần nhưng được truyền bằng tên string sang view; giữ nguyên. Không gộp query có thể làm đổi lock, cache hoặc transaction. |

## Kiểm tra sau khi dọn

- **PHP lint: 845/845 file PHP/Blade đạt.**
- **Blade:** biên dịch thành công vào thư mục kiểm tra riêng, 279 file PHP sinh ra qua kiểm tra cú pháp; không ghi đè thư mục compiled view của app đang chạy.
- **JavaScript:** 10/10 file qua `node --check`; 2/2 test JavaScript đạt.
- **Pint:** rule `no_unused_imports` đạt sau khi dọn (không tuyên bố toàn bộ style preset đã sạch).
- **Build:** `npm run build` thành công. `git diff --check` không có lỗi whitespace.
- **Dependency:** `composer validate --no-check-publish`, `composer check-platform-reqs` và `npm ls --depth=0` thành công.
- **Toàn bộ PHPUnit: 703 tests, 5.968 assertions, 7 failures, 1 risky.** Đã đối chiếu danh sách failure/risky với lượt chạy toàn bộ trước: cùng 7 failure và 1 risky, không xuất hiện tên test thất bại mới.
- Sáu failure của `DemoDataIntegrityTest` do DB test không chứa dữ liệu demo cố định; test instructor trong lớp này không có assertion vì dữ liệu rỗng. Một failure của `FavoritesTest::test_header_favorite_badge_disappears_after_unfavorite` do test đòi badge không còn trong HTML, còn template dùng `x-show` để ẩn. Đây là các kết quả đã được ghi nhận trước đợt dọn; không sửa/xóa assertion hoặc seed dữ liệu thật để làm test xanh.
- **Không thể tuyên bố toàn suite pass.** Các log xác minh được giữ tại `storage/app/cleanup-audit/` để đối chiếu.

- Snapshot và diff riêng: `storage/app/cleanup-audit/before.json`, `cleanup-only.diff`, `changed-files.json`.
- Route runtime: 417 trước và 417 sau. Method, URL, name, action, middleware và domain giống nhau; khác duy nhất số dòng source của một số closure do xóa một dòng route.
- Event/listener và danh sách lệnh Artisan giống hệt trước/sau.
- Không thay manifest/lockfile Composer/npm. Kiểm tra dependency dùng bản đã cài, không nâng cấp hoặc cài thêm package.
- PHPUnit chạy với MySQL test riêng tại 127.0.0.1:3307, database `web_onlinefea_test`. Không dùng database ứng dụng.

Giới hạn: chưa chạy browser end-to-end trên từng màn hình, thanh toán nhà cung cấp thật, S3/FFmpeg/Reverb thật hoặc tải đồng thời. Kết luận bảo toàn dựa trên diff nhỏ, kiểm tra đăng ký framework và kiểm thử tự động; không phải bảo đảm tuyệt đối mọi tổ hợp runtime.
