<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketThread;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function show(Ticket $ticket, string $path): StreamedResponse
    {
        $this->authorizeAccess($ticket);

        $attachment = TicketThread::where('ticket_id', $ticket->id)
            ->get()
            ->flatMap(fn ($thread) => $thread->attachments ?? [])
            ->first(fn ($item) => ($item['path'] ?? null) === $path);

        abort_unless($attachment, 404);

        $disk = Storage::disk($attachment['disk'] ?? 'public');

        abort_unless($disk->exists($attachment['path']), 404);

        return $disk->response($attachment['path'], $attachment['original_name']);
    }

    private function authorizeAccess(Ticket $ticket): void
    {
        $user = Auth::User();
        $role = strtolower($user->role->name);

        $access = match ($role) {
            'admin', 'super admin' => true,
            'user' => $ticket->created_by === $user->id,
            'employee' => $ticket->assigned_employee_id === $user->id,
            default => false,
        };

        abort_unless($access, 403);
    }
}
