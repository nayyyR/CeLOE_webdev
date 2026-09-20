<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'manage_users',
                'description' => 'Mengelola data pengguna.',
            ],
            [
                'name' => 'manage_roles',
                'description' => 'Mengelola role pengguna.',
            ],
            [
                'name' => 'manage_divisions',
                'description' => 'Mengelola divisi organisasi.',
            ],
            [
                'name' => 'manage_permissions',
                'description' => 'Mengelola permission dan assignment permission ke role.',
            ],
            [
                'name' => 'manage_tickets',
                'description' => 'Mengelola seluruh tiket.',
            ],
            [
                'name' => 'create_tickets',
                'description' => 'Membuat tiket baru.',
            ],
            [
                'name' => 'view_all_tickets',
                'description' => 'Melihat seluruh tiket.',
            ],
            [
                'name' => 'view_assigned_tickets',
                'description' => 'Melihat tiket yang ditugaskan kepada Employee.',
            ],
            [
                'name' => 'assign_tickets',
                'description' => 'Mengassign tiket kepada Employee.',
            ],
            [
                'name' => 'resolve_tickets',
                'description' => 'Menyelesaikan tiket.',
            ],
            [
                'name' => 'reply_tickets',
                'description' => 'Membalas thread tiket.',
            ],
            [
                'name' => 'confirm_resolution',
                'description' => 'Mengonfirmasi bahwa penyelesaian tiket sudah benar.',
            ],
            [
                'name' => 'reject_resolution',
                'description' => 'Menolak penyelesaian tiket dan mengembalikannya ke proses pengerjaan.',
            ],
            [
                'name' => 'view_activity_logs',
                'description' => 'Melihat riwayat aktivitas tiket.',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission['name'],
                ],
                [
                    'description' => $permission['description'],
                ]
            );
        }

        $superAdmin = Role::where('name', 'Super Admin')->firstOrFail();

        $admin = Role::where('name', 'Admin')->firstOrFail();

        $employee = Role::where('name', 'Employee')->firstOrFail();

        $user = Role::where('name', 'User')->firstOrFail();

        $allPermissions = Permission::all();

        $superAdmin->permissions()->sync(
            $allPermissions->pluck('id')
        );

        $admin->permissions()->sync(
            Permission::whereIn('name', [
                'manage_users',
                'view_all_tickets',
                'assign_tickets',
                'view_activity_logs',
            ])->pluck('id')
        );

        $employee->permissions()->sync(
            Permission::whereIn('name', [
                'view_assigned_tickets',
                'resolve_tickets',
                'reply_tickets',
                'view_activity_logs',
            ])->pluck('id')
        );

        $user->permissions()->sync(
            Permission::whereIn('name', [
                'create_tickets',
                'reply_tickets',
                'confirm_resolution',
                'reject_resolution',
            ])->pluck('id')
        );
    }
}
