<article id="reviews" class="scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-[#161615] sm:p-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-300">Phản hồi học viên</p>
            <h2 class="mt-1 text-2xl font-extrabold text-slate-950 dark:text-white">Đánh giá khóa học</h2>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400">Chỉ đánh giá đã duyệt được tính vào điểm chung.</p>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)]">
        <div class="rounded-2xl bg-slate-950 p-6 text-center text-white dark:bg-slate-900">
            <div class="text-5xl font-black text-amber-400">{{ number_format($ratingSummary['average'], 1) }}</div>
            <div class="mt-2 flex justify-center gap-1" aria-label="{{ number_format($ratingSummary['average'], 1) }} trên 5 sao">
                @for($star = 1; $star <= 5; $star++)
                    <svg class="h-5 w-5 {{ $star <= round($ratingSummary['average']) ? 'text-amber-400' : 'text-slate-600' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            <p class="mt-2 text-sm font-semibold text-slate-300">{{ number_format($ratingSummary['count']) }} đánh giá đã duyệt</p>
        </div>

        <div class="space-y-2" aria-label="Phân bố số sao">
            @for($star = 5; $star >= 1; $star--)
                @php
                    $count = $ratingDistribution[$star] ?? 0;
                    $percent = $ratingSummary['count'] > 0 ? round(($count / $ratingSummary['count']) * 100) : 0;
                @endphp
                <a href="{{ route('courses.show', ['slug' => $course->slug, 'review_rating' => $star, 'review_sort' => $reviewSort]).'#reviews' }}" class="group flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 transition-colors hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:hover:bg-slate-900">
                    <span class="w-12 text-sm font-bold text-slate-700 dark:text-slate-200">{{ $star }} sao</span>
                    <span class="h-2.5 min-w-0 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        <span class="block h-full rounded-full bg-amber-400" style="width: {{ $percent }}%"></span>
                    </span>
                    <span class="w-16 text-right text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $percent }}% ({{ $count }})</span>
                </a>
            @endfor
        </div>
    </div>

    @auth
        @if($userReview)
            <section class="mt-7 rounded-2xl border border-indigo-200 bg-indigo-50/60 p-5 dark:border-indigo-500/30 dark:bg-indigo-500/5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-extrabold text-slate-950 dark:text-white">Đánh giá của bạn</h3>
                        <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $userReview->status->badgeClasses() }}">{{ $userReview->status->label() }}</span>
                    </div>
                    @if($canDeleteReview)
                        <form method="POST" action="{{ route('courses.reviews.destroy', [$course, $userReview]) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa đánh giá này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cursor-pointer rounded-lg border border-rose-200 px-3 py-2 text-sm font-bold text-rose-700 transition-colors hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10">Xóa đánh giá</button>
                        </form>
                    @endif
                </div>

                @if($userReview->moderation_note && $userReview->status !== \App\Enums\ReviewStatus::Approved)
                    <p class="mt-3 rounded-lg bg-white px-3 py-2 text-sm text-slate-700 dark:bg-slate-900 dark:text-slate-200"><strong>Lý do kiểm duyệt:</strong> {{ $userReview->moderation_note }}</p>
                @endif

                @if($canUpdateReview)
                    <form method="POST" action="{{ route('courses.reviews.update', [$course, $userReview]) }}" class="mt-4 space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf
                        @method('PUT')
                        @include('courses.partials.review-form-fields', ['review' => $userReview, 'submitLabel' => 'Cập nhật đánh giá'])
                    </form>
                @else
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">Bạn không thể sửa đánh giá khi không còn quyền truy cập khóa học.</p>
                @endif
            </section>
        @elseif($canReview)
            <section class="mt-7 rounded-2xl border border-indigo-200 bg-indigo-50/60 p-5 dark:border-indigo-500/30 dark:bg-indigo-500/5">
                <h3 class="font-extrabold text-slate-950 dark:text-white">Chia sẻ trải nghiệm của bạn</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Đánh giá hữu ích giúp học viên khác lựa chọn khóa học phù hợp.</p>
                <form method="POST" action="{{ route('courses.reviews.store', $course) }}" class="mt-4 space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    @include('courses.partials.review-form-fields', ['review' => null, 'submitLabel' => 'Gửi đánh giá'])
                </form>
            </section>
        @elseif($isEnrolled && auth()->user()->isStudent())
            <div class="mt-7 rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-600 dark:border-slate-700 dark:text-slate-300">
                Hãy bắt đầu ít nhất một bài học trước khi gửi đánh giá khóa học.
            </div>
        @endif
    @endauth

    <div class="mt-8 flex flex-col gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-extrabold text-slate-950 dark:text-white">Nhận xét đã duyệt</h3>
        <form method="GET" action="{{ route('courses.show', $course->slug) }}" class="flex flex-wrap gap-2">
            @if($reviewRating)<input type="hidden" name="review_rating" value="{{ $reviewRating }}">@endif
            <label for="review-sort" class="sr-only">Sắp xếp đánh giá</label>
            <select id="review-sort" name="review_sort" class="cursor-pointer rounded-lg border-slate-300 bg-white text-sm font-semibold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                <option value="latest" @selected($reviewSort === 'latest')>Mới nhất</option>
                <option value="helpful" @selected($reviewSort === 'helpful')>Hữu ích nhất</option>
            </select>
            <button type="submit" class="cursor-pointer rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-500">Áp dụng</button>
            @if($reviewRating)
                <a href="{{ route('courses.show', $course->slug).'#reviews' }}" class="rounded-lg px-3 py-2 text-sm font-bold text-indigo-700 hover:bg-indigo-50 dark:text-indigo-300 dark:hover:bg-indigo-500/10">Bỏ lọc</a>
            @endif
        </form>
    </div>

    <div class="mt-5 space-y-4">
        @forelse($reviews as $review)
            <section class="rounded-2xl border border-slate-200 p-5 dark:border-slate-800">
                <div class="flex items-start gap-3">
                    @if($review->user?->avatar)
                        <img src="{{ $review->user->avatarUrl() }}" alt="Ảnh đại diện của {{ $review->user->name }}" class="h-11 w-11 rounded-full object-cover">
                    @else
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-extrabold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">{{ mb_strtoupper(mb_substr($review->user?->name ?? 'H', 0, 1)) }}</div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-extrabold text-slate-950 dark:text-white">{{ $review->user?->name ?? 'Học viên' }}</h4>
                            @if($review->verified_purchase)
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30">Học viên đã đăng ký</span>
                            @endif
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-2">
                            <span class="flex gap-0.5" aria-label="{{ $review->rating }} trên 5 sao">
                                @for($star = 1; $star <= 5; $star++)
                                    <svg class="h-4 w-4 {{ $star <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                                @endfor
                            </span>
                            <time class="text-xs text-slate-500 dark:text-slate-400" datetime="{{ $review->created_at->toIso8601String() }}">{{ $review->created_at->diffForHumans() }}</time>
                        </div>
                    </div>
                </div>

                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $review->comment }}</p>

                @if($review->instructor_reply)
                    <div class="mt-4 rounded-xl border-l-4 border-indigo-500 bg-indigo-50 p-4 dark:bg-indigo-500/10">
                        <div class="flex flex-wrap items-center gap-2">
                            <strong class="text-sm text-slate-950 dark:text-white">Phản hồi từ giảng viên</strong>
                            @if($review->replied_at)<time class="text-xs text-slate-500 dark:text-slate-400">{{ $review->replied_at->diffForHumans() }}</time>@endif
                        </div>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">{{ $review->instructor_reply }}</p>
                    </div>
                @endif

                <div class="mt-4 flex items-center gap-3">
                    @auth
                        @if((int) auth()->id() !== (int) $review->user_id)
                            <form method="POST" action="{{ route('reviews.helpful.toggle', $review) }}">
                                @csrf
                                <button type="submit" class="cursor-pointer rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold transition-colors hover:border-indigo-300 hover:bg-indigo-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:border-slate-700 dark:hover:border-indigo-500 dark:hover:bg-indigo-500/10 {{ in_array($review->id, $helpfulReviewIds, true) ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-300' }}">
                                    {{ in_array($review->id, $helpfulReviewIds, true) ? 'Đã thấy hữu ích' : 'Hữu ích' }} · {{ $review->helpful_count }}
                                </button>
                            </form>
                        @else
                            <span class="text-xs font-semibold text-slate-500">{{ $review->helpful_count }} người thấy hữu ích</span>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-700 hover:underline dark:text-indigo-300">Đăng nhập để đánh dấu hữu ích · {{ $review->helpful_count }}</a>
                    @endauth
                </div>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-10 text-center dark:border-slate-700">
                <svg class="mx-auto h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16l-4 3v-4a8 8 0 1114.9-4"/></svg>
                <p class="mt-3 font-bold text-slate-700 dark:text-slate-200">Chưa có đánh giá phù hợp</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Hãy là người đầu tiên chia sẻ trải nghiệm sau khi bắt đầu học.</p>
            </div>
        @endforelse
    </div>

    @if($reviews->hasPages())
        <div class="mt-6">{{ $reviews->links() }}</div>
    @endif
</article>
