<?php

namespace Tests\Feature;

use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MergeConflictRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        return $admin;
    }

    public function test_registered_controller_actions_exist(): void
    {
        foreach (Route::getRoutes() as $route) {
            if ($route->getActionName() === 'Closure') {
                continue;
            }
            [$class, $method] = array_pad(explode('@', $route->getActionName()), 2, '__invoke');
            $this->assertTrue(method_exists($class, $method), $route->uri().' -> '.$route->getActionName());
        }
    }

    public function test_bulk_actions_and_restore_preserve_self_and_admin_accounts(): void
    {
        $admin = $this->admin();
        $student = User::factory()->create(['role' => 'student']);
        $this->post(route('admin.users.bulk'), ['action' => 'block', 'users' => [$student->id, $admin->id]])
            ->assertSessionHasErrors('users');
        $this->assertTrue($student->fresh()->is_active);
        foreach (['block' => false, 'activate' => true] as $action => $active) {
            $this->post(route('admin.users.bulk'), ['action' => $action, 'users' => [$student->id]])->assertSessionHas('success');
            $this->assertSame($active, $student->fresh()->is_active);
        }
        $this->post(route('admin.users.bulk'), ['action' => 'delete', 'users' => [$student->id]])->assertSessionHas('success');
        $this->assertSoftDeleted($student);
        $this->post(route('admin.users.restore', $student->id))->assertSessionHas('success');
        $this->assertNotSoftDeleted($student);
    }

    public function test_force_delete_requires_trashed_non_admin(): void
    {
        $this->admin();
        $student = User::factory()->create(['role' => 'student']);
        $this->delete(route('admin.users.force-delete', $student->id))->assertNotFound();
        $student->delete();
        $this->delete(route('admin.users.force-delete', $student->id))->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $student->id]);
    }

    public function test_csv_import_validates_whole_file_and_hashes_passwords(): void
    {
        $this->admin();
        $header = "name,username,email,phone,role,password,status\n";
        $valid = "New User,new_user,new-user@example.test,,student,ValidPass123!,active\n";
        $invalid = "Bad User,bad_user,not-an-email,,student,ValidPass123!,active\n";
        $this->post(route('admin.users.import'), ['file' => UploadedFile::fake()->createWithContent('users.csv', $header.$valid.$invalid)])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.test']);
        $this->post(route('admin.users.import'), ['file' => UploadedFile::fake()->createWithContent('users.csv', $header.$valid)])
            ->assertSessionHas('success');
        $user = User::where('email', 'new-user@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('ValidPass123!', $user->password));
    }

    public function test_exports_omit_passwords_and_escape_csv_formulas(): void
    {
        $this->admin();
        $user = User::factory()->create(['name' => '=1+1', 'role' => 'student']);
        $csv = $this->get(route('admin.users.export.csv'))->assertOk()->streamedContent();
        $this->assertStringContainsString("'=1+1", $csv);
        $this->assertStringNotContainsString($user->password, $csv);
        $this->get(route('admin.users.export.pdf'))->assertOk()->assertSee($user->email);
    }

    public function test_student_cannot_use_restored_admin_endpoints(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']));
        foreach (['admin.users.export.csv', 'admin.users.export.pdf'] as $route) {
            $this->getJson(route($route))->assertForbidden();
        }
        $this->postJson(route('admin.users.bulk'))->assertForbidden();
        $this->postJson(route('admin.users.import'))->assertForbidden();
        $this->postJson(route('admin.users.restore', 1))->assertForbidden();
        $this->deleteJson(route('admin.users.force-delete', 1))->assertForbidden();
    }

    public function test_learning_path_show_redirects_to_editor_and_checks_owner(): void
    {
        $owner = User::factory()->create(['role' => 'instructor']);
        $path = LearningPath::create(['created_by' => $owner->id, 'title' => 'Demo', 'slug' => 'demo']);
        $this->actingAs($owner)->get(route('instructor.learning-paths.show', $path))
            ->assertRedirect(route('instructor.learning-paths.edit', $path));
        $this->actingAs(User::factory()->create(['role' => 'instructor']))
            ->get(route('instructor.learning-paths.show', $path))->assertForbidden();
        $this->admin();
        $this->get(route('admin.learning-paths.show', $path))->assertRedirect(route('admin.learning-paths.edit', $path));
    }
}
