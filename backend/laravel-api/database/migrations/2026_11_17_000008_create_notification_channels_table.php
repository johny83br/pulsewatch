<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->string('name', 100);
            $table->jsonb('configuration');
            $table->boolean('enabled')->default(true);
            $table->timestampsTz();

            $table->index(['user_id', 'enabled']);
        });

        DB::statement("
            ALTER TABLE notification_channels
            ADD CONSTRAINT notification_channels_type_check
            CHECK (type IN ('EMAIL', 'TELEGRAM', 'DISCORD', 'WEBHOOK'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_channels');
    }
};
