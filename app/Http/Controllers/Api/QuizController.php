<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    use ApiResponse;

    public function show(Quiz $quiz): JsonResponse
    {
        return $this->success($quiz);
    }

    public function start(Request $request, Quiz $quiz): JsonResponse
    {
        $attempt = QuizAttempt::create([
            'user_id' => $request->user()->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
        ]);

        return $this->success([
            'attempt' => $attempt,
            'quiz' => $quiz,
        ]);
    }

    public function submit(Request $request, Quiz $quiz): JsonResponse
    {
        $validated = $request->validate([
            'answers' => 'required|array', // e.g. [question_id => selected_option_index, ...]
        ]);

        $questions = $quiz->questions ?? [];
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($questions as $question) {
            $questionId = $question['id'] ?? null;
            $points = $question['points'] ?? 10;
            $totalPoints += $points;

            if (isset($validated['answers'][$questionId])) {
                $selectedIndex = $validated['answers'][$questionId];
                
                // Find correct option index in options array
                $correctIndex = null;
                foreach (($question['options'] ?? []) as $idx => $opt) {
                    if (!empty($opt['is_correct'])) {
                        $correctIndex = $idx;
                        break;
                    }
                }

                if ($correctIndex !== null && $correctIndex == $selectedIndex) {
                    $earnedPoints += $points;
                }
            }
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $isPassed = $score >= $quiz->pass_score;

        $attempt = QuizAttempt::create([
            'user_id' => $request->user()->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'is_passed' => $isPassed,
            'answers' => $validated['answers'],
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        return $this->success([
            'attempt' => $attempt,
            'score' => $score,
            'is_passed' => $isPassed,
            'pass_score' => $quiz->pass_score,
        ], $isPassed ? 'Chúc mừng! Bạn đã vượt qua bài kiểm tra' : 'Bạn chưa đạt điểm yêu cầu');
    }
}
