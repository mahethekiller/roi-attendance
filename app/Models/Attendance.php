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

    protected $appends = [
        'total_time',
    ];

    protected function casts(): array
    {
        return [
            'punch_date' => 'date',
            'check_in_datetime' => 'datetime',
            'check_out_datetime' => 'datetime',
        ];
    }

    /**
     * Compute total time duration between check in and check out.
     */
    public function getTotalTimeAttribute(): ?string
    {
        $in = $this->check_in_datetime;
        $out = $this->check_out_datetime;

        if (!$in && !empty($this->check_in_time) && !empty($this->punch_date)) {
            $in = \Carbon\Carbon::parse($this->punch_date->format('Y-m-d') . ' ' . $this->check_in_time);
        }

        if (!$out && !empty($this->check_out_time) && !empty($this->punch_date)) {
            $out = \Carbon\Carbon::parse($this->punch_date->format('Y-m-d') . ' ' . $this->check_out_time);
        }

        if (!$in || !$out || $in->equalTo($out) || $out->lessThan($in)) {
            return null;
        }

        $diffMinutes = $in->diffInMinutes($out);
        $hours = intdiv($diffMinutes, 60);
        $minutes = $diffMinutes % 60;

        return sprintf('%dh %02dm', $hours, $minutes);
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
