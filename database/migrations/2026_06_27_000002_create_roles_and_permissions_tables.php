<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('group')->default('system');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Toàn quyền quản trị hệ thống.', 'is_system' => true],
            ['name' => 'Instructor', 'slug' => 'instructor', 'description' => 'Quản lý khóa học và học viên.', 'is_system' => true],
            ['name' => 'Student', 'slug' => 'student', 'description' => 'Học viên sử dụng nền tảng.', 'is_system' => true],
            ['name' => 'User', 'slug' => 'user', 'description' => 'Vai trò cơ bản cho tài khoản mới.', 'is_system' => true],
        ];

        $permissions = [
            ['group' => 'users', 'name' => 'Xem người dùng', 'slug' => 'users.view'],
            ['group' => 'users', 'name' => 'Thêm người dùng', 'slug' => 'users.create'],
            ['group' => 'users', 'name' => 'Sửa người dùng', 'slug' => 'users.update'],
            ['group' => 'users', 'name' => 'Xóa người dùng', 'slug' => 'users.delete'],
            ['group' => 'users', 'name' => 'Restore người dùng', 'slug' => 'users.restore'],
            ['group' => 'users', 'name' => 'Force delete người dùng', 'slug' => 'users.force_delete'],
            ['group' => 'users', 'name' => 'Import người dùng', 'slug' => 'users.import'],
            ['group' => 'users', 'name' => 'Export người dùng', 'slug' => 'users.export'],
            ['group' => 'roles', 'name' => 'Xem vai trò', 'slug' => 'roles.view'],
            ['group' => 'roles', 'name' => 'Thêm vai trò', 'slug' => 'roles.create'],
            ['group' => 'roles', 'name' => 'Sửa vai trò', 'slug' => 'roles.update'],
            ['group' => 'roles', 'name' => 'Xóa vai trò', 'slug' => 'roles.delete'],
            ['group' => 'courses', 'name' => 'Duyệt khóa học', 'slug' => 'courses.approve'],
            ['group' => 'audit', 'name' => 'Xem audit log', 'slug' => 'audit.view'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([...$role, 'created_at' => now(), 'updated_at' => now()]);
        }

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([...$permission, 'created_at' => now(), 'updated_at' => now()]);
        }

        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');
        $permissionIds = DB::table('permissions')->pluck('id');

        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
