<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\RecentlyViewedCourse;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourseRecommendationService
{
    private const CACHE_TTL_MINUTES = 30;

    private const MAX_CURRENT_TAG_SCORE = 30;

    private const MAX_PROFILE_TAG_SCORE = 45;

    public function getRelatedCourses(Course $course, int $limit = 4, ?User $viewer = null): EloquentCollection
    {
        return $this->getPersonalizedRecommendations($course, $viewer, $limit);
    }

    public function getPersonalizedRecommendations(Course $currentCourse, ?User $user = null, int $limit = 4): EloquentCollection
    {
        if ($limit < 1) {
            return new EloquentCollection;
        }

        $viewer = $user?->isStudent() ? $user : null;
        $cacheKey = $this->cacheKey($currentCourse, $viewer, $limit);

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($currentCourse, $viewer, $limit) {
            $profile = $this->buildUserPreferenceProfile($viewer);
            $sourceTags = $this->normalizeTags($currentCourse->tags);
            $excludeIds = $this->excludedCourseIds($currentCourse, $profile);
            $poolLimit = $this->candidatePoolLimit($limit);

            $candidates = $this->getHybridCandidates($currentCourse, $profile, $sourceTags, $viewer, $excludeIds, $poolLimit);

            if ($candidates->unique('id')->count() < $limit) {
                $candidates = $this->appendFallbackCandidates($currentCourse, $profile, $candidates, $viewer, $excludeIds, $limit, $poolLimit);
            }

            $ranked = $candidates
                ->unique('id')
                ->map(function (Course $candidate) use ($currentCourse, $profile, $sourceTags) {
                    $score = $this->calculateRecommendationScore($currentCourse, $candidate, $profile, $sourceTags);
                    $candidate->setAttribute('related_score', $score['score']);
                    $candidate->setAttribute('recommendation_reason', $score['reason']);
                    $candidate->setAttribute('recommendation_type', $score['type']);

                    return $candidate;
                })
                ->sort(fn (Course $first, Course $second) => $this->compareRank($first, $second))
                ->take($limit)
                ->values();

            return new EloquentCollection($ranked->all());
        });
    }

    private function buildUserPreferenceProfile(?User $user): array
    {
        $profile = $this->emptyProfile();

        if (! $user) {
            return $profile;
        }

        $this->addRecentViewSignals($profile, $user);
        $this->addEnrollmentSignals($profile, $user);
        $this->addPaidOrderSignals($profile, $user);
        $this->addWishlistSignals($profile, $user);
        $this->addReviewSignals($profile, $user);
        $this->addCollaborativeSignals($profile, $user);

        $profile['has_personal_data'] = $profile['recent_course_ids'] !== []
            || $profile['owned_course_ids'] !== []
            || $profile['wishlist_course_ids'] !== []
            || $profile['reviewed_course_ids'] !== [];

        return $profile;
    }

    private function emptyProfile(): array
    {
        return [
            'categories' => [],
            'tags' => [],
            'instructors' => [],
            'levels' => [],
            'price_weighted_total' => 0.0,
            'price_weight_total' => 0.0,
            'owned_course_ids' => [],
            'wishlist_course_ids' => [],
            'recent_course_ids' => [],
            'reviewed_course_ids' => [],
            'owned_categories' => [],
            'owned_tags' => [],
            'owned_instructors' => [],
            'wishlist_categories' => [],
            'wishlist_tags' => [],
            'wishlist_instructors' => [],
            'recent_categories' => [],
            'recent_tags' => [],
            'recent_instructors' => [],
            'positive_categories' => [],
            'positive_tags' => [],
            'positive_instructors' => [],
            'negative_categories' => [],
            'negative_tags' => [],
            'negative_instructors' => [],
            'collaborative_scores' => [],
            'collaborative_course_ids' => [],
            'has_personal_data' => false,
        ];
    }

    private function addRecentViewSignals(array &$profile, User $user): void
    {
        $recentViews = RecentlyViewedCourse::query()
            ->forUser($user)
            ->with(['course' => fn ($query) => $query->select($this->profileCourseColumns())])
            ->recentFirst()
            ->limit(15)
            ->get();

        foreach ($recentViews as $view) {
            if (! $view->course) {
                continue;
            }

            $decay = $this->timeDecay($view->last_viewed_at ?? $view->updated_at ?? $view->created_at, 'view');
            $weight = 18 * $decay;
            $profile['recent_course_ids'][] = (int) $view->course_id;
            $this->addCourseSignals($profile, $view->course, $weight, 'recent');
        }
    }

    private function addEnrollmentSignals(array &$profile, User $user): void
    {
        $enrollments = Enrollment::query()
            ->where('user_id', $user->id)
            ->withLearningAccess()
            ->with(['course' => fn ($query) => $query->select($this->profileCourseColumns())])
            ->orderByDesc('enrolled_at')
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();

        foreach ($enrollments as $enrollment) {
            if (! $enrollment->course) {
                continue;
            }

            $decay = $this->timeDecay($enrollment->enrolled_at ?? $enrollment->updated_at ?? $enrollment->created_at, 'strong');
            $weight = 25 * $decay;
            $profile['owned_course_ids'][] = (int) $enrollment->course_id;
            $this->addCourseSignals($profile, $enrollment->course, $weight, 'owned');
        }
    }

    private function addPaidOrderSignals(array &$profile, User $user): void
    {
        $paidRows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->where('orders.status', 'paid')
            ->select('order_items.course_id', DB::raw('MAX(order_items.updated_at) as interacted_at'))
            ->groupBy('order_items.course_id')
            ->limit(40)
            ->get();

        if ($paidRows->isEmpty()) {
            return;
        }

        $paidCourseIds = $paidRows->pluck('course_id')->map(fn ($id) => (int) $id)->all();
        $courses = Course::query()
            ->whereIn('id', $paidCourseIds)
            ->get($this->profileCourseColumns())
            ->keyBy('id');

        foreach ($paidRows as $row) {
            $course = $courses->get((int) $row->course_id);
            if (! $course) {
                continue;
            }

            $decay = $this->timeDecay($row->interacted_at, 'strong');
            $profile['owned_course_ids'][] = (int) $row->course_id;
            $this->addCourseSignals($profile, $course, 22 * $decay, 'owned');
        }
    }

    private function addWishlistSignals(array &$profile, User $user): void
    {
        $wishlists = Wishlist::query()
            ->where('user_id', $user->id)
            ->with(['course' => fn ($query) => $query->select($this->profileCourseColumns())])
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();

        foreach ($wishlists as $wishlist) {
            if (! $wishlist->course) {
                continue;
            }

            $decay = $this->timeDecay($wishlist->updated_at ?? $wishlist->created_at, 'strong');
            $profile['wishlist_course_ids'][] = (int) $wishlist->course_id;
            $this->addCourseSignals($profile, $wishlist->course, 28 * $decay, 'wishlist');
        }
    }

    private function addReviewSignals(array &$profile, User $user): void
    {
        $reviews = Review::query()
            ->where('user_id', $user->id)
            ->with(['course' => fn ($query) => $query->select($this->profileCourseColumns())])
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();

        foreach ($reviews as $review) {
            if (! $review->course) {
                continue;
            }

            $profile['reviewed_course_ids'][] = (int) $review->course_id;
            $decay = $this->timeDecay($review->updated_at ?? $review->created_at, 'strong');

            if ((int) $review->rating >= 4) {
                $this->addCourseSignals($profile, $review->course, 12 * $decay, 'positive');
            } elseif ((int) $review->rating <= 2) {
                $this->addNegativeSignals($profile, $review->course, 10 * $decay);
            }
        }
    }

    private function addCollaborativeSignals(array &$profile, User $user): void
    {
        $seedCourseIds = array_values(array_unique(array_merge(
            $profile['owned_course_ids'],
            $profile['wishlist_course_ids'],
        )));

        if ($seedCourseIds === []) {
            return;
        }

        $similarities = [];

        DB::table('enrollments')
            ->select('user_id', DB::raw('COUNT(DISTINCT course_id) as overlap_count'))
            ->where('user_id', '!=', $user->id)
            ->whereIn('course_id', $seedCourseIds)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->groupBy('user_id')
            ->orderByDesc('overlap_count')
            ->limit(30)
            ->get()
            ->each(function ($row) use (&$similarities) {
                $similarities[(int) $row->user_id] = (($similarities[(int) $row->user_id] ?? 0) + ((int) $row->overlap_count * 1.0));
            });

        DB::table('wishlists')
            ->select('user_id', DB::raw('COUNT(DISTINCT course_id) as overlap_count'))
            ->where('user_id', '!=', $user->id)
            ->whereIn('course_id', $seedCourseIds)
            ->groupBy('user_id')
            ->orderByDesc('overlap_count')
            ->limit(30)
            ->get()
            ->each(function ($row) use (&$similarities) {
                $similarities[(int) $row->user_id] = (($similarities[(int) $row->user_id] ?? 0) + ((int) $row->overlap_count * 0.6));
            });

        arsort($similarities);
        $similarities = array_slice($similarities, 0, 25, true);

        if ($similarities === []) {
            return;
        }

        $similarUserIds = array_keys($similarities);
        $excludedCourseIds = array_values(array_unique(array_merge($profile['owned_course_ids'], $seedCourseIds)));
        $scores = [];

        DB::table('enrollments')
            ->select('user_id', 'course_id')
            ->whereIn('user_id', $similarUserIds)
            ->whereNotIn('course_id', $excludedCourseIds)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->limit(300)
            ->get()
            ->each(function ($row) use (&$scores, $similarities) {
                $scores[(int) $row->course_id] = ($scores[(int) $row->course_id] ?? 0) + (($similarities[(int) $row->user_id] ?? 0) * 8);
            });

        DB::table('wishlists')
            ->select('user_id', 'course_id')
            ->whereIn('user_id', $similarUserIds)
            ->whereNotIn('course_id', $excludedCourseIds)
            ->limit(300)
            ->get()
            ->each(function ($row) use (&$scores, $similarities) {
                $scores[(int) $row->course_id] = ($scores[(int) $row->course_id] ?? 0) + (($similarities[(int) $row->user_id] ?? 0) * 5);
            });

        foreach ($scores as $courseId => $score) {
            $profile['collaborative_scores'][(int) $courseId] = min(30, round($score, 2));
        }

        arsort($profile['collaborative_scores']);
        $profile['collaborative_course_ids'] = array_slice(array_keys($profile['collaborative_scores']), 0, 60);
    }

    private function addCourseSignals(array &$profile, Course $course, float $weight, string $source): void
    {
        if ($weight <= 0) {
            return;
        }

        if ($course->category_id) {
            $this->addWeight($profile['categories'], (int) $course->category_id, $weight);
            $this->addWeight($profile[$source.'_categories'], (int) $course->category_id, $weight);
        }

        if ($course->instructor_id) {
            $this->addWeight($profile['instructors'], (int) $course->instructor_id, $weight * 0.7);
            $this->addWeight($profile[$source.'_instructors'], (int) $course->instructor_id, $weight * 0.7);
        }

        if (filled($course->level)) {
            $this->addWeight($profile['levels'], (string) $course->level, $weight * 0.45);
        }

        foreach ($this->normalizeTags($course->tags) as $tag) {
            $this->addWeight($profile['tags'], $tag, $weight * 0.55);
            $this->addWeight($profile[$source.'_tags'], $tag, $weight * 0.55);
        }

        $price = $this->effectivePrice($course);
        $profile['price_weighted_total'] += $price * $weight;
        $profile['price_weight_total'] += $weight;
    }

    private function addNegativeSignals(array &$profile, Course $course, float $weight): void
    {
        if ($course->category_id) {
            $this->addWeight($profile['negative_categories'], (int) $course->category_id, $weight);
        }

        if ($course->instructor_id) {
            $this->addWeight($profile['negative_instructors'], (int) $course->instructor_id, $weight * 0.7);
        }

        foreach ($this->normalizeTags($course->tags) as $tag) {
            $this->addWeight($profile['negative_tags'], $tag, $weight * 0.55);
        }
    }

    private function getHybridCandidates(
        Course $currentCourse,
        array $profile,
        array $sourceTags,
        ?User $viewer,
        array $excludeIds,
        int $poolLimit
    ): EloquentCollection {
        $query = $this->baseCandidateQuery($viewer, $excludeIds)
            ->where(function (Builder $query) use ($currentCourse, $profile, $sourceTags) {
                $hasSignals = false;

                $hasSignals = $this->addCurrentCourseCandidateSignals($query, $currentCourse, $sourceTags) || $hasSignals;
                $hasSignals = $this->addProfileCandidateSignals($query, $profile) || $hasSignals;

                if (! $hasSignals) {
                    $query->whereRaw('1 = 1');
                }
            });

        return $this->rankQuery($query)
            ->limit($poolLimit)
            ->get();
    }

    private function appendFallbackCandidates(
        Course $currentCourse,
        array $profile,
        EloquentCollection $candidates,
        ?User $viewer,
        array $excludeIds,
        int $limit,
        int $poolLimit
    ): EloquentCollection {
        $fallbacks = [];

        if ($currentCourse->category_id) {
            $fallbacks[] = fn (Builder $query) => $query->where('courses.category_id', $currentCourse->category_id);
        }

        if (filled($currentCourse->level)) {
            $fallbacks[] = fn (Builder $query) => $query->where('courses.level', $currentCourse->level);
        }

        foreach ($this->topKeys($profile['categories'], 3) as $categoryId) {
            $fallbacks[] = fn (Builder $query) => $query->where('courses.category_id', $categoryId);
        }

        foreach ($this->topKeys($profile['instructors'], 2) as $instructorId) {
            $fallbacks[] = fn (Builder $query) => $query->where('courses.instructor_id', $instructorId);
        }

        $fallbacks[] = fn (Builder $query) => $query->where('courses.rating_avg', '>=', 4);
        $fallbacks[] = fn (Builder $query) => $query->where(function (Builder $nested) {
            $nested->where('courses.enrollment_count', '>', 0)
                ->orWhereNotNull('courses.published_at');
        });

        foreach ($fallbacks as $applyFallback) {
            if ($candidates->unique('id')->count() >= $limit) {
                break;
            }

            $candidateIds = $candidates->pluck('id')->map(fn ($id) => (int) $id)->all();
            $query = $this->baseCandidateQuery($viewer, array_values(array_unique(array_merge($excludeIds, $candidateIds))));

            $applyFallback($query);

            $fallbackCandidates = $this->rankQuery($query)
                ->limit(min($poolLimit, max($limit - $candidates->unique('id')->count(), 1) * 8))
                ->get();

            $candidates = new EloquentCollection(
                $candidates
                    ->concat($fallbackCandidates)
                    ->unique('id')
                    ->values()
                    ->all()
            );
        }

        return $candidates;
    }

    private function addCurrentCourseCandidateSignals(Builder $query, Course $currentCourse, array $sourceTags): bool
    {
        $hasSignals = false;

        if ($currentCourse->category_id) {
            $query->orWhere('courses.category_id', $currentCourse->category_id);
            $hasSignals = true;
        }

        if ($currentCourse->instructor_id) {
            $query->orWhere('courses.instructor_id', $currentCourse->instructor_id);
            $hasSignals = true;
        }

        if (filled($currentCourse->level)) {
            $query->orWhere('courses.level', $currentCourse->level);
            $hasSignals = true;
        }

        foreach ($sourceTags as $tag) {
            $this->orWhereTag($query, $tag);
            $hasSignals = true;
        }

        $this->orWhereNearPrice($query, $this->effectivePrice($currentCourse));
        $hasSignals = true;

        return $hasSignals;
    }

    private function addProfileCandidateSignals(Builder $query, array $profile): bool
    {
        $hasSignals = false;

        foreach ($this->topKeys($profile['categories'], 6) as $categoryId) {
            $query->orWhere('courses.category_id', $categoryId);
            $hasSignals = true;
        }

        foreach ($this->topKeys($profile['instructors'], 4) as $instructorId) {
            $query->orWhere('courses.instructor_id', $instructorId);
            $hasSignals = true;
        }

        foreach ($this->topKeys($profile['levels'], 3) as $level) {
            $query->orWhere('courses.level', $level);
            $hasSignals = true;
        }

        foreach ($this->topKeys($profile['tags'], 10) as $tag) {
            $this->orWhereTag($query, $tag);
            $hasSignals = true;
        }

        if ($profile['collaborative_course_ids'] !== []) {
            $query->orWhereIn('courses.id', $profile['collaborative_course_ids']);
            $hasSignals = true;
        }

        return $hasSignals;
    }

    private function baseCandidateQuery(?User $viewer, array $excludeIds): Builder
    {
        $query = Course::query()
            ->select($this->candidateCourseColumns())
            ->where('courses.status', Course::STATUS_PUBLISHED)
            ->where('courses.is_published', true)
            ->whereHas('instructor', fn (Builder $instructor) => $instructor->where('is_active', true))
            ->when($excludeIds !== [], fn (Builder $query) => $query->whereNotIn('courses.id', $excludeIds))
            ->with([
                'instructor:id,name,avatar,is_active',
                'category:id,parent_id,name,slug',
                'category.parent:id,name,slug',
            ])
            ->withCount('lessons');

        if ($viewer?->isStudent()) {
            $query->withExists([
                'wishlists as is_favorited' => fn (Builder $favoriteQuery) => $favoriteQuery->where('user_id', $viewer->id),
            ]);
        }

        return $query;
    }

    private function rankQuery(Builder $query): Builder
    {
        return $query
            ->orderByDesc('courses.rating_avg')
            ->orderByDesc('courses.enrollment_count')
            ->orderByDesc('courses.published_at')
            ->orderByDesc('courses.id');
    }

    private function calculateRecommendationScore(Course $currentCourse, Course $candidate, array $profile, array $sourceTags): array
    {
        $score = 0.0;
        $reasons = [];

        $contentScore = $this->currentCourseScore($currentCourse, $candidate, $sourceTags);
        $score += $contentScore['score'];
        $this->rememberReason($reasons, $contentScore['score'], $contentScore['reason'], 'content');

        $profileScore = $this->profileScore($candidate, $profile);
        $score += $profileScore['score'];
        $this->rememberReason($reasons, $profileScore['score'], $profileScore['reason'], 'personal');

        $actionScore = $this->actionScore($candidate, $profile);
        $score += $actionScore['score'];
        $this->rememberReason($reasons, $actionScore['score'], $actionScore['reason'], 'behavior');

        $collaborativeScore = (float) ($profile['collaborative_scores'][(int) $candidate->id] ?? 0);
        $score += $collaborativeScore;
        $this->rememberReason($reasons, $collaborativeScore, 'Học viên có sở thích giống bạn cũng học khóa này', 'collaborative');

        $qualityScore = $this->qualityScore($candidate);
        $score += $qualityScore;
        $this->rememberReason($reasons, $qualityScore, 'Khóa học nổi bật về đánh giá, lượt học hoặc độ mới', 'quality');

        $penalty = $this->negativePreferencePenalty($candidate, $profile);
        $score -= $penalty;

        $reason = collect($reasons)
            ->sortByDesc('score')
            ->first() ?? ['reason' => 'Cùng chủ đề với khóa học bạn đang xem', 'type' => 'content'];

        return [
            'score' => round(max(0, $score), 2),
            'reason' => $reason['reason'],
            'type' => $reason['type'],
        ];
    }

    private function currentCourseScore(Course $currentCourse, Course $candidate, array $sourceTags): array
    {
        $score = 0.0;
        $reasons = [];

        if ($currentCourse->category_id && (int) $candidate->category_id === (int) $currentCourse->category_id) {
            $score += 40;
            $this->rememberReason($reasons, 40, 'Cùng chủ đề với khóa học bạn đang xem', 'content');
        }

        $tagMatches = count(array_intersect($sourceTags, $this->normalizeTags($candidate->tags)));
        if ($tagMatches > 0) {
            $tagScore = min(self::MAX_CURRENT_TAG_SCORE, $tagMatches * 10);
            $score += $tagScore;
            $this->rememberReason($reasons, $tagScore, 'Có chủ đề giống khóa học bạn đang xem', 'content');
        }

        if ($currentCourse->instructor_id && (int) $candidate->instructor_id === (int) $currentCourse->instructor_id) {
            $score += 15;
            $this->rememberReason($reasons, 15, 'Cùng giảng viên với khóa học bạn đang xem', 'content');
        }

        if (filled($currentCourse->level) && $candidate->level === $currentCourse->level) {
            $score += 10;
            $this->rememberReason($reasons, 10, 'Phù hợp với trình độ của khóa học hiện tại', 'content');
        }

        $priceScore = $this->priceScore($this->effectivePrice($currentCourse), $this->effectivePrice($candidate));
        $score += $priceScore;

        return [
            'score' => $score,
            'reason' => collect($reasons)->sortByDesc('score')->first()['reason'] ?? 'Cùng chủ đề với khóa học bạn đang xem',
        ];
    }

    private function profileScore(Course $candidate, array $profile): array
    {
        $score = 0.0;
        $reasons = [];

        if ($candidate->category_id && isset($profile['categories'][(int) $candidate->category_id])) {
            $categoryScore = min(35, $profile['categories'][(int) $candidate->category_id]);
            $score += $categoryScore;
            $this->rememberReason($reasons, $categoryScore, 'Dựa trên chủ đề bạn thường quan tâm', 'personal');
        }

        $tagScore = 0.0;
        foreach ($this->normalizeTags($candidate->tags) as $tag) {
            $tagScore += min(15, (float) ($profile['tags'][$tag] ?? 0));
        }
        $tagScore = min(self::MAX_PROFILE_TAG_SCORE, $tagScore);
        if ($tagScore > 0) {
            $score += $tagScore;
            $this->rememberReason($reasons, $tagScore, 'Dựa trên các tag bạn quan tâm', 'personal');
        }

        if ($candidate->instructor_id && isset($profile['instructors'][(int) $candidate->instructor_id])) {
            $instructorScore = min(15, $profile['instructors'][(int) $candidate->instructor_id]);
            $score += $instructorScore;
            $this->rememberReason($reasons, $instructorScore, 'Cùng giảng viên bạn từng quan tâm', 'personal');
        }

        if (filled($candidate->level) && isset($profile['levels'][(string) $candidate->level])) {
            $levelScore = min(8, $profile['levels'][(string) $candidate->level]);
            $score += $levelScore;
            $this->rememberReason($reasons, $levelScore, 'Phù hợp với trình độ bạn thường học', 'personal');
        }

        $averagePrice = $this->averagePreferredPrice($profile);
        if ($averagePrice !== null) {
            $priceScore = $this->priceScore($averagePrice, $this->effectivePrice($candidate));
            $score += $priceScore;
        }

        return [
            'score' => $score,
            'reason' => collect($reasons)->sortByDesc('score')->first()['reason'] ?? 'Dựa trên sở thích học tập của bạn',
        ];
    }

    private function actionScore(Course $candidate, array $profile): array
    {
        $score = 0.0;
        $reasons = [];

        $ownedScore = $this->sourceMatchScore($candidate, $profile, 'owned', 32);
        if ($ownedScore > 0) {
            $score += $ownedScore;
            $this->rememberReason($reasons, $ownedScore, 'Tương tự các khóa bạn đã học hoặc đã mua', 'behavior');
        }

        $wishlistScore = $this->sourceMatchScore($candidate, $profile, 'wishlist', 36);
        if ($wishlistScore > 0) {
            $score += $wishlistScore;
            $this->rememberReason($reasons, $wishlistScore, 'Dựa trên khóa học trong danh sách yêu thích', 'behavior');
        }

        $recentScore = $this->sourceMatchScore($candidate, $profile, 'recent', 28);
        if ($recentScore > 0) {
            $score += $recentScore;
            $this->rememberReason($reasons, $recentScore, 'Dựa trên khóa học bạn đã xem gần đây', 'behavior');
        }

        $positiveScore = $this->sourceMatchScore($candidate, $profile, 'positive', 24);
        if ($positiveScore > 0) {
            $score += $positiveScore;
            $this->rememberReason($reasons, $positiveScore, 'Dựa trên các khóa bạn từng đánh giá tốt', 'behavior');
        }

        return [
            'score' => $score,
            'reason' => collect($reasons)->sortByDesc('score')->first()['reason'] ?? 'Dựa trên hành vi học tập của bạn',
        ];
    }

    private function sourceMatchScore(Course $candidate, array $profile, string $source, float $cap): float
    {
        $score = 0.0;

        if ($candidate->category_id) {
            $score += (float) ($profile[$source.'_categories'][(int) $candidate->category_id] ?? 0);
        }

        if ($candidate->instructor_id) {
            $score += (float) ($profile[$source.'_instructors'][(int) $candidate->instructor_id] ?? 0);
        }

        foreach ($this->normalizeTags($candidate->tags) as $tag) {
            $score += (float) ($profile[$source.'_tags'][$tag] ?? 0);
        }

        return min($cap, $score);
    }

    private function negativePreferencePenalty(Course $candidate, array $profile): float
    {
        $penalty = 0.0;

        if ($candidate->category_id) {
            $penalty += min(12, (float) ($profile['negative_categories'][(int) $candidate->category_id] ?? 0));
        }

        if ($candidate->instructor_id) {
            $penalty += min(8, (float) ($profile['negative_instructors'][(int) $candidate->instructor_id] ?? 0));
        }

        foreach ($this->normalizeTags($candidate->tags) as $tag) {
            $penalty += min(5, (float) ($profile['negative_tags'][$tag] ?? 0));
        }

        return min(20, $penalty);
    }

    private function qualityScore(Course $course): float
    {
        $score = max(0, min(10, (float) $course->rating_avg * 2));

        $score += match (true) {
            (int) $course->rating_count >= 100 => 5,
            (int) $course->rating_count >= 30 => 3,
            (int) $course->rating_count >= 5 => 1,
            default => 0,
        };

        $score += $this->enrollmentScore((int) $course->enrollment_count);
        $score += $this->freshnessScore($course);

        if ($this->hasDiscount($course)) {
            $score += 2;
        }

        return $score;
    }

    private function compareRank(Course $first, Course $second): int
    {
        return [
            (float) $second->related_score,
            (float) $second->rating_avg,
            (int) $second->enrollment_count,
            optional($second->published_at)->timestamp ?? 0,
            (int) $second->id,
        ] <=> [
            (float) $first->related_score,
            (float) $first->rating_avg,
            (int) $first->enrollment_count,
            optional($first->published_at)->timestamp ?? 0,
            (int) $first->id,
        ];
    }

    private function excludedCourseIds(Course $currentCourse, array $profile): array
    {
        return array_values(array_unique(array_map('intval', array_merge(
            [(int) $currentCourse->id],
            $profile['owned_course_ids'],
        ))));
    }

    private function cacheKey(Course $course, ?User $user, int $limit): string
    {
        $viewer = $user ? (string) $user->id : 'guest';

        return sprintf(
            'course_recommendations:%s:%d:%d:%s',
            $viewer,
            $course->id,
            $limit,
            $this->cacheFingerprint($course, $user),
        );
    }

    private function cacheFingerprint(Course $course, ?User $user): string
    {
        $parts = [
            'v2',
            $course->updated_at?->timestamp,
            Course::query()
                ->where('status', Course::STATUS_PUBLISHED)
                ->where('is_published', true)
                ->max('updated_at'),
        ];

        if ($user) {
            $parts[] = $this->aggregateFingerprint('recently_viewed_courses', $user->id, 'last_viewed_at');
            $parts[] = $this->aggregateFingerprint('wishlists', $user->id);
            $parts[] = $this->aggregateFingerprint('enrollments', $user->id);
            $parts[] = $this->aggregateFingerprint('reviews', $user->id);
            $parts[] = $this->aggregateFingerprint('orders', $user->id);
            $parts[] = $this->paidOrderItemsFingerprint($user->id);
        }

        return sha1(json_encode($parts));
    }

    private function aggregateFingerprint(string $table, int $userId, string $dateColumn = 'updated_at'): string
    {
        $row = DB::table($table)
            ->where('user_id', $userId)
            ->selectRaw("COUNT(*) as total, MAX({$dateColumn}) as latest_signal, MAX(updated_at) as latest_update")
            ->first();

        return implode(':', [
            $table,
            (int) ($row->total ?? 0),
            (string) ($row->latest_signal ?? ''),
            (string) ($row->latest_update ?? ''),
        ]);
    }

    private function paidOrderItemsFingerprint(int $userId): string
    {
        $row = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->where('orders.status', 'paid')
            ->selectRaw('COUNT(*) as total, MAX(order_items.updated_at) as latest_item, MAX(orders.updated_at) as latest_order')
            ->first();

        return implode(':', [
            'paid_order_items',
            (int) ($row->total ?? 0),
            (string) ($row->latest_item ?? ''),
            (string) ($row->latest_order ?? ''),
        ]);
    }

    private function normalizeTags(mixed $tags): array
    {
        if (is_string($tags)) {
            $decoded = json_decode($tags, true);
            $tags = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $tags);
        }

        if (! is_array($tags)) {
            return [];
        }

        return collect($tags)
            ->map(function ($tag) {
                if (is_array($tag)) {
                    $tag = $tag['slug'] ?? $tag['name'] ?? null;
                }

                return is_scalar($tag)
                    ? trim(mb_strtolower((string) $tag))
                    : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function addWeight(array &$bucket, int|string $key, float $weight): void
    {
        if ($weight <= 0) {
            return;
        }

        $bucket[$key] = round(($bucket[$key] ?? 0) + $weight, 4);
    }

    private function rememberReason(array &$reasons, float $score, string $reason, string $type): void
    {
        if ($score <= 0) {
            return;
        }

        $reasons[] = [
            'score' => $score,
            'reason' => $reason,
            'type' => $type,
        ];
    }

    private function topKeys(array $scores, int $limit): array
    {
        arsort($scores);

        return array_slice(array_keys($scores), 0, $limit);
    }

    private function timeDecay(mixed $date, string $mode = 'view'): float
    {
        if (! $date) {
            return $mode === 'view' ? 0.18 : 0.25;
        }

        $days = now()->diffInDays($date);

        if ($mode === 'view') {
            return match (true) {
                $days <= 7 => 1.0,
                $days <= 30 => 0.7,
                $days <= 90 => 0.4,
                default => 0.18,
            };
        }

        return match (true) {
            $days <= 30 => 1.0,
            $days <= 90 => 0.75,
            $days <= 180 => 0.5,
            default => 0.25,
        };
    }

    private function orWhereNearPrice(Builder $query, float $sourcePrice): void
    {
        $priceExpression = $this->effectivePriceExpression();

        if ($sourcePrice <= 0) {
            $query->orWhereRaw("{$priceExpression} <= ?", [0]);

            return;
        }

        $query->orWhereRaw(
            "{$priceExpression} BETWEEN ? AND ?",
            [max(0, $sourcePrice * 0.50), $sourcePrice * 1.50],
        );
    }

    private function orWhereTag(Builder $query, string $tag): void
    {
        $tag = trim(mb_strtolower($tag));

        if ($tag === '') {
            return;
        }

        $query->orWhereRaw('LOWER(courses.tags) LIKE ?', ['%"'.$tag.'"%']);
    }

    private function priceScore(float $sourcePrice, float $candidatePrice): int
    {
        if ($sourcePrice <= 0) {
            return $candidatePrice <= 0 ? 10 : ($candidatePrice <= 200000 ? 5 : 0);
        }

        $ratio = abs($candidatePrice - $sourcePrice) / $sourcePrice;

        return match (true) {
            $ratio <= 0.10 => 10,
            $ratio <= 0.25 => 7,
            $ratio <= 0.50 => 5,
            default => 0,
        };
    }

    private function enrollmentScore(int $enrollmentCount): int
    {
        return match (true) {
            $enrollmentCount >= 1000 => 10,
            $enrollmentCount >= 500 => 8,
            $enrollmentCount >= 100 => 6,
            $enrollmentCount >= 25 => 4,
            $enrollmentCount > 0 => 2,
            default => 0,
        };
    }

    private function freshnessScore(Course $course): int
    {
        if (! $course->published_at) {
            return 0;
        }

        return match (true) {
            $course->published_at->gte(now()->subDays(30)) => 5,
            $course->published_at->gte(now()->subDays(90)) => 3,
            $course->published_at->gte(now()->subDays(180)) => 1,
            default => 0,
        };
    }

    private function averagePreferredPrice(array $profile): ?float
    {
        if ($profile['price_weight_total'] <= 0) {
            return null;
        }

        return (float) ($profile['price_weighted_total'] / $profile['price_weight_total']);
    }

    private function effectivePriceExpression(): string
    {
        return 'COALESCE(courses.discount_price, courses.sale_price, courses.price, 0)';
    }

    private function effectivePrice(Course $course): float
    {
        return (float) ($course->discount_price ?? $course->sale_price ?? $course->price ?? 0);
    }

    private function hasDiscount(Course $course): bool
    {
        $price = (float) ($course->price ?? 0);

        return $price > 0 && $this->effectivePrice($course) > 0 && $this->effectivePrice($course) < $price;
    }

    private function candidatePoolLimit(int $limit): int
    {
        return min(max($limit * 24, 80), 160);
    }

    private function profileCourseColumns(): array
    {
        return [
            'id',
            'instructor_id',
            'category_id',
            'level',
            'price',
            'discount_price',
            'sale_price',
            'tags',
        ];
    }

    private function candidateCourseColumns(): array
    {
        return [
            'courses.id',
            'courses.instructor_id',
            'courses.category_id',
            'courses.title',
            'courses.slug',
            'courses.short_description',
            'courses.description',
            'courses.thumbnail',
            'courses.level',
            'courses.price',
            'courses.discount_price',
            'courses.sale_price',
            'courses.rating_avg',
            'courses.rating_count',
            'courses.enrollment_count',
            'courses.tags',
            'courses.is_featured',
            'courses.status',
            'courses.is_published',
            'courses.published_at',
            'courses.created_at',
            'courses.updated_at',
        ];
    }
}
