# Kết quả chạy toàn bộ bộ test

Chạy trên MySQL riêng: web_onlinefea_test, cổng 3307. Không kiểm thử giao dịch thanh toán/S3 thật và không thao tác UI trình duyệt.

Tổng testcase trong JUnit: 748. Test có failure/error: 64. Skipped: 0.

JavaScript: 14/14 đạt.

Lưu ý: chạy toàn bộ test hiện có không đồng nghĩa đã bao phủ mọi tổ hợp input của mọi form. Test không đạt cần đối chiếu kỳ vọng và dữ liệu chuẩn bị, không mặc định là lỗi ứng dụng.

## Danh sách test không đạt

Kết quả PHPUnit: 748 tests; 6.290 assertions; 61 failures; 3 errors; 2 risky. Risky có thể trùng với test thất bại, không cộng trực tiếp các số này.

## Đối chiếu kết quả

- Nhiều test thao tác khóa học/import/curriculum bị HTTP 403 trước khi tới validation. Cần chuẩn bị hồ sơ và chuyên ngành giảng viên phù hợp với InstructorCourseCategoryAccess; không bỏ kiểm tra quyền để làm test xanh.
- DemoDataIntegrityTest kỳ vọng dữ liệu demo có sẵn nhưng database kiểm thử không có bộ dữ liệu đó. Không dùng kết quả này để kết luận database thật mất dữ liệu.
- Test hoàn thành bài đọc còn kỳ vọng hoàn thành ngay, trong khi cơ chế hiện tại yêu cầu 30 giây.
- Test trang thanh toán thành công kiểm tra không được có bất kỳ URL dashboard nào; header hiện có URL này. Cần kiểm tra riêng đích của nút Vào học ngay.
- Test badge yêu thích kiểm tra sự vắng mặt của thuộc tính trong HTML. CẦN KIỂM TRA THÊM khả năng badge vẫn có trong DOM nhưng được ẩn trên giao diện.
- Ba errors là không tìm thấy model Course (hai test) và Lesson (một test) sau thao tác tạo. Cần xử lý nguyên nhân thao tác tạo bị chặn trước, không mặc định là lỗi kết nối database.

Chi tiết từng test dưới đây là kết quả thực tế, chưa sửa source hoặc thay đổi kỳ vọng để ép test đạt.

### Tests.Feature.CourseCategoryManagementTest :: test_instructor_sees_categories_in_course_create_form

Vị trí: D:\DATN\tests\Feature\CourseCategoryManagementTest.php:103

' [UTF-8](length: 70040) contains "Programming" [ASCII](length: 11).

### Tests.Feature.CourseCategoryManagementTest :: test_instructor_can_store_valid_child_category_id

Vị trí: D:\DATN\tests\Feature\CourseCategoryManagementTest.php:116

Illuminate\Database\Eloquent\ModelNotFoundException: No query results for model [App\Models\Course].

### Tests.Feature.CourseCategoryManagementTest :: test_instructor_can_store_active_leaf_category

Vị trí: D:\DATN\tests\Feature\CourseCategoryManagementTest.php:154

Illuminate\Database\Eloquent\ModelNotFoundException: No query results for model [App\Models\Course].

### Tests.Feature.CourseCategoryManagementTest :: test_course_edit_selects_current_category

Vị trí: D:\DATN\tests\Feature\CourseCategoryManagementTest.php:182

Expected response status code [200] but received 403.

### Tests.Feature.CourseReviewWorkflowTest :: test_controller_validates_copyright_agreement_input

Vị trí: D:\DATN\tests\Feature\CourseReviewWorkflowTest.php:259

Session is missing expected key [errors].

### Tests.Feature.CourseReviewWorkflowTest :: test_controller_allows_submission_when_copyright_agreed_in_post_payload

Vị trí: D:\DATN\tests\Feature\CourseReviewWorkflowTest.php:271

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.CourseReviewWorkflowTest :: test_controller_allows_submission_when_course_has_prior_copyright_agreement

Vị trí: D:\DATN\tests\Feature\CourseReviewWorkflowTest.php:287

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.CourseReviewWorkflowTest :: test_adding_lesson_to_chapter_assigns_course_id

Vị trí: D:\DATN\tests\Feature\CourseReviewWorkflowTest.php:302

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.CourseSubmissionUxTest :: test_all_ready_videos_with_a_missing_thumbnail_return_the_actionable_validator_error

Vị trí: D:\DATN\tests\Feature\CourseSubmissionUxTest.php:17

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.CourseSubmissionUxTest :: test_all_submission_requirements_satisfied_allows_review_submission

