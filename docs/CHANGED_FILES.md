# CHANGED_FILES.md

## File đã tạo mới

| File | Mục đích |
|------|----------|
| `docs/CURSOR_ANALYSIS.md` | Phân tích codebase và kế hoạch |
| `docs/COURSE_WORKFLOW.md` | Mô tả luồng nghiệp vụ |
| `docs/DATABASE_CHANGES.md` | Thay đổi database |
| `docs/TEST_ACCOUNTS.md` | Tài khoản test |
| `docs/SETUP_GUIDE.md` | Hướng dẫn cài đặt |
| `app/Enums/CourseStatus.php` | Trạng thái khóa học |
| `app/Enums/CourseReviewStatus.php` | Trạng thái kiểm duyệt |
| `config/course.php` | Cấu hình validation/completion |
| `app/Models/CourseReview.php` | Lịch sử kiểm duyệt |
| `app/Models/Assignment.php` | Bài tập |
| `app/Models/AssignmentSubmission.php` | Nộp bài tập |
| `app/Models/LessonProgress.php` | Tiến độ bài học |
| `app/Models/SocialAccount.php` | OAuth accounts |
| `app/Models/VideoNote.php` | Fix reference từ Lesson |
| `app/Services/CourseValidationService.php` | Checklist gửi duyệt |
| `app/Services/CourseReviewService.php` | Submit/approve/reject |
| `app/Services/QuizService.php` | Chấm quiz |
| `app/Services/CourseCompletionService.php` | Điều kiện hoàn thành |
| `app/Services/SocialAuthService.php` | Google/GitHub login |
| `app/Policies/CoursePolicy.php` | Phân quyền khóa học |
| `app/Policies/EnrollmentPolicy.php` | Phân quyền enrollment |
| `app/Http/Controllers/Web/Admin/CourseReviewController.php` | Admin kiểm duyệt |
| `app/Http/Requests/Course/*.php` | Form requests |
| `database/migrations/2026_07_09_*` | 7 migrations mới |
| `database/seeders/CourseReviewSeeder.php` | Dữ liệu kiểm duyệt |
| `resources/views/admin/course-reviews/*` | UI admin review |
| `tests/Feature/CourseReviewWorkflowTest.php` | Feature tests |
| `tests/Feature/LearningProgressTest.php` | Progress tests |

## File đã sửa

| File | Thay đổi |
|------|----------|
| `app/Models/Course.php` | Status, relationships, completion config |
| `app/Models/Enrollment.php` | Progress fields, order() |
| `app/Models/User.php` | socialAccounts, instructorApplication |
| `app/Models/Lesson.php` | is_required |
| `app/Services/LearningProgressService.php` | Nâng cấp tiến độ video |
| `app/Http/Controllers/Web/Instructor/CourseController.php` | CourseReviewService submit |
| `app/Http/Controllers/Web/Admin/ManageController.php` | Delegate approve/reject |
| `app/Http/Controllers/Web/AuthController.php` | SocialAuthService |
| `app/Http/Controllers/Web/Student/QuizController.php` | QuizService |
| `app/Http/Controllers/Web/CourseController.php` | Progress API response |
| `routes/web.php` | course-reviews routes, student progress |
| `.env.example` | OAuth keys |
| `database/seeders/*` | is_published, draft/pending courses |
| `phpunit.xml` | MySQL for tests |

## Chưa hoàn thiện

- Assignment submit/grade UI controllers (model + DB đã có)
- Instructor checklist component trên edit page (validation service sẵn sàng)
- Video progress JS heartbeat trên lesson.blade.php
- Payment gateway thật (MoMo/VNPay)
- Discussions, support tickets, AI modules
