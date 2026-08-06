<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'facebook_id')) {
                $table->string('facebook_id')->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'github_id')) {
                $table->string('github_id')->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'microsoft_id')) {
                $table->string('microsoft_id')->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }

            if (! Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable();
            }

            if (! Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable();
            }

            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('activity_logs', 'description')) {
                    $table->text('description')->nullable();
                }
                if (! Schema::hasColumn('activity_logs', 'properties')) {
                    $table->json('properties')->nullable();
                }
                if (! Schema::hasColumn('activity_logs', 'ip_address')) {
                    $table->ipAddress('ip_address')->nullable();
                }
                if (! Schema::hasColumn('activity_logs', 'user_agent')) {
                    $table->text('user_agent')->nullable();
                }
            });
        }

        if (Schema::hasTable('push_notifications')) {
            Schema::table('push_notifications', function (Blueprint $table) {
                if (! Schema::hasColumn('push_notifications', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('push_notifications', 'title')) {
                    $table->string('title')->nullable();
                }
                if (! Schema::hasColumn('push_notifications', 'message')) {
                    $table->text('message')->nullable();
                }
                if (! Schema::hasColumn('push_notifications', 'type')) {
                    $table->string('type')->nullable();
                }
                if (! Schema::hasColumn('push_notifications', 'url')) {
                    $table->string('url')->nullable();
                }
                if (! Schema::hasColumn('push_notifications', 'is_read')) {
                    $table->boolean('is_read')->default(false);
                }
                if (! Schema::hasColumn('push_notifications', 'read_at')) {
                    $table->timestamp('read_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'username',
                'google_id',
                'facebook_id',
                'github_id',
                'microsoft_id',
                'last_login_at',
                'last_login_ip',
                'password_changed_at',
                'deleted_at',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
