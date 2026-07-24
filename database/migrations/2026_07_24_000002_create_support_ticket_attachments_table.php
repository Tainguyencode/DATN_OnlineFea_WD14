<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'support_ticket_attachments',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('ticket_id')
                    ->constrained('support_tickets')
                    ->cascadeOnDelete();

                $table->foreignId('message_id')
                    ->nullable()
                    ->constrained('support_ticket_messages')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->string('file_path');
                $table->string('original_name');
                $table->string('mime_type', 150);
                $table->unsignedBigInteger('file_size');
                $table->timestamps();

                $table->index(['ticket_id', 'message_id']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_attachments');
    }
};
