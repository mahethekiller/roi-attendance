<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetAttendanceApiRequest;
use App\Http\Requests\Api\HRsaleAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\HRsaleAttendanceResource;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AttendanceApiController extends Controller
{
    public function token(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'token_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ], 401);
        }

        $tokenName = $request->input('token_name', 'api-client-token');
        $token = $user->createToken($tokenName, ['attendance:read'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token generated successfully.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    public function index(GetAttendanceApiRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Attendance::with('employee')->latest('punch_date');

        if (!empty($validated['start_date'])) {
            $query->whereDate('punch_date', '>=', $validated['start_date']);
        }

        if (!empty($validated['end_date'])) {
            $query->whereDate('punch_date', '<=', $validated['end_date']);
        }

        if (!empty($validated['card_no'])) {
            $query->where('card_no', $validated['card_no']);
        }

        if (!empty($validated['status'])) {
            $query->where('show_status', $validated['status']);
        }

        if (!empty($validated['employee_id'])) {
            $query->whereHas('employee', function ($q) use ($validated) {
                $q->where('employee_id', $validated['employee_id']);
            });
        }

        if (!empty($validated['company'])) {
            $query->whereHas('employee', function ($q) use ($validated) {
                $q->where('company', 'like', "%{$validated['company']}%");
            });
        }

        $perPage = (int) ($validated['per_page'] ?? 25);
        $attendances = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Attendance records retrieved successfully.',
            'data'    => AttendanceResource::collection($attendances->items()),
            'meta'    => [
                'current_page' => $attendances->currentPage(),
                'last_page'    => $attendances->lastPage(),
                'per_page'     => $attendances->perPage(),
                'total'        => $attendances->total(),
            ]
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $attendance = Attendance::with('employee')->find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => "Attendance record with ID {$id} not found."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new AttendanceResource($attendance),
        ]);
    }

    public function dailySummary(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $date = $request->input('date', Carbon::today('Asia/Kolkata')->format('Y-m-d'));

        $totalPunches = Attendance::whereDate('punch_date', $date)->count();
        $totalPresent = Attendance::whereDate('punch_date', $date)->where('show_status', 'present')->count();
        $totalLate = Attendance::whereDate('punch_date', $date)->where('show_status', 'late')->count();

        return response()->json([
            'success' => true,
            'date'    => $date,
            'summary' => [
                'total_punches' => $totalPunches,
                'present_count' => $totalPresent,
                'late_count'    => $totalLate,
            ]
        ]);
    }

    /**
     * HRsale Compatible Attendance Retrieval Endpoint
     * POST /api/attendance or POST /api/v1/attendance
     */
    public function hrsaleAttendance(HRsaleAttendanceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Attendance::with('employee');

        // Date filtering logic
        if (!empty($validated['punch_date'])) {
            $query->whereDate('punch_date', $validated['punch_date']);
        } elseif (!empty($validated['start_date']) || !empty($validated['end_date'])) {
            if (!empty($validated['start_date'])) {
                $query->whereDate('punch_date', '>=', $validated['start_date']);
            }
            if (!empty($validated['end_date'])) {
                $query->whereDate('punch_date', '<=', $validated['end_date']);
            }
        } else {
            // Default to today's server date if no date filters are supplied
            $query->whereDate('punch_date', Carbon::today('Asia/Kolkata')->format('Y-m-d'));
        }

        // Card number filter
        if (!empty($validated['card_no'])) {
            $query->where('card_no', $validated['card_no']);
        }

        // Employee filter (ID, badgenumber, or employee_id string)
        if (!empty($validated['employee_id'])) {
            $empId = $validated['employee_id'];
            $query->where(function ($q) use ($empId) {
                $q->where('badgenumber', $empId)
                  ->orWhere('card_no', $empId)
                  ->orWhereHas('employee', function ($eq) use ($empId) {
                      $eq->where('employee_id', $empId)
                         ->orWhere('id', $empId);
                  });
            });
        }

        // Company filter
        if (!empty($validated['company_id'])) {
            $companyId = $validated['company_id'];
            $query->whereHas('employee', function ($q) use ($companyId) {
                $q->where('company', 'like', "%{$companyId}%")
                  ->orWhere('id', $companyId);
            });
        }

        $attendances = $query->orderBy('punch_date', 'asc')->orderBy('check_in_time', 'asc')->get();

        $overrideService = app(\App\Services\AttendanceOverrideService::class);
        $attendances = $overrideService->adjustAttendanceList($attendances);

        return response()->json(HRsaleAttendanceResource::collection($attendances)->resolve(), 200);
    }
}

