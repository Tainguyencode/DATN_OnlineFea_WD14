<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

echo "\n===== DATABASE STATISTICS =====\n";
echo 'Users: '.User::count()."\n";
echo 'Courses: '.Course::count()."\n";
echo 'Lessons: '.Lesson::count()."\n";
echo 'Enrollments: '.Enrollment::count()."\n";
echo 'Lesson Progress: '.LessonProgress::count()."\n";
echo "============================\n\n";
