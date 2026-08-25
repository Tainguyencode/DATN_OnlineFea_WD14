<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Services\Ai\LearningPathAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LearningPathAiController extends Controller
{
    protected LearningPathAiService $aiService;

    public function __construct(LearningPathAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Lấy Session ID ẩn danh cho người dùng chưa đăng nhập.
     */
    protected function getSessionId(Request $request): string
    {
        $sessionId = $request->session()->get('learning_path_ai_session_id');
        if (! $sessionId) {
            $sessionId = (string) Str::uuid();
            $request->session()->put('learning_path_ai_session_id', $sessionId);
        }

        return $sessionId;
    }

    /**
     * Lấy lịch sử cuộc trò chuyện AI Lộ trình hiện tại để khôi phục giao diện.
     */
    public function getConversation(Request $request): JsonResponse
    {
        $user = auth()->user();
        $sessionId = $this->getSessionId($request);
        $learningPathId = $request->integer('learning_path_id') ?: null;

        $conversation = $this->aiService->getOrCreateConversation($user, $sessionId, $learningPathId);
        $messages = $conversation->messages()
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'metadata' => $msg->metadata,
                    'created_at' => $msg->created_at?->format('H:i d/m/Y'),
                ];
            });

        $currentLpInfo = null;
        if ($learningPathId) {
            $lp = LearningPath::find($learningPathId);
            if ($lp) {
                $currentLpInfo = [
                    'id' => $lp->id,
                    'title' => $lp->title,
                    'slug' => $lp->slug,
                    'level' => $lp->level,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'title' => $conversation->title,
            'context_data' => $conversation->context_data,
            'current_learning_path' => $currentLpInfo,
            'messages' => $messages,
        ]);
    }

    /**
     * Gửi tin nhắn và nhận phản hồi từ AI Tư Vấn Lộ Trình.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'onboarding' => 'nullable|array',
            'learning_path_id' => 'nullable|integer|exists:learning_paths,id',
        ], [
            'message.required' => 'Vui lòng nhập nội dung câu hỏi.',
            'message.max' => 'Câu hỏi không được vượt quá 2000 ký tự.',
        ]);

        $user = auth()->user();
        $sessionId = $this->getSessionId($request);
        $learningPathId = $request->integer('learning_path_id') ?: null;
        $onboardingData = (array) ($request->input('onboarding') ?: []);

        $conversation = $this->aiService->getOrCreateConversation($user, $sessionId, $learningPathId);

        $result = $this->aiService->processMessage(
            $conversation,
            $request->input('message'),
            $onboardingData
        );

        return response()->json($result);
    }

    /**
     * Khởi tạo cuộc trò chuyện mới.
     */
    public function reset(Request $request): JsonResponse
    {
        $user = auth()->user();
        $sessionId = $this->getSessionId($request);
        $learningPathId = $request->integer('learning_path_id') ?: null;

        $conversation = $this->aiService->resetConversation($user, $sessionId, $learningPathId);

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo cuộc trò chuyện mới thành công.',
            'conversation_id' => $conversation->id,
            'title' => $conversation->title,
            'context_data' => $conversation->context_data,
        ]);
    }
}
