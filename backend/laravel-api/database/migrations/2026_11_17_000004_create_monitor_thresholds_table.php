<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitor_thresholds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('monitor_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('failure_threshold')->default(3);
            $table->unsignedInteger('recovery_threshold')->default(2);
            $table->unsignedInteger('response_time_threshold_ms')->nullable();
            $table->timestampsTz();
        });

        DB::statement('ALTER TABLE monitor_thresholds ADD CONSTRAINT monitor_thresholds_failure_positive CHECK (failure_threshold > 0)');
        DB::statement('ALTER TABLE monitor_thresholds ADD CONSTRAINT monitor_thresholds_recovery_positive CHECK (recovery_threshold > 0)');
        DB::statement('ALTER TABLE monitor_thresholds ADD CONSTRAINT monitor_thresholds_response_positive CHECK (response_time_threshold_ms IS NULL OR response_time_threshold_ms > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_thresholds');
    }
};
