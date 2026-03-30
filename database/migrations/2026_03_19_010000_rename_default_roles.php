<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')
            ->where('name', 'admin')
            ->update(['name' => 'Admin']);

        DB::table('roles')
            ->where('name', 'user')
            ->update(['name' => 'User']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'Admin')
            ->update(['name' => 'admin']);

        DB::table('roles')
            ->where('name', 'User')
            ->update(['name' => 'user']);
    }
};
