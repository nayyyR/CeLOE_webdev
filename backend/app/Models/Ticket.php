<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'priority',
        'status',
        'created_by',
        'division_id',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'last_user_response_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_user_response_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }
    
    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targetDivision(): BelongsTo {
        return $this->belongsTo(Division::class, 'target_division_id');
    }

    public function assignedEmployee(): BelongsTo {
        return $this->belongsTo(User::class, 'assigned_employee_id');
    }
}
