<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('user', 'web');

        $admin = User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
        ]);

        $testUser = User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
        ]);

        $admin->syncRoles(['admin']);
        $testUser->syncRoles(['user']);

        $targetUsers = 10;
        $currentUsers = User::query()->count();

        if ($currentUsers < $targetUsers) {
            User::factory()
                ->count($targetUsers - $currentUsers)
                ->create()
                ->each(fn (User $user) => $user->assignRole('user'));
        }
    }
}
