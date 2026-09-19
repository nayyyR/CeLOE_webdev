<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->firstOrFail();
        $adminRole = Role::where('name', 'Admin')->firstOrFail();
        $employeeRole = Role::where('name', 'Employee')->firstOrFail();
        $userRole = Role::where('name', 'User')->firstOrFail();

        $generalDivision = Division::where('name', 'general')->firstOrFail();
        $itDivision = Division::where('name', 'IT')->firstOrFail();
        $academicDivision = Division::where('name', 'akademik')->firstOrFail();

        User::updateOrCreate(
            [
                'username' => 'superadmin',
            ],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'division_id' => $generalDivision->id,
            ]
        );

        User::updateOrCreate(
            [
                'username' => 'admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'division_id' => $generalDivision->id,
            ]
        );

        User::updateOrCreate(
            [
                'username' => 'employee_it',
            ],
            [
                'name' => 'Employee IT',
                'email' => 'employee.it@example.com',
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
                'division_id' => $itDivision->id,
            ]
        );

        User::updateOrCreate(
            [
                'username' => 'employee_akademik',
            ],
            [
                'name' => 'Employee Akademik',
                'email' => 'employee.akademik@example.com',
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
                'division_id' => $academicDivision->id,
            ]
        );

        User::updateOrCreate(
            [
                'username' => 'user',
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role_id' => $userRole->id,
                'division_id' => $generalDivision->id,
            ]
        );
    }
}