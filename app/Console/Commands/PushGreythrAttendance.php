<?php

namespace App\Console\Commands;

use App\Services\GreythrPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PushGreythrAttendance extends Command
{
    protected $signature = 'attendance:push-greythr 
                            {--start-date= : The start date in YYYY-MM-DD format} 
                            {--end-date= : The end date in YYYY-MM-DD format} 
                            {--days=1 : Number of past days to include (default 1: yesterday and today)}';

    protected $description = 'Push other companies biometric attendance swipes to greytHR API';

    public function handle(GreythrPushService $service): int
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

        $this->info("Initiating greytHR Attendance Swipes Push from {$startDate} to {$endDate}...");

        $result = $service->push($startDate, $endDate, 'command');

        if ($result['success']) {
            $this->info("✓ " . $result['message'] . " (Swipes: {$result['swipes_count']})");
            return Command::SUCCESS;
        } else {
            $this->error("✗ " . $result['message']);
            return Command::FAILURE;
        }
    }
}
