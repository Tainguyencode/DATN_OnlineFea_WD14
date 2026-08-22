<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PushNotification;
use App\Models\StudyGroup;
use App\Models\StudyGroupInvitation;
use App\Models\StudyGroupMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudyGroupTest extends TestCase
{
    use RefreshDatabase;

    private function createCourseWithEnrollment(User $student, ?User $instructor = null): Course
    {
        $instructor = $instructor ?? User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it-' . uniqid()]);
        
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Laravel Advanced',
            'slug' => 'laravel-advanced-' . uniqid(),
            'short_description' => 'Short desc',
            'description' => 'Detailed desc',
            'thumbnail' => 'laravel.png',
            'price' => 299.99,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        return $course;
    }

    public function test_student_without_enrollment_cannot_create_study_group(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it-' . uniqid()]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Laravel Advanced',
            'slug' => 'laravel-advanced-' . uniqid(),
            'short_description' => 'Short desc',
            'description' => 'Detailed desc',
            'thumbnail' => 'laravel.png',
            'price' => 299.99,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('study-groups.store'), [
                'course_id' => $course->id,
                'name' => 'Laravel Study Team',
                'description' => 'Learn together',
                'max_members' => 5
            ]);

        $response->assertStatus(403);
    }

    // CASE 1: Tạo group → Không giới hạn → max_members = NULL
    public function test_case_1_create_group_unlimited_members(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $response = $this->actingAs($student)
            ->postJson(route('study-groups.store'), [
                'course_id' => $course->id,
                'name' => 'Unlimited Team',
                'description' => 'No limit group',
                'max_members_type' => 'unlimited',
                'max_members' => null,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('study_groups', [
            'name' => 'Unlimited Team',
            'max_members' => null,
        ]);
    }

    // CASE 2: Tạo group → max_members = 100 → lưu 100
    public function test_case_2_create_group_with_custom_limit_100(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $response = $this->actingAs($student)
            ->postJson(route('study-groups.store'), [
                'course_id' => $course->id,
                'name' => 'Team 100',
                'description' => 'Max 100 members',
                'max_members_type' => 'custom',
                'max_members' => 100,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('study_groups', [
            'name' => 'Team 100',
            'max_members' => 100,
        ]);
    }

    // CASE 3: Group cũ → không bị áp 100 (max_members = NULL là không giới hạn)
    public function test_case_3_legacy_group_with_null_max_members_is_not_full(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Legacy Group',
            'max_members' => null,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);

        $this->assertFalse($group->isFull());
    }

    // CASE 4: Owner đổi 100 → 500 → thành công
    public function test_case_4_owner_can_update_max_members_to_500(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Original Group',
            'max_members' => 100,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);

        $response = $this->actingAs($student)
            ->putJson(route('study-groups.update', $group), [
                'name' => 'Updated Group Name',
                'max_members_type' => 'custom',
                'max_members' => 500,
            ]);

        $response->assertOk();
        $this->assertEquals(500, $group->fresh()->max_members);
    }

    // CASE 5: Instructor đổi 100 → 500 → thành công
    public function test_case_5_instructor_can_update_max_members(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student, $instructor);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Student Created Group',
            'max_members' => 100,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);

        $response = $this->actingAs($instructor)
            ->putJson(route('study-groups.update', $group), [
                'name' => 'Instructor Updated Group',
                'max_members_type' => 'custom',
                'max_members' => 500,
            ]);

        $response->assertOk();
        $this->assertEquals(500, $group->fresh()->max_members);
    }

    // CASE 6: Member thường đổi 100 → 500 → bị từ chối
    public function test_case_6_regular_member_cannot_update_max_members(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Team Alpha',
            'max_members' => 100,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);
        $group->members()->attach($student2->id, ['role' => 'member']);

        $response = $this->actingAs($student2)
            ->putJson(route('study-groups.update', $group), [
                'name' => 'Hacked Team Alpha',
                'max_members' => 500,
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Bạn không có quyền thay đổi giới hạn thành viên của nhóm.');
    }

    // CASE 7: Group có 3 member → không được đặt max = 2
    public function test_case_7_cannot_set_limit_lower_than_current_members(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $student3 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Team of 3',
            'max_members' => 10,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);
        $group->members()->attach($student2->id, ['role' => 'member']);
        $group->members()->attach($student3->id, ['role' => 'member']);

        $response = $this->actingAs($student1)
            ->putJson(route('study-groups.update', $group), [
                'name' => 'Team of 3',
                'max_members' => 2,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Không thể đặt giới hạn thấp hơn số thành viên hiện tại của nhóm.');
    }

    // CASE 8: Group 1/1 → không accept thêm
    public function test_case_8_group_full_cannot_join_or_accept(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Full Team',
            'max_members' => 1,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);

        $response = $this->actingAs($student2)
            ->postJson(route('study-groups.join', $group));

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Nhóm đã đạt giới hạn thành viên.');
    }

    // CASE 9: Hai invitation pending cùng lúc → member đầu accept → member sau bị từ chối nếu group đầy
    public function test_case_9_concurrent_invitations_second_accept_fails_if_group_becomes_full(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $student3 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        // Group allows max 2 members (currently 1 member: student1)
        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Almost Full Team',
            'max_members' => 2,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);

        // 2 pending invitations
        $invite1 = StudyGroupInvitation::create([
            'study_group_id' => $group->id,
            'invited_user_id' => $student2->id,
            'invited_by' => $student1->id,
            'status' => StudyGroupInvitation::STATUS_PENDING,
        ]);

        $invite2 = StudyGroupInvitation::create([
            'study_group_id' => $group->id,
            'invited_user_id' => $student3->id,
            'invited_by' => $student1->id,
            'status' => StudyGroupInvitation::STATUS_PENDING,
        ]);

        // Student2 accepts -> count becomes 2/2 (Full)
        $res1 = $this->actingAs($student2)
            ->postJson(route('study-groups.invitations.accept', $invite1));
        $res1->assertOk();
        $this->assertTrue($group->fresh()->hasMember($student2->id));

        // Student3 tries to accept -> Group is full -> Rejected
        $res2 = $this->actingAs($student3)
            ->postJson(route('study-groups.invitations.accept', $invite2));
        $res2->assertStatus(400)
            ->assertJsonPath('message', 'Nhóm đã đạt giới hạn thành viên.');
        $this->assertFalse($group->fresh()->hasMember($student3->id));
    }

    // CASE 10: Mời bằng username → đúng user
    public function test_case_10_invite_by_username(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student', 'username' => 'testuser123']);
        $course = $this->createCourseWithEnrollment($student1);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Invite Test Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);

        $response = $this->actingAs($student1)
            ->postJson(route('study-groups.invite', $group), [
                'identifier' => 'testuser123',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('study_group_invitations', [
            'study_group_id' => $group->id,
            'invited_user_id' => $student2->id,
            'status' => StudyGroupInvitation::STATUS_PENDING,
        ]);
    }

    // CASE 11: Mời bằng email → đúng user
    public function test_case_11_invite_by_email(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student', 'email' => 'targetstudent@fea.test']);
        $course = $this->createCourseWithEnrollment($student1);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Invite Email Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);

        $response = $this->actingAs($student1)
            ->postJson(route('study-groups.invite', $group), [
                'identifier' => 'targetstudent@fea.test',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('study_group_invitations', [
            'study_group_id' => $group->id,
            'invited_user_id' => $student2->id,
            'status' => StudyGroupInvitation::STATUS_PENDING,
        ]);
    }

    // CASE 12: Mời chính mình → từ chối
    public function test_case_12_cannot_invite_oneself(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email' => 'self@fea.test']);
        $course = $this->createCourseWithEnrollment($student);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Self Invite Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);

        $response = $this->actingAs($student)
            ->postJson(route('study-groups.invite', $group), [
                'identifier' => 'self@fea.test',
            ]);

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Bạn không thể mời chính mình.');
    }

    // CASE 13: Mời người đã là member → từ chối
    public function test_case_13_cannot_invite_existing_member(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student', 'email' => 'member@fea.test']);
        $course = $this->createCourseWithEnrollment($student1);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Existing Member Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);
        $group->members()->attach($student2->id, ['role' => 'member']);

        $response = $this->actingAs($student1)
            ->postJson(route('study-groups.invite', $group), [
                'identifier' => 'member@fea.test',
            ]);

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Người dùng này đã là thành viên của nhóm.');
    }

    // CASE 14: Duplicate invitation pending → không tạo record thứ hai
    public function test_case_14_cannot_create_duplicate_pending_invitation(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student', 'email' => 'invitee@fea.test']);
        $course = $this->createCourseWithEnrollment($student1);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Duplicate Invite Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);

        // First invitation
        StudyGroupInvitation::create([
            'study_group_id' => $group->id,
            'invited_user_id' => $student2->id,
            'invited_by' => $student1->id,
            'status' => StudyGroupInvitation::STATUS_PENDING,
        ]);

        // Attempt second invitation
        $response = $this->actingAs($student1)
            ->postJson(route('study-groups.invite', $group), [
                'identifier' => 'invitee@fea.test',
            ]);

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Lời mời đang chờ người dùng xác nhận.');

        $this->assertEquals(1, StudyGroupInvitation::where('study_group_id', $group->id)->where('invited_user_id', $student2->id)->count());
    }

    // CASE 15: Student gửi 5 message liên tiếp → Instructor/Members thấy đủ 5
    public function test_case_15_sending_5_messages_in_a_row(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student, $instructor);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Chat Active Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);
        $group->members()->attach($instructor->id, ['role' => 'member']);

        for ($i = 1; $i <= 5; $i++) {
            $this->actingAs($student)
                ->postJson(route('study-groups.messages.store', $group), [
                    'message' => "Message number {$i}",
                ])->assertStatus(201);
        }

        $res = $this->actingAs($instructor)
            ->getJson(route('study-groups.show', $group));

        $res->assertOk();
        $this->assertCount(5, $res->json('data.messages'));
    }

    // CASE 16 & 17: Reply message specific id (message 2 or message 5)
    public function test_case_16_and_17_reply_to_specific_message(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Reply Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);

        $msg1 = $group->messages()->create(['user_id' => $student->id, 'message' => 'First message']);
        $msg2 = $group->messages()->create(['user_id' => $student->id, 'message' => 'Second message']);

        // Reply to msg 2
        $response = $this->actingAs($student)
            ->postJson(route('study-groups.messages.store', $group), [
                'message' => 'Replying to message 2',
                'reply_to_message_id' => $msg2->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.reply_to_message_id', $msg2->id);

        $this->assertDatabaseHas('study_group_messages', [
            'study_group_id' => $group->id,
            'reply_to_message_id' => $msg2->id,
            'message' => 'Replying to message 2',
        ]);
    }

    // CASE 19: User thu hồi message của chính mình → is_recalled = true
    public function test_case_19_user_can_recall_own_message(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Recall Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);

        $msg = $group->messages()->create([
            'user_id' => $student->id,
            'message' => 'Secret message to be recalled',
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('study-groups.messages.recall', [$group, $msg]));

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertTrue($msg->fresh()->is_recalled);
        $this->assertEquals('Tin nhắn đã được thu hồi', $msg->fresh()->message);
    }

    // CASE 20: User cố thu hồi message người khác → bị từ chối 403
    public function test_case_20_user_cannot_recall_others_message(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Protected Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);
        $group->members()->attach($student2->id, ['role' => 'member']);

        $msg = $group->messages()->create([
            'user_id' => $student1->id,
            'message' => 'Student 1 message',
        ]);

        $response = $this->actingAs($student2)
            ->postJson(route('study-groups.messages.recall', [$group, $msg]));

        $response->assertStatus(403);
        $this->assertFalse($msg->fresh()->is_recalled);
    }

    // CASE 21 & 22: Message cũ không có reply hay recall → hoạt động bình thường
    public function test_case_21_and_22_legacy_messages_work_fine(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Legacy Messages Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);

        $msg = $group->messages()->create([
            'user_id' => $student->id,
            'message' => 'Old message with null reply and recall',
            'reply_to_message_id' => null,
            'is_recalled' => false,
        ]);

        $response = $this->actingAs($student)
            ->getJson(route('study-groups.show', $group));

        $response->assertOk()
            ->assertJsonPath('data.messages.0.message', 'Old message with null reply and recall')
            ->assertJsonPath('data.messages.0.reply_to_message_id', null);
    }

    // CASE 23: Notification & Email invitation → đến đúng người
    public function test_case_23_invitation_sends_notification_and_email_to_target_user(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $student1 = User::factory()->create(['role' => 'student', 'name' => 'Alice']);
        $student2 = User::factory()->create(['role' => 'student', 'name' => 'Bob', 'email' => 'bob@fea.test']);
        $course = $this->createCourseWithEnrollment($student1);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Notification Test Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student1->id, ['role' => 'moderator']);

        $this->actingAs($student1)
            ->postJson(route('study-groups.invite', $group), [
                'identifier' => 'bob@fea.test',
            ])->assertStatus(201);

        $this->assertDatabaseHas('push_notifications', [
            'user_id' => $student2->id,
            'type' => 'study_group_invitation',
            'title' => 'Bạn được mời vào nhóm học',
        ]);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\StudyGroupInvitationMail::class, function ($mail) use ($student2) {
            return $mail->hasTo('bob@fea.test');
        });
    }

    // CASE 24: Group chat private file attachment & download security
    public function test_case_24_member_can_upload_and_download_file(): void
    {
        Storage::fake('local');

        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $group = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'File Upload Group',
            'max_members' => 10,
        ]);
        $group->members()->attach($student->id, ['role' => 'moderator']);

        $file = UploadedFile::fake()->create('assignment.pdf', 500, 'application/pdf');

        $res = $this->actingAs($student)
            ->postJson(route('study-groups.messages.store', $group), [
                'message' => 'Đây là tài liệu',
                'file' => $file,
            ]);

        $res->assertStatus(201);
        $messageData = $res->json('data');

        $downloadRes = $this->actingAs($student)
            ->get(route('study-groups.messages.download', [$group, $messageData['id']]));

        $downloadRes->assertOk();
    }
}
