<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Exceptions\LessonImportException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\PreviewLessonImportRequest;
use App\Models\Course;
use App\Models\CourseSection;
use App\Services\CurriculumLessonService;
use App\Services\LessonImportPreviewService;
use App\Services\LessonImportTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LessonImportController extends Controller
{
    public function downloadTemplate(
        Request $request,
        Course $course,
        LessonImportTemplateService $templateService,
    ): StreamedResponse {
        abort_unless($course->isOwnedBy($request->user()), 403);

        return response()->streamDownload(
            fn () => $templateService->stream(),
            LessonImportTemplateService::FILENAME,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    public function preview(
        PreviewLessonImportRequest $request,
        Course $course,
        CourseSection $section,
        CurriculumLessonService $lessonService,
        LessonImportPreviewService $previewService,
    ): JsonResponse {
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
                $section,
                $request->user(),
            );
        } catch (LessonImportException $exception) {
            Log::warning('Lesson import preview rejected.', [
                'issue_code' => $exception->issueCode,
                'course_id' => $course->id,
                'section_id' => $section->id,
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
                'section_id' => $section->id,
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể preview file Excel lúc này. Vui lòng kiểm tra file và thử lại.',
            ], 500);
        }

        $batch = $result['batch'];

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
}
