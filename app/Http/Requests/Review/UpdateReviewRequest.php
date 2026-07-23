<?php

namespace App\Http\Requests\Review;

class UpdateReviewRequest extends StoreReviewRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('review')) ?? false;
    }
}
