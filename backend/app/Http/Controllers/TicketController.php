<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Division;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreTicketThreadRequest;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse {
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
            $query->where(
                'assigned_employee_id',
                $user->id
            );
        }

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

    public function store(StoreTicketRequest $request): JsonResponse {
        $user = $request->user();

        $data = $request->validated();

        $ticket = Ticket::create([
            'ticket_number' => $this->generateTicketNumber(),
            'subject' => $data['subject'],
            'description' => $data['description'],
            'priority' => 'medium',
            'status' => 'open',
            'created_by' => $user->id,
            'target_division_id' => $data['target_division_id'],
            'assigned_employee_id' => null,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        $attachments = $this->storeAttachments(
            $request->file('attachments', [])
        );

        TicketThread::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'body' => $request->description,
            'attachments' => $attachments,
        ]);

        ActivityLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => 'ticket_created',
            'old_status' => null,
            'new_status' => 'open',
            'metadata' => [
                'subject' => $ticket->subject,
                'target_division_id' => $ticket->target_division_id,
            ],
        ]);

        return response()->json([
            'message' => 'Ticket berhasil dibuat.',
            'data' => $ticket,
        ], 201);
    }

    public function assign(AssignTicketRequest $request, Ticket $ticket): JsonResponse {
        $employee = User::with([
            'role',
        ])->findOrFail(
            $request->assigned_employee_id
        );

        if (strtolower($employee->role->name) !== 'employee') {
            throw ValidationException::withMessages([
                'assigned_employee_id' => [
                    'Selected user is not an employee.',
                ],
            ]);
        }

        if ($employee->division_id !== $ticket->target_division_id) {
            throw ValidationException::withMessages([
                'assigned_employee_id' => [
                    'Employee must belong to the ticket target division.',
                ],
            ]);
        }

        if ($ticket->status !== 'open') {
            throw ValidationException::withMessages([
                'ticket' => [
                    'Only open tickets can be assigned.',
                ],
            ]);
        }

        $oldStatus = $ticket->status;

        $ticket->update([
            'assigned_employee_id' => $employee->id,
            'status' => 'in_progress',
            'last_activity_at' => now(),
        ]);

        ActivityLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => 'ticket_assigned',
            'old_status' => $oldStatus,
            'new_status' => 'in_progress',
            'metadata' => [
                'assigned_employee_id' => $employee->id,
            ],
        ]);

        return response()->json([
            'message' => 'Ticket assigned successfully.',
            'ticket' => $ticket->fresh([
                'creator',
                'targetDivision',
                'assignedEmployee',
            ]),
        ]);
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): JsonResponse {
        $user = $request->user();

        $newStatus = $request->status;

        $role = strtolower($user->role->name);

        $this->validateStatusTransition(
            $ticket,
            $user,
            $role,
            $newStatus
        );

        $oldStatus = $ticket->status;

        $data = [
            'status' => $newStatus,
            'last_activity_at' => now(),
        ];

        if ($newStatus === 'resolved') {
            $data['resolved_at'] = now();
        }

        if ($newStatus === 'closed') {
            $data['closed_at'] = now();
        }

        if ($newStatus === 'in_progress') {
            $data['resolved_at'] = null;
            $data['closed_at'] = null;
            $data['last_user_response_at'] = now();
        }

        $ticket->update($data);

        ActivityLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'status_changed',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'metadata' => [
                'changed_by_role' => $role,
            ],
        ]);

        return response()->json([
            'message' => 'Ticket status updated successfully.',
            'ticket' => $ticket->fresh([
                'creator',
                'targetDivision',
                'assignedEmployee',
            ]),
        ]);
    }

    public function addThread(StoreTicketThreadRequest $request, Ticket $ticket): JsonResponse {
        $user = $request->user();

        $data = $request->validated();

        $attachments = $this->storeAttachments(
            $request->file('attachments', [])
        );

        $thread = TicketThread::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $data['body'],
            'attachments' => $attachments,
        ]);

        $ticket->update([
            'last_activity_at' => now(),
            'last_user_response_at' => strtolower($user->role->name) === 'user' ? now(): $ticket->last_user_response_at,
        ]);

        ActivityLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'thread_added',
            'metadata' => [
                'thread_id' => $thread->_id,
                'attachments_count' => count($attachments),
            ],
        ]);

        return response()->json([
            'message' => 'Thread berhasil ditambahkan.',
            'data' => $thread,
        ], 201);
    }

    private function validateStatusTransition(Ticket $ticket, User $user, string $role, string $newStatus): void {
        if ($role === 'employee') {
            if ($ticket->assigned_employee_id !== $user->id) {
                abort(403, 'You are not assigned to this ticket.');
            }

            if ($ticket->status !== 'in_progress' || $newStatus !== 'resolved') {
                abort(422, 'Invalid employee status transition.');
            }

            return;
        }

        if ($role === 'user') {
            if ($ticket->created_by !== $user->id) {
                abort(403, 'You cannot modify this ticket.');
            }

            $valid = match ($ticket->status) {
                'resolved' => in_array(
                    $newStatus,
                    ['closed', 'in_progress'],
                    true
                ),

                default => false,
            };

            if (! $valid) {
                abort(422, 'Invalid user status transition.');
            }

            return;
        }

        abort(403);
    }

    private function storeAttachments(array $files): array {
        $attachments = [];

        foreach ($files as $file) {
            $path = $file->store(
                'tickets',
                'public'
            );

            $attachments[] = [
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        }

        return $attachments;
    }

    private function generateTicketNumber(): string {
        $lastId = (int) Ticket::max('id');

        return 'TKT-' . str_pad(
            $lastId + 1,
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    public function show(Ticket $ticket): JsonResponse {
        $user = request()->user();

        $role = strtolower($user->role->name);

        if ($role === 'user') {
            abort_if(
                $ticket->created_by !== $user->id,
                403,
                'You cannot view this ticket.'
            );
        }

        if ($role === 'employee') {
            abort_if(
                $ticket->assigned_employee_id !== $user->id,
                403,
                'You cannot view this ticket.'
            );
        }

        return response()->json(
            $ticket->load([
                'creator',
                'targetDivision',
                'assignedEmployee',
            ])
        );
    }
}