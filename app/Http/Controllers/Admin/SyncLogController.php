<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SyncLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SyncLogController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $trigger = $request->input('trigger');

        $logsQuery = SyncLog::latest();

        if (!empty($status)) {
            $logsQuery->where('status', $status);
        }

        if (!empty($trigger)) {
            $logsQuery->where('trigger_type', $trigger);
        }

        $logs = $logsQuery->paginate(20)->withQueryString();

        $totalSyncs = SyncLog::count();
        $successfulSyncs = SyncLog::where('status', 'success')->count();
        $failedSyncs = SyncLog::where('status', 'failed')->count();
        $totalImported = SyncLog::sum('imported_count');
        $totalUpdated = SyncLog::sum('updated_count');

        return view('admin.sync-logs.index', compact(
            'logs',
            'status',
            'trigger',
            'totalSyncs',
            'successfulSyncs',
            'failedSyncs',
            'totalImported',
            'totalUpdated'
        ));
    }

    /**
     * Clear biometric sync history logs based on chosen scope (Super Admin only).
     */
    public function clear(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'scope' => ['required', 'string', 'in:all,older_than_7_days,older_than_30_days,failed_only'],
        ]);

        $scope = $validated['scope'];

        switch ($scope) {
            case 'all':
                $count = SyncLog::count();
                SyncLog::truncate();
                $message = "All {$count} biometric sync history logs have been permanently cleared.";
                break;

            case 'older_than_7_days':
                $query = SyncLog::where('created_at', '<', now()->subDays(7));
                $count = $query->count();
                $query->delete();
                $message = "{$count} sync logs older than 7 days have been cleared.";
                break;

            case 'older_than_30_days':
                $query = SyncLog::where('created_at', '<', now()->subDays(30));
                $count = $query->count();
                $query->delete();
                $message = "{$count} sync logs older than 30 days have been cleared.";
                break;

            case 'failed_only':
                $query = SyncLog::where('status', 'failed');
                $count = $query->count();
                $query->delete();
                $message = "{$count} failed sync attempt logs have been cleared.";
                break;

            default:
                return redirect()->route('admin.sync-logs.index')
                    ->with('error', 'Invalid clear scope specified.');
        }

        return redirect()->route('admin.sync-logs.index')
            ->with('success', $message);
    }
}
