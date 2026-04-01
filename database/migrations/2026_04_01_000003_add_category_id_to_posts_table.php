<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            if (! Schema::hasColumn('posts', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('user_id')->constrained('categories')->nullOnDelete();
            }
        });

        if (! Schema::hasColumn('posts', 'category')) {
            return;
        }

        $existingNames = DB::table('posts')
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->map(fn ($name) => trim((string) $name))
            ->filter();

        foreach ($existingNames as $name) {
            DB::table('categories')->updateOrInsert([
                'name' => $name,
            ], [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $categoryIdByName = DB::table('categories')
            ->pluck('id', 'name');

        $posts = DB::table('posts')
            ->select('id', 'category')
            ->whereNotNull('category')
            ->get();

        foreach ($posts as $post) {
            $name = trim((string) $post->category);
            $categoryId = $categoryIdByName[$name] ?? null;

            if ($name !== '' && $categoryId !== null) {
                DB::table('posts')
                    ->where('id', $post->id)
                    ->update(['category_id' => $categoryId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            if (Schema::hasColumn('posts', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });
    }
};
