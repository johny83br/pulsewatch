<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('incident_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 50);
            $table->text('description');
            $table->jsonb('metadata')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['incident_id', 'created_at'], 'incident_events_timeline_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_events');
    }
};
