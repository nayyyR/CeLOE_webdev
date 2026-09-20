<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;

class AutoCloseResolvedTickets extends Command
{
    protected $signature = 'tickets:auto-close';

    protected $description = 'Automatically close resolved tickets after 3 days of inactivity';

    public function handle(): int
    {
        $systemUser = User::where('email', 'system@celoe.local')->first();

        if (! $systemUser) {
            $systemUser = User::first();
        }

        if (! $systemUser) {
            $this->error('No system user found.');

            return static::FAILURE;
        }

        $threshold = now()->subDays(3);

        $tickets = Ticket::where('status', 'resolved')
            ->where('last_user_response_at', '<=', $threshold)
            ->get();

        if ($tickets->isEmpty()) {
            $this->info('No tickets to auto-close.');

            return static::SUCCESS;
        }

        $count = 0;

        foreach ($tickets as $ticket) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => 'closed',
                'closed_at' => now(),
                'last_activity_at' => now(),
            ]);

            ActivityLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $systemUser->id,
                'action' => 'auto_closed',
                'old_status' => $oldStatus,
                'new_status' => 'closed',
                'metadata' => [
                    'reason' => 'Auto-closed after 3 days of inactivity.',
                    'last_user_response_at' => $ticket->last_user_response_at,
                ],
            ]);

            $count++;
        }

        $this->info("Auto-closed {$count} ticket(s).");

        return static::SUCCESS;
    }
}
