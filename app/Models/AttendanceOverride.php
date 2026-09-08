<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceOverride extends Model
{
    use HasFactory;

    protected $table = 'attendance_overrides';

    protected $fillable = [
        'employee_id',
        'card_no',
        'employee_name',
        'check_in_window_start',
        'check_in_window_end',
        'adjusted_in_min_minute',
        'adjusted_in_max_minute',
        'min_duration_hours',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'adjusted_in_min_minute' => 'integer',
        'adjusted_in_max_minute' => 'integer',
        'min_duration_hours' => 'float',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'card_no', 'card_no');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function matches(?string $employeeId, ?string $cardNo): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $cleanEmpId = trim((string) $employeeId);
        $cleanCard = trim((string) $cardNo);

        if (!empty($this->employee_id) && !empty($cleanEmpId) && $this->employee_id === $cleanEmpId) {
            return true;
        }

        if (!empty($this->card_no) && !empty($cleanCard) && $this->card_no === $cleanCard) {
            return true;
        }

        return false;
    }
}
