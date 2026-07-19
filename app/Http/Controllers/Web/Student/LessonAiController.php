<?php

namespace App\Http\Controllers\Web\Student;

use App\Exceptions\LessonAiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\ExplainLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\Ai\LessonAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonAiController extends Controller
{
    public function __construct(private readonly LessonAiService $lessonAi) {}

    public function summary(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        try {
            $this->lessonAi->assertCanUseAi($request->user(), $course, $lesson);

            $generate = $request->boolean('generate');
            $payload = $this->lessonAi->getSummary($lesson, $generate);

            $message = null;
            if ($payload['summary'] === null) {
                $message = $payload['has_source']
                    ? 'Chưa có bản tóm tắt. Nhấn “Tóm tắt bài học” để tạo.'
                    : 'Bài học chưa có đủ nội dung văn bản (mô tả hoặc transcript/phụ đề) để tóm tắt.';
            }

            return response()->json([
                'success' => true,
                'summary' => $payload['summary'],
                'key_points' => $payload['key_points'],
                'takeaways' => $payload['takeaways'],
                'cached' => $payload['cached'],
                'source_hash' => $payload['source_hash'],
                'has_source' => $payload['has_source'],
                'model' => $payload['model'],
                'message' => $message,
            ]);
        } catch (LessonAiException $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function explain(ExplainLessonRequest $request, Course $course, Lesson $lesson): JsonResponse
    {
        try {
            $this->lessonAi->assertCanUseAi($request->user(), $course, $lesson);
            $payload = $this->lessonAi->explain(
                $request->user(),
                $lesson,
                (string) $request->validated('question')
            );

            return response()->json([
                'success' => true,
                'question' => $payload['question'],
                'answer' => $payload['answer'],
                'message' => 'Đã nhận giải thích từ AI.',
            ]);
        } catch (LessonAiException $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function errorResponse(LessonAiException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $exception->codeKey,
            'message' => $exception->getMessage(),
        ], $exception->status);
    }
}
