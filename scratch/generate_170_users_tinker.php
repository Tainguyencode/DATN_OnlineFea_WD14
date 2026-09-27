<?php

use App\Models\Category;
use App\Models\InstructorApplication;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\InstructorTeachingField;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$result = (function (): array {
    $studentTotal = 120;
    $instructorTotal = 50;
    $plainPassword = '123456';
    $timezone = config('app.timezone', 'Asia/Ho_Chi_Minh');
    $startAt = CarbonImmutable::create(2026, 6, 1, 0, 0, 0, $timezone);
    $endAt = CarbonImmutable::now($timezone);

    $existingBatchAccounts = User::query()
        ->where(fn ($query) => $query
            ->where('email', 'like', '%@faculty.example.test')
            ->orWhere('email', 'like', '%@learners.example.test'))
        ->count();

    if ($existingBatchAccounts > 0) {
        throw new RuntimeException("Refusing to rerun: found {$existingBatchAccounts} existing generated accounts.");
    }

    $randomDateBetween = static function (CarbonImmutable $from, CarbonImmutable $to) use ($timezone): CarbonImmutable {
        if ($to->lessThanOrEqualTo($from)) {
            return $from;
        }

        return CarbonImmutable::createFromTimestamp(
            random_int($from->getTimestamp(), $to->getTimestamp()),
            $timezone
        );
    };

    $notAfterNow = static fn (CarbonImmutable $candidate): CarbonImmutable => $candidate->lessThan($endAt) ? $candidate : $endAt;

    $makeUsername = static function (string $name): string {
        $base = Str::of($name)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->limit(24, '')
            ->toString() ?: 'fea_member';

        do {
            $candidate = $base.'_'.strtolower(Str::random(5));
        } while (User::query()->where('username', $candidate)->exists());

        return $candidate;
    };

    $phonePrefixes = [
        '032', '033', '034', '035', '036', '037', '038', '039',
        '070', '076', '077', '078', '079', '081', '082', '083',
        '084', '085', '088', '090', '091', '093', '094', '096',
        '097', '098',
    ];

    $makePhone = static function () use ($phonePrefixes): string {
        do {
            $phone = $phonePrefixes[array_rand($phonePrefixes)]
                .str_pad((string) random_int(0, 9_999_999), 7, '0', STR_PAD_LEFT);
        } while (User::query()->where('phone', $phone)->exists());

        return $phone;
    };

    $faker = Faker\Factory::create('vi_VN');
    $makeName = static fn (): string => trim((string) preg_replace(
        '/^(?:Em|Bác|Cụ|Chú|Ông|Bà|Cô|Chị|Anh)\.\s+/u',
        '',
        $faker->unique()->name()
    ));
    $organizations = [
        'Học viện Công nghệ Ánh Dương',
        'Trung tâm Đào tạo Khai Minh',
        'Viện Phát triển Tri thức Việt',
        'Công ty Giáo dục Sao Khuê',
        'Trung tâm Nghiên cứu Đông Nam',
        'Học viện Kỹ năng Thành Công',
        'Viện Công nghệ Sáng tạo',
        'Trung tâm Đào tạo Tân Việt',
    ];
    $positions = [
        'Giảng viên chuyên môn',
        'Chuyên gia đào tạo',
        'Cố vấn học thuật',
        'Kỹ sư đào tạo',
        'Chuyên viên nghiên cứu',
        'Quản lý đào tạo',
    ];

    $categories = Category::query()
        ->where('status', true)
        ->whereNotNull('parent_id')
        ->whereHas('parent', fn ($query) => $query->where('status', true))
        ->with('parent')
        ->get();

    if ($categories->isEmpty()) {
        $categories = Category::query()->where('status', true)->get();
    }

    if ($categories->isEmpty()) {
        throw new RuntimeException('No active category is available for instructors.');
    }

    $reviewerId = User::query()
        ->whereIn('role', ['admin', 'super_admin'])
        ->where('is_active', true)
        ->value('id');

    return DB::transaction(function () use (
        $studentTotal,
        $instructorTotal,
        $plainPassword,
        $startAt,
        $endAt,
        $faker,
        $makeName,
        $categories,
        $organizations,
        $positions,
        $reviewerId,
        $makeUsername,
        $makePhone,
        $randomDateBetween,
        $notAfterNow
    ): array {
        $studentIds = [];
        $instructorIds = [];
        $profileIds = [];
        $certificateCount = 0;

        for ($index = 0; $index < $instructorTotal; $index++) {
            $name = $makeName();
            $username = $makeUsername($name);
            $phone = $makePhone();
            $email = $username.'@faculty.example.test';
            $createdAt = $randomDateBetween($startAt, $endAt);
            $verifiedAt = $randomDateBetween($createdAt, $notAfterNow($createdAt->addDays(2)));
            $submittedAt = $randomDateBetween($verifiedAt, $notAfterNow($verifiedAt->addDays(7)));
            $approvedAt = $randomDateBetween($submittedAt, $notAfterNow($submittedAt->addDays(10)));
            $updatedAt = $randomDateBetween($approvedAt, $endAt);
            $category = $categories->random();
            $organization = $organizations[array_rand($organizations)];
            $position = $positions[array_rand($positions)];
            $specialty = 'Chuyên môn '.$category->name;
            $experience = sprintf(
                'Có %d năm kinh nghiệm thực hành và đào tạo trong lĩnh vực %s. Từng xây dựng chương trình học, hướng dẫn dự án và hỗ trợ học viên ứng dụng kiến thức vào thực tế.',
                random_int(3, 15),
                $category->name
            );
            $bio = sprintf(
                '%s là %s trong lĩnh vực %s. Phương pháp giảng dạy tập trung vào ví dụ thực tế, bài tập ứng dụng và phản hồi cụ thể cho từng học viên.',
                $name,
                mb_strtolower($position),
                $category->name
            );

            $user = new User;
            $user->fill([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password' => $plainPassword,
                'role' => 'instructor',
                'avatar' => null,
                'bio' => $bio,
                'is_active' => true,
                'account_status' => 'active',
                'instructor_status' => 'approved',
                'submitted_for_review_at' => $submittedAt,
                'approved_at' => $approvedAt,
                'approved_by' => $reviewerId,
                'needs_admin_review' => false,
                'admin_last_reviewed_at' => $approvedAt,
                'password_changed_at' => $createdAt,
                'reactivation_status' => 'none',
            ]);
            $user->email_verified_at = $verifiedAt;
            $user->status = 'active';
            $user->created_at = $createdAt;
            $user->updated_at = $updatedAt;
            $user->save();

            $profile = new InstructorProfile;
            $profile->fill([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'phone' => $phone,
                'organization' => $organization,
                'position' => $position,
                'teaching_field' => $category->name,
                'specialty' => $specialty,
                'experience' => $experience,
                'bio' => $bio,
                'agree_information' => true,
                'agree_terms' => true,
            ]);
            $profile->created_at = $createdAt;
            $profile->updated_at = $updatedAt;
            $profile->save();

            $teachingField = new InstructorTeachingField;
            $teachingField->fill([
                'instructor_profile_id' => $profile->id,
                'category_id' => $category->id,
                'organization' => $organization,
                'position' => $position,
                'specialty' => $specialty,
                'experience' => $experience,
                'is_primary' => true,
                'approval_status' => 'approved',
                'submitted_at' => $submittedAt,
                'reviewed_at' => $approvedAt,
                'reviewed_by' => $reviewerId,
            ]);
            $teachingField->created_at = $createdAt;
            $teachingField->updated_at = $updatedAt;
            $teachingField->save();

            $application = new InstructorApplication;
            $application->fill([
                'user_id' => $user->id,
                'expertise' => $specialty,
                'experience' => $experience,
                'introduction' => $bio,
                'status' => 'approved',
                'admin_notes' => 'Hồ sơ dữ liệu mô phỏng phục vụ môi trường phát triển.',
                'reviewed_by' => $reviewerId,
                'reviewed_at' => $approvedAt,
            ]);
            $application->created_at = $createdAt;
            $application->updated_at = $updatedAt;
            $application->save();

            $requirements = InstructorDocumentRequirement::query()
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->where('is_required', true)
                ->orderBy('sort_order')
                ->get();

            if ($requirements->isEmpty() && $category->parent_id) {
                $requirements = InstructorDocumentRequirement::query()
                    ->where('category_id', $category->parent_id)
                    ->where('is_active', true)
                    ->where('is_required', true)
                    ->orderBy('sort_order')
                    ->get();
            }

            foreach ($requirements as $requirement) {
                $document = new InstructorCertificate;
                $document->fill([
                    'user_id' => $user->id,
                    'requirement_id' => $requirement->id,
                    'instructor_teaching_field_id' => $teachingField->id,
                    'source_type' => 'url',
                    'file_path' => null,
                    'document_url' => "https://evidence.example.test/{$username}/{$requirement->id}",
                    'original_name' => Str::slug($requirement->document_title).'.pdf',
                    'mime_type' => 'application/pdf',
                    'file_size' => 0,
                    'title' => $requirement->document_title,
                    'document_type' => $requirement->document_type,
                    'status' => 'approved',
                    'uploaded_at' => $submittedAt,
                    'reviewed_at' => $approvedAt,
                    'reviewed_by' => $reviewerId,
                ]);
                $document->created_at = $submittedAt;
                $document->updated_at = $approvedAt;
                $document->save();
                $certificateCount++;
            }

            $instructorIds[] = $user->id;
            $profileIds[] = $profile->id;
        }

        for ($index = 0; $index < $studentTotal; $index++) {
            $name = $makeName();
            $username = $makeUsername($name);
            $createdAt = $randomDateBetween($startAt, $endAt);
            $verifiedAt = $randomDateBetween($createdAt, $notAfterNow($createdAt->addDays(3)));
            $updatedAt = $randomDateBetween($verifiedAt, $endAt);

            $user = new User;
            $user->fill([
                'name' => $name,
                'username' => $username,
                'email' => $username.'@learners.example.test',
                'phone' => $makePhone(),
                'password' => $plainPassword,
                'role' => 'student',
                'avatar' => null,
                'bio' => null,
                'is_active' => true,
                'account_status' => 'active',
                'instructor_status' => null,
                'needs_admin_review' => false,
                'password_changed_at' => $createdAt,
                'reactivation_status' => 'none',
            ]);
            $user->email_verified_at = $verifiedAt;
            $user->status = 'active';
            $user->created_at = $createdAt;
            $user->updated_at = $updatedAt;
            $user->save();
            $studentIds[] = $user->id;
        }

        $allUserIds = [...$studentIds, ...$instructorIds];
        $expectedTotal = $studentTotal + $instructorTotal;
        $checks = [
            'students' => User::query()->whereIn('id', $studentIds)->where('role', 'student')->count(),
            'instructors' => User::query()->whereIn('id', $instructorIds)->where('role', 'instructor')->where('instructor_status', 'approved')->where('account_status', 'active')->where('is_active', true)->whereNotNull('email_verified_at')->count(),
            'profiles' => InstructorProfile::query()->whereIn('user_id', $instructorIds)->count(),
            'applications' => InstructorApplication::query()->whereIn('user_id', $instructorIds)->where('status', 'approved')->count(),
            'teaching_fields' => InstructorTeachingField::query()->whereIn('instructor_profile_id', $profileIds)->where('approval_status', 'approved')->count(),
            'bad_timestamps' => User::query()->whereIn('id', $allUserIds)->whereColumn('updated_at', '<', 'created_at')->count(),
            'outside_range' => User::query()->whereIn('id', $allUserIds)->where(fn ($query) => $query->where('created_at', '<', $startAt)->orWhere('created_at', '>', $endAt))->count(),
            'unique_emails' => User::query()->whereIn('id', $allUserIds)->distinct()->count('email'),
            'unique_usernames' => User::query()->whereIn('id', $allUserIds)->distinct()->count('username'),
            'unique_phones' => User::query()->whereIn('id', $allUserIds)->distinct()->count('phone'),
            'role_pivots' => DB::table('role_user')->whereIn('user_id', $allUserIds)->count(),
        ];

        if (
            $checks['students'] !== $studentTotal
            || $checks['instructors'] !== $instructorTotal
            || $checks['profiles'] !== $instructorTotal
            || $checks['applications'] !== $instructorTotal
            || $checks['teaching_fields'] !== $instructorTotal
            || $checks['bad_timestamps'] !== 0
            || $checks['outside_range'] !== 0
            || $checks['unique_emails'] !== $expectedTotal
            || $checks['unique_usernames'] !== $expectedTotal
            || $checks['unique_phones'] !== $expectedTotal
            || $checks['role_pivots'] < $expectedTotal
        ) {
            throw new RuntimeException('Post-generation validation failed: '.json_encode($checks));
        }

        return [
            'status' => 'COMPLETED',
            'students_created' => $checks['students'],
            'instructors_created' => $checks['instructors'],
            'instructor_profiles_created' => $checks['profiles'],
            'instructor_applications_created' => $checks['applications'],
            'approved_teaching_fields_created' => $checks['teaching_fields'],
            'approved_documents_created' => $certificateCount,
            'created_at_from' => $startAt->toDateTimeString(),
            'created_at_to' => $endAt->toDateTimeString(),
            'login_password' => $plainPassword,
            'email_sent' => false,
        ];
    }, attempts: 1);
})();

dump($result);
