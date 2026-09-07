<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Global HRsale-compatible Endpoint: POST /api/attendance
Route::middleware([LogApiRequests::class, 'auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/attendance', [AttendanceApiController::class, 'hrsaleAttendance']);
});

Route::prefix('v1')->middleware([LogApiRequests::class])->group(function () {

    // Authentication token endpoint (Public, Throttled)
    Route::post('/auth/token', [AttendanceApiController::class, 'token'])
        ->middleware('throttle:10,1');

    // Protected Attendance Retrieval Endpoints
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('/attendances', [AttendanceApiController::class, 'index']);
        Route::post('/attendance', [AttendanceApiController::class, 'hrsaleAttendance']);
        Route::get('/attendances/daily-summary', [AttendanceApiController::class, 'dailySummary']);
        Route::get('/attendances/{id}', [AttendanceApiController::class, 'show']);
    });
});

