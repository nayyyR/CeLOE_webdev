<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TicketThread extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'ticket_threads';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'body',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'ticket_id' => 'integer',
            'user_id' => 'integer',
            'attachments' => 'array',
        ];
    }
}
