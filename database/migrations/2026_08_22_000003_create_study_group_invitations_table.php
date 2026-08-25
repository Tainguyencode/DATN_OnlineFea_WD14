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
        Schema::create('study_group_invitations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('study_group_id')
                ->constrained('study_groups')
                ->cascadeOnDelete();

            $table->foreignId('invited_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('invited_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled', 'expired'])
                ->default('pending');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            // Prevent duplicate pending invitations for same group and user
            $table->index(['study_group_id', 'invited_user_id', 'status'], 'sg_invites_group_user_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_group_invitations');
    }
};
