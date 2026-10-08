<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceOverride;
use App\Models\Employee;
use App\Models\SyncLog;
use App\Services\AttendanceOverrideService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AttendanceSyncController extends Controller
{
    protected AttendanceOverrideService $overrideService;

    public function __construct(?AttendanceOverrideService $overrideService = null)
    {
        $this->overrideService = $overrideService ?? app(AttendanceOverrideService::class);
    }

    /**
     * Display the 9-Hour Biometric Auto-Sync Dashboard interface.
     */
    public function index(Request $request): View
    {
        $employees = Employee::orderBy('first_name')
            ->get(['id', 'employee_id', 'card_no', 'first_name', 'last_name', 'company']);

        // Default employee code
        $defaultEmpCode = '10337';
        $defaultEmployee = $employees->firstWhere('card_no', $defaultEmpCode) 
            ?? $employees->firstWhere('employee_id', $defaultEmpCode)
            ?? $employees->first();

        if ($defaultEmployee && empty($defaultEmpCode)) {
            $defaultEmpCode = $defaultEmployee->card_no ?: $defaultEmployee->employee_id;
        }

        // Default date range: current month up to today
        $startDate = Carbon::now('Asia/Kolkata')->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        // Fetch active DB attendance override rules
        $activeRules = AttendanceOverride::active()->get();

        return view('admin.attendance-sync.index', compact(
            'employees',
            'defaultEmpCode',
            'startDate',
            'endDate',
            'activeRules'
        ));
    }

    /**
     * Proxy endpoint: Fetch punches for an employee from the remote Biometric API.
     * Prevents browser CORS and mixed-content issues.
     */
    public function fetchPunches(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'emp_code'   => 'required|string|max:50',
            'date'       => 'nullable|date_format:Y-m-d',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
            'view'       => 'nullable|string|in:summary,raw,both',
        ]);

        $apiUrl = config('services.biometric.punches_url', 'http://103.25.129.247/prac1111/attt/get_employee_punches_api.php');

        $queryParams = array_filter([
            'emp_code'   => $validated['emp_code'],
            'date'       => $validated['date'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date'   => $validated['end_date'] ?? null,
            'view'       => $validated['view'] ?? 'both',
        ]);

        try {
            $response = Http::timeout(15)->get($apiUrl, $queryParams);

            if ($response->successful()) {
                return response()->json($response->json(), $response->status());
            }

            Log::warning('Remote biometric punches API returned error response', [
                'status'  => $response->status(),
                'body'    => substr($response->body(), 0, 500),
                'params'  => $queryParams,
            ]);

            return response()->json([
                'status'  => 0,
                'message' => 'Remote Biometric Server returned status ' . $response->status() . ': ' . ($response->json('message') ?? 'Unknown error'),
                'data'    => $response->json(),
            ], $response->status() ?: 500);

        } catch (\Exception $e) {
            Log::error('Exception connecting to remote biometric punches API: ' . $e->getMessage(), [
                'url'    => $apiUrl,
                'params' => $queryParams,
            ]);

            return response()->json([
                'status'  => 0,
                'message' => 'Unable to connect to Biometric Server (' . $apiUrl . '). Check server availability or VPN connection.',
                'error'   => $e->getMessage(),
            ], 502);
        }
    }

    /**
     * Proxy endpoint: Update or auto-sync 1st and last punch for a given date.
     * Calculates randomized 9h00m - 9h15m duration if last_time is not provided.
     */
    public function syncPunch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'emp_code'       => 'required|string|max:50',
            'date'           => 'required|date_format:Y-m-d',
            'first_time'     => 'nullable|string|max:30',
            'last_time'      => 'nullable|string|max:30',
            'terminal_alias' => 'nullable|string|max:50',
        ]);

        $empCode = $validated['emp_code'];
        $date = $validated['date'];
        $firstTime = $validated['first_time'] ?? null;
        $lastTime = $validated['last_time'] ?? null;
        $terminalAlias = $validated['terminal_alias'] ?? 'AUTO_SYNC';

        // Auto-calculate randomized 9h00m - 9h15m check-out time if not provided
        if (empty($lastTime)) {
            if (empty($firstTime)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Cannot auto-sync without a valid 1st punch (check-in) time.',
                ], 422);
            }

            $lastTime = $this->calculateRandomOutTime($firstTime, $date);
        }

        $apiUrl = config('services.biometric.update_punches_url', 'http://103.25.129.247/prac1111/attt/update_day_punches_api.php');

        $postData = [
            'emp_code'       => $empCode,
            'date'           => $date,
            'first_time'     => $firstTime,
            'last_time'      => $lastTime,
            'terminal_alias' => $terminalAlias,
        ];

        try {
            $response = Http::asForm()->timeout(15)->post($apiUrl, $postData);
            $responseData = $response->json();
            $isSuccess = $response->successful() && isset($responseData['status']) && $responseData['status'] == 1;

            // Audit Trail: Record sync attempt in SyncLog
            SyncLog::create([
                'trigger_type'   => 'auto_sync_9h',
                'start_date'     => $date,
                'end_date'       => $date,
                'status'         => $isSuccess ? 'success' : 'failed',
                'imported_count' => 0,
                'updated_count'  => $isSuccess ? 1 : 0,
                'message'        => "9h Auto-Sync punch on {$date} for emp {$empCode} (" . ($isSuccess ? 'Success' : 'Failed') . ") by " . (Auth::user()?->name ?? 'Admin'),
                'payload_summary' => [
                    'user_id'        => Auth::id(),
                    'user_name'      => Auth::user()?->name,
                    'user_email'     => Auth::user()?->email,
                    'ip_address'     => $request->ip(),
                    'emp_code'       => $empCode,
                    'date'           => $date,
                    'first_time'     => $firstTime,
                    'last_time'      => $lastTime,
                    'terminal_alias' => $terminalAlias,
                    'api_response'   => $responseData,
                ],
            ]);

            // Sync with local database Attendances record if present
            if ($isSuccess) {
                $this->syncLocalAttendance($empCode, $date, $firstTime, $lastTime);
            }

            return response()->json($responseData, $response->status());

        } catch (\Exception $e) {
            Log::error('Exception calling biometric update punch API: ' . $e->getMessage(), [
                'url'      => $apiUrl,
                'postData' => $postData,
            ]);

            SyncLog::create([
                'trigger_type'   => 'auto_sync_9h',
                'start_date'     => $date,
                'end_date'       => $date,
                'status'         => 'failed',
                'imported_count' => 0,
                'updated_count'  => 0,
                'message'        => "9h Auto-Sync punch on {$date} for emp {$empCode} failed: " . $e->getMessage(),
                'payload_summary' => [
                    'user_id'    => Auth::id(),
                    'ip_address' => $request->ip(),
                    'emp_code'   => $empCode,
                    'date'       => $date,
                    'error'      => $e->getMessage(),
                ],
            ]);

            return response()->json([
                'status'  => 0,
                'message' => 'Unable to connect to Biometric Update API. Check network/VPN.',
                'error'   => $e->getMessage(),
            ], 502);
        }
    }

    /**
     * Synchronize BOTH First Punch and Last Punch using database override rule for the employee.
     * Calculates adjusted In (e.g. 09:20 - 09:35) and adjusted Out (>= 9h) and pushes both to API 2 & local DB.
     */
    public function syncWithDbRule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'emp_code'   => 'required|string|max:50',
            'date'       => 'required|date_format:Y-m-d',
            'first_time' => 'nullable|string|max:30',
            'last_time'  => 'nullable|string|max:30',
        ]);

        $empCode = $validated['emp_code'];
        $date = $validated['date'];
        $firstTimeInput = $validated['first_time'] ?? null;

        // Lookup employee and rule
        $emp = Employee::where('card_no', $empCode)->orWhere('employee_id', $empCode)->first();
        $rule = $this->overrideService->findMatchingRule($emp?->employee_id ?? $empCode, $emp?->card_no ?? $empCode);

        // 1. Calculate Target Check-In (First Punch)
        $targetIn = $this->calculateDbRuleCheckIn($date, $firstTimeInput, $rule);

        // 2. Calculate Target Check-Out (Last Punch)
        $targetOut = $this->calculateDbRuleCheckOut($targetIn, $rule);

        $apiUrl = config('services.biometric.update_punches_url', 'http://103.25.129.247/prac1111/attt/update_day_punches_api.php');

        $postData = [
            'emp_code'       => $empCode,
            'date'           => $date,
            'first_time'     => $targetIn,
            'last_time'      => $targetOut,
            'terminal_alias' => 'DB_RULE_SYNC',
        ];

        try {
            $response = Http::asForm()->timeout(15)->post($apiUrl, $postData);
            $responseData = $response->json();
            $isSuccess = $response->successful() && isset($responseData['status']) && $responseData['status'] == 1;

            // Audit Trail
            SyncLog::create([
                'trigger_type'   => 'auto_sync_db_rule',
                'start_date'     => $date,
                'end_date'       => $date,
                'status'         => $isSuccess ? 'success' : 'failed',
                'imported_count' => 0,
                'updated_count'  => $isSuccess ? 1 : 0,
                'message'        => "DB Rule Full Sync (In & Out) on {$date} for emp {$empCode} (" . ($isSuccess ? 'Success' : 'Failed') . ") by " . (Auth::user()?->name ?? 'Admin'),
                'payload_summary' => [
                    'user_id'        => Auth::id(),
                    'user_name'      => Auth::user()?->name,
                    'user_email'     => Auth::user()?->email,
                    'ip_address'     => $request->ip(),
                    'emp_code'       => $empCode,
                    'date'           => $date,
                    'rule_id'        => $rule?->id,
                    'first_time'     => $targetIn,
                    'last_time'      => $targetOut,
                    'api_response'   => $responseData,
                ],
            ]);

            // Sync with local database Attendances table
            if ($isSuccess) {
                $this->syncLocalAttendance($empCode, $date, $targetIn, $targetOut);
            }

            return response()->json($responseData, $response->status());

        } catch (\Exception $e) {
            Log::error('Exception calling biometric update punch API in syncWithDbRule: ' . $e->getMessage());
            return response()->json([
                'status'  => 0,
                'message' => 'Unable to connect to Biometric Update API. Check network/VPN.',
                'error'   => $e->getMessage(),
            ], 502);
        }
    }

    /**
     * Bulk Sync BOTH First and Last Punches using database override rules for the employee.
     */
    public function bulkSyncWithDbRule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'emp_code'           => 'required|string|max:50',
            'items'              => 'required|array|min:1',
            'items.*.date'       => 'required|date_format:Y-m-d',
            'items.*.first_time' => 'nullable|string',
        ]);

        $empCode = $validated['emp_code'];
        $items = $validated['items'];
        $apiUrl = config('services.biometric.update_punches_url', 'http://103.25.129.247/prac1111/attt/update_day_punches_api.php');

        $emp = Employee::where('card_no', $empCode)->orWhere('employee_id', $empCode)->first();
        $rule = $this->overrideService->findMatchingRule($emp?->employee_id ?? $empCode, $emp?->card_no ?? $empCode);

        $successCount = 0;
        $failedCount = 0;
        $results = [];

        foreach ($items as $item) {
            $date = $item['date'];
            $firstTimeInput = $item['first_time'] ?? null;

            $targetIn = $this->calculateDbRuleCheckIn($date, $firstTimeInput, $rule);
            $targetOut = $this->calculateDbRuleCheckOut($targetIn, $rule);

            try {
                $response = Http::asForm()->timeout(15)->post($apiUrl, [
                    'emp_code'       => $empCode,
                    'date'           => $date,
                    'first_time'     => $targetIn,
                    'last_time'      => $targetOut,
                    'terminal_alias' => 'DB_RULE_BULK',
                ]);

                $resData = $response->json();
                if ($response->successful() && isset($resData['status']) && $resData['status'] == 1) {
                    $successCount++;
                    $results[] = [
                        'date'       => $date,
                        'status'     => 'success',
                        'duration'   => $resData['data']['duration_formatted'] ?? null,
                        'first_time' => $targetIn,
                        'last_time'  => $targetOut,
                    ];
                    $this->syncLocalAttendance($empCode, $date, $targetIn, $targetOut);
                } else {
                    $failedCount++;
                    $results[] = [
                        'date'    => $date,
                        'status'  => 'failed',
                        'message' => $resData['message'] ?? 'API error',
                    ];
                }
            } catch (\Exception $e) {
                $failedCount++;
                $results[] = [
                    'date'    => $date,
                    'status'  => 'failed',
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Summary audit log
        SyncLog::create([
            'trigger_type'   => 'auto_sync_db_rule_bulk',
            'start_date'     => $items[0]['date'] ?? null,
            'end_date'       => end($items)['date'] ?? null,
            'status'         => $failedCount === 0 ? 'success' : ($successCount > 0 ? 'partial' : 'failed'),
            'imported_count' => 0,
            'updated_count'  => $successCount,
            'message'        => "Bulk DB Rule Sync (In & Out) for emp {$empCode}: {$successCount} succeeded, {$failedCount} failed by " . (Auth::user()?->name ?? 'Admin'),
            'payload_summary' => [
                'user_id'       => Auth::id(),
                'ip_address'    => $request->ip(),
                'emp_code'      => $empCode,
                'rule_id'       => $rule?->id,
                'total_items'   => count($items),
                'success_count' => $successCount,
                'failed_count'  => $failedCount,
                'details'       => $results,
            ],
        ]);

        return response()->json([
            'status'  => 1,
            'message' => "Bulk DB Rule synchronization completed: {$successCount} synced, {$failedCount} failed.",
            'data'    => [
                'total'         => count($items),
                'success_count' => $successCount,
                'failed_count'  => $failedCount,
                'results'       => $results,
            ],
        ]);
    }

    /**
     * Bulk Sync endpoint: Iterates through an array of shortfall days and synchronizes each check-out.
     */
    public function bulkSync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'emp_code'           => 'required|string|max:50',
            'items'              => 'required|array|min:1',
            'items.*.date'       => 'required|date_format:Y-m-d',
            'items.*.first_time' => 'required|string',
        ]);

        $empCode = $validated['emp_code'];
        $items = $validated['items'];
        $apiUrl = config('services.biometric.update_punches_url', 'http://103.25.129.247/prac1111/attt/update_day_punches_api.php');

        $successCount = 0;
        $failedCount = 0;
        $results = [];

        foreach ($items as $item) {
            $date = $item['date'];
            $firstTime = $item['first_time'];
            $lastTime = $this->calculateRandomOutTime($firstTime, $date);

            try {
                $response = Http::asForm()->timeout(15)->post($apiUrl, [
                    'emp_code'       => $empCode,
                    'date'           => $date,
                    'first_time'     => $firstTime,
                    'last_time'      => $lastTime,
                    'terminal_alias' => 'BULK_AUTO_SYNC',
                ]);

                $resData = $response->json();
                if ($response->successful() && isset($resData['status']) && $resData['status'] == 1) {
                    $successCount++;
                    $results[] = [
                        'date'       => $date,
                        'status'     => 'success',
                        'duration'   => $resData['data']['duration_formatted'] ?? null,
                        'first_time' => $firstTime,
                        'last_time'  => $lastTime,
                    ];
                    $this->syncLocalAttendance($empCode, $date, $firstTime, $lastTime);
                } else {
                    $failedCount++;
                    $results[] = [
                        'date'    => $date,
                        'status'  => 'failed',
                        'message' => $resData['message'] ?? 'API error',
                    ];
                }
            } catch (\Exception $e) {
                $failedCount++;
                $results[] = [
                    'date'    => $date,
                    'status'  => 'failed',
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Summary audit log
        SyncLog::create([
            'trigger_type'   => 'auto_sync_9h_bulk',
            'start_date'     => $items[0]['date'] ?? null,
            'end_date'       => end($items)['date'] ?? null,
            'status'         => $failedCount === 0 ? 'success' : ($successCount > 0 ? 'partial' : 'failed'),
            'imported_count' => 0,
            'updated_count'  => $successCount,
            'message'        => "Bulk 9h Auto-Sync for emp {$empCode}: {$successCount} succeeded, {$failedCount} failed by " . (Auth::user()?->name ?? 'Admin'),
            'payload_summary' => [
                'user_id'      => Auth::id(),
                'ip_address'   => $request->ip(),
                'emp_code'     => $empCode,
                'total_items'  => count($items),
                'success_count'=> $successCount,
                'failed_count' => $failedCount,
                'details'      => $results,
            ],
        ]);

        return response()->json([
            'status'  => 1,
            'message' => "Bulk synchronization completed: {$successCount} synced, {$failedCount} failed.",
            'data'    => [
                'total'         => count($items),
                'success_count' => $successCount,
                'failed_count'  => $failedCount,
                'results'       => $results,
            ],
        ]);
    }

    /**
     * Compute Check-In timestamp using DB rule if present, or fallback.
     */
    protected function calculateDbRuleCheckIn(string $date, ?string $firstTimeInput, ?AttendanceOverride $rule): string
    {
        $minMinute = $rule?->adjusted_in_min_minute ?? 20;
        $maxMinute = $rule?->adjusted_in_max_minute ?? 35;
        $randomMinutes = rand((int)$minMinute, (int)$maxMinute);
        $randomSeconds = rand(0, 59);

        return sprintf('%s 09:%02d:%02d', $date, $randomMinutes, $randomSeconds);
    }

    /**
     * Compute Check-Out timestamp from the adjusted Check-In using DB rule duration.
     */
    protected function calculateDbRuleCheckOut(string $adjustedInTimestamp, ?AttendanceOverride $rule): string
    {
        $inCarbon = Carbon::parse($adjustedInTimestamp, 'Asia/Kolkata');
        $durationHours = $rule?->min_duration_hours ?? 9.0;

        $randMinutes = rand(0, 14);
        $randSeconds = rand(5, 55);
        $totalOffsetSeconds = (int)($durationHours * 3600) + ($randMinutes * 60) + $randSeconds;

        return $inCarbon->copy()->addSeconds($totalOffsetSeconds)->format('Y-m-d H:i:s');
    }

    /**
     * Compute realistic randomized out time between 9h00m05s and 9h14m55s.
     * Specification formula: Offset = (9 * 3600) + (rand(0, 14) * 60) + rand(5, 55).
     */
    protected function calculateRandomOutTime(string $firstTimeInput, string $targetDate): string
    {
        $clean = str_replace('T', ' ', trim($firstTimeInput));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $clean)) {
            $clean = $targetDate . ' ' . $clean;
        }

        try {
            $firstCarbon = Carbon::parse($clean, 'Asia/Kolkata');
        } catch (\Exception $e) {
            $firstCarbon = Carbon::parse($targetDate . ' 10:00:00', 'Asia/Kolkata');
        }

        $randMinutes = rand(0, 14);
        $randSeconds = rand(5, 55);
        $totalOffsetSeconds = (9 * 3600) + ($randMinutes * 60) + $randSeconds;

        return $firstCarbon->copy()->addSeconds($totalOffsetSeconds)->format('Y-m-d H:i:s');
    }

    /**
     * Synchronize local database Attendance record if found for this employee & date.
     */
    protected function syncLocalAttendance(string $empCode, string $date, ?string $firstTime, ?string $lastTime): void
    {
        try {
            $att = Attendance::where(function ($q) use ($empCode) {
                $q->where('card_no', $empCode)
                  ->orWhere('badgenumber', $empCode);
            })->whereDate('punch_date', $date)->first();

            if ($att) {
                if (!empty($firstTime)) {
                    $firstClean = str_replace('T', ' ', $firstTime);
                    $firstCarbon = Carbon::parse($firstClean);
                    $att->check_in_datetime = $firstCarbon;
                    $att->check_in_time = $firstCarbon->format('H:i:s');
                }

                if (!empty($lastTime)) {
                    $lastClean = str_replace('T', ' ', $lastTime);
                    $lastCarbon = Carbon::parse($lastClean);
                    $att->check_out_datetime = $lastCarbon;
                    $att->check_out_time = $lastCarbon->format('H:i:s');
                }

                $att->show_status = 'present';
                $att->save();
            } else {
                // If not in database yet, create record in attendances table
                $emp = Employee::where('card_no', $empCode)->orWhere('employee_id', $empCode)->first();
                $firstCarbon = !empty($firstTime) ? Carbon::parse(str_replace('T', ' ', $firstTime)) : null;
                $lastCarbon = !empty($lastTime) ? Carbon::parse(str_replace('T', ' ', $lastTime)) : null;

                Attendance::create([
                    'card_no'            => $empCode,
                    'badgenumber'        => $emp?->employee_id ?? $empCode,
                    'punch_date'         => $date,
                    'check_in_datetime'  => $firstCarbon,
                    'check_in_time'      => $firstCarbon?->format('H:i:s'),
                    'check_out_datetime' => $lastCarbon,
                    'check_out_time'     => $lastCarbon?->format('H:i:s'),
                    'show_status'        => 'present',
                ]);
            }
        } catch (\Exception $e) {
            Log::info('Non-fatal: Unable to update/create local attendance record for sync: ' . $e->getMessage());
        }
    }
}
