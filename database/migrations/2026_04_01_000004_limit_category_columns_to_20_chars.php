<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')->whereNotNull('name')->update([
            'name' => DB::raw('LEFT(name, 20)'),
        ]);

        DB::table('posts')->whereNotNull('category')->update([
            'category' => DB::raw('LEFT(category, 20)'),
        ]);

        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE categories MODIFY name VARCHAR(20) NOT NULL');
            DB::statement('ALTER TABLE posts MODIFY category VARCHAR(20) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE categories ALTER COLUMN name TYPE VARCHAR(20)');
            DB::statement('ALTER TABLE posts ALTER COLUMN category TYPE VARCHAR(20)');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE categories MODIFY name VARCHAR(120) NOT NULL');
            DB::statement('ALTER TABLE posts MODIFY category VARCHAR(120) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE categories ALTER COLUMN name TYPE VARCHAR(120)');
            DB::statement('ALTER TABLE posts ALTER COLUMN category TYPE VARCHAR(120)');
        }
    }
};
