<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('support_tickets', 'code')) {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->string('code', 32)->nullable()->after('id');
            });
        }

        if (! Schema::hasColumn('support_tickets', 'category')) {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->string('category', 50)->default('other')->after('subject');
            });
        }

        if (! Schema::hasColumn('support_tickets', 'assigned_to')) {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->foreignId('assigned_to')->nullable()->after('priority')->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('support_tickets', 'last_replied_at')) {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->timestamp('last_replied_at')->nullable()->after('assigned_to');
            });
        }

        if (! Schema::hasColumn('support_tickets', 'last_replied_by')) {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->foreignId('last_replied_by')->nullable()->after('last_replied_at')->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('support_tickets', 'resolved_at')) {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->timestamp('resolved_at')->nullable()->after('last_replied_by');
            });
        }

        if (! Schema::hasColumn('support_tickets', 'closed_at')) {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->timestamp('closed_at')->nullable()->after('resolved_at');
            });
        }

        // Sinh mã duy nhất cho các ticket cũ.
        DB::table('support_tickets')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->each(function (object $ticket): void {
                do {
                    $code = sprintf(
                        'TK-%s-%s',
                        now()->format('Y'),
                        Str::upper(Str::random(8))
                    );
                } while (
                    DB::table('support_tickets')
                        ->where('code', $code)
                        ->exists()
                );

                DB::table('support_tickets')
                    ->where('id', $ticket->id)
                    ->update([
                        'code' => $code,
                        'category' => 'other',
                    ]);
            });

        // Chỉ thêm unique sau khi đã backfill dữ liệu cũ.
        try {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->unique(
                    'code',
                    'support_tickets_code_unique'
                );
            });
        } catch (Throwable $e) {
            // Already unique
        }
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table): void {
            $table->dropUnique('support_tickets_code_unique');

            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['last_replied_by']);

            $table->dropColumn([
                'code',
                'category',
                'assigned_to',
                'last_replied_at',
                'last_replied_by',
                'resolved_at',
                'closed_at',
            ]);
        });
    }
};
