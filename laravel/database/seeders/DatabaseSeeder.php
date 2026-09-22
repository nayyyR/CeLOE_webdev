<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DivisionSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            TicketSeeder::class,
        ]);

        Ticket::query()->delete();

        User::query()
            ->whereNotIn('username', [
                'superadmin',
                'admin',
                'employee_it',
                'employee_akademik',
                'user',
            ])
            ->delete();

        Division::factory()->count(100)->create();

        User::factory()->count(100)->create();

        Ticket::factory()->count(100)->create();

        $this->command?->info('Generated: 100 divisions, 100 users, 100 tickets.');
    }
}
