<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('monitor_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('severity', 20)->default('MEDIUM');
            $table->string('status', 20)->default('OPEN');
            $table->timestampTz('started_at');
            $table->timestampTz('acknowledged_at')->nullable();
            $table->timestampTz('resolved_at')->nullable();
            $table->timestampsTz();

            $table->index(['monitor_id', 'started_at'], 'incidents_monitor_started_idx');
            $table->index(['status', 'started_at'], 'incidents_status_started_idx');
        });

        DB::statement("
            ALTER TABLE incidents
            ADD CONSTRAINT incidents_status_check
            CHECK (status IN ('OPEN', 'ACKNOWLEDGED', 'RESOLVED'))
        ");

        DB::statement("
            ALTER TABLE incidents
            ADD CONSTRAINT incidents_severity_check
            CHECK (severity IN ('LOW', 'MEDIUM', 'HIGH', 'CRITICAL'))
        ");

        DB::statement("
            ALTER TABLE incidents
            ADD CONSTRAINT incidents_resolution_check
            CHECK (
                (status = 'RESOLVED' AND resolved_at IS NOT NULL)
                OR
                (status IN ('OPEN', 'ACKNOWLEDGED') AND resolved_at IS NULL)
            )
        ");

        // Only one unresolved incident may exist per monitor.
        DB::statement("
            CREATE UNIQUE INDEX incidents_one_active_per_monitor
            ON incidents (monitor_id)
            WHERE status IN ('OPEN', 'ACKNOWLEDGED')
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
