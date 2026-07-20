<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class CourseRecommendationService
{
    private const MAX_TAG_SCORE = 30;

    public function getRelatedCourses(Course $course, int $limit = 4, ?User $viewer = null): EloquentCollection
    {
        if ($limit < 1) {
            return new EloquentCollection;
        }

        $sourceTags = $this->normalizeTags($course->tags);
        $poolLimit = $this->candidatePoolLimit($limit);
        $candidates = $this->primaryCandidates($course, $sourceTags, $viewer, $poolLimit);

        if ($candidates->unique('id')->count() < $limit) {
            $candidates = $this->appendFallbackCandidates($course, $candidates, $viewer, $limit, $poolLimit);
        }

        $ranked = $candidates
            ->unique('id')
            ->map(function (Course $candidate) use ($course, $sourceTags) {
                $candidate->setAttribute('related_score', $this->scoreCourse($course, $candidate, $sourceTags));

                return $candidate;
            })
            ->sort(function (Course $first, Course $second) {
                return $this->compareRank($first, $second);
            })
            ->take($limit)
            ->values();

        return new EloquentCollection($ranked->all());
    }

    private function primaryCandidates(Course $course, array $sourceTags, ?User $viewer, int $poolLimit): EloquentCollection
    {
        $query = $this->queryWithScore($course, $viewer)
            ->where(function (Builder $query) use ($course, $sourceTags) {
                if ($course->category_id) {
                    $query->orWhere('courses.category_id', $course->category_id);
                }

                if ($course->instructor_id) {
                    $query->orWhere('courses.instructor_id', $course->instructor_id);
                }

                if (filled($course->level)) {
                    $query->orWhere('courses.level', $course->level);
                }

                foreach ($sourceTags as $tag) {
                    $query->orWhereJsonContains('courses.tags', $tag);
                }

                $this->orWhereNearPrice($query, $this->effectivePrice($course));
            });

        return $this->rankQuery($query)
            ->limit($poolLimit)
            ->get();
    }

    private function appendFallbackCandidates(
        Course $course,
        EloquentCollection $candidates,
        ?User $viewer,
        int $limit,
        int $poolLimit
    ): EloquentCollection {
        $fallbacks = [];

        if ($course->category_id) {
            $fallbacks[] = fn (Builder $query) => $query->where('courses.category_id', $course->category_id);
        }

        if ($course->instructor_id) {
            $fallbacks[] = fn (Builder $query) => $query->where('courses.instructor_id', $course->instructor_id);
        }

        if (filled($course->level)) {
            $fallbacks[] = fn (Builder $query) => $query->where('courses.level', $course->level);
        }

        $fallbacks[] = fn (Builder $query) => $query->where('courses.rating_avg', '>', 0);
        $fallbacks[] = fn (Builder $query) => $query->where(function (Builder $nested) {
            $nested->where('courses.enrollment_count', '>', 0)
                ->orWhereNotNull('courses.published_at');
        });

        foreach ($fallbacks as $applyFallback) {
            if ($candidates->unique('id')->count() >= $limit) {
                break;
            }

            $excludeIds = $candidates->pluck('id')->push($course->id)->unique()->values();

            $query = $this->queryWithScore($course, $viewer)
                ->whereNotIn('courses.id', $excludeIds);

            $applyFallback($query);

            $fallbackCandidates = $this->rankQuery($query)
                ->limit(min($poolLimit, max($limit - $candidates->unique('id')->count(), 1) * 6))
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

    private function queryWithScore(Course $course, ?User $viewer): Builder
    {
        [$scoreSql, $bindings] = $this->sqlScoreExpression($course);

        $query = Course::query()
            ->select([
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
            ])
            ->selectRaw($scoreSql.' as related_score', $bindings)
            ->where('courses.status', Course::STATUS_PUBLISHED)
            ->where('courses.is_published', true)
            ->whereKeyNot($course->getKey())
            ->with([
                'instructor:id,name,avatar',
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
            ->orderByDesc('related_score')
            ->orderByDesc('courses.rating_avg')
            ->orderByDesc('courses.enrollment_count')
            ->orderByDesc('courses.published_at')
            ->orderByDesc('courses.id');
    }

    private function sqlScoreExpression(Course $course): array
    {
        $parts = [];
        $bindings = [];
        $priceExpression = $this->effectivePriceExpression();
        $sourcePrice = $this->effectivePrice($course);

        if ($course->category_id) {
            $parts[] = 'CASE WHEN courses.category_id = ? THEN 40 ELSE 0 END';
            $bindings[] = $course->category_id;
        }

        if ($course->instructor_id) {
            $parts[] = 'CASE WHEN courses.instructor_id = ? THEN 15 ELSE 0 END';
            $bindings[] = $course->instructor_id;
        }

        if (filled($course->level)) {
            $parts[] = 'CASE WHEN courses.level = ? THEN 10 ELSE 0 END';
            $bindings[] = $course->level;
        }

        if ($sourcePrice <= 0) {
            $parts[] = "CASE WHEN {$priceExpression} <= 0 THEN 10 WHEN {$priceExpression} <= 200000 THEN 5 ELSE 0 END";
        } else {
            $parts[] = "CASE
                WHEN ABS({$priceExpression} - ?) <= ? THEN 10
                WHEN ABS({$priceExpression} - ?) <= ? THEN 7
                WHEN ABS({$priceExpression} - ?) <= ? THEN 5
                ELSE 0
            END";
            array_push(
                $bindings,
                $sourcePrice,
                $sourcePrice * 0.10,
                $sourcePrice,
                $sourcePrice * 0.25,
                $sourcePrice,
                $sourcePrice * 0.50,
            );
        }

        $parts[] = '(COALESCE(courses.rating_avg, 0) * 2)';
        $parts[] = $this->enrollmentScoreSql();
        $parts[] = $this->freshnessScoreSql();
        array_push(
            $bindings,
            now()->subDays(30)->toDateTimeString(),
            now()->subDays(90)->toDateTimeString(),
            now()->subDays(180)->toDateTimeString(),
        );

        return [implode(' + ', $parts), $bindings];
    }

    private function scoreCourse(Course $source, Course $candidate, array $sourceTags): float
    {
        $score = 0.0;

        if ($source->category_id && (int) $candidate->category_id === (int) $source->category_id) {
            $score += 40;
        }

        $tagMatches = count(array_intersect($sourceTags, $this->normalizeTags($candidate->tags)));
        $score += min(self::MAX_TAG_SCORE, $tagMatches * 10);

        if ($source->instructor_id && (int) $candidate->instructor_id === (int) $source->instructor_id) {
            $score += 15;
        }

        if (filled($source->level) && $candidate->level === $source->level) {
            $score += 10;
        }

        $score += $this->priceScore($this->effectivePrice($source), $this->effectivePrice($candidate));
        $score += max(0, min(10, (float) $candidate->rating_avg * 2));
        $score += $this->enrollmentScore((int) $candidate->enrollment_count);
        $score += $this->freshnessScore($candidate);

        return round($score, 2);
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

    private function enrollmentScoreSql(): string
    {
        return 'CASE
            WHEN courses.enrollment_count >= 1000 THEN 10
            WHEN courses.enrollment_count >= 500 THEN 8
            WHEN courses.enrollment_count >= 100 THEN 6
            WHEN courses.enrollment_count >= 25 THEN 4
            WHEN courses.enrollment_count > 0 THEN 2
            ELSE 0
        END';
    }

    private function freshnessScoreSql(): string
    {
        return 'CASE
            WHEN courses.published_at >= ? THEN 5
            WHEN courses.published_at >= ? THEN 3
            WHEN courses.published_at >= ? THEN 1
            ELSE 0
        END';
    }

    private function effectivePriceExpression(): string
    {
        return 'COALESCE(courses.discount_price, courses.sale_price, courses.price, 0)';
    }

    private function effectivePrice(Course $course): float
    {
        return (float) ($course->discount_price ?? $course->sale_price ?? $course->price ?? 0);
    }

    private function candidatePoolLimit(int $limit): int
    {
        return min(max($limit * 12, 24), 80);
    }
}
