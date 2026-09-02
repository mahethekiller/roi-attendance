<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\SyncLogController;
use App\Http\Controllers\Admin\ApiTokenController;
use App\Http\Controllers\Admin\ApiDocsController;
use App\Http\Controllers\Admin\ApiLogController;

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/employees/sample-csv', [EmployeeController::class, 'downloadSampleCsv'])->name('employees.sample-csv');
    Route::post('/employees/import', [EmployeeController::class, 'importCsv'])->name('employees.import');
    Route::resource('employees', EmployeeController::class);

    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/sync', [AttendanceController::class, 'sync'])->name('attendances.sync');

    Route::get('/sync-logs', [SyncLogController::class, 'index'])->name('sync-logs.index');

    // API Token Management
    Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/api-tokens/{id}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

    // API Documentation & TXT Export
    Route::get('/api-docs', [ApiDocsController::class, 'index'])->name('api-docs.index');
    Route::get('/api-docs/export-txt', [ApiDocsController::class, 'exportTxt'])->name('api-docs.export-txt');

    // API Request Audit Logs
    Route::get('/api-logs', [ApiLogController::class, 'index'])->name('api-logs.index');

    Route::resource('users', UserController::class);
});

// External Cron Webhook Endpoint
Route::match(['GET', 'POST'], '/cron/sync-attendance', [AttendanceController::class, 'cronWebhook'])->name('cron.sync-attendance');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
