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
        Schema::table('instructor_certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_certificates', 'verification_method')) {
                $table->string('verification_method', 50)->nullable()->after('document_type');
            }
            if (! Schema::hasColumn('instructor_certificates', 'diploma_number')) {
                $table->string('diploma_number', 100)->nullable()->after('verification_method');
            }
            if (! Schema::hasColumn('instructor_certificates', 'book_reg_number')) {
                $table->string('book_reg_number', 100)->nullable()->after('diploma_number');
            }
            if (! Schema::hasColumn('instructor_certificates', 'lookup_url')) {
                $table->text('lookup_url')->nullable()->after('book_reg_number');
            }
            if (! Schema::hasColumn('instructor_certificates', 'supplementary_proof_type')) {
                $table->string('supplementary_proof_type', 50)->nullable()->after('lookup_url');
            }
            if (! Schema::hasColumn('instructor_certificates', 'supplementary_file_path')) {
                $table->string('supplementary_file_path', 255)->nullable()->after('supplementary_proof_type');
            }
            if (! Schema::hasColumn('instructor_certificates', 'credential_url')) {
                $table->text('credential_url')->nullable()->after('supplementary_file_path');
            }
        });

        Schema::table('instructor_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_profiles', 'id_card_number')) {
                $table->string('id_card_number', 50)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('instructor_profiles', 'id_card_name')) {
                $table->string('id_card_name', 255)->nullable()->after('id_card_number');
            }
            if (! Schema::hasColumn('instructor_profiles', 'id_card_front_path')) {
                $table->string('id_card_front_path', 255)->nullable()->after('id_card_name');
            }
            if (! Schema::hasColumn('instructor_profiles', 'id_card_back_path')) {
                $table->string('id_card_back_path', 255)->nullable()->after('id_card_front_path');
            }
            if (! Schema::hasColumn('instructor_profiles', 'portrait_image_path')) {
                $table->string('portrait_image_path', 255)->nullable()->after('id_card_back_path');
            }
            if (! Schema::hasColumn('instructor_profiles', 'identity_verified_at')) {
                $table->timestamp('identity_verified_at')->nullable()->after('portrait_image_path');
            }
            if (! Schema::hasColumn('instructor_profiles', 'commitment_agreed')) {
                $table->boolean('commitment_agreed')->default(false)->after('agree_terms');
            }
            if (! Schema::hasColumn('instructor_profiles', 'commitment_agreed_at')) {
                $table->timestamp('commitment_agreed_at')->nullable()->after('commitment_agreed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_certificates', function (Blueprint $table) {
            $columns = [
                'verification_method',
                'diploma_number',
                'book_reg_number',
                'lookup_url',
                'supplementary_proof_type',
                'supplementary_file_path',
                'credential_url',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('instructor_certificates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('instructor_profiles', function (Blueprint $table) {
            $columns = [
                'id_card_number',
                'id_card_name',
                'id_card_front_path',
                'id_card_back_path',
                'portrait_image_path',
                'identity_verified_at',
                'commitment_agreed',
                'commitment_agreed_at',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('instructor_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
