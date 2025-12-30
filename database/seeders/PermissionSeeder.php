<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Products
            ['name' => 'view_products', 'display_name' => 'View Products'],
            ['name' => 'create_products', 'display_name' => 'Create Products'],
            ['name' => 'edit_products', 'display_name' => 'Edit Products'],
            ['name' => 'delete_products', 'display_name' => 'Delete Products'],

            // Customers
            ['name' => 'view_customers', 'display_name' => 'View Customers'],
            ['name' => 'create_customers', 'display_name' => 'Create Customers'],
            ['name' => 'edit_customers', 'display_name' => 'Edit Customers'],
            ['name' => 'delete_customers', 'display_name' => 'Delete Customers'],

            // Suppliers
            ['name' => 'view_suppliers', 'display_name' => 'View Suppliers'],
            ['name' => 'create_suppliers', 'display_name' => 'Create Suppliers'],
            ['name' => 'edit_suppliers', 'display_name' => 'Edit Suppliers'],
            ['name' => 'delete_suppliers', 'display_name' => 'Delete Suppliers'],

            // Purchase Invoices
            ['name' => 'view-purchase-invoices', 'display_name' => 'View Purchase Invoices'],
            ['name' => 'create-purchase-invoices', 'display_name' => 'Create Purchase Invoices'],
            ['name' => 'edit-purchase-invoices', 'display_name' => 'Edit Purchase Invoices'],
            ['name' => 'delete-purchase-invoices', 'display_name' => 'Delete Purchase Invoices'],

            // Sales Invoices
            ['name' => 'view-sales-invoices', 'display_name' => 'View Sales Invoices'],
            ['name' => 'create-sales-invoices', 'display_name' => 'Create Sales Invoices'],
            ['name' => 'edit-sales-invoices', 'display_name' => 'Edit Sales Invoices'],
            ['name' => 'delete-sales-invoices', 'display_name' => 'Delete Sales Invoices'],

            // Inventory
            ['name' => 'view_inventory', 'display_name' => 'View Inventory'],
            ['name' => 'manage_inventory', 'display_name' => 'Manage Inventory'],

            // Profit Reports
            ['name' => 'view_profit', 'display_name' => 'View Profit Reports'],

            // Expenses
            ['name' => 'view_expenses', 'display_name' => 'View Expenses'],
            ['name' => 'manage_expenses', 'display_name' => 'Manage Expenses'],

            // Cashboxes
            ['name' => 'view_cashboxes', 'display_name' => 'View Cashboxes'],
            ['name' => 'manage_cashboxes', 'display_name' => 'Manage Cashboxes'],

            // Debts
            ['name' => 'view_debts', 'display_name' => 'View Debts'],
            ['name' => 'manage_debts', 'display_name' => 'Manage Debts'],

            // Users
            ['name' => 'view_users', 'display_name' => 'View Users'],
            ['name' => 'create_users', 'display_name' => 'Create Users'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users'],

            // Roles
            ['name' => 'view_roles', 'display_name' => 'View Roles'],
            ['name' => 'create_roles', 'display_name' => 'Create Roles'],
            ['name' => 'edit_roles', 'display_name' => 'Edit Roles'],
            ['name' => 'delete_roles', 'display_name' => 'Delete Roles'],

            // Permissions
            ['name' => 'view_permissions', 'display_name' => 'View Permissions'],
            ['name' => 'create_permissions', 'display_name' => 'Create Permissions'],
            ['name' => 'edit_permissions', 'display_name' => 'Edit Permissions'],
            ['name' => 'delete_permissions', 'display_name' => 'Delete Permissions'],

            // Sales (general)
            ['name' => 'manage_sales', 'display_name' => 'Manage Sales'],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
                'guard_name' => 'web',
            ]);
        }
    }
}
