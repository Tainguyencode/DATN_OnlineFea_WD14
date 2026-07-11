<?php
// File tạm thời để kiểm tra dữ liệu

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;

echo "\n========== DỮ LIỆU HIỆN TẠI ==========\n";
echo "✓ Users: " . User::count() . "\n";
echo "✓ Courses: " . Course::count() . "\n";
echo "✓ Chapters: " . Chapter::count() . "\n";
echo "✓ Lessons: " . Lesson::count() . "\n";
echo "✓ Enrollments: " . Enrollment::count() . "\n";
echo "✓ Lesson Progress: " . LessonProgress::count() . "\n";
echo "\n";

// Thống kê chi tiết
echo "========== CHI TIẾT ==========\n";
echo "Courses:\n";
Course::select('id', 'title')->limit(5)->get()->each(function($c) {
    echo "  - {$c->title}\n";
});

echo "\nStudents:\n";
User::where('id', '>', 3)->select('id', 'name', 'email')->limit(5)->get()->each(function($u) {
    echo "  - {$u->name}\n";
});

echo "\n";
