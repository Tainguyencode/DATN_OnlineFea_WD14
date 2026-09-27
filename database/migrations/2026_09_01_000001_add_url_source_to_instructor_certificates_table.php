<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_certificates', 'source_type')) {
                $table->string('source_type', 10)->default('file')->after('requirement_id');
            }

            if (! Schema::hasColumn('instructor_certificates', 'document_url')) {
                $table->text('document_url')->nullable()->after('file_path');
            }

            // URL evidence has no local file metadata. Existing file records retain their values.
            $table->string('file_path')->nullable()->change();
            $table->string('original_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        $hasUrlOnlyRecords = Schema::hasColumn('instructor_certificates', 'source_type')
            && DB::table('instructor_certificates')
                ->where(function ($query) {
                    $query->where('source_type', 'url')
                        ->orWhereNull('file_path')
                        ->orWhereNull('original_name');
                })
                ->exists();

        if ($hasUrlOnlyRecords) {
            throw new RuntimeException('Cannot roll back URL certificate support while URL-only records exist. Export or remove those records explicitly first.');
        }

        Schema::table('instructor_certificates', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'document_url']);
            $table->string('file_path')->nullable(false)->change();
            $table->string('original_name')->nullable(false)->change();
        });
    }
};
