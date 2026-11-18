<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Existing timestamp values are interpreted as UTC during conversion.
     */
    public function up(): void
    {
        DB::statement(<<<'SQL'
            ALTER TABLE users
                ALTER COLUMN created_at TYPE TIMESTAMPTZ
                    USING created_at AT TIME ZONE 'UTC',
                ALTER COLUMN updated_at TYPE TIMESTAMPTZ
                    USING updated_at AT TIME ZONE 'UTC',
                ALTER COLUMN email_verified_at TYPE TIMESTAMPTZ
                    USING email_verified_at AT TIME ZONE 'UTC'
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE failed_jobs
                ALTER COLUMN failed_at TYPE TIMESTAMPTZ
                    USING failed_at AT TIME ZONE 'UTC'
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE password_reset_tokens
                ALTER COLUMN created_at TYPE TIMESTAMPTZ
                    USING created_at AT TIME ZONE 'UTC'
        SQL);
    }

    public function down(): void
    {
        DB::statement(<<<'SQL'
            ALTER TABLE users
                ALTER COLUMN created_at TYPE TIMESTAMP WITHOUT TIME ZONE
                    USING created_at AT TIME ZONE 'UTC',
                ALTER COLUMN updated_at TYPE TIMESTAMP WITHOUT TIME ZONE
                    USING updated_at AT TIME ZONE 'UTC',
                ALTER COLUMN email_verified_at TYPE TIMESTAMP WITHOUT TIME ZONE
                    USING email_verified_at AT TIME ZONE 'UTC'
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE failed_jobs
                ALTER COLUMN failed_at TYPE TIMESTAMP WITHOUT TIME ZONE
                    USING failed_at AT TIME ZONE 'UTC'
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE password_reset_tokens
                ALTER COLUMN created_at TYPE TIMESTAMP WITHOUT TIME ZONE
                    USING created_at AT TIME ZONE 'UTC'
        SQL);
    }
};
