<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $managerRole = Role::create(['name' => 'Manager', 'slug' => 'manager']);
        $employeeRole = Role::create(['name' => 'Employee', 'slug' => 'employee']);

        // Assign all permissions to admin
        $adminRole->permissions()->attach(Permission::all());

        // Assign some permissions to manager
        $managerPermissions = Permission::whereIn('slug', ['view_profit', 'manage_products', 'manage_sales', 'manage_inventory', 'manage_expenses', 'manage_cashboxes'])->get();
        $managerRole->permissions()->attach($managerPermissions);

        // Assign limited permissions to employee
        $employeePermissions = Permission::whereIn('slug', ['manage_sales', 'manage_inventory'])->get();
        $employeeRole->permissions()->attach($employeePermissions);
    }
}
