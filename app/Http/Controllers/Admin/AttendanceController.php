<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $date = $request->input('date', date('Y-m-d'));
        $status = $request->input('status');

        $attendancesQuery = Attendance::with(['employee.user'])
            ->whereDate('punch_date', $date)
            ->latest('check_in_datetime');

        if (!empty($status)) {
            $attendancesQuery->where('show_status', $status);
        }

        if (!empty($search)) {
            $attendancesQuery->where(function ($q) use ($search) {
                $q->where('card_no', 'like', "%{$search}%")
                  ->orWhere('badgenumber', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($empQ) use ($search) {
                      $empQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('employee_id', 'like', "%{$search}%");
                  });
            });
        }

        $attendances = $attendancesQuery->paginate(15)->withQueryString();

        $totalPresentToday = Attendance::whereDate('punch_date', $date)->count();
        $totalLateToday = Attendance::whereDate('punch_date', $date)->where('show_status', 'late')->count();
        $totalEmployees = Employee::count();

        return view('admin.attendances.index', compact(
            'attendances',
            'search',
            'date',
            'status',
            'totalPresentToday',
            'totalLateToday',
            'totalEmployees'
        ));
    }

    public function sync(\App\Services\BiometricSyncService $service): \Illuminate\Http\RedirectResponse
    {
        $result = $service->sync(null, null, 'manual_ui');

        if ($result['success']) {
            return redirect()->route('admin.attendances.index')
                ->with('success', $result['message']);
        }

        return redirect()->route('admin.attendances.index')
            ->with('error', $result['message']);
    }

    public function cronWebhook(Request $request, \App\Services\BiometricSyncService $service): \Illuminate\Http\JsonResponse
    {
        $token = $request->input('token') ?? $request->header('X-Cron-Token');
        $expectedToken = env('BIOMETRIC_CRON_TOKEN', 'roi_attendance_secure_sync_2026');

        if ($token !== $expectedToken) {
            return response()->json(['success' => false, 'message' => 'Unauthorized cron token.'], 401);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $result = $service->sync($startDate, $endDate, 'webhook');

        return response()->json($result);
    }
}
