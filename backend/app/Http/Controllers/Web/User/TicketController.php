<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\StoreTicketThreadRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\ActivityLog;
use App\Models\Division;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    public function index(): View
    {
        $tickets = Ticket::where('created_by', auth()->id())
            ->with(['targetDivision', 'assignedEmployee'])
            ->latest()
            ->paginate(10);

        return view('user.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        $divisions = Division::where('name', '!=', 'general')
            ->orderBy('name')
            ->get();

        return view('user.tickets.create', compact('divisions'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = $this->ticketService->createTicket(
            $request->validated(),
            auth()->user()
        );

        return redirect()->route('user.tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket): View
    {
        abort_unless($ticket->created_by === auth()->id(), 403);

        $ticket->load(['targetDivision', 'assignedEmployee', 'creator']);

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

        return view('user.tickets.show', compact('ticket', 'threads', 'activityLogs', 'users'));
    }

    public function addThread(Ticket $ticket, StoreTicketThreadRequest $request): RedirectResponse
    {
        abort_unless($ticket->created_by === auth()->id(), 403);

        $this->ticketService->addThread(
            $ticket,
            $request->validated(),
            auth()->user()
        );

        return redirect()->route('user.tickets.show', $ticket)
            ->with('success', 'Reply sent successfully.');
    }

    public function updateStatus(Ticket $ticket, UpdateTicketStatusRequest $request): RedirectResponse
    {
        abort_unless($ticket->created_by === auth()->id(), 403);

        $this->ticketService->updateStatus(
            $ticket,
            $request->validated('status'),
            auth()->user()
        );

        return redirect()->route('user.tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }
}
