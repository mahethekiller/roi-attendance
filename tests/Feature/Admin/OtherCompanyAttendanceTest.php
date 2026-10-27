<?php

namespace Tests\Feature\Admin;

use App\Models\Employee;
use App\Models\OtherCompanyAttendance;
use App\Models\User;
use App\Services\BiometricSyncService;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OtherCompanyAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->adminUser = User::factory()->create([
            'email' => 'admin_test@example.com',
        ]);
        $this->adminUser->assignRole('admin');

        $this->regularUser = User::factory()->create([
            'email' => 'regular_test@example.com',
        ]);
        $this->regularUser->assignRole('user');
    }

    public function test_guest_is_redirected_to_login_when_accessing_other_attendances(): void
    {
        $response = $this->get(route('admin.other-attendances.index'));
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_is_forbidden(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.other-attendances.index'));
        $response->assertStatus(403);
    }

    public function test_authorized_admin_can_view_other_companies_attendance_page(): void
    {
        // Seed a sample other company record
        OtherCompanyAttendance::create([
            'card_no'            => 'CARD-999',
            'badgenumber'        => 'BADGE-999',
            'employee_name'      => 'Robert Partner',
            'company_name'       => 'Partner Tech Solutions',
            'punch_date'         => Carbon::today()->format('Y-m-d'),
            'check_in_datetime'  => Carbon::today()->setTime(9, 15, 0),
            'check_out_datetime' => Carbon::today()->setTime(18, 15, 0),
            'check_in_time'      => '09:15:00',
            'check_out_time'     => '18:15:00',
            'show_status'        => 'present',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.other-attendances.index', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertSee('Other Companies Attendance');
        $response->assertSee('Partner Tech Solutions');
        $response->assertSee('Robert Partner');
        $response->assertSee('CARD-999');
    }

    public function test_filters_by_company_and_search_query(): void
    {
        $today = Carbon::today()->format('Y-m-d');

        OtherCompanyAttendance::create([
            'card_no'            => 'CARD-111',
            'employee_name'      => 'Alice Alpha',
            'company_name'       => 'Alpha Dynamics',
            'punch_date'         => $today,
            'check_in_time'      => '09:00:00',
            'show_status'        => 'present',
        ]);

        OtherCompanyAttendance::create([
            'card_no'            => 'CARD-222',
            'employee_name'      => 'Bob Beta',
            'company_name'       => 'Beta Logistics',
            'punch_date'         => $today,
            'check_in_time'      => '09:30:00',
            'show_status'        => 'present',
        ]);

        // Filter by company 'Alpha Dynamics'
        $responseCompany = $this->actingAs($this->adminUser)->get(route('admin.other-attendances.index', [
            'date'    => $today,
            'company' => 'Alpha Dynamics',
        ]));

        $responseCompany->assertOk();
        $responseCompany->assertSee('Alice Alpha');
        $responseCompany->assertDontSee('Bob Beta');

        // Search by keyword 'Bob'
        $responseSearch = $this->actingAs($this->adminUser)->get(route('admin.other-attendances.index', [
            'date'   => $today,
            'search' => 'Bob',
        ]));

        $responseSearch->assertOk();
        $responseSearch->assertSee('Bob Beta');
        $responseSearch->assertDontSee('Alice Alpha');
    }

    public function test_biometric_sync_routes_other_companies_to_separate_table(): void
    {
        $today = Carbon::today()->format('Y-m-d');

        // Register one ROI Mantra employee and one Partner Company employee
        Employee::create([
            'employee_id' => 'ROI-001',
            'card_no'     => '10001',
            'first_name'  => 'John',
            'last_name'   => 'Roi',
            'email'       => 'john@roimantra.com',
            'company'     => 'ROI Mantra Private Limited',
        ]);

        Employee::create([
            'employee_id' => 'EXT-002',
            'card_no'     => '20002',
            'first_name'  => 'Partner',
            'last_name'   => 'Employee',
            'email'       => 'partner@external.com',
            'company'     => 'Acme Technologies',
        ]);

        // Mock external Biometric API response
        Http::fake([
            '*' => Http::response([
                'status'  => 1,
                'message' => 'Success',
                'data'    => [
                    [
                        'card_no'      => '10001',
                        'badgenumber'  => 'ROI-001',
                        'punch_date'   => $today,
                        'minchecktime' => "{$today} 09:30:00",
                        'maxchecktime' => "{$today} 18:30:00",
                        'mintime'      => '09:30:00',
                        'maxtime'      => '18:30:00',
                    ],
                    [
                        'card_no'      => '20002',
                        'badgenumber'  => 'EXT-002',
                        'punch_date'   => $today,
                        'minchecktime' => "{$today} 10:00:00",
                        'maxchecktime' => "{$today} 19:00:00",
                        'mintime'      => '10:00:00',
                        'maxtime'      => '19:00:00',
                    ],
                    [
                        'card_no'      => '30003',
                        'badgenumber'  => 'UNKNOWN-003',
                        'punch_date'   => $today,
                        'minchecktime' => "{$today} 08:45:00",
                        'maxchecktime' => "{$today} 17:45:00",
                        'mintime'      => '08:45:00',
                        'maxtime'      => '17:45:00',
                    ],
                ]
            ], 200),
        ]);

        $service = app(BiometricSyncService::class);
        $result = $service->sync($today, $today, 'test');

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported']); // 1 ROI Mantra
        $this->assertEquals(2, $result['other_company_imported']); // 1 Partner + 1 Unknown external

        // Assert ROI Mantra record saved to attendances table
        $roiAtt = \App\Models\Attendance::where('card_no', '10001')->first();
        $this->assertNotNull($roiAtt);
        $this->assertEquals($today, $roiAtt->punch_date->format('Y-m-d'));

        // Assert Other Company records saved to other_company_attendances table
        $otherAtt = OtherCompanyAttendance::where('card_no', '20002')->first();
        $this->assertNotNull($otherAtt);
        $this->assertEquals('Acme Technologies', $otherAtt->company_name);
        $this->assertEquals($today, $otherAtt->punch_date->format('Y-m-d'));

        $unknownAtt = OtherCompanyAttendance::where('card_no', '30003')->first();
        $this->assertNotNull($unknownAtt);
        $this->assertEquals($today, $unknownAtt->punch_date->format('Y-m-d'));
    }
}
