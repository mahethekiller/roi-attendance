<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->user = User::factory()->create([
            'email' => 'apitest@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;

        // Seed sample employee and attendance
        $employee = Employee::create([
            'user_id' => $this->user->id,
            'employee_id' => 'EMP-9901',
            'card_no' => 'CRD-9901',
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice.smith@example.com',
            'company' => 'Acme Inc',
        ]);

        Attendance::create([
            'card_no' => 'CRD-9901',
            'punch_date' => '2026-09-02',
            'check_in_time' => '08:50:00',
            'check_out_time' => '17:10:00',
            'show_status' => 'present',
        ]);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/v1/attendances');
        $response->assertStatus(401);
    }

    public function test_auth_token_can_be_issued(): void
    {
        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'apitest@example.com',
            'password' => 'password123',
            'token_name' => 'ci_test_token',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'token', 'token_type', 'user']);
    }

    public function test_authenticated_user_can_retrieve_attendances(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/attendances');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'card_no', 'punch_date', 'check_in_time', 'check_out_time', 'show_status', 'employee']
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total']
            ]);
    }

    public function test_filtering_attendances_by_card_and_status(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/attendances?card_no=CRD-9901&status=present');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));

        $mismatchResponse = $this->withToken($this->token)->getJson('/api/v1/attendances?status=late');
        $mismatchResponse->assertStatus(200);
        $this->assertEquals(0, count($mismatchResponse->json('data')));
    }

    public function test_daily_summary_endpoint(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/attendances/daily-summary?date=2026-09-02');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'date' => '2026-09-02',
                'summary' => [
                    'total_punches' => 1,
                    'present_count' => 1,
                    'late_count' => 0,
                ]
            ]);
    }

    public function test_api_request_is_logged_in_database(): void
    {
        $this->withToken($this->token)->getJson('/api/v1/attendances?status=present');

        $this->assertDatabaseHas('api_request_logs', [
            'method' => 'GET',
            'status_code' => 200,
        ]);
    }
}
