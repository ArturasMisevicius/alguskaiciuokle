<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => 'admin123',
                'initial_password' => 'admin123',
            ]
        );

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && ! $admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }

        // Create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Regular User',
                'password' => 'user123',
                'initial_password' => 'user123',
            ]
        );

        // Assign user role
        $userRole = Role::where('name', 'user')->first();
        if ($userRole && ! $user->roles()->where('role_id', $userRole->id)->exists()) {
            $user->roles()->attach($userRole->id);
        }

        // Create 10 worker accounts
        $userRole = Role::where('name', 'user')->first();
        for ($i = 1; $i <= 10; $i++) {
            $password = "worker{$i}pass";
            $worker = User::firstOrCreate(
                ['email' => "worker{$i}@algus.com"],
                [
                    'name' => "Worker {$i}",
                    'password' => $password,
                    'initial_password' => $password,
                ]
            );

            // Assign user role to worker
            if ($userRole && ! $worker->roles()->where('role_id', $userRole->id)->exists()) {
                $worker->roles()->attach($userRole->id);
            }
        }
    }
}
