<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HRsaleAttendanceApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->user = User::factory()->create([
            'email' => 'hrsale_test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->token = $this->user->createToken('hrsale-test-token')->plainTextToken;

        // Seed sample employee
        Employee::create([
            'user_id' => $this->user->id,
            'employee_id' => 'EMP-1002',
            'card_no' => '1002',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'company' => 'Demo Company',
        ]);

        // Seed sample attendance records
        Attendance::create([
            'card_no' => '1002',
            'punch_date' => '2026-06-29',
            'check_in_time' => '09:00:00',
            'check_out_time' => '18:00:00',
            'show_status' => 'present',
        ]);

        Attendance::create([
            'card_no' => '1002',
            'punch_date' => Carbon::today('Asia/Kolkata')->format('Y-m-d'),
            'check_in_time' => '09:15:00',
            'check_out_time' => '18:15:00',
            'show_status' => 'late',
        ]);
    }

    public function test_unauthenticated_request_returns_custom_401_json(): void
    {
        $response = $this->postJson('/api/attendance', []);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Missing or Invalid Authorization token'
            ]);
    }

    public function test_post_attendance_defaults_to_today_when_no_date_specified(): void
    {
        $today = Carbon::today('Asia/Kolkata')->format('Y-m-d');

        $response = $this->withToken($this->token)->postJson('/api/attendance', []);

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals($today, $data[0]['punch_date']);
        $this->assertEquals('1002', $data[0]['card_no']);
        $this->assertEquals('09:15:00', $data[0]['clock_in']);
        $this->assertEquals('18:15:00', $data[0]['clock_out']);
        $this->assertEquals('09:00:00', $data[0]['total_work']);
        $this->assertEquals('EMP-1002', $data[0]['employee_id']);
        $this->assertEquals('John Doe', $data[0]['employee_name']);
        $this->assertEquals('Demo Company', $data[0]['company_name']);

        // Assert removed fields are absent
        $this->assertArrayNotHasKey('attendance_status', $data[0]);
        $this->assertArrayNotHasKey('clock_in_ip_address', $data[0]);
        $this->assertArrayNotHasKey('clock_out_ip_address', $data[0]);
        $this->assertArrayNotHasKey('clock_in_out', $data[0]);
        $this->assertArrayNotHasKey('time_late', $data[0]);
        $this->assertArrayNotHasKey('total_rest', $data[0]);
    }

    public function test_post_attendance_filter_by_punch_date(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/attendance', [
            'punch_date' => '2026-06-29',
            'company_id' => 'Demo Company'
        ]);

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals('2026-06-29', $data[0]['punch_date']);
        $this->assertEquals('09:00:00', $data[0]['clock_in']);
        $this->assertEquals('18:00:00', $data[0]['clock_out']);
        $this->assertEquals('09:00:00', $data[0]['total_work']);
    }

    public function test_post_attendance_filter_by_date_range(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/attendance', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function test_post_attendance_filter_by_card_no_and_employee_id(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/attendance', [
            'punch_date' => '2026-06-29',
            'card_no' => '1002',
            'employee_id' => 'EMP-1002'
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data);

        $mismatch = $this->withToken($this->token)->postJson('/api/attendance', [
            'punch_date' => '2026-06-29',
            'card_no' => '9999'
        ]);
        $mismatch->assertStatus(200);
        $this->assertCount(0, $mismatch->json());
    }

    public function test_v1_post_attendance_endpoint_works(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/attendance', [
            'punch_date' => '2026-06-29',
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }
}
