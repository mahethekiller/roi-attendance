<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GreythrPushLog extends Model
{
    use HasFactory;

    protected $table = 'greythr_push_logs';

    protected $fillable = [
        'trigger_type',
        'start_date',
        'end_date',
        'records_count',
        'swipes_count',
        'status',
        'http_status',
        'message',
        'payload_sample',
        'response_payload',
        'triggered_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'payload_sample' => 'array',
            'response_payload' => 'array',
            'records_count' => 'integer',
            'swipes_count' => 'integer',
            'http_status' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function scopeStatus($query, ?string $status)
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeTrigger($query, ?string $trigger)
    {
        if (empty($trigger)) {
            return $query;
        }

        return $query->where('trigger_type', $trigger);
    }

    public function scopeDateRange($query, ?string $startDate, ?string $endDate)
    {
        if (!empty($startDate)) {
            $query->whereDate('start_date', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        return $query;
    }
}
