<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            ALTER COLUMN created_at TYPE TIMESTAMPTZ
            USING created_at AT TIME ZONE 'UTC'
        ");

        DB::statement("
            ALTER TABLE users
            ALTER COLUMN updated_at TYPE TIMESTAMPTZ
            USING updated_at AT TIME ZONE 'UTC'
        ");

        DB::statement("
            ALTER TABLE users
            ALTER COLUMN email_verified_at TYPE TIMESTAMPTZ
            USING email_verified_at AT TIME ZONE 'UTC'
        ");

        DB::statement("
            ALTER TABLE failed_jobs
            ALTER COLUMN failed_at TYPE TIMESTAMPTZ
            USING failed_at AT TIME ZONE 'UTC'
        ");

        DB::statement("
            ALTER TABLE password_reset_tokens
            ALTER COLUMN created_at TYPE TIMESTAMPTZ
            USING created_at AT TIME ZONE 'UTC'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE users
            ALTER COLUMN created_at TYPE TIMESTAMP,
            ALTER COLUMN updated_at TYPE TIMESTAMP,
            ALTER COLUMN email_verified_at TYPE TIMESTAMP
        ");

        DB::statement("
            ALTER TABLE failed_jobs
            ALTER COLUMN failed_at TYPE TIMESTAMP
        ");

        DB::statement("
            ALTER TABLE password_reset_tokens
            ALTER COLUMN created_at TYPE TIMESTAMP
        ");
    }
};
