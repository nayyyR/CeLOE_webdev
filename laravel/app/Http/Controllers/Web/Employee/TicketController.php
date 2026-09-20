<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketThreadRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\ActivityLog;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    public function index(): View
    {
        $tickets = Ticket::where('assigned_employee_id', Auth::id())
            ->with(['targetDivision', 'creator'])
            ->latest()
            ->paginate(10);

        return view('employee.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        abort_unless($ticket->assigned_employee_id === Auth::id(), 403);

        $ticket->load(['targetDivision', 'creator']);

        $threads = TicketThread::where('ticket_id', $ticket->id)
            ->orderBy('created_at')
            ->get();

        $activityLogs = ActivityLog::where('ticket_id', $ticket->id)
            ->orderByDesc('created_at')
            ->get();

        $userIds = $threads->pluck('user_id')
            ->merge($activityLogs->pluck('user_id'))
            ->unique()
            ->filter()
            ->values();

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $threads->each(function ($thread) use ($users) {
            $thread->setRelation('user', $users->get($thread->user_id));
        });

        $activityLogs->each(function ($log) use ($users) {
            $log->setRelation('user', $users->get($log->user_id));
        });

        return view('employee.tickets.show', compact('ticket', 'threads', 'activityLogs', 'users'));
    }

    public function addThread(Ticket $ticket, StoreTicketThreadRequest $request): RedirectResponse
    {
        abort_unless($ticket->assigned_employee_id === Auth::id(), 403);

        $this->ticketService->addThread(
            $ticket,
            $request->validated(),
            Auth::user()
        );

        return redirect()->route('employee.tickets.show', $ticket)
            ->with('success', 'Reply sent successfully.');
    }

    public function updateStatus(Ticket $ticket, UpdateTicketStatusRequest $request): RedirectResponse
    {
        abort_unless($ticket->assigned_employee_id === Auth::id(), 403);

        $this->ticketService->updateStatus(
            $ticket,
            $request->validated('status'),
            Auth::user()
        );

        return redirect()->route('employee.tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }
}
