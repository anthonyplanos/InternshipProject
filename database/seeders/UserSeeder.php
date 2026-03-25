<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
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

        $permissions = [
            'posts.view',
            'posts.manage',
            'users.manage',
            'users.view',
            'logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('Admin', 'web');
        $staffRole = Role::findOrCreate('Staff', 'web');
        $employeeRole = Role::findOrCreate('Employee', 'web');

        $adminRole->syncPermissions($permissions);
        $staffRole->syncPermissions([
            'posts.view',
            'users.view',
            'logs.view',
        ]);
        $employeeRole->syncPermissions([]);

        $admin = User::updateOrCreate([
            'email' => 'aplanos22-0551@cca.edu.ph',
        ], [
            'name' => 'Anthony',
            'role' => 'Admin',
            'password' => Hash::make('password'),
        ]);

        $testUser = User::updateOrCreate([
            'email' => 'anthony.vshore360agency@gmail.com',
        ], [
            'name' => 'Ton',
            'role' => 'Employee',
            'password' => Hash::make('password'),
        ]);

        $admin->syncRoles(['Admin']);
        $testUser->syncRoles(['Employee']);

        $targetUsers = 10;
        $currentUsers = User::query()->count();

        if ($currentUsers < $targetUsers) {
            User::factory()
                ->count($targetUsers - $currentUsers)
                ->create()
                ->each(fn (User $user) => $user->assignRole('Employee'));
        }

        $targetUsers = 20;
        $currentUsers = User::query()->count();

        if ($currentUsers < $targetUsers) {
            User::factory()
                ->count($targetUsers - $currentUsers)
                ->create()
                ->each(fn (User $user) => $user->assignRole('Staff'));
        }
    }
}
