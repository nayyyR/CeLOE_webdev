<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\StoreTicketThreadRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Ticket::with([
            'creator',
            'targetDivision',
            'assignedEmployee',
        ]);

        $role = strtolower($user->role->name);

        if ($role === 'user') {
            $query->where('created_by', $user->id);
        }

        if ($role === 'employee') {
            $query->where('assigned_employee_id', $user->id);
        }

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('division_id')) {
            $query->where('target_division_id', $request->division_id);
        }

        return $this->paginatedResponse(
            $query->latest()->paginate(10),
            'Tickets retrieved successfully.',
        );
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket(
            data: $request->validated(),
            user: $request->user(),
        );

        return $this->successResponse(
            data: $ticket,
            message: 'Ticket created successfully.',
            code: 201,
        );
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $user = request()->user();
        $role = strtolower($user->role->name);

        if ($role === 'user') {
            abort_if($ticket->created_by !== $user->id, 403, 'You cannot view this ticket.');
        }

        if ($role === 'employee') {
            abort_if($ticket->assigned_employee_id !== $user->id, 403, 'You cannot view this ticket.');
        }

        return $this->successResponse(
            data: $ticket->load(['creator', 'targetDivision', 'assignedEmployee']),
            message: 'Ticket retrieved successfully.',
        );
    }

    public function assign(AssignTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $ticket = $this->ticketService->assignTicket(
            ticket: $ticket,
            employeeId: $request->assigned_employee_id,
            assigner: $request->user(),
        );

        return $this->successResponse(
            data: $ticket,
            message: 'Ticket assigned successfully.',
        );
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): JsonResponse
    {
        $ticket = $this->ticketService->updateStatus(
            ticket: $ticket,
            newStatus: $request->status,
            user: $request->user(),
        );

        return $this->successResponse(
            data: $ticket,
            message: 'Ticket status updated successfully.',
        );
    }

    public function addThread(StoreTicketThreadRequest $request, Ticket $ticket): JsonResponse
    {
        $thread = $this->ticketService->addThread(
            ticket: $ticket,
            data: $request->validated(),
            user: $request->user(),
        );

        return $this->successResponse(
            data: $thread,
            message: 'Thread added successfully.',
            code: 201,
        );
    }
}