Vị trí: D:\DATN\tests\Feature\CourseSubmissionUxTest.php:54

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.CourseVideoReviewReadinessTest :: test_terminal_stale_content_update_does_not_override_a_current_ready_video

Vị trí: D:\DATN\tests\Feature\CourseVideoReviewReadinessTest.php:58

Expected response status code [200] but received 403.

### Tests.Feature.DefenseTopTenRegressionTest :: test_published_video_upload_creates_candidate_without_mutating_live_lesson

Vị trí: D:\DATN\tests\Feature\DefenseTopTenRegressionTest.php:205

Expected response status code [200] but received 403.

### Tests.Feature.DefenseTopTenRegressionTest :: test_payment_success_links_to_the_purchased_lesson_for_both_gateways

Vị trí: D:\DATN\tests\Feature\DefenseTopTenRegressionTest.php:223

' [UTF-8](length: 37853) does not contain "href="http://localhost/student/dashboard"" [ASCII](length: 41).

### Tests.Feature.DemoDataIntegrityTest :: test_demo_users_count_and_roles

Vị trí: D:\DATN\tests\Feature\DemoDataIntegrityTest.php:20

Failed asserting that 0 matches expected 1.

### Tests.Feature.DemoDataIntegrityTest :: test_demo_courses_count_and_integrity

Vị trí: D:\DATN\tests\Feature\DemoDataIntegrityTest.php:53

Failed asserting that 0 is equal to 60 or is greater than 60.

### Tests.Feature.DemoDataIntegrityTest :: test_learning_paths_are_valid_and_non_empty

Vị trí: D:\DATN\tests\Feature\DemoDataIntegrityTest.php:68

Failed asserting that 0 is equal to 8 or is greater than 8.

### Tests.Feature.DemoDataIntegrityTest :: test_real_mp4_video_files_exist_and_are_valid

Vị trí: D:\DATN\tests\Feature\DemoDataIntegrityTest.php:82

Failed asserting that 0 is equal to 50 or is greater than 50.

### Tests.Feature.DemoDataIntegrityTest :: test_certificates_only_for_100_percent_completed_enrollments

Vị trí: D:\DATN\tests\Feature\DemoDataIntegrityTest.php:102

Failed asserting that 0 is greater than 0.

### Tests.Feature.DemoDataIntegrityTest :: test_hls_conversion_pipeline_on_sample_lesson

Vị trí: D:\DATN\tests\Feature\DemoDataIntegrityTest.php:117

Failed asserting that null is not null.

### Tests.Feature.FavoritesTest :: test_header_favorite_badge_disappears_after_unfavorite

Vị trí: D:\DATN\tests\Feature\FavoritesTest.php:155

' [UTF-8](length: 87320) does not contain "data-favorite-badge" [ASCII](length: 19).

### Tests.Feature.InstructorCurriculumLessonTest :: test_legacy_section_lessons_url_redirects_owner_to_the_sections_actual_course

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:22

Expected response status code [200] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_lesson_form_starts_with_common_fields_and_type_panels_are_exclusive

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:35

Expected response status code [200] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_video_lesson_can_be_created_with_only_video_fields

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:55

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_document_lesson_can_be_created_without_video_data

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:82

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_quiz_lesson_redirects_to_existing_quiz_manager

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:104

Illuminate\Database\Eloquent\ModelNotFoundException: No query results for model [App\Models\Lesson].

### Tests.Feature.InstructorCurriculumLessonTest :: test_assignment_lesson_creates_assignment_record_from_existing_schema

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:130

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_rejected_course_uses_the_same_direct_creation_flow_as_draft_course

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:160

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_validation_depends_on_selected_lesson_type_and_keeps_old_type

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:243

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_edit_lesson_form_opens_current_type_panel

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:277

Expected response status code [200] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_update_document_lesson_keeps_existing_file_when_no_new_file_is_uploaded

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:295

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_section_create_and_update_keep_plain_text_description_without_code_leak

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:350

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.InstructorCurriculumLessonTest :: test_section_description_rejects_markup_and_legacy_blade_source_is_not_rendered

Vị trí: D:\DATN\tests\Feature\InstructorCurriculumLessonTest.php:384

Expected response status code [200] but received 403.

### Tests.Feature.LearningProgressTest :: test_document_lesson_can_be_manually_completed

Vị trí: D:\DATN\tests\Feature\LearningProgressTest.php:232

Expected response status code [200] but received 422.

