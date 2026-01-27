<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
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
                'description' => 'Manager role',
                'is_active' => true,
            ]
        );

        $hrdRole = Role::firstOrCreate(
            ['name' => 'hrd'],
            [
                'display_name' => 'HRD',
                'description' => 'Human Resource Department',
                'is_active' => true,
            ]
        );

        $employeeRole = Role::firstOrCreate(
            ['name' => 'employee'],
            [
                'display_name' => 'Employee',
                'description' => 'Standard employee access',
                'is_active' => true,
            ]
        );

        $permissions = [
            ['name' => 'karyawan.view', 'display_name' => 'View Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.create', 'display_name' => 'Create Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.edit', 'display_name' => 'Edit Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.delete', 'display_name' => 'Delete Karyawan', 'group' => 'karyawan'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        $adminRole->permissions()->sync(Permission::pluck('id'));

        $hrdRole->permissions()->attach(Permission::where('group', 'karyawan')->pluck('id'));

        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'hrd'],
            [
                'name' => 'HRD User',
                'password' => Hash::make('password'),
                'role_id' => $hrdRole->id,
                'is_active' => true,
            ]
        );

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
