<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ActivityLog extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'activity_logs';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'ticket_id' => 'integer',
            'user_id' => 'integer',
            'metadata' => 'array',
        ];
    }
}
