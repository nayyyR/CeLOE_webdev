<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Division;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('name', 'User');
        })->whereHas('division', function ($query) {
            $query->where('name', 'general');
        })->firstOrFail();

        $targetDivision = Division::where('name', 'IT')->where('name', '!=', 'general')->firstOrFail();

        $employee = User::whereHas('role', function ($query) {
            $query->where('name', 'Employee');
        })->where('division_id', $targetDivision->id)->firstOrFail();

        $openTicket = Ticket::create([
            'ticket_number' => 'TKT-000001',
            'subject' => 'Tidak dapat login ke sistem',
            'description' => 'User tidak dapat login menggunakan akun yang valid.',
            'priority' => 'high',
            'status' => 'open',
            'created_by' => $user->id,
            'target_division_id' => $targetDivision->id,
            'assigned_employee_id' => null,
            'resolved_at' => null,
            'closed_at' => null,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        TicketThread::create([
            'ticket_id' => $openTicket->id,
            'user_id' => $user->id,
            'body' => 'Saya tidak dapat login ke sistem meskipun username dan password sudah benar.',
            'attachments' => [],
        ]);

        ActivityLog::create([
            'ticket_id' => $openTicket->id,
            'user_id' => $user->id,
            'action' => 'ticket_created',
            'old_status' => null,
            'new_status' => 'open',
            'metadata' => [
                'target_division_id' => $targetDivision->id,
            ],
        ]);

        $inProgressTicket = Ticket::create([
            'ticket_number' => 'TKT-000002',
            'subject' => 'Aplikasi mengalami error',
            'description' => 'Aplikasi menampilkan error ketika user membuka halaman dashboard.',
            'priority' => 'urgent',
            'status' => 'in_progress',
            'created_by' => $user->id,
            'target_division_id' => $targetDivision->id,
            'assigned_employee_id' => $employee->id,
            'resolved_at' => null,
            'closed_at' => null,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        TicketThread::create([
            'ticket_id' => $inProgressTicket->id,
            'user_id' => $user->id,
            'body' => 'Aplikasi menampilkan error ketika saya membuka dashboard.',
            'attachments' => [],
        ]);

        $admin = User::whereHas('role', function ($query) {
            $query->where('name', 'Admin');
        })->whereHas('division', function ($query) {
            $query->where('name', 'general');
        })->firstOrFail();

        ActivityLog::create([
            'ticket_id' => $inProgressTicket->id,
            'user_id' => $admin->id,
            'action' => 'ticket_assigned',
            'old_status' => 'open',
            'new_status' => 'in_progress',
            'metadata' => [
                'assigned_employee_id' => $employee->id,
                'target_division_id' => $targetDivision->id,
            ],
        ]);

        TicketThread::create([
            'ticket_id' => $inProgressTicket->id,
            'user_id' => $employee->id,
            'body' => 'Ticket sudah diterima. Saya akan melakukan pengecekan terhadap masalah tersebut.',
            'attachments' => [],
        ]);
    }
}
