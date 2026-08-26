<?php

namespace App\Http\Controllers\Web\Student;

use App\Exceptions\LessonAiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\ChatLessonRequest;
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

    public function chat(ChatLessonRequest $request, Course $course, Lesson $lesson): JsonResponse
    {
        try {
            $this->lessonAi->assertCanUseAi($request->user(), $course, $lesson);

            $payload = $this->lessonAi->chat(
                $request->user(),
                $course,
                $lesson,
                (string) $request->validated('message'),
                $request->validated('conversation_id') ? (int) $request->validated('conversation_id') : null
            );

            return response()->json([
                'success' => true,
                'message' => $payload['message'],
                'answer' => $payload['message'],
                'conversation_id' => $payload['conversation_id'],
            ]);
        } catch (LessonAiException $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function history(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        try {
            $this->lessonAi->assertCanUseAi($request->user(), $course, $lesson);

            $payload = $this->lessonAi->history($request->user(), $course, $lesson);

            return response()->json([
                'success' => true,
                'conversation_id' => $payload['conversation_id'],
                'messages' => $payload['messages'],
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
            'message' => $this->publicErrorMessage($exception->codeKey),
        ], $exception->status);
    }

    private function publicErrorMessage(string $code): string
    {
        return match ($code) {
            'no_source' => 'Bài học chưa có đủ nội dung văn bản để dùng AI.',
            'validation', 'invalid_request' => 'Dữ liệu câu hỏi không hợp lệ.',
            'forbidden' => 'Bạn không có quyền dùng AI hỗ trợ bài học.',
            'lesson_mismatch' => 'Bài học không thuộc khóa học này.',
            'conversation_mismatch' => 'Cuộc hội thoại không thuộc bài học hiện tại.',
            'content_blocked' => 'Nội dung này chưa thể được AI xử lý. Vui lòng thử câu hỏi khác.',
            'response_truncated' => 'Phản hồi AI bị cắt vì quá dài. Hãy hỏi ngắn hơn.',
            'empty_response' => 'AI không trả về nội dung. Vui lòng thử lại.',
            'invalid_response' => 'Phản hồi AI không hợp lệ. Vui lòng thử lại.',
            'timeout' => 'AI đang phản hồi chậm. Vui lòng thử lại.',
            default => 'Tính năng AI hiện chưa khả dụng. Vui lòng thử lại sau.',
        };
    }
}
