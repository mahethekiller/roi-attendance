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
        $totalEmployees = Employee::count();
        $totalAbsentToday = max(0, $totalEmployees - $totalPresentToday);
        $totalLateToday = 0;

        return view('admin.attendances.index', compact(
            'attendances',
            'search',
            'date',
            'status',
            'totalPresentToday',
            'totalAbsentToday',
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
        $expectedToken = (string) config('services.biometric.cron_token');

        if (empty($expectedToken) || empty($token) || !hash_equals($expectedToken, (string) $token)) {
            \Illuminate\Support\Facades\Log::warning('Unauthorized biometric cron webhook invocation attempt', [
                'ip' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            ]);
            return response()->json(['success' => false, 'message' => 'Unauthorized cron token.'], 401);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $result = $service->sync($startDate, $endDate, 'webhook');

        return response()->json($result);
    }
}
