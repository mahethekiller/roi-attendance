<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherCompanyAttendance extends Model
{
    use HasFactory;

    protected $table = 'other_company_attendances';

    protected $fillable = [
        'card_no',
        'badgenumber',
        'employee_name',
        'company_name',
        'punch_date',
        'check_in_datetime',
        'check_out_datetime',
        'check_in_time',
        'check_out_time',
        'show_status',
        'raw_payload',
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
            'raw_payload' => 'array',
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
            $in = Carbon::parse($this->punch_date->format('Y-m-d') . ' ' . $this->check_in_time);
        }

        if (!$out && !empty($this->check_out_time) && !empty($this->punch_date)) {
            $out = Carbon::parse($this->punch_date->format('Y-m-d') . ' ' . $this->check_out_time);
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

    public function scopeForCompany($query, ?string $company)
    {
        if (empty($company)) {
            return $query;
        }

        return $query->where('company_name', $company);
    }

    public function scopeForCard($query, string $cardNo)
    {
        return $query->where('card_no', $cardNo);
    }

    public function scopeStatus($query, ?string $status)
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('show_status', $status);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('card_no', 'like', "%{$term}%")
              ->orWhere('badgenumber', 'like', "%{$term}%")
              ->orWhere('employee_name', 'like', "%{$term}%")
              ->orWhere('company_name', 'like', "%{$term}%")
              ->orWhereHas('employee', function ($empQ) use ($term) {
                  $empQ->where('first_name', 'like', "%{$term}%")
                       ->orWhere('last_name', 'like', "%{$term}%")
                       ->orWhere('employee_id', 'like', "%{$term}%")
                       ->orWhere('company', 'like', "%{$term}%");
              });
        });
    }
}
