# CURSOR_ANALYSIS — FEA Online Learning Platform

> Phân tích codebase trước khi hoàn thiện nghiệp vụ E-learning.  
> Ngày: 2026-07-09 | Laravel 13 | PHP 8.3+

---

## 1. Kiến trúc hiện tại

| Thành phần  | Chi tiết                                                                     |
| ----------- | ---------------------------------------------------------------------------- |
| Framework   | Laravel **13.x** (`laravel/framework: ^13.0`)                                |
| PHP         | `^8.3`                                                                       |
| Pattern     | Monolithic MVC + Service layer                                               |
| Routing     | Chỉ `routes/web.php` (không có API routes đăng ký)                           |
| Auth        | Session guard `web`, model `User`, cột `role` (`student\|instructor\|admin`) |
| RBAC        | Song song: `users.role` + bảng `roles/permissions` + Gates động              |
| Frontend    | Blade + Tailwind CSS 4 + Vite 7 + Alpine.js 3                                |
| DB mặc định | SQLite (`.env.example`)                                                      |
| OAuth       | Laravel Socialite (Google, Facebook, GitHub, Microsoft)                      |

### Cấu trúc thư mục chính

```
app/
├── Enums/              ← (thiếu) cần CourseStatus
├── Http/Controllers/Web/{Admin,Instructor,Student,Auth,Course,...}
├── Http/Controllers/Api/  ← tồn tại nhưng KHÔNG có routes
├── Http/Middleware/    active, role, 2fa
├── Http/Requests/      Auth + Admin (thiếu Course/Lesson/Quiz)
├── Models/             29 models (thiếu Assignment, CourseReview, SocialAccount, LessonProgress)
├── Policies/           UserPolicy, RolePolicy (thiếu CoursePolicy)
└── Services/           Auth, LearningProgress, Captcha, ActivityLog, Notification, Recommendation
```

---

## 2. Các module đã có

### Hoạt động ổn định (Web)

| Module                                     | Trạng thái | Ghi chú                                                                                  |
| ------------------------------------------ | ---------- | ---------------------------------------------------------------------------------------- |
| Auth (login/register/2FA/captcha)          | ✅         | Form Request, AuthService                                                                |
| Social login (Google/GitHub/...)           | ⚠️         | Có code trong AuthController, lưu `*_id` trên users, chưa có `social_accounts`           |
| Khóa học công khai                         | ✅         | index, show, lesson, enroll                                                              |
| Instructor CRUD khóa học                   | ✅         | draft → submit → pending                                                                 |
| Curriculum (sections + legacy chapters)    | ⚠️         | Hai hệ thống song song                                                                   |
| Quiz (instructor builder + student submit) | ✅         | Chấm điểm backend, không lộ đáp án trước nộp                                             |
| Giỏ hàng / Checkout mock                   | ✅         | Tạo Order, Payment, Enrollment                                                           |
| Admin duyệt/từ chối khóa học               | ⚠️         | Có approve/reject nhưng **không có lịch sử**, `pending` thay vì `pending_review`         |
| Learning progress                          | ⚠️         | `LearningProgressService` cơ bản, thiếu `progress_percent`, `course_id`, ngưỡng cấu hình |
| Push notifications                         | ✅         | `PushNotification` model, bell composer                                                  |
| RBAC admin users/roles                     | ✅         | UserController, RoleController, Policies                                                 |

### Có DB nhưng chưa có nghiệp vụ

- `assignments` / `submissions` — không có model/controller
- `discussions`, `support_tickets`, `live_sessions`, `study_groups` — DB only
- `certificates` — hiển thị, chưa có workflow cấp chứng chỉ tự động
- `instructor_applications` — controller/view tồn tại, **routes chưa đăng ký**

### Dead code

- `app/Http/Controllers/Api/*` — 3 controllers, 0 routes
- `Student\DashboardController` — không được route
- `Admin\AuthController` — không có route/guard riêng

---

## 3. Lỗi phát hiện

### Critical

1. **Trạng thái khóa học không chuẩn** — dùng `pending` thay `pending_review`, thiếu `approved`, `suspended`
2. **Không có bảng `course_reviews`** — lịch sử kiểm duyệt bị ghi đè qua `reject_reason`
3. **Validation gửi duyệt yếu** — chỉ kiểm tra title/description/thumbnail/1 section/1 lesson (hard-code trong controller)
4. **`CourseSeeder`** — `status=published` nhưng thiếu `is_published=true` → khóa học không hiện catalog
5. **`Lesson` model** tham chiếu `Assignment`, `VideoNote` — class không tồn tại
6. **Reject reason** — min length không validate (chỉ `required|string|max:1000`)
7. **Khóa học `pending_review`** — instructor vẫn có thể sửa (chưa lock)
8. **Quiz grading** — logic trong controller, chưa tách `QuizService`
9. **Course completion** — chỉ dựa % bài hoàn thành, không tính quiz/assignment bắt buộc
10. **lesson_progress** — thiếu unique index `user_id+lesson_id`, thiếu `course_id`, `progress_percent`, `duration_seconds`

