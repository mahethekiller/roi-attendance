<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->admin = User::where('email', 'admin@example.com')->first();
    }

    public function test_guest_is_redirected_to_login_from_api_docs(): void
    {
        $response = $this->get(route('admin.api-docs.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_api_documentation(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.api-docs.index'));

        $response->assertOk();
        $response->assertSee('REST API Reference & Documentation', false);
        $response->assertSee('Download Full Spec (.txt)', false);
        $response->assertSee('/api/attendance', false);
        $response->assertSee('/v1/auth/token', false);
        $response->assertSee('/v1/attendances', false);
        $response->assertSee('/v1/attendances/daily-summary', false);
        $response->assertSee('/v1/attendances/{id}', false);
    }

    public function test_admin_can_export_full_txt_specification(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.api-docs.export-txt'));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="attendance_api_specification.txt"');
        $content = $response->streamedContent();
        $this->assertStringContainsString('ROI ATTENDANCE REST API SPECIFICATION', $content);
        $this->assertStringContainsString('1. AUTHENTICATION & TOKEN GENERATION', $content);
        $this->assertStringContainsString('2. HRSALE COMPATIBLE ATTENDANCE ENDPOINT', $content);
        $this->assertStringContainsString('3. PAGINATED ATTENDANCE RECORDS', $content);
        $this->assertStringContainsString('4. GET DAILY ATTENDANCE SUMMARY METRICS', $content);
        $this->assertStringContainsString('5. GET SINGLE ATTENDANCE RECORD', $content);
    }

    public function test_admin_can_export_individual_hrsale_attendance_spec(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.api-docs.export-endpoint', ['endpoint' => 'hrsale-attendance']));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="hrsale_attendance_endpoint_spec.txt"');
        $content = $response->streamedContent();
        $this->assertStringContainsString('HRsale ATTENDANCE ENDPOINT SPECIFICATION', $content);
        $this->assertStringContainsString('POST', $content);
        $this->assertStringContainsString('/attendance', $content);
        $this->assertStringContainsString('punch_date', $content);
    }

    public function test_admin_can_export_individual_auth_token_spec(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.api-docs.export-endpoint', ['endpoint' => 'auth-token']));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="auth_token_endpoint_spec.txt"');
        $content = $response->streamedContent();
        $this->assertStringContainsString('AUTHENTICATION TOKEN ENDPOINT SPECIFICATION', $content);
        $this->assertStringContainsString('/v1/auth/token', $content);
        $this->assertStringContainsString('token_name', $content);
    }

    public function test_admin_can_export_individual_attendances_list_spec(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.api-docs.export-endpoint', ['endpoint' => 'attendances-list']));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="attendances_list_endpoint_spec.txt"');
        $content = $response->streamedContent();
        $this->assertStringContainsString('PAGINATED ATTENDANCE RECORDS ENDPOINT SPECIFICATION', $content);
        $this->assertStringContainsString('GET', $content);
        $this->assertStringContainsString('/v1/attendances', $content);
    }

    public function test_admin_can_export_individual_daily_summary_spec(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.api-docs.export-endpoint', ['endpoint' => 'daily-summary']));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="daily_summary_endpoint_spec.txt"');
        $content = $response->streamedContent();
        $this->assertStringContainsString('DAILY ATTENDANCE SUMMARY ENDPOINT SPECIFICATION', $content);
        $this->assertStringContainsString('/v1/attendances/daily-summary', $content);
    }

    public function test_admin_can_export_individual_attendance_single_spec(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.api-docs.export-endpoint', ['endpoint' => 'attendance-single']));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="attendance_single_endpoint_spec.txt"');
        $content = $response->streamedContent();
        $this->assertStringContainsString('SINGLE ATTENDANCE RECORD ENDPOINT SPECIFICATION', $content);
        $this->assertStringContainsString('/v1/attendances/{id}', $content);
    }

    public function test_export_invalid_endpoint_returns_404(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.api-docs.export-endpoint', ['endpoint' => 'invalid-endpoint-slug']));

        $response->assertNotFound();
    }
}
