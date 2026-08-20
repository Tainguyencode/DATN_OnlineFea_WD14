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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('submitted_for_review_at')->nullable()->after('instructor_status');
        });

        // Migrate existing certificates from instructor_applications if any
        if (Schema::hasTable('instructor_applications')) {
            $applications = DB::table('instructor_applications')
                ->whereNotNull('certificate_path')
                ->where('certificate_path', '!=', '')
                ->get();

            foreach ($applications as $app) {
                $user = DB::table('users')->where('id', $app->user_id)->first();
                $status = ($user && $user->instructor_status === 'approved') ? 'approved' : 'pending';

                DB::table('instructor_certificates')->insert([
                    'user_id' => $app->user_id,
                    'file_path' => $app->certificate_path,
                    'original_name' => basename($app->certificate_path),
                    'mime_type' => 'application/pdf',
                    'file_size' => 0,
                    'title' => 'Chứng chỉ ban đầu',
                    'status' => $status,
                    'uploaded_at' => $app->created_at ?? now(),
                    'created_at' => $app->created_at ?? now(),
                    'updated_at' => $app->updated_at ?? now(),
                ]);

                // If user was already pending or approved, mark submitted_for_review_at
                if ($user && in_array($user->instructor_status, ['pending', 'approved', 'rejected'], true)) {
                    DB::table('users')->where('id', $user->id)->update([
                        'submitted_for_review_at' => $app->created_at ?? now(),
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['submitted_for_review_at']);
        });
    }
};
