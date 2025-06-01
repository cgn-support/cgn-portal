<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Optional: if you also want to create permissions here

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define your application's roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'account_manager', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'client_user', 'guard_name' => 'web']);
        // Add any other roles you might need, e.g., 'editor', 'viewer'

        // Example: If you want to create some basic permissions and assign them (optional here)
        // Permission::firstOrCreate(['name' => 'manage projects', 'guard_name' => 'web']);
        // Permission::firstOrCreate(['name' => 'view own projects', 'guard_name' => 'web']);

        // $adminRole = Role::findByName('admin');
        // $adminRole->givePermissionTo(Permission::all()); // Example

        // $amRole = Role::findByName('account_manager');
        // $amRole->givePermissionTo('manage projects');

        // $clientRole = Role::findByName('client_user');
        // $clientRole->givePermissionTo('view own projects');
    }
}
