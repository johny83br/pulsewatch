<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbox_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type', 100);
            $table->string('aggregate_type', 50);
            $table->uuid('aggregate_id');
            $table->jsonb('payload');
            $table->string('status', 20)->default('PENDING');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestampTz('available_at')->useCurrent();
            $table->timestampTz('published_at')->nullable();
            $table->timestampsTz();

            $table->index(
                ['status', 'available_at', 'created_at'],
                'outbox_messages_dispatch_idx'
            );
        });

        DB::statement("
            ALTER TABLE outbox_messages
            ADD CONSTRAINT outbox_messages_status_check
            CHECK (status IN ('PENDING', 'PUBLISHED', 'FAILED'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_messages');
    }
};
