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

        $hrdRole = Role::firstOrCreate(
            ['name' => 'hrd'],
            [
                'display_name' => 'HRD',
                'description' => 'Human Resources Department',
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

        $permissions = [
            // Karyawan permissions
            ['name' => 'karyawan.view', 'display_name' => 'View Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.create', 'display_name' => 'Create Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.edit', 'display_name' => 'Edit Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.delete', 'display_name' => 'Delete Karyawan', 'group' => 'karyawan'],

            // Project permissions
            ['name' => 'project.view', 'display_name' => 'View Project', 'group' => 'project'],
            ['name' => 'project.create', 'display_name' => 'Create Project', 'group' => 'project'],
            ['name' => 'project.edit', 'display_name' => 'Edit Project', 'group' => 'project'],
            ['name' => 'project.delete', 'display_name' => 'Delete Project', 'group' => 'project'],

            // Jabatan permissions
            ['name' => 'jabatan.view', 'display_name' => 'View Jabatan', 'group' => 'jabatan'],
            ['name' => 'jabatan.create', 'display_name' => 'Create Jabatan', 'group' => 'jabatan'],
            ['name' => 'jabatan.edit', 'display_name' => 'Edit Jabatan', 'group' => 'jabatan'],
            ['name' => 'jabatan.delete', 'display_name' => 'Delete Jabatan', 'group' => 'jabatan'],

            // Formasi permissions
            ['name' => 'formasi.view', 'display_name' => 'View Formasi', 'group' => 'formasi'],
            ['name' => 'formasi.create', 'display_name' => 'Create Formasi', 'group' => 'formasi'],
            ['name' => 'formasi.edit', 'display_name' => 'Edit Formasi', 'group' => 'formasi'],
            ['name' => 'formasi.delete', 'display_name' => 'Delete Formasi', 'group' => 'formasi'],

            // Izin permissions
            ['name' => 'izin.view', 'display_name' => 'View Izin', 'group' => 'izin'],
            ['name' => 'izin.create', 'display_name' => 'Create Izin', 'group' => 'izin'],
            ['name' => 'izin.edit', 'display_name' => 'Edit Izin', 'group' => 'izin'],
            ['name' => 'izin.delete', 'display_name' => 'Delete Izin', 'group' => 'izin'],
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(
                ['name' => $perm['name']],
                [
                    'display_name' => $perm['display_name'],
                    'group' => $perm['group'],
                    'description' => $perm['display_name'],
                    'is_active' => true,
                ]
            );

            $adminRole->permissions()->syncWithoutDetaching($permission->id);
            if (str_contains($perm['name'], 'view')) {
                $hrdRole->permissions()->syncWithoutDetaching($permission->id);
            } else {
                $hrdRole->permissions()->syncWithoutDetaching($permission->id);
            }
        }

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
