<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\SyncLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today();
        $todayDateString = $today->toDateString();

        // 1. Core Corporate KPI Calculations
        $totalEmployees = Employee::count();
        $totalCardsAssigned = Employee::whereNotNull('card_no')->where('card_no', '!=', '')->count();

        // Today's Attendances
        $todayAttendances = Attendance::with('employee')
            ->whereDate('punch_date', $todayDateString)
            ->get();

        $presentEmployees = $todayAttendances->unique('card_no');
        $todayPresent = $presentEmployees->count();

        $lateEmployees = $todayAttendances->filter(function ($att) {
            if ($att->show_status === 'Late') {
                return true;
            }
            if (!empty($att->check_in_time) && $att->check_in_time > '09:15:00') {
                return true;
            }
            return false;
        })->unique('card_no');
        $todayLate = $lateEmployees->count();

        $todayAbsent = max(0, $totalEmployees - $todayPresent);
        $attendanceRate = $totalEmployees > 0 ? round(($todayPresent / $totalEmployees) * 100, 1) : 0;
        $lateRate = $todayPresent > 0 ? round(($todayLate / $todayPresent) * 100, 1) : 0;

        // 2. 7-Day Attendance Trend Line/Bar Series
        $trendLabels = [];
        $trendPresent = [];
        $trendLate = [];
        $trendAbsent = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->toDateString();
            $label = $date->format('M d');
            $trendLabels[] = $label;

            $dayAttendances = Attendance::whereDate('punch_date', $dateStr)->get();
            $dayPresent = $dayAttendances->unique('card_no')->count();
            $dayLate = $dayAttendances->filter(function ($att) {
                return $att->show_status === 'Late' || (!empty($att->check_in_time) && $att->check_in_time > '09:15:00');
            })->unique('card_no')->count();
            $dayAbsent = max(0, $totalEmployees - $dayPresent);

            $trendPresent[] = $dayPresent;
            $trendLate[] = $dayLate;
            $trendAbsent[] = $dayAbsent;
        }

        // 3. Hourly Check-in Distribution (06:00 to 18:00)
        $hourlyLabels = [];
        $hourlyPunches = [];
        for ($h = 6; $h <= 18; $h++) {
            $hourStr = str_pad($h, 2, '0', STR_PAD_LEFT);
            $nextHour = str_pad($h + 1, 2, '0', STR_PAD_LEFT);
            $hourlyLabels[] = Carbon::createFromTime($h, 0)->format('h A');

            $punchesInHour = $todayAttendances->filter(function ($att) use ($hourStr, $nextHour) {
                if ($att->check_in_datetime) {
                    return $att->check_in_datetime->format('H') === $hourStr;
                }
                if ($att->check_in_time) {
                    return substr($att->check_in_time, 0, 2) === $hourStr;
                }
                return false;
            })->count();

            $hourlyPunches[] = $punchesInHour;
        }

        // 4. Company / Department Attendance Breakdown
        $companies = Employee::select('company')
            ->distinct()
            ->pluck('company')
            ->filter()
            ->values();

        $companyBreakdown = [];
        foreach ($companies as $comp) {
            $compTotal = Employee::where('company', $comp)->count();
            $compPresent = $todayAttendances->filter(function ($att) use ($comp) {
                return $att->employee && $att->employee->company === $comp;
            })->unique('card_no')->count();

            $compRate = $compTotal > 0 ? round(($compPresent / $compTotal) * 100) : 0;
            $companyBreakdown[] = [
                'name' => $comp,
                'total' => $compTotal,
                'present' => $compPresent,
                'rate' => $compRate,
            ];
        }

        // 5. Biometric Sync Engine Status
        $latestSync = SyncLog::latest()->first();
        $todaySyncCount = SyncLog::whereDate('created_at', $todayDateString)->count();
        $todayImportedCount = SyncLog::whereDate('created_at', $todayDateString)->sum('imported_count');

        // 6. Recent Punches Feed (Today or most recent)
        $recentPunches = Attendance::with('employee')
            ->whereDate('punch_date', $todayDateString)
            ->latest('check_in_datetime')
            ->latest('id')
            ->take(8)
            ->get();

        if ($recentPunches->isEmpty()) {
            $recentPunches = Attendance::with('employee')
                ->latest('punch_date')
                ->latest('check_in_datetime')
                ->latest('id')
                ->take(8)
                ->get();
        }

        // 7. API Health Summary
        $todayApiRequests = ApiRequestLog::whereDate('created_at', $todayDateString)->count();
        $avgApiLatency = round(ApiRequestLog::whereDate('created_at', $todayDateString)->avg('duration_ms') ?? 0, 1);

        // 8. Employee Self-Service Stats (if regular user with linked employee)
        $employeeRecord = $user->employee;
        $personalStats = null;
        if ($employeeRecord) {
            $personalMonthAttendances = Attendance::where('card_no', $employeeRecord->card_no)
                ->whereMonth('punch_date', $today->month)
                ->whereYear('punch_date', $today->year)
                ->get();

            $todayPersonalAttendance = $todayAttendances->firstWhere('card_no', $employeeRecord->card_no);

            $personalStats = [
                'daysPresent' => $personalMonthAttendances->count(),
                'daysLate' => $personalMonthAttendances->filter(fn($a) => $a->show_status === 'Late' || (!empty($a->check_in_time) && $a->check_in_time > '09:15:00'))->count(),
                'todayRecord' => $todayPersonalAttendance,
                'monthlyLogs' => $personalMonthAttendances->sortByDesc('punch_date')->take(5),
            ];
        }

        return view('admin.dashboard', compact(
            'totalEmployees',
            'totalCardsAssigned',
            'todayPresent',
            'todayLate',
            'todayAbsent',
            'attendanceRate',
            'lateRate',
            'trendLabels',
            'trendPresent',
            'trendLate',
            'trendAbsent',
            'hourlyLabels',
            'hourlyPunches',
            'companyBreakdown',
            'latestSync',
            'todaySyncCount',
            'todayImportedCount',
            'recentPunches',
            'todayApiRequests',
            'avgApiLatency',
            'personalStats',
            'employeeRecord'
        ));
    }
}
