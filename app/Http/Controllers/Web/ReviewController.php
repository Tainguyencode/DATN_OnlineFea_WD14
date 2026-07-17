<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Models\Course;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviews) {}

    public function store(StoreReviewRequest $request, Course $course): RedirectResponse
    {
        $this->reviews->create($course, $request->user(), $request->validated());

        return redirect()->to(route('courses.show', $course->slug).'#reviews')
            ->with('success', 'Đánh giá đã được gửi và đang chờ kiểm duyệt.');
    }

    public function update(UpdateReviewRequest $request, Course $course, Review $review): RedirectResponse
    {
        abort_unless((int) $review->course_id === (int) $course->id, 404);
        $this->reviews->update($review, $request->validated());

        return redirect()->to(route('courses.show', $course->slug).'#reviews')
            ->with('success', 'Đánh giá đã được cập nhật và gửi kiểm duyệt lại.');
    }

    public function destroy(Course $course, Review $review): RedirectResponse
    {
        abort_unless((int) $review->course_id === (int) $course->id, 404);
        $this->authorize('delete', $review);
        $this->reviews->delete($review);

        return redirect()->to(route('courses.show', $course->slug).'#reviews')
            ->with('success', 'Đã xóa đánh giá của bạn.');
    }
}
