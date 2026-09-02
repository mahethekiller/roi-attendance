<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiLogController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $method = $request->input('method');
        $search = $request->input('search');

        $query = ApiRequestLog::with('user')->latest();

        if (!empty($status)) {
            if ($status === '2xx') {
                $query->whereBetween('status_code', [200, 299]);
            } elseif ($status === '4xx') {
                $query->whereBetween('status_code', [400, 499]);
            } elseif ($status === '5xx') {
                $query->whereBetween('status_code', [500, 599]);
            } else {
                $query->where('status_code', $status);
            }
        }

        if (!empty($method)) {
            $query->where('method', strtoupper($method));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('token_name', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $totalRequests = ApiRequestLog::count();
        $successfulRequests = ApiRequestLog::whereBetween('status_code', [200, 299])->count();
        $clientErrors = ApiRequestLog::whereBetween('status_code', [400, 499])->count();
        $avgDuration = round(ApiRequestLog::avg('duration_ms') ?? 0, 1);

        return view('admin.api-logs.index', compact(
            'logs',
            'status',
            'method',
            'search',
            'totalRequests',
            'successfulRequests',
            'clientErrors',
            'avgDuration'
        ));
    }
}
