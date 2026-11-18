<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('group_id')->nullable();

            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->text('url');
            $table->string('method', 10)->default('GET');
            $table->jsonb('headers')->nullable();
            $table->jsonb('request_body')->nullable();
            // JSONB is used instead of SMALLINT[] for convenient Laravel validation.
            $table->jsonb('expected_status_codes')->default('[200]');

            $table->string('status', 20)->default('ACTIVE');
            $table->string('current_state', 20)->default('UNKNOWN');
            $table->unsignedInteger('interval_seconds')->default(60);
            $table->unsignedInteger('timeout_ms')->default(5000);

            $table->timestampTz('next_check_at')->nullable();
            $table->timestampTz('last_check_at')->nullable();
            $table->unsignedInteger('consecutive_failures')->default(0);
            $table->unsignedInteger('consecutive_successes')->default(0);

            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['status', 'next_check_at'], 'monitors_schedule_idx');
            $table->index(['user_id', 'status'], 'monitors_user_status_idx');
            $table->index('group_id');
        });

        // Ensures a monitor can only reference a group owned by the same user.
        DB::statement('
            ALTER TABLE monitors
            ADD CONSTRAINT monitors_group_user_fk
            FOREIGN KEY (group_id, user_id)
            REFERENCES monitor_groups (id, user_id)
            ON DELETE SET NULL (group_id)
        ');

        DB::statement("
            ALTER TABLE monitors
            ADD CONSTRAINT monitors_status_check
            CHECK (status IN ('ACTIVE', 'PAUSED'))
        ");

        DB::statement("
            ALTER TABLE monitors
            ADD CONSTRAINT monitors_current_state_check
            CHECK (current_state IN ('UNKNOWN', 'UP', 'DOWN', 'DEGRADED'))
        ");

        DB::statement("
            ALTER TABLE monitors
            ADD CONSTRAINT monitors_method_check
            CHECK (method IN ('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'))
        ");

        DB::statement('ALTER TABLE monitors ADD CONSTRAINT monitors_interval_positive CHECK (interval_seconds > 0)');
        DB::statement('ALTER TABLE monitors ADD CONSTRAINT monitors_timeout_positive CHECK (timeout_ms > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
