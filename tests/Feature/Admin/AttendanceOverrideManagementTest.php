<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\AttendanceOverride;
use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AttendanceOverrideManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $manager;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->superAdmin = User::where('email', 'admin@example.com')->first();

        $this->manager = User::factory()->create(['email' => 'manager@example.com']);
        $this->manager->assignRole('manager');

        $this->regularUser = User::factory()->create(['email' => 'user@example.com']);
        $this->regularUser->assignRole('user');
    }

    public function test_super_admin_can_view_attendance_overrides_list(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/attendance-overrides');

        $response->assertStatus(200);
        $response->assertViewIs('admin.attendance-overrides.index');
        $response->assertSee('Attendance Overrides');
        $response->assertSee('I2K2-0340');
        $response->assertSee('1234');
    }

    public function test_super_admin_can_create_override_rule(): void
    {
        $payload = [
            'employee_id'            => 'EMP-9001',
            'card_no'                => '9001',
            'employee_name'          => 'Custom VIP User',
            'check_in_window_start'  => '10:00',
            'check_in_window_end'    => '10:20',
            'adjusted_in_min_minute' => 22,
            'adjusted_in_max_minute' => 32,
            'min_duration_hours'     => 9.00,
            'is_active'              => '1',
            'notes'                  => 'VIP test rule',
        ];

        $response = $this->actingAs($this->superAdmin)->post('/admin/attendance-overrides', $payload);

        $response->assertRedirect('/admin/attendance-overrides');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendance_overrides', [
            'employee_id'            => 'EMP-9001',
            'card_no'                => '9001',
            'employee_name'          => 'Custom VIP User',
            'adjusted_in_min_minute' => 22,
            'adjusted_in_max_minute' => 32,
            'is_active'              => true,
        ]);
    }

    public function test_super_admin_can_update_override_rule(): void
    {
        $rule = AttendanceOverride::create([
            'employee_id'            => 'EMP-8888',
            'card_no'                => '8888',
            'employee_name'          => 'Old Name',
            'check_in_window_start'  => '10:00:00',
            'check_in_window_end'    => '10:20:00',
            'adjusted_in_min_minute' => 20,
            'adjusted_in_max_minute' => 35,
            'min_duration_hours'     => 9.00,
            'is_active'              => true,
        ]);

        $payload = [
            'employee_id'            => 'EMP-8888',
            'card_no'                => '8888',
            'employee_name'          => 'Updated VIP Name',
            'check_in_window_start'  => '10:05',
            'check_in_window_end'    => '10:25',
            'adjusted_in_min_minute' => 25,
            'adjusted_in_max_minute' => 30,
            'min_duration_hours'     => 8.50,
            'is_active'              => '1',
            'notes'                  => 'Updated note',
        ];

        $response = $this->actingAs($this->superAdmin)->put("/admin/attendance-overrides/{$rule->id}", $payload);

        $response->assertRedirect('/admin/attendance-overrides');
        $this->assertDatabaseHas('attendance_overrides', [
            'id'                     => $rule->id,
            'employee_name'          => 'Updated VIP Name',
            'adjusted_in_min_minute' => 25,
            'min_duration_hours'     => 8.50,
        ]);
    }

    public function test_super_admin_can_toggle_override_rule_status(): void
    {
        $rule = AttendanceOverride::create([
            'employee_id'            => 'EMP-7777',
            'card_no'                => '7777',
            'employee_name'          => 'Toggle Test',
            'check_in_window_start'  => '10:00:00',
            'check_in_window_end'    => '10:20:00',
            'adjusted_in_min_minute' => 20,
            'adjusted_in_max_minute' => 35,
            'min_duration_hours'     => 9.00,
            'is_active'              => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->patch("/admin/attendance-overrides/{$rule->id}/toggle");

        $response->assertRedirect('/admin/attendance-overrides');
        $this->assertDatabaseHas('attendance_overrides', [
            'id'        => $rule->id,
            'is_active' => false,
        ]);
    }

    public function test_super_admin_can_delete_override_rule(): void
    {
        $rule = AttendanceOverride::create([
            'employee_id'            => 'EMP-DEL',
            'card_no'                => 'DEL123',
            'employee_name'          => 'Delete Me',
            'check_in_window_start'  => '10:00:00',
            'check_in_window_end'    => '10:20:00',
            'adjusted_in_min_minute' => 20,
            'adjusted_in_max_minute' => 35,
            'min_duration_hours'     => 9.00,
            'is_active'              => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete("/admin/attendance-overrides/{$rule->id}");

        $response->assertRedirect('/admin/attendance-overrides');
        $this->assertDatabaseMissing('attendance_overrides', [
            'id' => $rule->id,
        ]);
    }

    public function test_manager_and_regular_user_cannot_access_or_manage_overrides(): void
    {
        // Manager
        $managerIndex = $this->actingAs($this->manager)->get('/admin/attendance-overrides');
        $managerIndex->assertStatus(403);

        $managerStore = $this->actingAs($this->manager)->post('/admin/attendance-overrides', [
            'card_no' => '9999',
            'check_in_window_start' => '10:00',
            'check_in_window_end' => '10:20',
            'adjusted_in_min_minute' => 20,
            'adjusted_in_max_minute' => 35,
            'min_duration_hours' => 9,
        ]);
        $managerStore->assertStatus(403);

        // Regular User
        $userIndex = $this->actingAs($this->regularUser)->get('/admin/attendance-overrides');
        $userIndex->assertStatus(403);
    }

    public function test_biometric_sync_uses_dynamic_database_override_rules(): void
    {
        // Register employee
        Employee::create([
            'employee_id' => 'EMP-DYN',
            'card_no' => '5555',
            'first_name' => 'Dynamic',
            'last_name' => 'RuleUser',
            'email' => 'dyn.rule@example.com',
            'company' => 'Acme Corp',
        ]);

        // Create dynamic rule for this employee with custom window 10:30 - 10:50 and 10 hours min duration
        AttendanceOverride::create([
            'employee_id'            => 'EMP-DYN',
            'card_no'                => '5555',
            'employee_name'          => 'Dynamic Rule User',
            'check_in_window_start'  => '10:30:00',
            'check_in_window_end'    => '10:50:00',
            'adjusted_in_min_minute' => 22,
            'adjusted_in_max_minute' => 28,
            'min_duration_hours'     => 10.00,
            'is_active'              => true,
        ]);

        $mockData = [
            'status' => 1,
            'message' => 'Success',
            'data' => [
                [
                    'card_no'      => '5555',
                    'badgenumber'  => 'EMP-DYN',
                    'punch_date'   => '2026-09-08',
                    'mintime'      => '10:35:00', // within 10:30 - 10:50 window
                    'minchecktime' => '2026-09-08 10:35:00',
                    'maxtime'      => '17:00:00',
                    'maxchecktime' => '2026-09-08 17:00:00',
                ],
            ],
        ];

        Http::fake([
            '*get_today_data_api_new.php*' => Http::response($mockData, 200),
        ]);

        $this->artisan('attendance:sync-biometric')
            ->assertSuccessful();

        $attendance = Attendance::where('card_no', '5555')->first();
        $this->assertNotNull($attendance);

        // Check-in must be shifted back to 09:22 - 09:28
        $this->assertStringStartsWith('2026-09-08 09:', $attendance->check_in_datetime);
        $min = (int) substr($attendance->check_in_time, 3, 2);
        $this->assertGreaterThanOrEqual(22, $min);
        $this->assertLessThanOrEqual(28, $min);

        // Check-out must be extended to between 10 hours and 10.5 hours (randomized)
        $inTime = strtotime($attendance->check_in_datetime);
        $outTime = strtotime($attendance->check_out_datetime);
        $durationSeconds = $outTime - $inTime;
        $this->assertGreaterThanOrEqual(10 * 3600 + 1, $durationSeconds);
        $this->assertLessThanOrEqual(10.5 * 3600 + 59, $durationSeconds);
    }
}
