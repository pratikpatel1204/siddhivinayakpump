<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignPermissionsToAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure permissions exist
        $permissions = ['view', 'create', 'edit', 'delete'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Find or create the 'admin' role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Assign permissions to the 'admin' role
        $adminRole->givePermissionTo($permissions);
    }
}