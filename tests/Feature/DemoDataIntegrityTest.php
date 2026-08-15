<?php

namespace Tests\Feature;

use App\Jobs\ConvertVideoToHLS;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\InstructorApplication;
use App\Models\InstructorProfile;
use App\Models\LearningPath;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoDataIntegrityTest extends TestCase
{
    public function test_demo_users_count_and_roles(): void
    {
        $superAdmins = User::where('email', 'superadmin@onlinefea.test')->count();
        $this->assertEquals(1, $superAdmins, 'Super Admin must exist');

        $admins = User::where('email', 'like', 'admin.%@onlinefea.test')->count();
        $this->assertGreaterThanOrEqual(3, $admins, 'At least 3 Admin accounts must exist');

        $instructors = User::where('role', 'instructor')->where('email', 'like', '%@onlinefea.test')->count();
        $this->assertGreaterThanOrEqual(30, $instructors, 'At least 30 Instructor accounts must exist');

        $students = User::where('role', 'student')->where('email', 'like', '%@onlinefea.test')->count();
        $this->assertGreaterThanOrEqual(300, $students, 'At least 300 Student accounts must exist');
    }

    public function test_instructors_have_valid_profiles_and_applications(): void
    {
        $instructors = User::where('role', 'instructor')
            ->where('email', 'like', 'instructor.%@onlinefea.test')
            ->get();

        foreach ($instructors as $instructor) {
            $profile = InstructorProfile::where('user_id', $instructor->id)->first();
            $this->assertNotNull($profile, "Instructor {$instructor->id} must have an InstructorProfile");
            $this->assertNotEmpty($profile->position);
            $this->assertNotEmpty($profile->organization);

            $application = InstructorApplication::where('user_id', $instructor->id)->first();
            $this->assertNotNull($application, "Instructor {$instructor->id} must have an InstructorApplication");
            $this->assertEquals('approved', $application->status);
        }
    }

    public function test_demo_courses_count_and_integrity(): void
    {
        $courses = Course::where('slug', 'like', 'demo-%')->get();
        $this->assertGreaterThanOrEqual(60, $courses->count(), 'At least 60 demo courses must exist');

        foreach ($courses as $course) {
            $this->assertTrue($course->is_published);
            $this->assertEquals('published', $course->status);
            $this->assertNotNull($course->instructor_id);
            $this->assertNotNull($course->category_id);
            $this->assertGreaterThan(0, $course->chapters()->count(), "Course {$course->id} must have chapters");
            $this->assertGreaterThan(0, $course->lessons()->count(), "Course {$course->id} must have lessons");
        }
    }

    public function test_learning_paths_are_valid_and_non_empty(): void
    {
        $paths = LearningPath::where('slug', 'like', 'demo-%')->with('courses')->get();
        $this->assertGreaterThanOrEqual(8, $paths->count(), 'At least 8 learning paths must exist');

        foreach ($paths as $path) {
            $this->assertGreaterThanOrEqual(2, $path->courses->count(), "Learning path {$path->slug} must have courses attached");
            foreach ($path->courses as $course) {
                $this->assertNotEmpty($course->pivot->stage_name, "Course in path {$path->slug} must have a stage_name");
                $this->assertGreaterThan(0, $course->pivot->sort_order, "Course in path {$path->slug} must have a valid sort_order");
            }
        }
    }

    public function test_real_mp4_video_files_exist_and_are_valid(): void
    {
        $videoLessons = Lesson::where('type', Lesson::TYPE_VIDEO)
            ->whereNotNull('video_path')
            ->where('video_path', 'like', 'videos/sources/%')
            ->get();

        $this->assertGreaterThanOrEqual(50, $videoLessons->count(), 'At least 50 video lessons must have video sources attached');

        foreach ($videoLessons->take(20) as $vLesson) {
            $publicPath = Storage::disk('public')->path($vLesson->video_path);
            $privatePath = Storage::disk('local')->path($vLesson->video_path);

            $this->assertTrue(file_exists($publicPath) || file_exists($privatePath), "MP4 file must exist for lesson {$vLesson->id}");
            $filePath = file_exists($publicPath) ? $publicPath : $privatePath;
            $this->assertGreaterThan(10000, filesize($filePath), "MP4 file size must be > 10KB for lesson {$vLesson->id}");
            $this->assertEquals('video/mp4', $vLesson->video_mime);
        }
    }

    public function test_certificates_only_for_100_percent_completed_enrollments(): void
    {
        $certificates = Certificate::all();
        $this->assertGreaterThan(0, $certificates->count(), 'There should be issued certificates');

        foreach ($certificates as $cert) {
            $enrollment = Enrollment::where('user_id', $cert->user_id)
                ->where('course_id', $cert->course_id)
                ->first();

            $this->assertNotNull($enrollment, "Certificate {$cert->id} must have a matching enrollment");
            $this->assertEquals(100.00, (float) $enrollment->progress_percent, "Certificate {$cert->id} must belong to 100% completed enrollment");
        }
    }

    public function test_hls_conversion_pipeline_on_sample_lesson(): void
    {
        $lesson = Lesson::where('type', Lesson::TYPE_VIDEO)
            ->whereNotNull('video_path')
            ->where('video_path', 'like', 'videos/sources/%')
            ->first();

        $this->assertNotNull($lesson, 'Must have at least one video lesson with source');

        // Run HLS conversion job
        $job = new ConvertVideoToHLS($lesson);
        $job->handle();

        $lesson->refresh();
        $this->assertEquals('completed', $lesson->processing_status);

        $hlsDir = Storage::disk('local')->path('lesson-hls/' . $lesson->id);
        $playlistPath = $hlsDir . DIRECTORY_SEPARATOR . 'playlist.m3u8';
        $this->assertTrue(file_exists($playlistPath), 'playlist.m3u8 must be generated');
        $this->assertGreaterThan(0, filesize($playlistPath), 'playlist.m3u8 must not be empty');

        $segments = File::glob($hlsDir . DIRECTORY_SEPARATOR . 'segment_*.ts');
        $this->assertNotEmpty($segments, 'HLS .ts segments must be generated');
    }
}
