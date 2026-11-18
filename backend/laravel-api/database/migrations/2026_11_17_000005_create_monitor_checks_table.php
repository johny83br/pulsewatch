<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitor_checks', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Also used as check_id in RabbitMQ messages.
            $table->foreignUuid('monitor_id')->constrained()->cascadeOnDelete();

            $table->string('execution_status', 20)->default('PENDING');
            $table->boolean('success')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->string('error_type', 100)->nullable();
            $table->text('error_message')->nullable();
            $table->timestampTz('checked_at')->nullable();

            $table->timestampsTz();

            $table->index(['monitor_id', 'checked_at'], 'monitor_checks_history_idx');
            $table->index(['execution_status', 'created_at'], 'monitor_checks_pending_idx');
        });

        DB::statement("
            ALTER TABLE monitor_checks
            ADD CONSTRAINT monitor_checks_execution_status_check
            CHECK (execution_status IN ('PENDING', 'COMPLETED', 'EXPIRED'))
        ");

        DB::statement("
            ALTER TABLE monitor_checks
            ADD CONSTRAINT monitor_checks_result_consistency_check
            CHECK (
                (execution_status = 'PENDING' AND success IS NULL AND checked_at IS NULL)
                OR
                (execution_status IN ('COMPLETED', 'EXPIRED') AND success IS NOT NULL AND checked_at IS NOT NULL)
            )
        ");

        DB::statement('ALTER TABLE monitor_checks ADD CONSTRAINT monitor_checks_http_status_check CHECK (status_code IS NULL OR status_code BETWEEN 100 AND 599)');
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_checks');
    }
};
