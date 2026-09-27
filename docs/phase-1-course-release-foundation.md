# Phase 1 — Immutable Course Release Foundation

Branch: `AnhTai_Dev`.

## A. Architecture implemented

`Course` tiếp tục là business identity; analytics vẫn dùng `course_id`. `courses.published_version_id` trỏ release hiện hành. `Enrollment.course_version_id` là pin của enrollment, được giữ nguyên cả khi publish mới, callback lặp hoặc kích hoạt lại enrollment bị cancelled.

`CourseVersion` có manifest relational và snapshot rule. Các mapping tái sử dụng `CourseSectionVersion`, `LessonVersion`, `QuizVersion` và `AssignmentVersion`; QuizVersion tiếp tục dùng mapping QuestionVersion hiện có. Không tạo Course row cho mỗi release, không thêm JSON snapshot system.

## B. Migration added

`database/migrations/2026_09_03_000001_add_course_release_manifests_and_enrollment_pins.php`:

- Thêm `enrollments.course_version_id` nullable, FK restrict đến `course_versions`.
- Thêm `course_version_sections`, `course_version_lessons`.
- Thêm `course_versions.manifest_built_at`; NULL phân biệt metadata snapshot legacy với release đầy đủ.
- Snapshot sáu field thực tế: `required_video_percent`, `required_lesson_percent`, `minimum_quiz_score`, `require_all_quizzes`, `require_all_assignments`, `certificate_enabled`. Giá trị fallback từ config được resolve khi tạo release.
- Thêm index `(course_id, published_at)` phục vụ backfill.
- `down()` gỡ FK/mapping/field mới theo thứ tự phụ thuộc; không rename/drop bảng nghiệp vụ cũ.

Giữ pin nullable vì dữ liệu legacy có thể unresolved. Chỉ xem xét NOT NULL sau khi audit/backfill đạt 100% và các consumer đã sẵn sàng.

## C. Models

Mới: `CourseVersionSection`, `CourseVersionLesson`, concern `BelongsToImmutableCourseRelease`.

Đổi: `CourseVersion` thêm casts, `sectionMappings()`, `lessonMappings()`, `enrollments()`; `Enrollment` thêm `courseVersion()` và guard chống đổi pin; `CourseSectionVersion`/`LessonVersion` cho phép resolve identity đã archive qua quan hệ version. Concern `HasImmutableVersionState` chặn sửa nội dung/xóa version đã kết thúc, vẫn cho phép published → superseded.

## D. Manifest schema

| Bảng | Nội dung |
| --- | --- |
| `course_version_sections` | id, course_version_id, course_section_id, course_section_version_id, sort_order, timestamps |
| `course_version_lessons` | id, course_version_id, course_section_id nullable, lesson_id, lesson_version_id, sort_order, is_required, quiz_version_id nullable, assignment_version_id nullable, timestamps |

Unique membership theo `(course_version_id, course_section_id)` và `(course_version_id, lesson_id)`. Composite FK buộc section của lesson nằm trong cùng release. Nullable section hỗ trợ identity lesson legacy không có section; `LessonVersion.legacy_chapter_id` hiện có được giữ lại. Mapping section xác định section version nên không lặp `course_section_version_id` trong từng lesson.

Có index order section/lesson và FK index child version. Quan hệ eager load cho phép lấy release, section versions, lesson versions, quiz/question versions và assignment versions bằng số query cố định theo loại quan hệ. Quan hệ identity trên mapping bỏ riêng scope `not_archived`.

## E. Enrollment pinning paths

`EnrollmentVersionService::firstOrCreate()` là điểm tạo mới duy nhất trong `app/` sau thay đổi:

- `Web\CourseController::enroll()`.
- Cart/free/coupon checkout trong `Student\CartController`.
- `PaymentGatewayService::enrollStudent()`: PayOS, MoMo, free finalization và mock.
- Enrollment tạm cho progress và quiz của admin/instructor.

