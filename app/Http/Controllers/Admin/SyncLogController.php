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
}
