<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketMasterController extends Controller
{
    public function index(Request $request): JsonResponse {
        $query = Ticket::with([
            'creator',
            'targetDivision',
            'assignedEmployee',
        ]);

        if ($request->filled('search')) {
            $query->where(
                'subject',
                'like',
                '%' . $request->search . '%'
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('division_id')) {
            $query->where(
                'target_division_id',
                $request->division_id
            );
        }

        return response()->json(
            $query->latest()->paginate(10)
        );
    }

    public function show(Ticket $ticket): JsonResponse {
        return response()->json(
            $ticket->load([
                'creator',
                'targetDivision',
                'assignedEmployee',
            ])
        );
    }

    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => [
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'urgent',
                ]),
            ],
            'status' => [
                'required',
                Rule::in([
                    'open',
                    'in_progress',
                    'resolved',
                    'closed',
                ]),
            ],
            'created_by' => [
                'required',
                'exists:users,id',
            ],
            'target_division_id' => [
                'required',
                'exists:divisions,id',
            ],
            'assigned_employee_id' => [
                'nullable',
                'exists:users,id',
            ],
        ]);

        $data['ticket_number'] =
            'TKT-' . str_pad(
                ((int) Ticket::max('id')) + 1,
                6,
                '0',
                STR_PAD_LEFT
            );

        $ticket = Ticket::create($data);

        return response()->json([
            'message' => 'Ticket created successfully.',
            'ticket' => $ticket,
        ], 201);
    }

    public function update(Request $request, Ticket $ticket): JsonResponse {
        $data = $request->validate([
            'subject' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'priority' => [
                'sometimes',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'urgent',
                ]),
            ],
            'status' => [
                'sometimes',
                Rule::in([
                    'open',
                    'in_progress',
                    'resolved',
                    'closed',
                ]),
            ],
            'target_division_id' => [
                'sometimes',
                'exists:divisions,id',
            ],
            'assigned_employee_id' => [
                'nullable',
                'exists:users,id',
            ],
        ]);

        $ticket->update($data);

        return response()->json([
            'message' => 'Ticket updated successfully.',
            'ticket' => $ticket->fresh(),
        ]);
    }

    public function destroy(Ticket $ticket): JsonResponse {
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted successfully.',
        ]);
    }
}