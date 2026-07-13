<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpandedSampleDataSeeder extends Seeder
{
    /**
     * Seed the application's database with expanded sample data.
     */
    public function run(): void
    {
        echo "\n========== THÊM DỮ LIỆU MẪU BỔ SUNG ==========\n";

        // ========================
        // 1. THÊM USERS (30 người)
        // ========================
        echo "\n▶ Tạo 30 users...\n";

        $users = [];

        // Admin & Instructors (already exist, but add more)
        $instructors = [
            ['name' => 'Nguyễn Văn A', 'email' => 'instructor.a@edu.com', 'role' => 'instructor'],
            ['name' => 'Trần Thị B', 'email' => 'instructor.b@edu.com', 'role' => 'instructor'],
            ['name' => 'Lê Văn C', 'email' => 'instructor.c@edu.com', 'role' => 'instructor'],
            ['name' => 'Phạm Thị D', 'email' => 'instructor.d@edu.com', 'role' => 'instructor'],
            ['name' => 'Hoàng Văn E', 'email' => 'instructor.e@edu.com', 'role' => 'instructor'],
        ];

        foreach ($instructors as $instructor) {
            $user = User::firstOrCreate(
                ['email' => $instructor['email']],
                [
                    'name' => $instructor['name'],
                    'password' => bcrypt('password123'),
                ]
            );
            $users[] = $user;
        }

        echo "   ✓ Tạo 5 instructors\n";

        // Students (20 sinh viên)
        $firstNames = ['Hoàng', 'Trần', 'Nguyễn', 'Phạm', 'Lê', 'Vũ', 'Bùi', 'Đặng', 'Dương', 'Võ'];
        $lastNames = ['Minh', 'Hùng', 'Khoa', 'Linh', 'Tùng', 'Hân', 'Yến', 'An', 'Bắc', 'Đông', 'Sơn', 'Hải', 'Thành', 'Quang', 'Tuấn', 'Nhân', 'Khánh', 'Tân', 'Dũng', 'Thắng'];

        $studentCount = 0;
        for ($i = 0; $i < 20; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[$i % count($lastNames)];
            $email = strtolower(str_replace(' ', '.', "$firstName.$lastName").'.student'.($i + 1).'@edu.com');

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "$firstName $lastName",
                    'password' => bcrypt('password123'),
                ]
            );
            $users[] = $user;
            $studentCount++;
        }

        echo "   ✓ Tạo 20 sinh viên\n";

        // ========================
        // 2. THÊM COURSES (12 khóa học)
        // ========================
        echo "\n▶ Tạo 12 courses...\n";

        $coursesData = [
            ['title' => 'Laravel từ Zero đến Hero', 'description' => 'Tìm hiểu Laravel từ cơ bản đến nâng cao', 'instructor_id' => $users[0]->id, 'price' => 500000],
            ['title' => 'React.js Masterclass', 'description' => 'Thành thạo React.js', 'instructor_id' => $users[1]->id, 'price' => 450000],
            ['title' => 'UI/UX Design Fundamentals', 'description' => 'Học design cơ bản', 'instructor_id' => $users[2]->id, 'price' => 350000],
            ['title' => 'Python cho Data Science', 'description' => 'Python cho khoa học dữ liệu', 'instructor_id' => $users[3]->id, 'price' => 600000],
            ['title' => 'Vue.js - Progressive Framework', 'description' => 'Lập trình Vue.js', 'instructor_id' => $users[4]->id, 'price' => 400000],
            ['title' => 'Node.js & Express', 'description' => 'Backend với Node.js', 'instructor_id' => $users[0]->id, 'price' => 550000],
            ['title' => 'Flutter - Cross-Platform', 'description' => 'Lập trình mobile với Flutter', 'instructor_id' => $users[1]->id, 'price' => 500000],
            ['title' => 'MySQL & Database Design', 'description' => 'Thiết kế cơ sở dữ liệu', 'instructor_id' => $users[2]->id, 'price' => 350000],
            ['title' => 'Docker & Kubernetes', 'description' => 'Container orchestration', 'instructor_id' => $users[3]->id, 'price' => 650000],
            ['title' => 'AWS Cloud Services', 'description' => 'Lên cloud với AWS', 'instructor_id' => $users[4]->id, 'price' => 700000],
            ['title' => 'GraphQL API Development', 'description' => 'Xây dựng API GraphQL', 'instructor_id' => $users[0]->id, 'price' => 480000],
            ['title' => 'TypeScript Advanced', 'description' => 'TypeScript nâng cao', 'instructor_id' => $users[1]->id, 'price' => 420000],
        ];

        $courses = [];
        foreach ($coursesData as $courseData) {
            $course = Course::firstOrCreate(
                ['title' => $courseData['title']],
                [
                    ...$courseData,
                    'slug' => \Illuminate\Support\Str::slug($courseData['title']),
                ]
            );
            $courses[] = $course;
        }

        echo "   ✓ Tạo 12 courses\n";

        // ========================
        // 3. THÊM CHAPTERS (48 chương)
        // ========================
        echo "\n▶ Tạo chapters...\n";

        foreach ($courses as $course) {
            $chaptersPerCourse = rand(3, 5);
            for ($i = 1; $i <= $chaptersPerCourse; $i++) {
                Chapter::firstOrCreate(
                    ['course_id' => $course->id, 'title' => "Chapter $i: ".ucfirst(\Str::random(10))],
                    [
                        'sort_order' => $i,
                    ]
                );
            }
        }

        echo "   ✓ Tạo chapters\n";

        // ========================
        // 4. THÊM LESSONS (150+ bài học)
        // ========================
        echo "\n▶ Tạo lessons...\n";

        $lessonCount = 0;
        $chapters = Chapter::all();

        foreach ($chapters as $chapter) {
            $lessonsPerChapter = rand(4, 8);
            for ($i = 1; $i <= $lessonsPerChapter; $i++) {
                $lesson = Lesson::firstOrCreate(
                    [
                        'chapter_id' => $chapter->id,
                        'title' => "Lesson $i: ".ucfirst(\Str::random(15)),
                    ],
                    [
                        'content' => "Bài học $i trong chương {$chapter->title}",
                        'type' => $this->randomLessonType(),
                        'duration_seconds' => rand(600, 3600),
                        'sort_order' => $i,
                    ]
                );
                $lessonCount++;
            }
        }

        echo "   ✓ Tạo $lessonCount lessons\n";

        // ========================
        // 5. THÊM ENROLLMENTS (150+)
        // ========================
        echo "\n▶ Tạo enrollments...\n";

        $enrollmentCount = 0;
        $students = collect($users)->slice(5); // Skip admin & instructors

        foreach ($courses as $course) {
            $enrollmentsPerCourse = rand(4, 12);
            $randomStudents = $students->random(min($enrollmentsPerCourse, count($students)));

            foreach ($randomStudents as $student) {
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'enrolled_at' => now()->subDays(rand(1, 90)),
                    ]
                );
                $enrollmentCount++;
            }
        }

        echo "   ✓ Tạo $enrollmentCount enrollments\n";

        // ========================
        // 6. THÊM LESSON PROGRESS (300+)
        // ========================
        echo "\n▶ Tạo lesson progress records...\n";

        $progressCount = 0;
        $lessons = Lesson::all();

        foreach ($lessons as $lesson) {
            $course = $lesson->chapter->course;
            $enrolledStudents = Enrollment::where('course_id', $course->id)->pluck('user_id');

            foreach ($enrolledStudents as $userId) {
                $isCompleted = rand(1, 100) <= 60; // 60% chance of completion

                $progress = LessonProgress::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'lesson_id' => $lesson->id,
                    ],
                    [
                        'watched_seconds' => $isCompleted ? $lesson->duration_seconds : rand(0, $lesson->duration_seconds),
                        'is_completed' => $isCompleted,
                        'completed_at' => $isCompleted ? now()->subDays(rand(0, 60)) : null,
                    ]
                );
                $progressCount++;
            }
        }

        echo "   ✓ Tạo $progressCount lesson progress records\n";

        // ========================
        // 7. SUMMARY
        // ========================
        echo "\n========== HOÀN THÀNH ==========\n";
        echo '✓ Users: '.User::count()."\n";
        echo '✓ Courses: '.Course::count()."\n";
        echo '✓ Chapters: '.Chapter::count()."\n";
        echo '✓ Lessons: '.Lesson::count()."\n";
        echo '✓ Enrollments: '.Enrollment::count()."\n";
        echo '✓ Lesson Progress: '.LessonProgress::count()."\n";
        echo "\n✨ Dữ liệu mẫu bổ sung đã được tạo thành công!\n\n";
    }

    /**
     * Random lesson type
     */
    private function randomLessonType(): string
    {
        return collect(['video', 'document', 'quiz', 'assignment'])->random();
    }
}
