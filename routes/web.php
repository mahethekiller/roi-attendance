<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\SyncLogController;
use App\Http\Controllers\Admin\ApiTokenController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApiDocsController;
use App\Http\Controllers\Admin\ApiLogController;
use App\Http\Controllers\Admin\AttendanceOverrideController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    // Employees
    Route::middleware('permission:employees.import')->group(function () {
        Route::get('/employees/sample-csv', [EmployeeController::class, 'downloadSampleCsv'])->name('employees.sample-csv');
        Route::post('/employees/import', [EmployeeController::class, 'importCsv'])->name('employees.import');
    });

    Route::get('/employees', [EmployeeController::class, 'index'])->middleware('permission:employees.view')->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->middleware('permission:employees.create')->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->middleware('permission:employees.create')->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->middleware('permission:employees.view')->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->middleware('permission:employees.edit')->name('employees.edit');
    Route::match(['put', 'patch'], '/employees/{employee}', [EmployeeController::class, 'update'])->middleware('permission:employees.edit')->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->middleware('permission:employees.delete')->name('employees.destroy');

    // Attendances
    Route::get('/attendances', [AttendanceController::class, 'index'])->middleware('permission:attendances.view')->name('attendances.index');
    Route::post('/attendances/sync', [AttendanceController::class, 'sync'])->middleware('permission:attendances.sync')->name('attendances.sync');

    // Sync History Logs
    Route::get('/sync-logs', [SyncLogController::class, 'index'])->middleware('permission:sync-logs.view')->name('sync-logs.index');
    Route::post('/sync-logs/clear', [SyncLogController::class, 'clear'])->middleware('permission:sync-logs.clear')->name('sync-logs.clear');

    // Attendance Overrides Management (Super Admin)
    Route::middleware('permission:attendance.overrides.manage')->group(function () {
        Route::get('/attendance-overrides', [AttendanceOverrideController::class, 'index'])->name('attendance-overrides.index');
        Route::get('/attendance-overrides/create', [AttendanceOverrideController::class, 'create'])->name('attendance-overrides.create');
        Route::post('/attendance-overrides', [AttendanceOverrideController::class, 'store'])->name('attendance-overrides.store');
        Route::get('/attendance-overrides/{attendanceOverride}/edit', [AttendanceOverrideController::class, 'edit'])->name('attendance-overrides.edit');
        Route::match(['put', 'patch'], '/attendance-overrides/{attendanceOverride}', [AttendanceOverrideController::class, 'update'])->name('attendance-overrides.update');
        Route::patch('/attendance-overrides/{attendanceOverride}/toggle', [AttendanceOverrideController::class, 'toggle'])->name('attendance-overrides.toggle');
        Route::delete('/attendance-overrides/{attendanceOverride}', [AttendanceOverrideController::class, 'destroy'])->name('attendance-overrides.destroy');
    });

    // API Token Management
    Route::middleware('permission:api.tokens.manage')->group(function () {
        Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::delete('/api-tokens/{id}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
    });

    // API Documentation & TXT Export
    Route::middleware('permission:api.docs.view')->group(function () {
        Route::get('/api-docs', [ApiDocsController::class, 'index'])->name('api-docs.index');
        Route::get('/api-docs/export-txt', [ApiDocsController::class, 'exportTxt'])->name('api-docs.export-txt');
        Route::get('/api-docs/export/{endpoint}', [ApiDocsController::class, 'exportEndpointTxt'])->name('api-docs.export-endpoint');
    });

    // API Request Audit Logs
    Route::get('/api-logs', [ApiLogController::class, 'index'])->middleware('permission:api.logs.view')->name('api-logs.index');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:users.view')->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.edit')->name('users.edit');
    Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');
});

// External Cron Webhook Endpoint
Route::match(['GET', 'POST'], '/cron/sync-attendance', [AttendanceController::class, 'cronWebhook'])->name('cron.sync-attendance');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

