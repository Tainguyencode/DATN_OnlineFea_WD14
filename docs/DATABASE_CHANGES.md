# DATABASE_CHANGES.md

## Bảng mới

| Bảng              | Mô tả                                 |
| ----------------- | ------------------------------------- |
| `course_reviews`  | Lịch sử kiểm duyệt khóa học           |
| `social_accounts` | OAuth providers (Google, GitHub, ...) |

## Cột mới

### courses

- `target_audience`, `requirements`
- `submission_count`, `approved_at`, `suspended_at`
- `required_video_percent`, `required_lesson_percent`, `minimum_quiz_score`
- `require_all_quizzes`, `require_all_assignments`, `certificate_enabled`

### lesson_progress

- `course_id`, `duration_seconds`, `progress_percent`, `last_watched_at`
- Unique: `user_id + lesson_id`

### enrollments

- `completed_lessons`, `total_lessons`, `last_accessed_at`

### lessons

- `is_required` (boolean, default true)

### assignments

- `course_id`, `instructions`, `passing_score`, `due_days`, `is_required`, `allowed_file_types`, `maximum_file_size`

### submissions

- `graded_by`

## Migration status values

`pending` → `pending_review`  
Thêm: `approved`, `suspended`

## Relationships

- `Course hasMany CourseReview`
- `CourseReview belongsTo Course, User (reviewer)`
- `User hasMany SocialAccount`
- `Lesson hasOne Assignment`
- `Assignment hasMany AssignmentSubmission (submissions table)`
