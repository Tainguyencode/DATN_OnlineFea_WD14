<?php

namespace Tests\Feature;

use App\Events\CourseDiscussionMessageBroadcasted;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\DiscussionChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DiscussionMessengerTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_and_instructor_receive_complete_canonical_message_contract(): void
    {
        Event::fake([CourseDiscussionMessageBroadcasted::class]);
        [$student, $instructor, $course, $lesson] = $this->chatContext();

        $created = $this->actingAs($student)->postJson(
            route('courses.lessons.discussions.store', [$course, $lesson]),
            ['content' => 'Câu hỏi đầu tiên', 'sender_id' => $instructor->id, 'is_instructor_answer' => true]
        )->assertCreated();

        $created->assertJsonPath('data.key', 'discussion:'.$created->json('discussion_id'))
            ->assertJsonPath('data.sender.id', $student->id)
            ->assertJsonPath('data.sender.role', 'student')
            ->assertJsonPath('data.lesson.id', $lesson->id)
            ->assertJsonPath('data.permissions.can_reply', true)
            ->assertJsonPath('data.permissions.can_recall', true);
        $this->assertSame($student->id, Discussion::first()->user_id);

        $discussion = Discussion::firstOrFail();
        $reply = $this->actingAs($instructor)->postJson(
            route('discussions.replies.store', $discussion),
            ['content' => 'Giảng viên trả lời', 'reply_to_key' => 'discussion:'.$discussion->id]
        )->assertCreated();

        $reply->assertJsonPath('data.key', 'reply:'.$reply->json('data.id'))
            ->assertJsonPath('data.sender.id', $instructor->id)
            ->assertJsonPath('data.sender.role', 'instructor')
            ->assertJsonPath('data.reply_to.key', 'discussion:'.$discussion->id)
            ->assertJsonPath('data.reply_to.sender.id', $student->id)
            ->assertJsonPath('data.permissions.can_reply', true);

        $stored = DiscussionReply::findOrFail($reply->json('data.id'));
        $this->assertSame($instructor->id, $stored->user_id);
        $this->assertTrue($stored->is_instructor_answer);
        $this->assertSame($discussion->id, $stored->reply_to_discussion_id);
        Event::assertDispatched(CourseDiscussionMessageBroadcasted::class, fn ($event) => $event->messageKey === 'reply:'.$stored->id);
    }

    public function test_private_channel_authorization_matches_http_policy(): void
    {
        [$student, $instructor, $course, $lesson] = $this->chatContext();
        $other = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $discussion = $this->createDiscussion($student, $course, $lesson);
        $callback = Broadcast::connection()->getChannels()['course-discussion.{discussionId}'];

        $this->assertTrue($callback($student, $discussion->id));
        $this->assertTrue($callback($instructor, $discussion->id));
        $this->assertFalse($callback($other, $discussion->id));

        $student->forceFill(['is_active' => false])->save();
        $this->assertFalse($callback($student->fresh(), $discussion->id));
    }

    public function test_unauthorized_users_cannot_read_send_or_download_attachments(): void
    {
        [$student, $instructor, $course, $lesson] = $this->chatContext();
        $other = User::factory()->create(['role' => 'student']);
        $discussion = $this->createDiscussion($student, $course, $lesson, [
            'attachment_path' => 'discussions/attachments/private.pdf',
            'attachment_name' => 'private.pdf',
            'attachment_type' => 'file',
        ]);

        $this->actingAs($other)->getJson(route('discussions.messages', $discussion))->assertForbidden();
        $this->actingAs($other)->postJson(route('discussions.replies.store', $discussion), ['content' => 'Bypass'])->assertForbidden();
        $this->actingAs($other)->get(route('discussion-messages.attachment', ['kind' => 'discussion', 'message' => $discussion->id]))->assertForbidden();
    }

    public function test_reply_target_is_scoped_to_its_conversation_and_student_cannot_mark_own_reply_helpful(): void
    {
        [$student, $instructor, $course, $lesson] = $this->chatContext();
        $otherStudent = User::factory()->create(['role' => 'student']);
        $this->enroll($otherStudent, $course);
        $first = $this->createDiscussion($student, $course, $lesson);
        $second = $this->createDiscussion($otherStudent, $course, $lesson);
        $foreignReply = DiscussionReply::create([
            'discussion_id' => $second->id,
            'lesson_id' => $lesson->id,
            'user_id' => $otherStudent->id,
            'content' => 'Foreign',
            'is_instructor_answer' => false,
        ]);

        $this->actingAs($student)
            ->postJson(route('discussions.replies.store', $first), [
                'content' => 'Invalid quote',
                'reply_to_key' => 'reply:'.$foreignReply->id,
            ])
            ->assertUnprocessable();

        $ownReply = DiscussionReply::create([
            'discussion_id' => $first->id,
            'lesson_id' => $lesson->id,
            'user_id' => $student->id,
            'content' => 'Own answer',
            'is_instructor_answer' => false,
        ]);
        $this->actingAs($student)
            ->postJson(route('discussions.replies.toggle-helpful', $ownReply))
            ->assertForbidden();
    }

    public function test_recall_permissions_and_attachment_validation_are_enforced(): void
    {
        [$student, $instructor, $course, $lesson] = $this->chatContext();
        $other = User::factory()->create(['role' => 'student']);
        $discussion = $this->createDiscussion($student, $course, $lesson);
        $reply = DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'lesson_id' => $lesson->id,
            'user_id' => $instructor->id,
            'content' => 'Answer',
            'is_instructor_answer' => true,
        ]);

        $this->actingAs($other)->postJson(route('discussions.replies.recall', $reply))->assertForbidden();
        $this->actingAs($instructor)
            ->postJson(route('discussions.replies.recall', $reply))
            ->assertOk()
            ->assertJsonPath('kind', 'reply');

        $this->actingAs($student)
            ->postJson(route('discussions.replies.store', $discussion), [
                'attachment' => UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('attachment');
    }

    public function test_duplicate_prevention_unread_mark_read_and_list_order_use_real_last_message(): void
    {
        [$student, $instructor, $course, $lesson] = $this->chatContext();

        $first = $this->actingAs($student)->postJson(
            route('courses.lessons.discussions.store', [$course, $lesson]),
            ['content' => 'First']
        )->assertCreated();
        $this->actingAs($student)->postJson(
            route('courses.lessons.discussions.store', [$course, $lesson]),
            ['content' => 'Second']
        )->assertCreated();

        $this->assertDatabaseCount('discussions', 1);
        $this->assertDatabaseCount('discussion_replies', 1);
        $discussion = Discussion::findOrFail($first->json('discussion_id'));
        $this->assertDatabaseHas('discussion_participants', [
            'discussion_id' => $discussion->id,
            'user_id' => $instructor->id,
            'unread_count' => 2,
        ]);

        $list = $this->actingAs($instructor)
            ->getJson(route('messenger.conversations.index'))
            ->assertOk()
            ->assertJsonPath('data.0.id', $discussion->id)
            ->assertJsonPath('data.0.last_message.content', 'Second')
            ->assertJsonPath('data.0.unread_count', 2)
            ->assertJsonPath('meta.unread_total', 2);

        $this->actingAs($instructor)
            ->postJson(route('discussions.read', $discussion))
            ->assertOk();
        $this->assertDatabaseHas('discussion_participants', [
            'discussion_id' => $discussion->id,
            'user_id' => $instructor->id,
            'unread_count' => 0,
        ]);

        $this->actingAs($instructor)->postJson(
            route('discussions.replies.store', $discussion),
            ['content' => 'Newest instructor answer']
        )->assertCreated();
        $this->assertDatabaseHas('discussion_participants', [
            'discussion_id' => $discussion->id,
            'user_id' => $student->id,
            'unread_count' => 1,
        ]);

        $this->actingAs($student)
            ->getJson(route('messenger.conversations.index'))
            ->assertJsonPath('data.0.last_message.content', 'Newest instructor answer')
            ->assertJsonPath('data.0.unread_count', 1);
    }

    public function test_incremental_cursor_only_returns_new_messages(): void
    {
        [$student, $instructor, $course, $lesson] = $this->chatContext();
        $discussion = $this->createDiscussion($student, $course, $lesson);
        app(DiscussionChatService::class)->recordMessage($discussion, $student, $discussion);

        $initial = $this->actingAs($student)
            ->getJson(route('discussions.messages', $discussion))
            ->assertOk();
        $cursor = $initial->json('cursor');

        $this->actingAs($instructor)->postJson(
            route('discussions.replies.store', $discussion),
            ['content' => 'Only new message']
        )->assertCreated();

        $this->actingAs($student)
            ->getJson(route('discussions.messages', [$discussion, 'after' => $cursor]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.content', 'Only new message');
    }

    public function test_shared_layout_mounts_messenger_but_learning_layout_does_not(): void
    {
        $this->assertStringContainsString('<x-messenger.floating />', file_get_contents(resource_path('views/components/instructor-layout.blade.php')));
        $this->assertStringContainsString('<x-messenger.floating />', file_get_contents(resource_path('views/student/dashboard/layouts/app.blade.php')));
        $this->assertStringNotContainsString('messenger.floating', file_get_contents(resource_path('views/layouts/learning.blade.php')));
        $this->assertStringNotContainsString('max-w-4xl', file_get_contents(resource_path('views/instructor/discussions/show.blade.php')));
        $this->assertStringNotContainsString('max-h-[500px]', file_get_contents(resource_path('views/instructor/discussions/show.blade.php')));
    }

    /** @return array{User, User, Course, Lesson} */
    private function chatContext(): array
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Chat', 'slug' => 'chat-'.uniqid()]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Realtime Chat Course',
            'slug' => 'realtime-chat-'.uniqid(),
            'short_description' => 'Short',
            'description' => 'Description',
            'thumbnail' => 'course.png',
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 1]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Lesson',
            'type' => 'video',
            'video_url' => 'https://example.com/video.mp4',
            'duration_seconds' => 60,
            'content' => 'Lesson content',
            'sort_order' => 1,
            'is_required' => true,
            'status' => 'published',
        ]);
        $this->enroll($student, $course);

        return [$student, $instructor, $course, $lesson];
    }

    private function enroll(User $student, Course $course): Enrollment
    {
        return Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);
    }

    private function createDiscussion(User $student, Course $course, Lesson $lesson, array $attributes = []): Discussion
    {
        return Discussion::create(array_merge([
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'user_id' => $student->id,
            'title' => 'Question',
            'content' => 'Question content',
            'last_message_at' => now(),
            'last_message_user_id' => $student->id,
        ], $attributes));
    }
}
