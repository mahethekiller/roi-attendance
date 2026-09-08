<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\SyncLog;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->superAdmin = User::where('email', 'admin@example.com')->first();

        $this->adminUser = User::factory()->create([
            'email' => 'testadmin@example.com',
        ]);
        $this->adminUser->assignRole('admin');
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $responseRoot = $this->get('/dashboard');
        $responseRoot->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard_with_kpi_metrics(): void
    {
        // Create employees
        $emp1 = Employee::create([
            'employee_id' => 'EMP-001',
            'card_no' => '1001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'company' => 'Acme Corp',
        ]);

        $emp2 = Employee::create([
            'employee_id' => 'EMP-002',
            'card_no' => '1002',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'company' => 'Acme Corp',
        ]);

        $emp3 = Employee::create([
            'employee_id' => 'EMP-003',
            'card_no' => '1003',
            'first_name' => 'Alice',
            'last_name' => 'Brown',
            'email' => 'alice@example.com',
            'company' => 'Globex',
        ]);

        $today = Carbon::today()->toDateString();

        // 1 on-time punch
        Attendance::create([
            'card_no' => '1001',
            'punch_date' => $today,
            'check_in_time' => '08:45:00',
            'check_in_datetime' => Carbon::today()->setHour(8)->setMinute(45),
            'show_status' => 'Present',
        ]);

        // 1 late punch (>09:15)
        Attendance::create([
            'card_no' => '1002',
            'punch_date' => $today,
            'check_in_time' => '09:30:00',
            'check_in_datetime' => Carbon::today()->setHour(9)->setMinute(30),
            'show_status' => 'Late',
        ]);

        // emp3 has no punch today (absent)

        // Seed sync log
        SyncLog::create([
            'trigger_type' => 'manual',
            'status' => 'success',
            'imported_count' => 2,
            'updated_count' => 0,
            'message' => 'Synced 2 punches',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');

        // Check view variables
        $response->assertViewHas('totalEmployees', 3);
        $response->assertViewHas('todayPresent', 2);
        $response->assertViewHas('todayAbsent', 1);
        $response->assertViewHas('attendanceRate', 66.7);
        $response->assertViewHas('companyBreakdown');
        $response->assertViewHas('trendLabels');
        $response->assertViewHas('trendPresent');
        $response->assertViewHas('trendAbsent');
        $response->assertViewHas('hourlyLabels');
        $response->assertViewHas('hourlyPunches');

        // Check HTML content
        $response->assertSee('Dashboard Overview');
        $response->assertSee('Total Staff');
        $response->assertSee('Present Today');
        $response->assertSee('Absent Today');
        $response->assertSee('Attendance Rate');
        $response->assertSee('Acme Corp');
        $response->assertSee('Globex');
        $response->assertSee('attendanceTrendChart');
        $response->assertSee('hourlyPunchChart');
    }

    public function test_super_admin_has_unrestricted_access(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
    }

    public function test_employee_self_service_stats_rendered_when_user_linked_to_employee(): void
    {
        $emp = Employee::create([
            'user_id' => $this->adminUser->id,
            'employee_id' => 'EMP-ADMIN',
            'card_no' => '9999',
            'first_name' => 'Admin',
            'last_name' => 'Officer',
            'email' => $this->adminUser->email,
            'company' => 'HQ',
        ]);

        $today = Carbon::today()->toDateString();
        Attendance::create([
            'card_no' => '9999',
            'punch_date' => $today,
            'check_in_time' => '08:50:00',
            'check_in_datetime' => Carbon::today()->setHour(8)->setMinute(50),
            'show_status' => 'Present',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('personalStats');
        $response->assertSee('My Attendance Card');
        $response->assertSee('Card ID: 9999');
        $response->assertSee('1 Days Present');
    }
}
