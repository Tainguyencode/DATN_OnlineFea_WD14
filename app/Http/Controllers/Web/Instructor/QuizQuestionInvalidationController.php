<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\QuizVersionQuestion;
use App\Services\QuizQuestionInvalidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuizQuestionInvalidationController extends Controller
{
    public function __construct(private readonly QuizQuestionInvalidationService $service) {}

    public function store(Request $request, QuizVersionQuestion $mapping): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:5000'],
        ], [
            'reason.required' => 'Vui lòng nhập lý do hủy câu hỏi.',
            'reason.min' => 'Lý do hủy phải có ít nhất 5 ký tự.',
        ]);

        $this->service->request($mapping, $request->user(), $validated['reason']);

        return back()->with('success', 'Đã gửi yêu cầu hủy câu hỏi tới admin.');
    }
}
