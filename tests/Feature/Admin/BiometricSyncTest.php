<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BiometricSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->adminUser = User::where('email', 'admin@example.com')->first();
    }

    public function test_artisan_command_syncs_biometric_data(): void
    {
        $mockData = [
            'status' => 1,
            'message' => 'Success',
            'data' => [
                [
                    'card_no' => '7701',
                    'badgenumber' => '1001',
                    'punch_date' => '2026-09-02',
                    'mintime' => '08:55:00',
                    'minchecktime' => '2026-09-02 08:55:00',
                    'maxtime' => '17:05:00',
                    'maxchecktime' => '2026-09-02 17:05:00',
                ],
            ],
        ];

        Http::fake([
            '*get_today_data_api_new.php*' => Http::response($mockData, 200),
        ]);

        $this->artisan('attendance:sync-biometric')
            ->assertSuccessful();

        $this->assertDatabaseHas('attendances', [
            'card_no' => '7701',
            'check_in_time' => '08:55:00',
            'check_out_time' => '17:05:00',
            'show_status' => 'present',
        ]);
    }

    public function test_admin_can_trigger_sync_from_ui(): void
    {
        $mockData = [
            'status' => 1,
            'message' => 'Success',
            'data' => [
                [
                    'card_no' => '8802',
                    'badgenumber' => '1002',
                    'punch_date' => '2026-09-02',
                    'mintime' => '09:45:00', // Late
                    'minchecktime' => '2026-09-02 09:45:00',
                    'maxtime' => '18:00:00',
                    'maxchecktime' => '2026-09-02 18:00:00',
                ],
            ],
        ];

        Http::fake([
            '*get_today_data_api_new.php*' => Http::response($mockData, 200),
        ]);

        $response = $this->actingAs($this->adminUser)->post('/admin/attendances/sync');
        $response->assertRedirect('/admin/attendances');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'card_no' => '8802',
            'show_status' => 'late',
        ]);
    }

    public function test_cron_webhook_requires_valid_token(): void
    {
        $response = $this->getJson('/cron/sync-attendance?token=invalid_token');
        $response->assertStatus(401);
    }

    public function test_cron_webhook_syncs_with_valid_token(): void
    {
        $mockData = [
            'status' => 1,
            'message' => 'Success',
            'data' => [],
        ];

        Http::fake([
            '*get_today_data_api_new.php*' => Http::response($mockData, 200),
        ]);

        $validToken = env('BIOMETRIC_CRON_TOKEN', 'roi_attendance_secure_sync_2026');
        $response = $this->getJson("/cron/sync-attendance?token={$validToken}");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
