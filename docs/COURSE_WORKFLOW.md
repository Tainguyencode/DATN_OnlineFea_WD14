# COURSE_WORKFLOW.md

## Luồng tạo khóa học (Instructor)

1. Instructor tạo khóa học → `draft`
2. Thêm chương (sections) và bài học (video/document/quiz/assignment)
3. Hệ thống kiểm tra qua `CourseValidationService` + `config/course.php`
4. Checklist hiển thị trên trang edit khóa học
5. Nút **Gửi duyệt** chỉ hoạt động khi đủ điều kiện

## Luồng gửi duyệt

```
draft / rejected → pending_review
```

- Mỗi lần gửi tạo bản ghi mới trong `course_reviews`
- `submission_count` tăng, không ghi đè lịch sử

## Luồng admin duyệt

```
pending_review → approved → published (mặc định duyệt + xuất bản)
```

- Admin xem tại `/admin/course-reviews`
- Checklist bắt buộc trước khi duyệt
- Từ chối: lý do tối thiểu 10 ký tự

## Luồng từ chối và gửi lại

```
pending_review → rejected → instructor sửa → pending_review
```

- Giảng viên xem lý do qua `reject_reason` và timeline `course_reviews`

## Luồng học (Student)

1. Đăng ký / mua khóa học → `enrollments`
2. Xem video → POST progress mỗi 10–15 giây
3. Làm quiz → chấm backend qua `QuizService`
4. Nộp bài tập → `assignments` / `submissions`
5. `CourseCompletionService` kiểm tra điều kiện hoàn thành
6. Cấp chứng chỉ nếu `certificate_enabled`

## Social Login

Google / GitHub → `SocialAuthService` → `social_accounts` + liên kết email trùng
