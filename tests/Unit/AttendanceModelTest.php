<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_attendance_record_can_be_created_and_linked_to_employee(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-TEST-01',
            'card_no' => 'CRD-PUNCH-99',
            'first_name' => 'Gordon',
            'last_name' => 'Freeman',
            'email' => $user->email,
        ]);

        $attendance = Attendance::create([
            'card_no' => $employee->card_no,
            'punch_date' => '2026-09-02',
            'check_in_datetime' => '2026-09-02 08:55:00',
            'check_out_datetime' => '2026-09-02 17:05:00',
            'badgenumber' => 'BDG-001',
            'check_in_time' => '08:55:00',
            'check_out_time' => '17:05:00',
            'show_status' => 'present',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'card_no' => 'CRD-PUNCH-99',
            'badgenumber' => 'BDG-001',
            'show_status' => 'present',
        ]);

        $this->assertInstanceOf(Employee::class, $attendance->employee);
        $this->assertEquals($employee->id, $attendance->employee->id);
        $this->assertCount(1, $employee->attendances);
    }
}
