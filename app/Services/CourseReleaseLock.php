<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CourseReleaseLock
{
    /** Keep the existing finance prefix: instructors -> courses -> enrollments. */
    public function courses(array $ids): void
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('Course release locks require a transaction.');
        }
        $instructors = Course::whereIn('id', $ids)->pluck('instructor_id')->unique();
        User::whereIn('id', $instructors)->orderBy('id')->lockForUpdate()->get();
        Course::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
    }

    public function course(int $id): Course
    {
        $this->courses([$id]);

        // A normal SELECT could read an earlier REPEATABLE READ snapshot after
        // waiting for publication. Return the row through a current locking read.
        return Course::query()->lockForUpdate()->findOrFail($id);
    }
}
