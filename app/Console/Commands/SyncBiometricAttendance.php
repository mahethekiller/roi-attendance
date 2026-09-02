<?php

namespace App\Console\Commands;

use App\Services\BiometricSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncBiometricAttendance extends Command
{
    protected $signature = 'attendance:sync-biometric 
                            {--start-date= : The start date in YYYY-MM-DD format} 
                            {--end-date= : The end date in YYYY-MM-DD format} 
                            {--days=3 : Number of past days to sync}';

    protected $description = 'Sync biometric machine attendance logs from external API';

    public function handle(BiometricSyncService $service): int
    {
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $days = (int) $this->option('days');

        if (!$startDate) {
            $startDate = Carbon::now('Asia/Kolkata')->subDays($days)->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now('Asia/Kolkata')->format('Y-m-d');
        }

        $this->info("Initiating Biometric Attendance Sync from {$startDate} to {$endDate}...");

        $result = $service->sync($startDate, $endDate, 'command');

        if ($result['success']) {
            $this->info("✓ " . $result['message']);
            return Command::SUCCESS;
        } else {
            $this->error("✗ " . $result['message']);
            return Command::FAILURE;
        }
    }
}
