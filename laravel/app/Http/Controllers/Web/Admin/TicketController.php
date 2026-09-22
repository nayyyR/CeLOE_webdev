<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignTicketRequest;
use App\Http\Requests\StoreTicketThreadRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\ActivityLog;
use App\Models\Role;
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
        $tickets = Ticket::with(['targetDivision', 'creator', 'assignedEmployee'])
            ->latest()
            ->paginate(15);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['targetDivision', 'creator', 'assignedEmployee']);

        $threadLimit = 100;

        $threads = TicketThread::where('ticket_id', $ticket->id)
            ->orderByDesc('created_at')
            ->limit($threadLimit + 1)
            ->get();

        $threadsTruncated = $threads->count() > $threadLimit;
        $threads = $threads->take($threadLimit)->reverse()->values();

        $activityLogLimit = 50;

        $activityLogs = ActivityLog::where('ticket_id', $ticket->id)
            ->orderByDesc('created_at')
            ->limit($activityLogLimit + 1)
            ->get();

        $activityLogsTruncated = $activityLogs->count() > $activityLogLimit;
        $activityLogs = $activityLogs->take($activityLogLimit)->values();

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

        $employeeRoleId = Role::where('name', 'employee')->first()?->id;

        $employees = User::where('role_id', $employeeRoleId)
            ->where('division_id', $ticket->target_division_id)
            ->withCount([
                'assignedTickets as active_count' => fn ($q) => $q->where('status', 'in_progress'),
                'assignedTickets as completed_count' => fn ($q) => $q->whereIn('status', ['resolved', 'closed']),
            ])
            ->get();

        return view('admin.tickets.show', compact('ticket', 'threads', 'threadsTruncated', 'threadLimit', 'activityLogs', 'activityLogsTruncated', 'activityLogLimit', 'users', 'employees'));
    }

    public function assign(Ticket $ticket, AssignTicketRequest $request): RedirectResponse
    {
        $this->ticketService->assignTicket(
            $ticket,
            $request->validated('assigned_employee_id'),
            Auth::user()
        );

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket assigned successfully.');
    }

    public function addThread(Ticket $ticket, StoreTicketThreadRequest $request): RedirectResponse
    {
        $this->ticketService->addThread(
            $ticket,
            $request->validated(),
            Auth::user()
        );

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Reply sent successfully.');
    }

    public function updateStatus(Ticket $ticket, UpdateTicketStatusRequest $request): RedirectResponse
    {
        $this->ticketService->updateStatus(
            $ticket,
            $request->validated('status'),
            Auth::user()
        );

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }
}
