<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LearningPathSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo các Lộ trình học tập đa dạng (Learning Paths)
        $paths = [
            [
                'title' => 'Lộ trình trở thành Web Fullstack Developer',
                'slug' => Str::slug('Lộ trình trở thành Web Fullstack Developer'),
                'description' => 'Chương trình đào tạo toàn diện từ A-Z giúp bạn làm chủ cả Frontend (React.js) và Backend (Laravel Framework), xây dựng ứng dụng Web thực tế chuẩn doanh nghiệp.',
                'thumbnail' => null,
                'level' => 'intermediate',
                'target_role' => 'Fullstack Web Developer',
                'salary_range' => '15 - 35 triệu/tháng',
                'estimated_duration' => '6 - 8 tháng (180h học)',
                'skills' => json_encode(['HTML5/CSS3', 'JavaScript ES6+', 'React.js', 'Laravel Framework', 'RESTful API', 'MySQL Database', 'Git']),
                'is_featured' => true,
            ],
            [
                'title' => 'Lộ trình chuyên sâu UI/UX & Thiết kế sản phẩm',
                'slug' => Str::slug('Lộ trình chuyên sâu UI/UX Thiết kế sản phẩm'),
                'description' => 'Trang bị tư duy thiết kế lấy người dùng làm trung tâm (User-Centered Design), nghiên cứu trải nghiệm người dùng, thiết kế giao diện Figma chuyên nghiệp và xây dựng Design System.',
                'thumbnail' => null,
                'level' => 'beginner',
                'target_role' => 'UI/UX Designer / Product Designer',
                'salary_range' => '12 - 28 triệu/tháng',
                'estimated_duration' => '4 - 5 tháng (120h học)',
                'skills' => json_encode(['Figma Core & Prototyping', 'User Research & Wireframing', 'Design System & Component', 'Usability Testing', 'Design Thinking']),
                'is_featured' => true,
            ],
            [
                'title' => 'Lộ trình Phân tích dữ liệu & AI Application Engineer',
                'slug' => Str::slug('Lộ trình Phân tích dữ liệu AI Application Engineer'),
                'description' => 'Làm chủ ngôn ngữ Python, các thư viện xử lý dữ liệu Pandas, NumPy, trực quan hóa dữ liệu và huấn luyện mô hình Machine Learning thực tế.',
                'thumbnail' => null,
                'level' => 'intermediate',
                'target_role' => 'Data Analyst / AI Engineer',
                'salary_range' => '18 - 40 triệu/tháng',
                'estimated_duration' => '6 - 7 tháng (160h học)',
                'skills' => json_encode(['Python Programming', 'Pandas & NumPy', 'SQL Database', 'Matplotlib & Seaborn', 'Scikit-Learn Machine Learning']),
                'is_featured' => true,
            ],
            [
                'title' => 'Lộ trình Digital Marketing & Growth Hacking',
                'slug' => Str::slug('Lộ trình Digital Marketing Growth Hacking'),
                'description' => 'Trở thành Marketer toàn diện nắm vững quy trình SEO Google top 1, tối ưu hóa các chiến dịch quảng cáo Facebook/TikTok Ads và chiến lược thương hiệu.',
                'thumbnail' => null,
                'level' => 'beginner',
                'target_role' => 'Digital Marketing Specialist / Growth Marketer',
                'salary_range' => '10 - 25 triệu/tháng',
                'estimated_duration' => '3 - 4 tháng (90h học)',
                'skills' => json_encode(['Keyword Research & SEO', 'Facebook & TikTok Ads', 'Content Strategy', 'Google Analytics', 'Conversion Rate Optimization']),
                'is_featured' => true,
            ],
            [
                'title' => 'Lộ trình Chinh phục IELTS 7.0+ & Tiếng Anh Công sở',
                'slug' => Str::slug('Lộ trình Chinh phục IELTS 7 0 Tiếng Anh Công sở'),
                'description' => 'Lộ trình bứt phá kỹ năng giao tiếp tiếng Anh chuyên nghiệp và hoàn thiện 4 kỹ năng Listening, Reading, Writing Task 1-2, Speaking đạt band điểm 7.0+.',
                'thumbnail' => null,
                'level' => 'intermediate',
                'target_role' => 'IELTS Candidates / Global Professional',
                'salary_range' => 'Không giới hạn',
                'estimated_duration' => '5 - 6 tháng (150h học)',
                'skills' => json_encode(['IELTS Academic Writing', 'Fluent English Speaking', 'Business Communication', 'Academic Vocabulary']),
                'is_featured' => true,
            ],
        ];

        foreach ($paths as $pathData) {
            DB::table('learning_paths')->updateOrInsert(
                ['slug' => $pathData['slug']],
                array_merge($pathData, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        // 2. Đảm bảo tất cả các khóa học mẫu đều ở trạng thái xuất bản (published)
        DB::table('courses')->update(['status' => 'published', 'is_published' => 1]);

        // 3. Liên kết các khóa học vào từng Lộ trình
        $laravelCourse = DB::table('courses')->where('slug', Str::slug('Laravel từ Zero đến Hero'))->value('id');
        $reactCourse = DB::table('courses')->where('slug', Str::slug('React.js Masterclass'))->value('id');
        $figmaCourse = DB::table('courses')->where('slug', Str::slug('Figma Prototype Design System'))->value('id');
        $pythonCourse = DB::table('courses')->where('slug', Str::slug('Python cho Phân tích dữ liệu Machine Learning'))->value('id');
        $marketingCourse = DB::table('courses')->where('slug', Str::slug('Digital Marketing SEO Performance Mastery'))->value('id');
        $ieltsCourse = DB::table('courses')->where('slug', Str::slug('Luyen thi IELTS 7 0 4 Ky nang Speaking Practical'))->value('id');

        $fullstackPathId = DB::table('learning_paths')->where('slug', $paths[0]['slug'])->value('id');
        $uiuxPathId = DB::table('learning_paths')->where('slug', $paths[1]['slug'])->value('id');
        $dataPathId = DB::table('learning_paths')->where('slug', $paths[2]['slug'])->value('id');
        $marketingPathId = DB::table('learning_paths')->where('slug', $paths[3]['slug'])->value('id');
        $ieltsPathId = DB::table('learning_paths')->where('slug', $paths[4]['slug'])->value('id');

        if ($laravelCourse && $reactCourse && $fullstackPathId) {
            DB::table('learning_path_courses')->updateOrInsert(
                ['learning_path_id' => $fullstackPathId, 'course_id' => $reactCourse],
                ['sort_order' => 1, 'stage_name' => 'Giai đoạn 1: Frontend Single Page Application với React.js']
            );
            DB::table('learning_path_courses')->updateOrInsert(
                ['learning_path_id' => $fullstackPathId, 'course_id' => $laravelCourse],
                ['sort_order' => 2, 'stage_name' => 'Giai đoạn 2: Backend RESTful API & Cơ sở dữ liệu với Laravel']
            );
        }

        if ($figmaCourse && $uiuxPathId) {
            DB::table('learning_path_courses')->updateOrInsert(
                ['learning_path_id' => $uiuxPathId, 'course_id' => $figmaCourse],
                ['sort_order' => 1, 'stage_name' => 'Giai đoạn 1: Nền tảng UI/UX & Wireframing với Figma']
            );
        }

        if ($pythonCourse && $dataPathId) {
            DB::table('learning_path_courses')->updateOrInsert(
                ['learning_path_id' => $dataPathId, 'course_id' => $pythonCourse],
                ['sort_order' => 1, 'stage_name' => 'Giai đoạn 1: Phân tích dữ liệu & Machine Learning với Python']
            );
        }

        if ($marketingCourse && $marketingPathId) {
            DB::table('learning_path_courses')->updateOrInsert(
                ['learning_path_id' => $marketingPathId, 'course_id' => $marketingCourse],
                ['sort_order' => 1, 'stage_name' => 'Giai đoạn 1: Chiến lược Digital Marketing & SEO Mastery']
            );
        }

        if ($ieltsCourse && $ieltsPathId) {
            DB::table('learning_path_courses')->updateOrInsert(
                ['learning_path_id' => $ieltsPathId, 'course_id' => $ieltsCourse],
                ['sort_order' => 1, 'stage_name' => 'Giai đoạn 1: Bứt phá Band điểm IELTS 7.0+ 4 Kỹ năng']
            );
        }
    }
}
