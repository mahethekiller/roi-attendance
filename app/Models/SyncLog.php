<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasFactory;

    protected $table = 'sync_logs';

    protected $fillable = [
        'trigger_type',
        'start_date',
        'end_date',
        'status',
        'imported_count',
        'updated_count',
        'message',
        'payload_summary',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'payload_summary' => 'array',
        ];
    }
}
