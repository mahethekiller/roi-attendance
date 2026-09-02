<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'card_no',
        'punch_date',
        'check_in_datetime',
        'check_out_datetime',
        'badgenumber',
        'check_in_time',
        'check_out_time',
        'show_status',
    ];

    protected function casts(): array
    {
        return [
            'punch_date' => 'date',
            'check_in_datetime' => 'datetime',
            'check_out_datetime' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'card_no', 'card_no');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('punch_date', $date);
    }

    public function scopeForCard($query, string $cardNo)
    {
        return $query->where('card_no', $cardNo);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('show_status', $status);
    }
}
