<?php

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
        Schema::table('instructor_profile_teaching_fields', function (Blueprint $table) {
            $table->string('organization')->nullable()->after('category_id');
            $table->string('position')->nullable()->after('organization');
            $table->string('specialty')->nullable()->after('position');
            $table->text('experience')->nullable()->after('specialty');
        });

        // Copy existing profile details to primary teaching field
        $profiles = DB::table('instructor_profiles')->get();
        foreach ($profiles as $profile) {
            DB::table('instructor_profile_teaching_fields')
                ->where('instructor_profile_id', $profile->id)
                ->where('is_primary', true)
                ->update([
                    'organization' => $profile->organization,
                    'position' => $profile->position,
                    'specialty' => $profile->specialty,
                    'experience' => $profile->experience,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_profile_teaching_fields', function (Blueprint $table) {
            $table->dropColumn(['organization', 'position', 'specialty', 'experience']);
        });
    }
};
