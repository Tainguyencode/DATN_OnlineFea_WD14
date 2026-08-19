<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->enum('creator_type', ['admin', 'instructor'])->default('admin')->after('code');
            $table->foreignId('instructor_id')->nullable()->after('creator_type')->constrained('users')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->after('instructor_id')->constrained('courses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropForeign(['course_id']);
            $table->dropColumn(['creator_type', 'instructor_id', 'course_id']);
        });
    }
};
