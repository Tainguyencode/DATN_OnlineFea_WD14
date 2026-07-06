<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive', 'pending', 'blocked'])
                    ->default('active')
                    ->after('role');
            }

            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }
        });

        if (Schema::hasColumn('users', 'is_active')) {
            DB::table('users')->where('is_active', false)->update(['status' => 'blocked']);
        }

        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');

        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('phone');
            });
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'instructor', 'admin', 'super_admin') NOT NULL DEFAULT 'student'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone_verified_at')) {
                $table->dropColumn('phone_verified_at');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'instructor', 'admin') NOT NULL DEFAULT 'student'");
    }
};
