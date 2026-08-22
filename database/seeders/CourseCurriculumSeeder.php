<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\Assignment;
use Illuminate\Support\Str;

class CourseCurriculumSeeder extends Seeder
{
    public function run()
    {
        // 1. Find the target course
        $course = Course::where('title', 'TypeScript thực chiến từ cơ bản đến nâng cao')->first();
        if (!$course) {
            $this->command->error("Course 'TypeScript thực chiến từ cơ bản đến nâng cao' not found. Please run the course creator script first.");
            return;
        }

        $this->command->info("Seeding curriculum for course: {$course->title} (ID: {$course->id})");

        // ==========================================
        // CHƯƠNG 1: TypeScript cơ bản
        // ==========================================
        $chapter1 = Chapter::updateOrCreate(
            [
                'course_id' => $course->id,
                'title' => 'CHƯƠNG 1: TypeScript cơ bản'
            ],
            [
                'sort_order' => 1
            ]
        );

        // Lesson 1.1: Video
        $lesson1_1 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter1->id,
                'title' => 'Bài 1: TypeScript là gì? Cài đặt và thiết lập môi trường'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder1',
                'content' => 'Giới thiệu về TypeScript, lịch sử phát triển và lý do tại sao nên dùng TypeScript. Hướng dẫn cài đặt NodeJS, TypeScript compiler và cấu hình Visual Studio Code.',
                'duration' => 900,
                'duration_seconds' => 900,
                'is_preview' => true,
                'is_required' => true,
                'sort_order' => 1,
                'status' => 'published'
            ]
        );

        // Lesson 1.2: Video
        $lesson1_2 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter1->id,
                'title' => 'Bài 2: Kiểu dữ liệu cơ bản trong TypeScript'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder2',
                'content' => 'Tìm hiểu chi tiết các kiểu dữ liệu cơ bản trong TypeScript: string, number, boolean, array, tuple, any, void, null, undefined.',
                'duration' => 1200,
                'duration_seconds' => 1200,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 2,
                'status' => 'published'
            ]
        );

        // Lesson 1.3: Video
        $lesson1_3 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter1->id,
                'title' => 'Bài 3: Interface và Type Alias'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder3',
                'content' => 'So sánh chi tiết sự khác nhau giữa Interface và Type Alias. Cách khai báo kiểu dữ liệu cho object và các thuộc tính readonly, optional.',
                'duration' => 1500,
                'duration_seconds' => 1500,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 3,
                'status' => 'published'
            ]
        );

        // Lesson 1.4: Assignment (Bài tập)
        $lesson1_4 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter1->id,
                'title' => 'Bài 4: Bài tập thực hành TypeScript cơ bản'
            ],
            [
                'type' => 'assignment',
                'content' => 'Định nghĩa các interface cho một hệ thống quản lý thư viện sách và viết các hàm tìm kiếm, mượn sách.',
                'duration' => 1800,
                'duration_seconds' => 1800,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 4,
                'status' => 'published'
            ]
        );

        Assignment::updateOrCreate(
            [
                'course_id' => $course->id,
                'lesson_id' => $lesson1_4->id,
            ],
            [
                'title' => 'Bài tập thực hành TypeScript cơ bản',
                'description' => 'Yêu cầu định nghĩa cấu trúc dữ liệu cho Book, Member, Transaction và viết các hàm xử lý mượn/trả sách.',
                'instructions' => 'Nộp bài dưới dạng file .ts hoặc link github chứa code TypeScript.',
                'max_score' => 10,
                'passing_score' => 7,
                'is_required' => true,
                'due_days' => 7
            ]
        );


        // ==========================================
        // CHƯƠNG 2: TypeScript nâng cao
        // ==========================================
        $chapter2 = Chapter::updateOrCreate(
            [
                'course_id' => $course->id,
                'title' => 'CHƯƠNG 2: TypeScript nâng cao'
            ],
            [
                'sort_order' => 2
            ]
        );

        // Lesson 2.1: Video
        $lesson2_1 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter2->id,
                'title' => 'Bài 1: Function và Generic trong TypeScript'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder4',
                'content' => 'Định nghĩa kiểu cho function. Giới thiệu khái niệm Generic, Generic Class, Generic Interface và Generic Constraints.',
                'duration' => 1500,
                'duration_seconds' => 1500,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 1,
                'status' => 'published'
            ]
        );

        // Lesson 2.2: Video
        $lesson2_2 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter2->id,
                'title' => 'Bài 2: Class và OOP với TypeScript'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder5',
                'content' => 'Xây dựng lớp (class), sử dụng access modifiers (public, private, protected, readonly), kế thừa, abstract class và implements interface.',
                'duration' => 1800,
                'duration_seconds' => 1800,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 2,
                'status' => 'published'
            ]
        );

        // Lesson 2.3: Video
        $lesson2_3 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter2->id,
                'title' => 'Bài 3: Enum, Utility Types và Type Manipulation'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder6',
                'content' => 'Sử dụng Enum hiệu quả. Tìm hiểu các Utility Types hữu ích như Partial, Omit, Pick, Readonly, Record. Khái niệm Keyof, Mapped Types.',
                'duration' => 1800,
                'duration_seconds' => 1800,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 3,
                'status' => 'published'
            ]
        );

        // Lesson 2.4: Quiz
        $lesson2_4 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter2->id,
                'title' => 'Bài 4: Quiz kiểm tra TypeScript nâng cao'
            ],
            [
                'type' => 'quiz',
                'content' => 'Bài trắc nghiệm đánh giá kiến thức TypeScript nâng cao, Generic, OOP và Utility Types.',
                'duration' => 900,
                'duration_seconds' => 900,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 4,
                'status' => 'published'
            ]
        );

        $quiz = Quiz::updateOrCreate(
            [
                'lesson_id' => $lesson2_4->id,
            ],
            [
                'title' => 'Quiz kiểm tra TypeScript nâng cao',
                'description' => 'Bài kiểm tra trắc nghiệm 5 câu hỏi về Generic, OOP, Class và Utility Types trong TypeScript.',
                'pass_score' => 80,
                'time_limit_minutes' => 15,
                'max_attempts' => 3,
                'is_active' => true
            ]
        );

        // Seed 5 Quiz Questions and Answers
        $questionsData = [
            [
                'question' => 'Generic trong TypeScript dùng để làm gì?',
                'explanation' => 'Generic cho phép viết mã linh hoạt và tái sử dụng được với nhiều kiểu dữ liệu mà vẫn duy trì tính chặt chẽ.',
                'options' => [
                    ['text' => 'Tạo ra các component có thể hoạt động với nhiều kiểu dữ liệu khác nhau mà vẫn giữ được type safety.', 'is_correct' => true],
                    ['text' => 'Tăng hiệu năng chạy của code JavaScript sau khi compile.', 'is_correct' => false],
                    ['text' => 'Ép kiểu dữ liệu về kiểu any.', 'is_correct' => false],
                    ['text' => 'Tạo ra các class kế thừa lồng nhau.', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Từ khóa nào dùng để giới hạn kiểu dữ liệu nhận vào của một Generic (Generic Constraints)?',
                'explanation' => 'extends được dùng trong generic constraint để ràng buộc kiểu dữ liệu.',
                'options' => [
                    ['text' => 'extends', 'is_correct' => true],
                    ['text' => 'implements', 'is_correct' => false],
                    ['text' => 'instanceof', 'is_correct' => false],
                    ['text' => 'typeof', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Utility Type "Omit<User, \'password\'>" có tác dụng gì?',
                'explanation' => 'Omit dùng để loại bỏ các key cụ thể khỏi interface.',
                'options' => [
                    ['text' => 'Tạo ra một kiểu dữ liệu mới từ User nhưng loại bỏ thuộc tính "password".', 'is_correct' => true],
                    ['text' => 'Tạo ra một kiểu dữ liệu mới chỉ chứa duy nhất thuộc tính "password".', 'is_correct' => false],
                    ['text' => 'Biến thuộc tính "password" thành tùy chọn (optional).', 'is_correct' => false],
                    ['text' => 'Biến thuộc tính "password" thành readonly.', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Trong Class của TypeScript, access modifier nào chỉ cho phép truy cập thuộc tính trong nội bộ class và class con kế thừa?',
                'explanation' => 'protected cho phép class con kế thừa truy cập, còn private thì hoàn toàn không.',
                'options' => [
                    ['text' => 'protected', 'is_correct' => true],
                    ['text' => 'private', 'is_correct' => false],
                    ['text' => 'public', 'is_correct' => false],
                    ['text' => 'readonly', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'TypeScript có biên dịch mã nguồn trực tiếp thành mã máy để chạy không?',
                'explanation' => 'TypeScript là một superset của JavaScript và được biên dịch (transpile) thành JavaScript thông thường để chạy trên Node.js hoặc Browser.',
                'options' => [
                    ['text' => 'Không, nó biên dịch thành JavaScript và chạy trên môi trường JavaScript runtime.', 'is_correct' => true],
                    ['text' => 'Có, nó biên dịch thành mã nhị phân .exe.', 'is_correct' => false],
                    ['text' => 'Có, nó chạy trực tiếp trên trình duyệt mà không cần compile.', 'is_correct' => false],
                    ['text' => 'Không, nó chạy thông qua Python VM.', 'is_correct' => false],
                ]
            ]
        ];

        foreach ($questionsData as $qIdx => $qData) {
            $question = QuizQuestion::updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'question' => $qData['question'],
                ],
                [
                    'type' => 'single',
                    'points' => 20,
                    'explanation' => $qData['explanation'],
                    'sort_order' => $qIdx + 1
                ]
            );

            foreach ($qData['options'] as $oIdx => $oData) {
                QuizOption::updateOrCreate(
                    [
                        'quiz_question_id' => $question->id,
                        'option_text' => $oData['text'],
                    ],
                    [
                        'is_correct' => $oData['is_correct'],
                        'sort_order' => $oIdx + 1
                    ]
                );
            }
        }


        // ==========================================
        // CHƯƠNG 3: TypeScript với React
        // ==========================================
        $chapter3 = Chapter::updateOrCreate(
            [
                'course_id' => $course->id,
                'title' => 'CHƯƠNG 3: TypeScript với React'
            ],
            [
                'sort_order' => 3
            ]
        );

        // Lesson 3.1: Video
        $lesson3_1 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter3->id,
                'title' => 'Bài 1: Cấu hình TypeScript cho React'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder7',
                'content' => 'Khởi tạo dự án React + TypeScript với Vite. Tìm hiểu cấu trúc file tsconfig.json và cấu hình tối ưu.',
                'duration' => 1200,
                'duration_seconds' => 1200,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 1,
                'status' => 'published'
            ]
        );

        // Lesson 3.2: Video
        $lesson3_2 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter3->id,
                'title' => 'Bài 2: Typing Props và State trong React'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder8',
                'content' => 'Định nghĩa kiểu dữ liệu cho React Component Props, Children, useState, Event Handler (onClick, onChange) và Form Submit.',
                'duration' => 1500,
                'duration_seconds' => 1500,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 2,
                'status' => 'published'
            ]
        );

        // Lesson 3.3: Video
        $lesson3_3 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter3->id,
                'title' => 'Bài 3: Custom Hook với TypeScript'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=placeholder9',
                'content' => 'Cách viết Custom Hook có sử dụng Generic và trả về các giá trị chuẩn kiểu dữ liệu (strongly typed) trong React.',
                'duration' => 1800,
                'duration_seconds' => 1800,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 3,
                'status' => 'published'
            ]
        );

        // Lesson 3.4: Assignment (Bài thực hành)
        $lesson3_4 = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'chapter_id' => $chapter3->id,
                'title' => 'Bài 4: Xây dựng ứng dụng React + TypeScript'
            ],
            [
                'type' => 'assignment',
                'content' => 'Thực hành xây dựng ứng dụng Todo App hoặc Cart App hoàn chỉnh sử dụng React + TypeScript.',
                'duration' => 2700,
                'duration_seconds' => 2700,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 4,
                'status' => 'published'
            ]
        );

        Assignment::updateOrCreate(
            [
                'course_id' => $course->id,
                'lesson_id' => $lesson3_4->id,
            ],
            [
                'title' => 'Xây dựng ứng dụng React + TypeScript',
                'description' => 'Xây dựng ứng dụng Todo App có chức năng thêm, sửa, xóa, tìm kiếm và phân loại Todo. Tất cả các components, props, state, custom hooks phải được định nghĩa kiểu dữ liệu đầy đủ.',
                'instructions' => 'Nộp link Github repository chứa mã nguồn dự án React + TypeScript.',
                'max_score' => 10,
                'passing_score' => 8,
                'is_required' => true,
                'due_days' => 7
            ]
        );

        // Clean up empty chapters & sections for course 5
        $allChapters = Chapter::where('course_id', $course->id)->get();
        foreach ($allChapters as $chapter) {
            $lessonsCount = Lesson::where('chapter_id', $chapter->id)->count();
            if ($lessonsCount === 0) {
                Chapter::where('id', $chapter->id)->delete();
            }
        }

        $allSections = \Illuminate\Support\Facades\DB::table('course_sections')->where('course_id', $course->id)->get();
        foreach ($allSections as $section) {
            $lessonsCount = Lesson::where('section_id', $section->id)->count();
            if ($lessonsCount === 0) {
                \Illuminate\Support\Facades\DB::table('course_sections')->where('id', $section->id)->delete();
            }
        }

        // Sync chapters to course_sections and update lessons' section_id
        $chaptersList = Chapter::where('course_id', $course->id)->orderBy('id')->get();
        foreach ($chaptersList as $chapter) {
            \Illuminate\Support\Facades\DB::table('course_sections')->updateOrInsert(
                [
                    'course_id' => $course->id,
                    'title' => $chapter->title,
                ],
                [
                    'description' => null,
                    'sort_order' => $chapter->sort_order,
                    'created_at' => $chapter->created_at ?? now(),
                    'updated_at' => $chapter->updated_at ?? now(),
                ]
            );
            
            $sectionId = \Illuminate\Support\Facades\DB::table('course_sections')
                ->where('course_id', $course->id)
                ->where('title', $chapter->title)
                ->value('id');

            Lesson::where('chapter_id', $chapter->id)->update([
                'section_id' => $sectionId,
                'duration' => \Illuminate\Support\Facades\DB::raw('COALESCE(duration, duration_seconds)'),
            ]);
        }

        $this->command->info("Curriculum seeded successfully for 'TypeScript thực chiến từ cơ bản đến nâng cao'!");
    }
}