### Schema / Migration

11. Dual curriculum: `chapters` + `course_sections`
12. `reject_reason` + `rejection_reason` trùng lặp trên `courses`
13. `enrollments` thiếu `completed_lessons`, `total_lessons`, `last_accessed_at`
14. `assignments` schema đơn giản, thiếu `passing_score`, `is_required`, `allowed_file_types`
15. Enrollment migration duplicate (`2026_07_01_000004`)

### Security

16. Instructor authorize chỉ `abort_unless(isOwnedBy)` — chưa dùng Policy
17. `quick-login` route — rủi ro production
18. Social login — không có bảng `social_accounts`, link email trùng chưa tách provider

---

## 4. Bảng còn thiếu / cần bổ sung

| Bảng              | Hành động                                                                                       |
| ----------------- | ----------------------------------------------------------------------------------------------- |
| `course_reviews`  | **Tạo mới** — lịch sử kiểm duyệt                                                                |
| `social_accounts` | **Tạo mới** — OAuth providers                                                                   |
| `courses`         | Thêm: `target_audience`, `requirements`, `submission_count`, completion config fields           |
| `lesson_progress` | Thêm: `course_id`, `duration_seconds`, `progress_percent`, `last_watched_at`, unique index      |
| `enrollments`     | Thêm: `completed_lessons`, `total_lessons`, `last_accessed_at`                                  |
| `lessons`         | Thêm: `is_required` (boolean)                                                                   |
| `assignments`     | Thêm: `instructions`, `passing_score`, `is_required`, `allowed_file_types`, `maximum_file_size` |
| `submissions`     | Chuẩn hóa status: `draft/submitted/graded/resubmit_required`                                    |

---

## 5. Route còn thiếu

| Route                                                          | Mục đích                                  |
| -------------------------------------------------------------- | ----------------------------------------- |
| `GET /admin/course-reviews`                                    | Danh sách khóa chờ duyệt (chuẩn hóa)      |
| `GET /admin/course-reviews/{course}`                           | Chi tiết kiểm duyệt + checklist           |
| `POST /admin/course-reviews/{course}/approve`                  | Duyệt + lưu lịch sử                       |
| `POST /admin/course-reviews/{course}/reject`                   | Từ chối + lý do bắt buộc                  |
| `POST /student/courses/{course}/lessons/{lesson}/progress`     | Cập nhật tiến độ video (JSON)             |
| `POST /student/assignments/{assignment}/submit`                | Nộp bài tập                               |
| `POST /instructor/assignments/{assignment}/grade/{submission}` | Chấm bài                                  |
| `GET /student/become-instructor`                               | Đăng ký giảng viên (view có, route thiếu) |

---

## 6. Kế hoạch sửa theo giai đoạn

### Giai đoạn 1 — Phân tích ✅

- Tạo `docs/CURSOR_ANALYSIS.md`

### Giai đoạn 2 — Database & Models

- `app/Enums/CourseStatus.php`, `CourseReviewStatus.php`
- `config/course.php`
- Migrations: `course_reviews`, enhance courses/lesson_progress/enrollments/lessons/assignments, `social_accounts`
- Models: `CourseReview`, `Assignment`, `AssignmentSubmission`, `LessonProgress`, `SocialAccount`
- Cập nhật `Course`, `Enrollment`, `User`, `Lesson`

### Giai đoạn 3 — Services

- `CourseValidationService` — checklist gửi duyệt
- `CourseReviewService` — submit/approve/reject + notifications + history
- `QuizService` — tách chấm điểm
- `LearningProgressService` — nâng cấp tiến độ video
- `CourseCompletionService` — `checkCourseCompletion()`
- `SocialAuthService` — Google/GitHub chuẩn hóa

### Giai đoạn 4 — Authorization

- `CoursePolicy`, `EnrollmentPolicy`
- Form Requests: Submit/Reject/Progress/Quiz/Assignment
- Middleware giữ nguyên `role:admin|instructor|student`

### Giai đoạn 5 — Course Review Workflow

- `Admin\CourseReviewController`
- Cập nhật `Instructor\CourseController@submit`
- Cập nhật `ManageController` (delegate hoặc redirect)
- Views: admin course-reviews, instructor checklist + timeline

### Giai đoạn 6 — Learning, Quiz, Assignment

