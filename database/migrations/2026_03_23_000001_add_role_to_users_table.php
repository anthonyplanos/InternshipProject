<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->after('email');
            });
        }

        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $rolesTable = $tableNames['roles'] ?? 'roles';
        $modelHasRolesTable = $tableNames['model_has_roles'] ?? 'model_has_roles';
        $modelMorphKey = $columnNames['model_morph_key'] ?? 'model_id';
        $rolePivotKey = $columnNames['role_pivot_key'] ?? 'role_id';

        if (! Schema::hasTable($rolesTable) || ! Schema::hasTable($modelHasRolesTable)) {
            return;
        }

        $roleSnapshots = DB::table('users')
            ->leftJoin("{$modelHasRolesTable} as mhr", function ($join) use ($modelMorphKey) {
                $join->on('users.id', '=', "mhr.{$modelMorphKey}")
                    ->where('mhr.model_type', '=', 'App\\Models\\User');
            })
            ->leftJoin("{$rolesTable} as roles", "roles.id", '=', "mhr.{$rolePivotKey}")
            ->select('users.id', DB::raw('MIN(roles.name) as role_name'))
            ->groupBy('users.id')
            ->get();

        foreach ($roleSnapshots as $snapshot) {
            DB::table('users')
                ->where('id', $snapshot->id)
                ->update(['role' => $snapshot->role_name]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