`resolvePublishedVersionForEnrollment()` yêu cầu transaction, chỉ đọc pointer chính thức bằng locking read, kiểm tra course ownership, published status, published_at và manifest hoàn chỉnh. Pointer NULL/sai course/draft/rejected/superseded/metadata-only bị từ chối; không fallback latest.

Đã có enrollment thì trả lại trước bước resolve release hiện hành. Callback không đổi pin; cancelled enrollment được kích hoạt lại trên pin cũ. Enrollment legacy đã tồn tại vẫn giữ NULL để backfill xử lý riêng.

## F. Publication and locking

Initial approval: publish quiz drafts hiện có → snapshot child versions → tạo CourseVersion draft → xây/seal manifest → publish version và pointer trong transaction.

Approved update: lấy manifest hiện hành trước khi mutate base projection; apply candidate/version đã duyệt và delta create/delete/reorder vào bản manifest mới. Unchanged content tái sử dụng child version. Metadata candidate trở thành release đầy đủ khi publish. Mỗi ContentUpdate curriculum được duyệt tạo CourseVersion mới; batch review nằm trong transaction ngoài hiện có.

Lesson quiz mới giữ `quiz_version_id` của draft tại submit; pending creation khóa chỉnh sửa quiz đó. Approval publish đúng candidate rồi đưa vào manifest. Không đổi QuizAttempt flow.

Delete loại membership khỏi release mới, giữ identity/version/assets của release cũ. Không hard-delete historical content. Cleanup document/video/thumbnail được chặn khi còn version published/superseded tham chiếu.

Lock order giữ prefix của finance: instructor IDs tăng dần → Course IDs tăng dần → candidate/version hoặc Enrollment. Paid flow giữ các khóa Order/Payment/Coupon hiện có trước khóa Course. Free checkout lấy instructor lock trước coupon. Refund khóa Course trước Enrollment. Pointer và version được đọc bằng `FOR UPDATE` để tránh snapshot cũ của MySQL REPEATABLE READ.

Không có bước update `enrollments.course_version_id` khi publish. Release superseded vẫn đọc được. Base Course/Lesson tiếp tục là compatibility projection.

## G. Backfill strategy

Artisan command:

```text
php artisan enrollments:backfill-releases
php artisan enrollments:backfill-releases --dry-run
php artisan enrollments:backfill-releases --dry-run --course=2
```

Mặc định dry-run, không ghi DB. Có `--execute` cho lần vận hành được chủ động quyết định; không chạy tùy chọn này trên database nghiệp vụ trong implementation.

Output: course, enrollment, selected version, reason, unresolved reason. Command stream theo chunk 200. Cấm kết hợp `--dry-run` và `--execute`.

Chọn version published/superseded gần nhất có `published_at <= enrolled_at`, cùng Course; cần manifest đã tồn tại tại enrolled_at. Không bỏ qua một metadata snapshot không đầy đủ để lùi về release khác. Timestamp publication trùng nhau được báo ambiguous. Pin đã hợp lệ được giữ nguyên. Execute service được kiểm thử chỉ trong database test.

Đối chiếu QuizAttempt.quiz_version_id và Submission.assignment_version_id theo user/course. History thiếu pin hoặc không khớp manifest được báo unresolved; không rewrite attempt/submission.

## H. Legacy unresolved behavior

Course có published flag nhưng pointer NULL (bao gồm case Course 2 trong audit) được báo `legacy_missing_published_pointer` và skip. Enrollment trước lịch sử được báo `enrollment_predates_release_history`; thiếu manifest lịch sử được báo `historical_manifest_not_proven`. Pointer hỏng, thiếu enrolled_at, timestamp mơ hồ, lịch sử assessment xung đột và pin cũ sai đều được báo riêng.

Không bịa manifest lịch sử hoặc gán latest. Release forward được tạo khi có lần duyệt mới mang timestamp hiện tại; không backdate để hợp thức hóa enrollment cũ. Dữ liệu nghiệp vụ chưa được migrate/backfill trong lần triển khai này, nên chưa có báo cáo số lượng unresolved thực tế mới.