### Tests.Feature.LessonImportConfirmTest :: test_confirm_imports_all_supported_types_in_order_as_ready_non_preview_shells

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:41

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportConfirmTest :: test_same_completed_token_is_idempotent_even_after_preview_expiry

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:134

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportConfirmTest :: test_error_and_expired_batches_are_rejected_without_lesson_writes

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:154

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportConfirmTest :: test_confirm_rechecks_batch_user_course_and_section_snapshots

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:184

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportConfirmTest :: test_confirm_rechecks_course_eligibility_and_never_creates_content_update

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:220

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportConfirmTest :: test_duplicate_file_is_blocked_only_for_same_user_course_and_section

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:239

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportConfirmTest :: test_canonical_payload_is_revalidated_and_empty_batches_are_rejected

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:274

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportConfirmTest :: test_mid_batch_technical_failure_rolls_back_everything_and_failed_batch_can_retry

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:311

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportConfirmTest :: test_confirm_accepts_only_batch_token_and_handles_missing_or_importing_batch_safely

Vị trí: D:\DATN\tests\Feature\LessonImportConfirmTest.php:351

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_template_endpoint_returns_verified_xlsx_schema

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:41

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_template_endpoint_returns_v2_only_when_explicitly_requested

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:78

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_curriculum_renders_lesson_import_trigger_dialog_and_section_routes_for_owner

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:106

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_curriculum_import_dialog_explains_that_a_section_is_required

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:153

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_preview_reuses_phase_zero_course_eligibility_rule

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:228

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_request_rejects_non_xlsx_fake_extension_macro_and_oversized_files

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:269

Expected response status code [422] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_parser_rejects_corrupt_workbook_and_structural_schema_errors_without_batch

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:326

Expected response status code [422] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_formula_cell_is_rejected_without_evaluation_or_batch

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:363

Expected response status code [422] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_exactly_100_rows_are_allowed_and_101_rows_are_rejected

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:383

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_business_row_errors_are_saved_in_preview_batch

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:407

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_shell_and_duplicate_title_warnings_while_quiz_shell_remains_valid

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:438

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_assignment_defaults_and_strict_assignment_rules

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:470

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportPreviewTest :: test_preview_canonicalizes_values_creates_only_batch_and_tracks_expiration

Vị trí: D:\DATN\tests\Feature\LessonImportPreviewTest.php:498

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportV2ParserValidatorTest :: test_preview_returns_the_v2_contract_without_creating_course_content

Vị trí: D:\DATN\tests\Feature\LessonImportV2ParserValidatorTest.php:132

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportV2ParserValidatorTest :: test_confirm_creates_a_version_aware_quiz_tree_and_is_idempotent

Vị trí: D:\DATN\tests\Feature\LessonImportV2ParserValidatorTest.php:161

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportV2ParserValidatorTest :: test_confirm_allows_an_inactive_draft_quiz_shell

Vị trí: D:\DATN\tests\Feature\LessonImportV2ParserValidatorTest.php:223

Expected response status code [200] but received 403.

### Tests.Feature.LessonImportV2ParserValidatorTest :: test_confirm_rolls_back_the_entire_v2_tree_when_option_creation_fails

Vị trí: D:\DATN\tests\Feature\LessonImportV2ParserValidatorTest.php:254

Expected response status code [200] but received 403.

### Tests.Feature.PendingUpdateUdemyWorkflowTest :: test_instructor_adding_lesson_to_published_course_creates_content_update_draft_and_not_real_lesson

Vị trí: D:\DATN\tests\Feature\PendingUpdateUdemyWorkflowTest.php:65

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.PendingUpdateUdemyWorkflowTest :: test_instructor_can_edit_a_draft_lesson_content_update

Vị trí: D:\DATN\tests\Feature\PendingUpdateUdemyWorkflowTest.php:97

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.PendingUpdateUdemyWorkflowTest :: test_pending_update_course_keeps_using_content_update_workflow

Vị trí: D:\DATN\tests\Feature\PendingUpdateUdemyWorkflowTest.php:133

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

### Tests.Feature.PendingUpdateUdemyWorkflowTest :: test_instructor_curriculum_renders_merged_lessons_with_draft_badge

Vị trí: D:\DATN\tests\Feature\PendingUpdateUdemyWorkflowTest.php:160

Expected response status code [200] but received 403.

### Tests.Feature.PendingUpdateUdemyWorkflowTest :: test_admin_approval_of_assignment_lesson_creates_assignment_atomically

Vị trí: D:\DATN\tests\Feature\PendingUpdateUdemyWorkflowTest.php:302

Expected response status code [201, 301, 302, 303, 307, 308] but received 403.

