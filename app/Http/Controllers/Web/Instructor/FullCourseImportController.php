<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Exceptions\LessonImportException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\ConfirmFullCourseImportRequest;
use App\Http\Requests\Instructor\PreviewFullCourseImportRequest;
use App\Models\FullCourseImportBatch;
use App\Services\FullCourseImportConfirmService;
use App\Services\FullCourseImportPreviewService;
use App\Services\FullCourseImportTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class FullCourseImportController extends Controller
{
    public function create(): View
    {
        return view('instructor.courses.full-import');
    }

    public function downloadTemplate(FullCourseImportTemplateService $template): StreamedResponse
    {
        return response()->streamDownload(
            fn () => $template->stream(), $template::FILENAME,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    public function preview(PreviewFullCourseImportRequest $request, FullCourseImportPreviewService $preview): JsonResponse
    {
        try {
            $result = $preview->preview($request->file('file'), $request->user());
        } catch (LessonImportException $exception) {
            return response()->json(['success' => false, 'message' => $exception->userMessage, 'error_code' => $exception->issueCode], $exception->httpStatus);
        } catch (Throwable $exception) {
            Log::error('Full course import preview failed.', ['user_id' => $request->user()->id, 'exception' => $exception]);

            return response()->json(['success' => false, 'message' => 'Không thể preview file Excel lúc này.'], 500);
        }
        $batch = $result['batch'];
        $payload = $result['canonical_payload'];

        return response()->json([
            'success' => true,
            'batch' => ['token' => $batch->token, 'template_version' => 3, 'status' => $batch->status, 'expires_at' => $batch->expires_at->toIso8601String(), 'can_confirm' => $batch->error_count === 0],
            'course' => $payload['course'], 'summary' => $result['summary'], 'sections' => $payload['sections'], 'lessons' => $payload['lessons'],
            'quizzes' => $payload['quizzes'], 'questions' => $payload['questions'], 'options' => $payload['options'], 'issues' => $result['issues'], 'sheets' => $result['sheets'],
        ]);
    }

    public function confirm(ConfirmFullCourseImportRequest $request, FullCourseImportConfirmService $confirm): JsonResponse
    {
        try {
            $result = $confirm->confirm($request->validated('batch_token'), $request->user());
        } catch (LessonImportException $exception) {
            return response()->json(['success' => false, 'message' => $exception->userMessage, 'error_code' => $exception->issueCode], $exception->httpStatus);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->validator->errors()->first() ?: 'Dữ liệu quiz không hợp lệ.',
                'errors' => $exception->errors(),
            ], 422);
        } catch (Throwable $exception) {
            Log::error('Full course import confirm failed.', ['user_id' => $request->user()->id, 'exception' => $exception]);

            return response()->json(['success' => false, 'message' => 'Không thể tạo khóa học lúc này. Vui lòng thử lại.'], 500);
        }

        $batch = $result['batch'];
        $course = $result['course'];

        return response()->json([
            'success' => true,
            'idempotent' => $result['idempotent'],
            'batch' => [
                'token' => $batch->token,
                'status' => $batch->status,
                'completed_at' => $batch->completed_at?->toIso8601String(),
                'result' => $batch->result_payload,
            ],
            'redirect_url' => route('instructor.courses.curriculum', $course),
            'message' => 'Khóa học đã được tạo thành công. Video chưa có nguồn cần được tải lên sau.',
        ]);
    }

    public function show(Request $request, FullCourseImportBatch $batch): View
    {
        abort_unless((int) $batch->user_id === (int) $request->user()->id, 403);

        return view('instructor.courses.full-import', ['previewBatch' => $batch]);
    }
}
