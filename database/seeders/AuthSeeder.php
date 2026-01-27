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
            ['name' => 'karyawan.view', 'display_name' => 'Lihat Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.create', 'display_name' => 'Buat Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.edit', 'display_name' => 'Edit Karyawan', 'group' => 'karyawan'],
            ['name' => 'karyawan.delete', 'display_name' => 'Hapus Karyawan', 'group' => 'karyawan'],
            ['name' => 'project.view', 'display_name' => 'Lihat Project', 'group' => 'project'],
            ['name' => 'project.create', 'display_name' => 'Buat Project', 'group' => 'project'],
            ['name' => 'project.edit', 'display_name' => 'Edit Project', 'group' => 'project'],
            ['name' => 'project.delete', 'display_name' => 'Hapus Project', 'group' => 'project'],
        ];

        foreach ($permissions as $p) {
            $permission = Permission::firstOrCreate(['name' => $p['name']], $p);
            if (in_array($p['group'], ['karyawan', 'project'])) {
                $adminRole->permissions()->syncWithoutDetaching($permission);
                $hrdRole->permissions()->syncWithoutDetaching($permission);
            }
        }

        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Super Admin',
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
