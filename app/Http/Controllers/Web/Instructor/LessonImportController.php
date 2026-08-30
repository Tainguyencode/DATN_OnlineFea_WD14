<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Exceptions\LessonImportException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\ConfirmLessonImportRequest;
use App\Http\Requests\Instructor\PreviewLessonImportRequest;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Services\CurriculumLessonService;
use App\Services\LessonImportPreviewService;
use App\Services\LessonImportService;
use App\Services\LessonImportTemplateService;
use App\Services\InstructorCourseCategoryAccess;
use App\Support\LessonImportWorkbookSchema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LessonImportController extends Controller
{
    public function __construct(private readonly InstructorCourseCategoryAccess $courseCategoryAccess) {}

    public function downloadTemplate(
        Request $request,
        Course $course,
        LessonImportTemplateService $templateService,
    ): StreamedResponse {
        $this->authorizeCourse($course, $request->user());

        $requestedVersion = $request->query('version');
        $version = $requestedVersion === null
            ? LessonImportWorkbookSchema::VERSION_V1
            : filter_var($requestedVersion, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        abort_unless(
            $version !== null
                && in_array($version, [
                    LessonImportWorkbookSchema::VERSION_V1,
                    LessonImportWorkbookSchema::VERSION_V2,
                ], true),
            422,
        );

        return response()->streamDownload(
            fn () => $templateService->stream($version),
            $templateService->filenameForVersion($version),
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    public function preview(
        PreviewLessonImportRequest $request,
        Course $course,
        $section,
        CurriculumLessonService $lessonService,
        LessonImportPreviewService $previewService,
    ): JsonResponse {
        $this->authorizeCourse($course, $request->user());

        $sectionModel = $section instanceof CourseSection ? $section : CourseSection::where('course_id', $course->id)->find($section);
        if (! $sectionModel) {
            return response()->json([
                'success' => false,
                'message' => 'Chương học được chọn không tồn tại hoặc đã bị xóa. Vui lòng tải lại trang (F5) để cập nhật danh sách chương mới nhất.',
            ], 422);
        }

        if (! $lessonService->canCreateDirectly($course)) {
            return response()->json([
                'success' => false,
                'message' => 'Import Excel hiện chỉ hỗ trợ khóa học nháp hoặc khóa học bị từ chối chưa xuất bản.',
            ], 422);
        }

        try {
            $result = $previewService->preview(
                $request->file('file'),
                $course,
                $sectionModel,
                $request->user(),
            );
        } catch (LessonImportException $exception) {
            Log::warning('Lesson import preview rejected.', [
                'issue_code' => $exception->issueCode,
                'course_id' => $course->id,
                'section_id' => $sectionModel->id,
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'success' => false,
                'message' => $exception->userMessage,
                'error_code' => $exception->issueCode,
            ], 422);
        } catch (Throwable $exception) {
            Log::error('Lesson import preview failed unexpectedly.', [
                'course_id' => $course->id,
                'section_id' => $sectionModel->id,
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể preview file Excel lúc này. Vui lòng kiểm tra file và thử lại.',
            ], 500);
        }

        $batch = $result['batch'];

        if (($result['template_version'] ?? LessonImportWorkbookSchema::VERSION_V1) === LessonImportWorkbookSchema::VERSION_V2) {
            return response()->json([
                'success' => true,
                'batch' => [
                    'token' => $batch->token,
                    'template_version' => $batch->template_version,
                    'row_count' => $batch->row_count,
                    'valid_count' => $batch->valid_count,
                    'warning_count' => $batch->warning_count,
                    'error_count' => $batch->error_count,
                    'expires_at' => $batch->expires_at->toIso8601String(),
                ],
                'summary' => $result['summary'],
                'sheets' => $result['sheets'],
                'issues' => $result['issues'],
            ]);
        }

        return response()->json([
            'success' => true,
            'batch' => [
                'token' => $batch->token,
                'row_count' => $batch->row_count,
                'valid_count' => $batch->valid_count,
                'warning_count' => $batch->warning_count,
                'error_count' => $batch->error_count,
                'expires_at' => $batch->expires_at->toIso8601String(),
            ],
            'rows' => $result['rows'],
        ]);
    }

    public function confirm(
        ConfirmLessonImportRequest $request,
        Course $course,
        $section,
        LessonImportService $importService,
    ): JsonResponse {
        $this->authorizeCourse($course, $request->user());

        $sectionModel = $section instanceof CourseSection ? $section : CourseSection::where('course_id', $course->id)->find($section);
        if (! $sectionModel) {
            return response()->json([
                'success' => false,
                'message' => 'Chương học được chọn không tồn tại hoặc đã bị xóa. Vui lòng tải lại trang (F5) để cập nhật danh sách chương mới nhất.',
            ], 422);
        }

        try {
            $result = $importService->confirm(
                $request->validated('batch_token'),
                $course,
                $sectionModel,
                $request->user(),
            );
        } catch (LessonImportException $exception) {
            if ($exception->httpStatus < 500) {
                Log::warning('Lesson import confirm rejected.', [
                    'issue_code' => $exception->issueCode,
                    'course_id' => $course->id,
                    'section_id' => $sectionModel->id,
                    'user_id' => $request->user()->id,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $exception->userMessage,
                'error_code' => $exception->issueCode,
            ], $exception->httpStatus);
        } catch (Throwable $exception) {
            Log::error('Lesson import confirm controller failed unexpectedly.', [
                'course_id' => $course->id,
                'section_id' => $sectionModel->id,
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể import bài học. Không có dữ liệu nào được thay đổi.',
            ], 500);
        }

        $sectionTitle = $result['section_title'] !== '' ? $result['section_title'] : 'chương đã chọn';
        $request->session()->flash(
            'success',
            "Đã import {$result['imported_count']} bài học vào {$sectionTitle}.",
        );

        return response()->json([
            'success' => true,
            'batch' => [
                'token' => $result['token'],
                'status' => $result['status'],
                'imported_count' => $result['imported_count'],
            ],
            'redirect_url' => route('instructor.courses.curriculum', $course),
        ]);
    }

    private function authorizeCourse(Course $course, User $user): void
    {
        abort_unless(
            $this->courseCategoryAccess->canManageCourse($user, $course),
            403,
            'Bạn không có quyền chỉnh sửa nội dung khóa học này.'
        );
    }
}
