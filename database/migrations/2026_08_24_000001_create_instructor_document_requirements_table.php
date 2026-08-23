<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tạo bảng instructor_document_requirements
        if (! Schema::hasTable('instructor_document_requirements')) {
            Schema::create('instructor_document_requirements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->string('document_type', 50)->default('certificate');
                $table->string('document_title');
                $table->text('description')->nullable();
                $table->boolean('is_required')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['category_id', 'is_active', 'is_required'], 'idx_req_cat_active_req');
            });
        }

        // 2. Bổ sung category_id vào instructor_profiles
        if (Schema::hasTable('instructor_profiles') && ! Schema::hasColumn('instructor_profiles', 'category_id')) {
            Schema::table('instructor_profiles', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('user_id')->constrained('categories')->nullOnDelete();
            });
        }

        // 3. Bổ sung requirement_id vào instructor_certificates
        if (Schema::hasTable('instructor_certificates') && ! Schema::hasColumn('instructor_certificates', 'requirement_id')) {
            Schema::table('instructor_certificates', function (Blueprint $table) {
                $table->foreignId('requirement_id')->nullable()->after('user_id')->constrained('instructor_document_requirements')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('instructor_certificates') && Schema::hasColumn('instructor_certificates', 'requirement_id')) {
            Schema::table('instructor_certificates', function (Blueprint $table) {
                $table->dropConstrainedForeignId('requirement_id');
            });
        }

        if (Schema::hasTable('instructor_profiles') && Schema::hasColumn('instructor_profiles', 'category_id')) {
            Schema::table('instructor_profiles', function (Blueprint $table) {
                $table->dropConstrainedForeignId('category_id');
            });
        }

        Schema::dropIfExists('instructor_document_requirements');
    }
};
