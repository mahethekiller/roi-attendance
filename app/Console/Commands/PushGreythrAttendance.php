<?php

namespace App\Console\Commands;

use App\Services\GreythrPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PushGreythrAttendance extends Command
{
    protected $signature = 'attendance:push-greythr 
                            {--start-date= : The start date in YYYY-MM-DD format (default: today)} 
                            {--end-date= : The end date in YYYY-MM-DD format (default: today)} 
                            {--days=0 : Number of past days to include (default 0: current date only)}
                            {--employee= : Optional specific employee ID (e.g. i2k2-2265) or card number}';

    protected $description = 'Push other companies biometric attendance swipes to greytHR API for the current date';

    public function handle(GreythrPushService $service): int
    {
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $days = (int) $this->option('days');
        $employee = $this->option('employee');
        $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        if (!$startDate) {
            $startDate = $days > 0 ? Carbon::now('Asia/Kolkata')->subDays($days)->format('Y-m-d') : $today;
        }
        if (!$endDate) {
            $endDate = $today;
        }

        $filterMsg = $employee ? " for employee [{$employee}]" : '';
        $this->info("Initiating greytHR Attendance Swipes Push from {$startDate} to {$endDate}{$filterMsg}...");

        $result = $service->push($startDate, $endDate, 'command', null, $employee);

        if ($result['success']) {
            $this->info("✓ " . $result['message'] . " (Swipes: {$result['swipes_count']})");
            return Command::SUCCESS;
        } else {
            $this->error("✗ " . $result['message']);
            return Command::FAILURE;
        }
    }
}