Course/public page và enrollment cũ tiếp tục dùng luồng hiện hành. Ghi danh mới vào Course chưa có release hợp lệ bị fail closed cho đến khi có phương án legacy/baseline được duyệt.

## I. Files changed

- `app/Console/Commands/BackfillEnrollmentReleases.php`
- `app/Http/Controllers/Web/CourseController.php`
- `app/Http/Controllers/Web/Instructor/CourseController.php`
- `app/Http/Controllers/Web/Instructor/CurriculumController.php`
- `app/Http/Controllers/Web/Student/CartController.php`
- `app/Http/Controllers/Web/Student/QuizController.php`
- `app/Models/Concerns/BelongsToImmutableCourseRelease.php`
- `app/Models/Concerns/HasImmutableVersionState.php`
- `app/Models/CourseSectionVersion.php`
- `app/Models/CourseVersion.php`
- `app/Models/CourseVersionLesson.php`
- `app/Models/CourseVersionSection.php`
- `app/Models/Enrollment.php`
- `app/Models/LessonVersion.php`
- `app/Services/ContentUpdateService.php`
- `app/Services/ContentVersionRollbackService.php`
- `app/Services/ContentVersionService.php`
- `app/Services/CourseReleaseLock.php`
- `app/Services/CourseReleaseService.php`
- `app/Services/CourseReviewService.php`
- `app/Services/CurriculumLessonService.php`
- `app/Services/EnrollmentReleaseBackfillService.php`
- `app/Services/EnrollmentVersionService.php`
- `app/Services/PaymentGatewayService.php`
- `app/Services/QuizVersioningService.php`
- `database/migrations/2026_09_03_000001_add_course_release_manifests_and_enrollment_pins.php`
- `docs/phase-1-course-release-foundation.md`
- `tests/Feature/CartCheckoutTest.php`
- `tests/Feature/ContentVersionActivationTest.php`
- `tests/Feature/ContentVersionHistoryRollbackTest.php`
- `tests/Feature/CourseReleaseConcurrencyTest.php`
- `tests/Feature/CourseReleaseFoundationTest.php`
- `tests/Feature/MultiGenerationContentVersioningTest.php`
- `tests/Feature/QuizVersionedAuthoringTest.php`
 Thay đổi có sẵn trong `resources/css/app.css` và `storage/framework/PsySH/` không thuộc implementation này và được giữ nguyên.

## J. Regression tests

`CourseReleaseFoundationTest`: lifecycle V1/V2/draft V3/published V3; pin immutability; callbacks PayOS/MoMo/mock/free; reactivation; direct route; add/delete/reorder; section deletion; assignment/quiz mappings; new quiz candidate; completion rules; metadata arrays; superseded reads; invalid pointers; manifest/content guards; dry-run; idempotent historical backfill; legacy unresolved; conflicting submission history; atomic publication rollback; competing MySQL lock.

`CourseReleaseConcurrencyTest`: hai connection thật với committed fixture, giữ snapshot V1 trong transaction đọc, publish V2 trên connection khác, rồi ghi danh trong transaction cũ phải nhận V2 qua current locking read và giữ enrollment A ở V1. Transaction đó tiếp tục publish V3 và phải giữ nguyên mapping child vừa được V2 thêm. Test riêng xác nhận migration Phase 1 down/up giữ Course/Enrollment legacy.

Fixture các test version/quiz/cart liên quan được bổ sung approved teaching field thực tế; fixture checkout có release thật. Không thay rule phân quyền để làm test pass.

## K–L. Exact tests and validation results

Database kiểm thử: MySQL `web_onlinefea_test`; không chạy migration/backfill trên DB nghiệp vụ.

Lệnh nhóm regression liên quan:

```text
php artisan test --compact tests/Feature/CourseReleaseFoundationTest.php tests/Feature/ContentVersionActivationTest.php tests/Feature/ContentVersionFoundationTest.php tests/Feature/ContentVersionHistoryRollbackTest.php tests/Feature/MultiGenerationContentVersioningTest.php tests/Feature/CartCheckoutTest.php tests/Feature/PayOSPaymentSecurityTest.php tests/Feature/MomoPaymentTest.php tests/Feature/QuizVersionedAuthoringTest.php tests/Feature/CourseReleaseConcurrencyTest.php
```

Kết quả: **122 passed, 0 failed, 681 assertions**, 93.99 giây.

Sau khi tăng cường current reads cho child manifest và mở rộng test publish từ snapshot cũ, chạy lại đúng nhóm chịu ảnh hưởng:

```text
php artisan test --compact tests/Feature/CourseReleaseFoundationTest.php tests/Feature/CourseReleaseConcurrencyTest.php
```

Kết quả lượt cuối: **28 passed, 0 failed, 105 assertions**, 64.27 giây.

Các lượt thử đầu đã phát hiện và sửa lỗi tích hợp/fixture; không dùng kết quả lỗi đó làm kết quả cuối. Test concurrency ban đầu đạt assertions nhưng teardown `DatabaseMigrations` lỗi ở rollback migration discussions có sẵn; test hiện dùng `DatabaseTruncation` và kiểm thử riêng down/up migration Phase 1, không sửa migration discussions.

PHP lint được chạy trên toàn bộ 33 file PHP thêm/sửa. `git diff --check` đã pass. Không chạy full suite.

## M. Remaining Phase 2+ work and operational limits

- Student LearningPlayer/dashboard/progress/certificate UI chưa chuyển sang pin; đây là phạm vi Phase tiếp theo. Việc bảo đảm trải nghiệm học V1 hoàn chỉnh cần chuyển các consumer này sang resolver.
- Quiz attempt và assignment submission hiện hành giữ nguyên; dùng release mappings cho lần bắt đầu assessment sau này ở Phase tương ứng.
- Cần rollout migration và chạy dry-run trên dữ liệu nghiệp vụ; giải quyết unresolved bằng bằng chứng lịch sử hoặc baseline được business duyệt. Chưa được ép NOT NULL.
- Manifest/version writes phải qua service và model guard; SQL/query-builder bulk writes có thể bỏ qua Eloquent events. Không có database trigger chống mọi SQL trực tiếp.
- Không kiểm thử gọi PayOS/MoMo thật, tải S3/HLS thật hoặc load test nhiều worker; kiểm thử dùng database MySQL test và gateway fixtures/mocks.
- Retention/garbage collection tài nguyên lịch sử và quyết định rollback toàn bộ curriculum cần quy trình riêng. Rollback metadata hiện có vẫn là rollback theo loại nội dung; không tự phục hồi toàn bộ Course release.
- Rollback toàn bộ migration cũ có lỗi `discussions_course_student_unique` đang phục vụ FK; không thuộc thay đổi Phase 1. Rollback riêng migration Phase 1 được kiểm thử độc lập.
- Course locking có prefix instructor để tương thích finance, vì vậy nhiều Course của cùng instructor có thể phải chờ nhau trong transaction publication/enrollment.

## N. Confirmations

| Yêu cầu | Kết quả |
| --- | --- |
| Existing enrollment auto-migrated on publish | NO |
| New enrollment gets current published release | YES — khi Course có release hợp lệ; nếu không thì fail closed |
| Old superseded release remains readable | YES |
| Student learning fully version-aware | NO — deferred to next phase |

## O. Readiness

PHASE 1 FOUNDATION READY: YES.

READY FOR PHASE 2: YES.

Áp dụng cho code foundation đã kiểm thử; database nghiệp vụ vẫn cần rollout migration và xử lý unresolved trước khi consumer Phase 2 yêu cầu mọi enrollment đều có pin.
