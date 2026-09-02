<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLog extends Model
{
    use HasFactory;

    protected $table = 'api_request_logs';

    protected $fillable = [
        'user_id',
        'token_name',
        'ip_address',
        'method',
        'url',
        'query_params',
        'status_code',
        'duration_ms',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'query_params' => 'array',
            'duration_ms' => 'float',
            'status_code' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
