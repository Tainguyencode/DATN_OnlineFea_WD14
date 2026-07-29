<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewHelpfulController extends Controller
{
    public function __construct(private readonly ReviewService $reviews) {}

    public function toggle(Request $request, Review $review): JsonResponse|RedirectResponse
    {
        $this->authorize('markHelpful', $review);
        $marked = $this->reviews->toggleHelpful($review, $request->user());
        $review->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'marked' => $marked,
                'helpful_count' => $review->helpful_count,
            ]);
        }

        return back()->with('success', $marked ? 'Đã ghi nhận đánh giá hữu ích.' : 'Đã bỏ đánh dấu hữu ích.');
    }
}
