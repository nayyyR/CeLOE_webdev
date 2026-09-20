<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketMasterRequest;
use App\Http\Requests\UpdateTicketMasterRequest;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketMasterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with([
            'creator',
            'targetDivision',
            'assignedEmployee',
        ]);

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

    public function show(Ticket $ticket): JsonResponse
    {
        return $this->successResponse(
            data: $ticket->load(['creator', 'targetDivision', 'assignedEmployee']),
            message: 'Ticket retrieved successfully.',
        );
    }

    public function store(StoreTicketMasterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['ticket_number'] = 'TKT-'.str_pad(
            ((int) Ticket::max('id')) + 1,
            6,
            '0',
            STR_PAD_LEFT,
        );

        $ticket = Ticket::create($data);

        return $this->successResponse(
            data: $ticket,
            message: 'Ticket created successfully.',
            code: 201,
        );
    }

    public function update(UpdateTicketMasterRequest $request, Ticket $ticket): JsonResponse
    {
        $data = $request->validated();

        $ticket->update($data);

        return $this->successResponse(
            data: $ticket->fresh(),
            message: 'Ticket updated successfully.',
        );
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();

        return $this->successResponse(message: 'Ticket deleted successfully.');
    }
}
