<?php

namespace Tests\Feature\Admin;

use App\Models\Employee;
use App\Models\GreythrPushLog;
use App\Models\OtherCompanyAttendance;
use App\Models\User;
use App\Services\GreythrPushService;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GreythrPushTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->adminUser = User::factory()->create([
            'email' => 'admin_push@example.com',
        ]);
        $this->adminUser->assignRole('admin');

        $this->regularUser = User::factory()->create([
            'email' => 'regular_push@example.com',
        ]);
        $this->regularUser->assignRole('user');
    }

    public function test_guest_is_redirected_to_login_when_accessing_greythr_logs(): void
    {
        $response = $this->get(route('admin.greythr-logs.index'));
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_cannot_access_greythr_logs(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.greythr-logs.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_greythr_logs_page_and_metrics(): void
    {
        // Seed sample log
        GreythrPushLog::create([
            'trigger_type'     => 'cron',
            'start_date'       => Carbon::yesterday()->format('Y-m-d'),
            'end_date'         => Carbon::today()->format('Y-m-d'),
            'records_count'    => 5,
            'swipes_count'     => 10,
            'status'           => 'success',
            'http_status'      => 201,
            'message'          => 'Employee swipes are saved successfully.',
            'payload_sample'   => ["2026-10-27T09:30:00.000+05:30,89170020,door 1,1"],
            'response_payload' => ['messageType' => 'SUCCESS'],
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.greythr-logs.index'));

        $response->assertOk();
        $response->assertSee('greytHR Push Logs');
        $response->assertSee('i2k2-networks.greythr.com');
        $response->assertSee('10'); // Swipes
        $response->assertSee('Employee swipes are saved successfully.');
        $response->assertSee('89170020'); // Employee ID in sent payload
        $response->assertSee('door 1,1'); // Swipe direction in sent payload
    }

    public function test_service_obtains_and_caches_oauth_token(): void
    {
        Cache::flush();

        Http::fake([
            'https://api.greythr.com/oauth2/v2/client-token' => Http::response([
                'access_token' => 'mock_token_jwt_12345',
                'expires_in'   => 3600,
                'token_type'   => 'bearer',
            ], 200),
        ]);

        $service = app(GreythrPushService::class);
        $token1 = $service->getAccessToken();

        $this->assertEquals('mock_token_jwt_12345', $token1);

        // Second call should return cached token without HTTP request
        Http::fake([
            'https://api.greythr.com/oauth2/v2/client-token' => Http::response(['error' => 'should_not_reach'], 500),
        ]);

        $token2 = $service->getAccessToken();
        $this->assertEquals('mock_token_jwt_12345', $token2);
    }

    public function test_service_formats_swipes_in_required_csv_iso_structure(): void
    {
        $date = Carbon::parse('2026-10-27')->format('Y-m-d');

        // Link with an employee
        $emp = Employee::create([
            'employee_id' => '89170020',
            'card_no'     => 'CRD-8917',
            'first_name'  => 'Ankit',
            'last_name'   => 'Sharma',
            'email'       => 'ankit@partner.com',
            'company'     => 'i2k2 Networks',
        ]);

        OtherCompanyAttendance::create([
            'card_no'            => 'CRD-8917',
            'badgenumber'        => '89170020',
            'employee_name'      => 'Ankit Sharma',
            'company_name'       => 'i2k2 Networks',
            'punch_date'         => $date,
            'check_in_time'      => '09:30:00',
            'check_out_time'     => '18:30:00',
            'show_status'        => 'present',
        ]);

        $service = app(GreythrPushService::class);
        $formatted = $service->formatSwipes($date, $date);

        $this->assertEquals(1, $formatted['records_count']);
        $this->assertCount(2, $formatted['swipes']);

        // Check format: "2026-10-27T09:30:00.000+05:30,89170020,door 1,1"
        $this->assertStringContainsString('89170020,door 1,1', $formatted['swipes'][0]);
        $this->assertStringContainsString('09:30:00.000+05:30', $formatted['swipes'][0]);

        $this->assertStringContainsString('89170020,door 2,0', $formatted['swipes'][1]);
        $this->assertStringContainsString('18:30:00.000+05:30', $formatted['swipes'][1]);
    }

    public function test_service_pushes_swipes_and_records_audit_log(): void
    {
        $date = Carbon::parse('2026-10-27')->format('Y-m-d');

        OtherCompanyAttendance::create([
            'card_no'            => 'CRD-9001',
            'badgenumber'        => '9001',
            'employee_name'      => 'Test User',
            'company_name'       => 'i2k2 Networks',
            'punch_date'         => $date,
            'check_in_time'      => '10:00:00',
            'check_out_time'     => '19:00:00',
            'show_status'        => 'present',
        ]);

        Http::fake([
            'https://api.greythr.com/oauth2/v2/client-token' => Http::response([
                'access_token' => 'valid_token_xyz',
                'expires_in'   => 3600,
            ], 200),
            'https://api.greythr.com/attendance/v2/employee/swipes' => Http::response([
                'message'        => 'Employee swipes are saved successfully.',
                'invalidRecords' => null,
                'messageType'    => 'SUCCESS',
            ], 201),
        ]);

        $service = app(GreythrPushService::class);
        $result = $service->push($date, $date, 'command');

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['swipes_count']);

        // Assert audit log was stored
        $log = GreythrPushLog::latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals('command', $log->trigger_type);
        $this->assertEquals('success', $log->status);
        $this->assertEquals(201, $log->http_status);
        $this->assertEquals(2, $log->swipes_count);
        $this->assertCount(2, $log->payload_sample);
    }

    public function test_console_command_runs_successfully(): void
    {
        $date = Carbon::today()->format('Y-m-d');

        OtherCompanyAttendance::create([
            'card_no'       => 'CRD-1234',
            'badgenumber'   => '1234',
            'company_name'  => 'i2k2 Networks',
            'punch_date'    => $date,
            'check_in_time' => '09:00:00',
        ]);

        Http::fake([
            'https://api.greythr.com/oauth2/v2/client-token' => Http::response([
                'access_token' => 'token_cli',
                'expires_in'   => 3600,
            ], 200),
            'https://api.greythr.com/attendance/v2/employee/swipes' => Http::response([
                'message' => 'Swipes saved',
            ], 201),
        ]);

        $this->artisan('attendance:push-greythr --days=0')
            ->assertSuccessful();
    }

    public function test_admin_can_trigger_push_from_ui(): void
    {
        $today = Carbon::today()->format('Y-m-d');

        Http::fake([
            'https://api.greythr.com/oauth2/v2/client-token' => Http::response([
                'access_token' => 'token_ui',
                'expires_in'   => 3600,
            ], 200),
            'https://api.greythr.com/attendance/v2/employee/swipes' => Http::response([
                'message' => 'Saved',
            ], 201),
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.greythr-logs.push'), [
            'start_date' => $today,
            'end_date'   => $today,
        ]);

        $response->assertRedirect(route('admin.greythr-logs.index'));
        $response->assertSessionHas('success');
    }

    public function test_employee_id_is_prioritized_over_card_number(): void
    {
        $date = Carbon::parse('2026-10-27')->format('Y-m-d');

        // Employee with specific ID format e.g. i2k2-2265 and card 12265
        Employee::create([
            'employee_id' => 'i2k2-2265',
            'card_no'     => '12265',
            'first_name'  => 'RamJi',
            'last_name'   => 'Gupta',
            'email'       => 'ram@i2k2.com',
            'company'     => 'I2K2 Networks Private Limited',
        ]);

        OtherCompanyAttendance::create([
            'card_no'        => '12265',
            'badgenumber'    => '12265', // Numeric badgenumber from machine
            'employee_name'  => 'RamJi Gupta',
            'company_name'   => 'I2K2 Networks Private Limited',
            'punch_date'     => $date,
            'check_in_time'  => '09:45:00',
            'check_out_time' => '19:15:00',
            'show_status'    => 'present',
        ]);

        $service = app(GreythrPushService::class);
        $formatted = $service->formatSwipes($date, $date);

        $this->assertEquals(1, $formatted['records_count']);
        $this->assertCount(2, $formatted['swipes']);

        // Must resolve to employee_id (i2k2-2265), NOT raw card_no (12265)
        $this->assertStringContainsString(',i2k2-2265,door 1,1', $formatted['swipes'][0]);
        $this->assertStringContainsString(',i2k2-2265,door 2,0', $formatted['swipes'][1]);
        $this->assertStringNotContainsString(',12265,door', $formatted['swipes'][0]);
    }

    public function test_employee_filter_in_service_and_cli(): void
    {
        $date = Carbon::parse('2026-10-27')->format('Y-m-d');

        Employee::create([
            'employee_id' => 'i2k2-2265',
            'card_no'     => '12265',
            'first_name'  => 'RamJi',
            'last_name'   => 'Gupta',
            'email'       => 'ram@i2k2.com',
            'company'     => 'I2K2 Networks Private Limited',
        ]);

        Employee::create([
            'employee_id' => 'i2k2-9999',
            'card_no'     => '19999',
            'first_name'  => 'Other',
            'last_name'   => 'Person',
            'email'       => 'other@i2k2.com',
            'company'     => 'I2K2 Networks Private Limited',
        ]);

        OtherCompanyAttendance::create([
            'card_no'        => '12265',
            'badgenumber'    => '12265',
            'punch_date'     => $date,
            'check_in_time'  => '09:00:00',
        ]);

        OtherCompanyAttendance::create([
            'card_no'        => '19999',
            'badgenumber'    => '19999',
            'punch_date'     => $date,
            'check_in_time'  => '10:00:00',
        ]);

        $service = app(GreythrPushService::class);

        // Filter by employee_id 'i2k2-2265'
        $formatted = $service->formatSwipes($date, $date, 'i2k2-2265');
        $this->assertEquals(1, $formatted['records_count']);
        $this->assertCount(1, $formatted['swipes']);
        $this->assertStringContainsString('i2k2-2265', $formatted['swipes'][0]);
    }

    public function test_admin_can_clear_logs(): void
    {
        GreythrPushLog::create([
            'trigger_type' => 'cron',
            'start_date'   => Carbon::today()->format('Y-m-d'),
            'end_date'     => Carbon::today()->format('Y-m-d'),
            'status'       => 'failed',
            'message'      => 'Error',
        ]);

        $this->assertEquals(1, GreythrPushLog::count());

        $response = $this->actingAs($this->adminUser)->post(route('admin.greythr-logs.clear'), [
            'scope' => 'all',
        ]);

        $response->assertRedirect(route('admin.greythr-logs.index'));
        $this->assertEquals(0, GreythrPushLog::count());
    }
}
