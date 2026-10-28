<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GreythrPushLog;
use App\Services\GreythrPushService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GreythrPushLogController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $trigger = $request->input('trigger');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = GreythrPushLog::with('user')->latest();

        if (!empty($status)) {
            $query->status($status);
        }

        if (!empty($trigger)) {
            $query->trigger($trigger);
        }

        if (!empty($startDate) || !empty($endDate)) {
            $query->dateRange($startDate, $endDate);
        }

        $logs = $query->paginate(15)->withQueryString();

        // Summary KPI Metrics
        $totalRuns = GreythrPushLog::count();
        $successfulRuns = GreythrPushLog::where('status', 'success')->count();
        $failedRuns = GreythrPushLog::where('status', 'failed')->count();
        $totalSwipesPushed = GreythrPushLog::where('status', 'success')->sum('swipes_count');

        $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');
        $defaultStart = $today;
        $defaultEnd = $today;

        return view('admin.greythr-logs.index', compact(
            'logs',
            'status',
            'trigger',
            'startDate',
            'endDate',
            'totalRuns',
            'successfulRuns',
            'failedRuns',
            'totalSwipesPushed',
            'defaultStart',
            'defaultEnd'
        ));
    }

    public function pushNow(Request $request, GreythrPushService $service): RedirectResponse
    {
        $validated = $request->validate([
            'start_date'  => 'nullable|date_format:Y-m-d',
            'end_date'    => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'employee_id' => 'nullable|string|max:60',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $employeeId = !empty($validated['employee_id']) ? trim($validated['employee_id']) : null;

        $result = $service->push($startDate, $endDate, 'manual_ui', Auth::id(), $employeeId);

        if ($result['success']) {
            return redirect()->route('admin.greythr-logs.index')
                ->with('success', $result['message']);
        }

        return redirect()->route('admin.greythr-logs.index')
            ->with('error', $result['message']);
    }

    public function clear(Request $request): RedirectResponse
    {
        $scope = $request->input('scope', 'all');

        switch ($scope) {
            case 'all':
                $count = GreythrPushLog::count();
                GreythrPushLog::truncate();
                $msg = "All {$count} greytHR push logs have been cleared successfully.";
                break;
            case 'older_7':
                $query = GreythrPushLog::where('created_at', '<', now()->subDays(7));
                $count = $query->count();
                $query->delete();
                $msg = "Successfully cleared {$count} push logs older than 7 days.";
                break;
            case 'older_30':
                $query = GreythrPushLog::where('created_at', '<', now()->subDays(30));
                $count = $query->count();
                $query->delete();
                $msg = "Successfully cleared {$count} push logs older than 30 days.";
                break;
            case 'failed':
                $query = GreythrPushLog::where('status', 'failed');
                $count = $query->count();
                $query->delete();
                $msg = "Successfully cleared {$count} failed push logs.";
                break;
            default:
                $msg = "No logs were deleted.";
        }

        return redirect()->route('admin.greythr-logs.index')->with('success', $msg);
    }
}
