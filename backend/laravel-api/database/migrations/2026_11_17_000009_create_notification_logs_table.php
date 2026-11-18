<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('channel_id')->constrained('notification_channels')->restrictOnDelete();
            $table->string('event_type', 50);
            $table->string('status', 20)->default('PENDING');
            $table->unsignedInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestampTz('sent_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['incident_id', 'created_at'], 'notification_logs_incident_idx');
            $table->index(['status', 'created_at'], 'notification_logs_status_idx');
        });

        DB::statement("
            ALTER TABLE notification_logs
            ADD CONSTRAINT notification_logs_status_check
            CHECK (status IN ('PENDING', 'SENT', 'FAILED'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