- Progress endpoint + JS heartbeat
- Assignment submit/grade controllers
- Certificate trigger on completion

### Giai đoạn 7 — Social Login

- `social_accounts` table + `SocialAuthService`
- Cập nhật `.env.example` OAuth keys
- GitHub email-less handling

### Giai đoạn 8 — Seeders & Tests

- `CourseReviewSeeder`, fix `CourseSeeder` (`is_published`)
- Feature tests (18 cases theo spec)
- Docs: WORKFLOW, DATABASE_CHANGES, SETUP, TEST_ACCOUNTS, CHANGED_FILES

---

## 7. Danh sách file dự kiến chỉnh sửa

### Tạo mới

```
app/Enums/CourseStatus.php
app/Enums/CourseReviewStatus.php
app/Enums/SubmissionStatus.php
config/course.php
app/Models/CourseReview.php
app/Models/Assignment.php
app/Models/AssignmentSubmission.php
app/Models/LessonProgress.php
app/Models/SocialAccount.php
app/Services/CourseValidationService.php
app/Services/CourseReviewService.php
app/Services/QuizService.php
app/Services/CourseCompletionService.php
app/Services/SocialAuthService.php
app/Policies/CoursePolicy.php
app/Policies/EnrollmentPolicy.php
app/Http/Requests/Course/SubmitCourseReviewRequest.php
app/Http/Requests/Course/RejectCourseRequest.php
app/Http/Requests/Learning/UpdateLessonProgressRequest.php
app/Http/Requests/Assignment/SubmitAssignmentRequest.php
app/Http/Requests/Assignment/GradeAssignmentRequest.php
app/Http/Controllers/Web/Admin/CourseReviewController.php
app/Http/Controllers/Web/Student/AssignmentController.php
app/Http/Controllers/Web/Instructor/AssignmentController.php
database/migrations/2026_07_09_000001_create_course_reviews_table.php
database/migrations/2026_07_09_000002_enhance_courses_for_workflow.php
database/migrations/2026_07_09_000003_enhance_lesson_progress_table.php
database/migrations/2026_07_09_000004_enhance_enrollments_and_lessons.php
database/migrations/2026_07_09_000005_create_social_accounts_table.php
database/migrations/2026_07_09_000006_enhance_assignments_submissions.php
database/migrations/2026_07_09_000007_migrate_course_status_values.php
database/seeders/CourseReviewSeeder.php
tests/Feature/CourseReviewWorkflowTest.php
tests/Feature/LearningProgressTest.php
tests/Feature/QuizGradingTest.php
tests/Feature/SocialAuthTest.php
tests/Feature/AuthorizationTest.php
docs/COURSE_WORKFLOW.md
docs/DATABASE_CHANGES.md
docs/TEST_ACCOUNTS.md
docs/SETUP_GUIDE.md
docs/CHANGED_FILES.md
resources/views/admin/course-reviews/index.blade.php
resources/views/admin/course-reviews/show.blade.php
resources/views/components/course-submission-checklist.blade.php
```

### Sửa đổi

```
app/Models/Course.php
app/Models/Enrollment.php
app/Models/User.php
app/Models/Lesson.php
app/Services/LearningProgressService.php
app/Http/Controllers/Web/Instructor/CourseController.php
app/Http/Controllers/Web/Admin/ManageController.php
app/Http/Controllers/Web/Student/QuizController.php
app/Http/Controllers/Web/CourseController.php
app/Http/Controllers/Web/AuthController.php
app/Providers/AppServiceProvider.php
routes/web.php
.env.example
database/seeders/CourseSeeder.php
database/seeders/DatabaseSeeder.php
database/seeders/InteractionSeeder.php
resources/views/instructor/courses/edit.blade.php
resources/views/admin/courses/review.blade.php
resources/views/courses/lesson.blade.php
resources/js/app.js
```

---

## 8. Mapping trạng thái khóa học (migration)

| Cũ          | Mới               |
| ----------- | ----------------- |
| `draft`     | `draft`           |
| `pending`   | `pending_review`  |
| `published` | `published`       |
| `rejected`  | `rejected`        |
| `archived`  | `archived`        |
| —           | `approved` (mới)  |
| —           | `suspended` (mới) |

Luồng: `draft` → `pending_review` → `approved` → `published`  
Từ chối: `pending_review` → `rejected` → sửa → `pending_review`

---

## 9. Rủi ro & giới hạn

- Không chạy `migrate:fresh` tự động — chỉ migration additive
- Giữ dual curriculum (sections ưu tiên, chapters fallback) để không phá dữ liệu cũ
- Payment gateway (MoMo/VNPay) vẫn mock — ngoài phạm vi lần này
- AI chat/support tickets — không triển khai trong phase này
