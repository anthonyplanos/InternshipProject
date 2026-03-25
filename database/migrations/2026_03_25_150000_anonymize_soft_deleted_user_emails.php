<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'email') || ! Schema::hasColumn('users', 'deleted_at')) {
            return;
        }

        DB::statement("\n            UPDATE users\n            SET email = CASE\n                    WHEN INSTR(email, '@') > 0 THEN CONCAT(\n                        SUBSTRING_INDEX(email, '@', 1),\n                        '*deleted+',\n                        id,\n                        '+',\n                        UNIX_TIMESTAMP(COALESCE(deleted_at, NOW())),\n                        '@',\n                        SUBSTRING_INDEX(email, '@', -1)\n                    )\n                    ELSE CONCAT(\n                        email,\n                        '*deleted+',\n                        id,\n                        '+',\n                        UNIX_TIMESTAMP(COALESCE(deleted_at, NOW())),\n                        '@deleted.local'\n                    )\n                END,\n                email_verified_at = NULL\n            WHERE deleted_at IS NOT NULL\n              AND email NOT LIKE '%*deleted+%@%'\n        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: original emails are intentionally anonymized for privacy and uniqueness safety.
    }
};
