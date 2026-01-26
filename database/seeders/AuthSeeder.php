<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles (use firstOrCreate to avoid duplicates)
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'System administrator with full access',
                'is_active' => true,
            ]
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Project manager with limited access',
                'is_active' => true,
            ]
        );

        $employeeRole = Role::firstOrCreate(
            ['name' => 'employee'],
            [
                'display_name' => 'Employee',
                'description' => 'Regular employee',
                'is_active' => true,
            ]
        );

        // Create Permissions
        $permissions = [
            // User management
            ['name' => 'user.view', 'display_name' => 'View Users', 'group' => 'user'],
            ['name' => 'user.create', 'display_name' => 'Create User', 'group' => 'user'],
            ['name' => 'user.update', 'display_name' => 'Update User', 'group' => 'user'],
            ['name' => 'user.delete', 'display_name' => 'Delete User', 'group' => 'user'],

            // Role management
            ['name' => 'role.view', 'display_name' => 'View Roles', 'group' => 'role'],
            ['name' => 'role.create', 'display_name' => 'Create Role', 'group' => 'role'],
            ['name' => 'role.update', 'display_name' => 'Update Role', 'group' => 'role'],
            ['name' => 'role.delete', 'display_name' => 'Delete Role', 'group' => 'role'],

            // Employee management
            ['name' => 'employee.view', 'display_name' => 'View Employees', 'group' => 'employee'],
            ['name' => 'employee.create', 'display_name' => 'Create Employee', 'group' => 'employee'],
            ['name' => 'employee.update', 'display_name' => 'Update Employee', 'group' => 'employee'],
            ['name' => 'employee.delete', 'display_name' => 'Delete Employee', 'group' => 'employee'],
        ];

        foreach ($permissions as $permData) {
            Permission::firstOrCreate(
                ['name' => $permData['name']],
                array_merge($permData, ['is_active' => true])
            );
        }

        // Assign all permissions to admin (if not already assigned)
        if ($adminRole->permissions()->count() === 0) {
            $adminRole->permissions()->attach(Permission::all());
        }

        // Assign limited permissions to manager
        if ($managerRole->permissions()->count() === 0) {
            $managerRole->permissions()->attach(
                Permission::whereIn('name', [
                    'user.view',
                    'employee.view',
                    'employee.create',
                    'employee.update',
                ])->get()
            );
        }

        // Create default admin user
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        // Create manager user
        User::firstOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id,
                'is_active' => true,
            ]
        );

        // Create employee user
        User::firstOrCreate(
            ['username' => 'employee'],
            [
                'name' => 'Employee User',
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
                'is_active' => true,
            ]
        );
    }
}
