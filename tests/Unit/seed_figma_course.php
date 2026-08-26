<?php

use App\Models\Assignment;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$course = Course::where('title', 'like', '%Figma Prototype%')
    ->orWhere('id', 3)
    ->first();

if (! $course) {
    echo "Course 'Figma Prototype' not found in database!\n";
    exit(1);
}

echo 'Found Figma Course: ID '.$course->id.' - '.$course->title."\n";

// Ensure we have a section
$section = CourseSection::where('course_id', $course->id)->first();
if (! $section) {
    $section = CourseSection::create([
        'course_id' => $course->id,
        'title' => 'Chương trình học Figma',
        'description' => 'Các bài học thiết kế prototype trên Figma',
        'sort_order' => 1,
    ]);
}

// Ensure we have a chapter
$chapter = Chapter::where('course_id', $course->id)->first();
if (! $chapter) {
    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => 'Chương 1: Figma Prototype Cơ bản',
        'sort_order' => 1,
    ]);
}

// Clean up existing chapters, sections, and lessons for this course to make a fresh state
Lesson::where('course_id', $course->id)->delete();
Chapter::where('course_id', $course->id)->delete();
CourseSection::where('course_id', $course->id)->delete();

$chaptersData = [
    [
        'number' => 1,
        'title' => 'Chương 1: Figma Prototype Cơ bản',
        'lessons' => [
            ['title' => 'Bài 1: Giới thiệu giao diện Prototype Figma', 'type' => 'video', 'duration' => 600],
            ['title' => 'Bài 2: Các khái niệm cơ bản về Trigger & Action', 'type' => 'video', 'duration' => 600],
            ['title' => 'Bài 3: Bài trắc nghiệm ôn tập Figma phần 1', 'type' => 'quiz'],
            ['title' => 'Bài 4: Bài trắc nghiệm ôn tập Figma phần 2', 'type' => 'quiz'],
            ['title' => 'Bài 5: Bài tập thực hành thiết kế Prototype hoàn chỉnh', 'type' => 'assignment'],
        ],
    ],
    [
        'number' => 2,
        'title' => 'Chương 2: Figma Prototype Nâng cao',
        'lessons' => [
            ['title' => 'Bài 6: Thiết kế Smart Animate nâng cao', 'type' => 'video', 'duration' => 720],
            ['title' => 'Bài 7: Xây dựng Component tương tác & Variant', 'type' => 'video', 'duration' => 720],
            ['title' => 'Bài 8: Trắc nghiệm nâng cao Component Variant', 'type' => 'quiz'],
            ['title' => 'Bài 9: Trắc nghiệm nâng cao Variables & Expressions', 'type' => 'quiz'],
            ['title' => 'Bài 10: Bài tập thực hành Responsive Prototype liên kết API', 'type' => 'assignment'],
        ],
    ],
];

$globalSortOrder = 1;

foreach ($chaptersData as $cData) {
    $chapterNum = $cData['number'];
    echo "Creating Chapter {$chapterNum}: {$cData['title']}...\n";

    // Create Chapter
    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => $cData['title'],
        'sort_order' => $chapterNum,
    ]);

    // Create CourseSection
    $section = CourseSection::create([
        'course_id' => $course->id,
        'title' => $cData['title'],
        'description' => 'Các bài học của '.$cData['title'],
        'sort_order' => $chapterNum,
    ]);

    foreach ($cData['lessons'] as $lData) {
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'chapter_id' => $chapter->id,
            'title' => $lData['title'],
            'type' => $lData['type'],
            'content' => 'Nội dung chi tiết của bài giảng: '.$lData['title'],
            'duration_seconds' => $lData['duration'] ?? 0,
            'duration' => $lData['duration'] ?? 0,
            'video_url' => $lData['type'] === 'video' ? 'https://www.w3schools.com/html/mov_bbb.mp4' : null,
            'is_preview' => ($chapterNum === 1 && $globalSortOrder === 1), // Preview bài đầu tiên chương 1
            'sort_order' => $globalSortOrder,
        ]);

        if ($lData['type'] === 'quiz') {
            $quiz = Quiz::create([
                'lesson_id' => $lesson->id,
                'title' => 'Trắc nghiệm: '.$lData['title'],
                'description' => 'Đánh giá mức độ nắm vững bài học trắc nghiệm',
                'passing_score' => 80,
                'time_limit' => 10,
            ]);

            for ($q = 1; $q <= 3; $q++) {
                $question = $quiz->questions()->create([
                    'question' => "Câu hỏi trắc nghiệm số $q cho bài: ".$lData['title'].'?',
                    'type' => 'single',
                    'points' => 10,
                ]);
                $question->options()->create([
                    'option_text' => 'Lựa chọn A (Đáp án đúng)',
                    'is_correct' => true,
                ]);
                $question->options()->create([
                    'option_text' => 'Lựa chọn B',
                    'is_correct' => false,
                ]);
            }
            echo " - Created quiz: {$lesson->title}\n";
        } elseif ($lData['type'] === 'assignment') {
            Assignment::create([
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
                'title' => $lData['title'],
                'description' => 'Yêu cầu thực hành chi tiết cho bài: '.$lData['title'],
                'instructions' => 'Vui lòng hoàn thành yêu cầu và gửi bài nộp tại đây.',
                'max_score' => 100,
            ]);
            echo " - Created assignment: {$lesson->title}\n";
        } else {
            echo " - Created video: {$lesson->title} ({$lesson->duration_seconds}s)\n";
        }

        $globalSortOrder++;
    }
}

// Update course status to pending_review
$course->update([
    'status' => 'pending_review',
]);

echo "Figma Prototype course has been updated with Chapter 1 and Chapter 2 (total 10 lessons) and set to pending_review.\n";
echo "Run successfully!\n";
