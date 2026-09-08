<?php

namespace Tests\Unit;

use App\Services\AttendanceOverrideService;
use Tests\TestCase;

class AttendanceOverrideServiceTest extends TestCase
{
    protected AttendanceOverrideService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AttendanceOverrideService(
            enabled: true,
            targetEmployeeIds: ['I2K2-0340', 'EMP-TEST'],
            targetCardNos: ['1234', '9876']
        );
    }

    public function test_matches_target_employee_by_id_or_card_no(): void
    {
        $this->assertTrue($this->service->matchesEmployee('I2K2-0340', '9999'));
        $this->assertTrue($this->service->matchesEmployee('OTHER-EMP', '1234'));
        $this->assertTrue($this->service->matchesEmployee('EMP-TEST', '9876'));

        $this->assertFalse($this->service->matchesEmployee('OTHER-EMP', '9999'));
        $this->assertFalse($this->service->matchesEmployee(null, null));
    }

    public function test_override_can_be_disabled(): void
    {
        $disabledService = new AttendanceOverrideService(
            enabled: false,
            targetEmployeeIds: ['I2K2-0340'],
            targetCardNos: ['1234']
        );

        $this->assertFalse($disabledService->matchesEmployee('I2K2-0340', '1234'));
        $this->assertSame('2026-09-08 10:10:00', $disabledService->adjustCheckIn('2026-09-08 10:10:00'));
    }

    public function test_adjust_check_in_within_10_00_to_10_20_window(): void
    {
        $original = '2026-09-08 10:14:22';
        $adjusted = $this->service->adjustCheckIn($original);

        $this->assertNotSame($original, $adjusted);
        $this->assertStringStartsWith('2026-09-08 09:', $adjusted);

        // Minutes must be between 20 and 35
        $timePart = substr($adjusted, 11);
        [$hour, $min, $sec] = explode(':', $timePart);

        $this->assertSame('09', $hour);
        $this->assertGreaterThanOrEqual(20, (int) $min);
        $this->assertLessThanOrEqual(35, (int) $min);
        $this->assertGreaterThanOrEqual(0, (int) $sec);
        $this->assertLessThanOrEqual(59, (int) $sec);
    }

    public function test_does_not_adjust_check_in_outside_10_00_to_10_20_window(): void
    {
        // 09:45:00 is before 10:00:00
        $this->assertSame('2026-09-08 09:45:00', $this->service->adjustCheckIn('2026-09-08 09:45:00'));

        // 10:00:00 boundary
        $this->assertSame('2026-09-08 10:00:00', $this->service->adjustCheckIn('2026-09-08 10:00:00'));

        // 10:20:00 boundary
        $this->assertSame('2026-09-08 10:20:00', $this->service->adjustCheckIn('2026-09-08 10:20:00'));

        // 10:30:00 is after 10:20:00
        $this->assertSame('2026-09-08 10:30:00', $this->service->adjustCheckIn('2026-09-08 10:30:00'));
    }

    public function test_adjust_check_out_ensures_at_least_9_hours_duration(): void
    {
        $checkIn = '2026-09-08 09:25:00';

        // 7 hours duration (less than 9 hours) -> should be modified to checkIn + 9 hours
        $checkOutShort = '2026-09-08 16:25:00';
        $adjustedOut = $this->service->adjustCheckOut($checkIn, $checkOutShort);

        $this->assertSame('2026-09-08 18:25:00', $adjustedOut);

        // 9.5 hours duration (already >= 9 hours) -> should remain unchanged
        $checkOutLong = '2026-09-08 19:00:00';
        $this->assertSame($checkOutLong, $this->service->adjustCheckOut($checkIn, $checkOutLong));

        // Same time checkIn == checkOut -> should remain unchanged
        $this->assertSame($checkIn, $this->service->adjustCheckOut($checkIn, $checkIn));
    }

    public function test_adjust_attendance_list_transforms_records_correctly(): void
    {
        $records = [
            (object) [
                'employee_id' => 'I2K2-0340',
                'card_no' => '1234',
                'check_in_datetime' => '2026-09-08 10:12:00',
                'check_out_datetime' => '2026-09-08 17:00:00',
                'check_in_time' => '10:12:00',
                'check_out_time' => '17:00:00',
            ],
            (object) [
                'employee_id' => 'OTHER-001',
                'card_no' => '9999',
                'check_in_datetime' => '2026-09-08 10:12:00',
                'check_out_datetime' => '2026-09-08 17:00:00',
                'check_in_time' => '10:12:00',
                'check_out_time' => '17:00:00',
            ]
        ];

        $adjusted = $this->service->adjustAttendanceList($records);

        // Target record modified
        $this->assertStringStartsWith('2026-09-08 09:', $adjusted[0]->check_in_datetime);
        $this->assertStringStartsWith('09:', $adjusted[0]->check_in_time);
        // Check out duration at least 9 hours from adjusted in
        $inTime = strtotime($adjusted[0]->check_in_datetime);
        $outTime = strtotime($adjusted[0]->check_out_datetime);
        $this->assertGreaterThanOrEqual(9 * 3600, $outTime - $inTime);

        // Other record left untouched
        $this->assertSame('2026-09-08 10:12:00', $adjusted[1]->check_in_datetime);
        $this->assertSame('2026-09-08 17:00:00', $adjusted[1]->check_out_datetime);
    }
}
