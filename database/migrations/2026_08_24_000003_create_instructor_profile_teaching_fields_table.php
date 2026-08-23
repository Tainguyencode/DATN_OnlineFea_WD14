<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('instructor_profile_teaching_fields')) {
            Schema::create('instructor_profile_teaching_fields', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_profile_id')->constrained('instructor_profiles')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['instructor_profile_id', 'category_id'], 'uq_inst_profile_category');
                $table->index(['category_id', 'instructor_profile_id'], 'idx_cat_inst_profile');
            });
        }

        // Tự động migrate dữ liệu cũ từ instructor_profiles sang bảng pivot mới
        $profiles = DB::table('instructor_profiles')->get();
        foreach ($profiles as $profile) {
            $catId = $profile->category_id;

            // Fallback: nếu category_id null nhưng có chuỗi teaching_field
            if (! $catId && ! empty($profile->teaching_field)) {
                $foundCat = DB::table('categories')->where('name', $profile->teaching_field)->first();
                if ($foundCat) {
                    $catId = $foundCat->id;
                }
            }

            if ($catId) {
                $exists = DB::table('instructor_profile_teaching_fields')
                    ->where('instructor_profile_id', $profile->id)
                    ->where('category_id', $catId)
                    ->exists();

                if (! $exists) {
                    DB::table('instructor_profile_teaching_fields')->insert([
                        'instructor_profile_id' => $profile->id,
                        'category_id' => $catId,
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_profile_teaching_fields');
    }
};
