<?php

namespace Tests\Feature\Admin;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $manager;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->superAdmin = User::where('email', 'admin@example.com')->first();

        $this->admin = User::factory()->create([
            'name' => 'Regular Admin',
            'email' => 'regular_admin@example.com',
        ]);
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create([
            'name' => 'Department Manager',
            'email' => 'manager@example.com',
        ]);
        $this->manager->assignRole('manager');

        $this->user = User::factory()->create([
            'name' => 'Staff Employee',
            'email' => 'staff@example.com',
        ]);
        $this->user->assignRole('user');
    }

    public function test_super_admin_sees_all_navigation_components(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('User Accounts');
        $response->assertSee('Employee Directory');
        $response->assertSee('Attendance Logs');
        $response->assertSee('Reports & Analytics');
        $response->assertSee('API Access Tokens');
        $response->assertSee('API Documentation');
        $response->assertSee('API Traffic Logs');
        $response->assertSee('Sync History Logs');
        $response->assertSee('Roles & Spatie RBAC');
        $response->assertSee('System Settings');
        $response->assertSee('Add Employee');
        $response->assertSee('View Attendance Logs');
    }

    public function test_admin_sees_operational_nav_but_not_developer_api_tokens(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Employee Directory');
        $response->assertSee('User Accounts');
        $response->assertSee('Attendance Logs');
        // Admin does not have api.tokens.manage permission
        $response->assertDontSee('API Access Tokens');
    }

    public function test_manager_sees_monitoring_components_but_not_user_management(): void
    {
        $response = $this->actingAs($this->manager)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Employee Directory');
        $response->assertSee('Attendance Logs');
        // Manager does not have users.view or api.tokens.manage
        $response->assertDontSee('User Accounts');
        $response->assertDontSee('API Access Tokens');
    }

    public function test_standard_user_cannot_access_or_see_administrative_modules(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Employee Directory');
        $response->assertDontSee('User Accounts');
        $response->assertDontSee('API Access Tokens');
        $response->assertDontSee('API Documentation');

        // Direct route access should be rejected with 403 Forbidden
        $usersResponse = $this->actingAs($this->user)->get('/admin/users');
        $usersResponse->assertStatus(403);

        $tokensResponse = $this->actingAs($this->user)->get('/admin/api-tokens');
        $tokensResponse->assertStatus(403);

        $employeesResponse = $this->actingAs($this->user)->get('/admin/employees');
        $employeesResponse->assertStatus(403);
    }

    public function test_action_buttons_are_omitted_for_users_without_permission(): void
    {
        $aliceUser = User::factory()->create(['name' => 'Alice Smith', 'email' => 'alice@example.com']);
        Employee::create([
            'user_id' => $aliceUser->id,
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'employee_id' => 'EMP-ALICE',
            'email' => 'alice@example.com',
        ]);

        // Manager can view employees list, but cannot see Add Employee, Import CSV, or Delete
        $response = $this->actingAs($this->manager)->get('/admin/employees');
        $response->assertStatus(200);
        $response->assertSee('Alice Smith');
        $response->assertDontSee('Add Employee');
        $response->assertDontSee('Import CSV');
        $response->assertDontSee('Sample CSV');
        $response->assertDontSee('deleteEmployeeModal');
    }

    public function test_super_admin_can_see_delete_and_add_buttons(): void
    {
        $bobUser = User::factory()->create(['name' => 'Bob Jones', 'email' => 'bob@example.com']);
        Employee::create([
            'user_id' => $bobUser->id,
            'first_name' => 'Bob',
            'last_name' => 'Jones',
            'employee_id' => 'EMP-BOB',
            'email' => 'bob@example.com',
        ]);

        $response = $this->actingAs($this->superAdmin)->get('/admin/employees');
        $response->assertStatus(200);
        $response->assertSee('Add Employee');
        $response->assertSee('Import CSV');
        $response->assertSee('Sample CSV');
        $response->assertSee('data-bs-target="#deleteEmployeeModal"', false);
    }

    public function test_authorized_blade_component_renders_conditionally(): void
    {
        $view = Blade::render(
            '<x-authorized permission="api.tokens.manage"><span>SuperSecretTokenButton</span></x-authorized>'
        );

        // Guest / unauthenticated renders empty
        $this->assertStringNotContainsString('SuperSecretTokenButton', $view);

        // Authenticated as super-admin renders slot
        $this->actingAs($this->superAdmin);
        $renderedSuper = Blade::render(
            '<x-authorized permission="api.tokens.manage"><span>SuperSecretTokenButton</span></x-authorized>'
        );
        $this->assertStringContainsString('SuperSecretTokenButton', $renderedSuper);

        // Authenticated as standard user renders empty in hide mode
        $this->actingAs($this->user);
        $renderedUser = Blade::render(
            '<x-authorized permission="api.tokens.manage"><span>SuperSecretTokenButton</span></x-authorized>'
        );
        $this->assertStringNotContainsString('SuperSecretTokenButton', $renderedUser);

        // Authenticated as standard user renders disabled in disable mode
        $renderedDisabled = Blade::render(
            '<x-authorized permission="api.tokens.manage" mode="disable" disabledTooltip="Restricted"><span>SuperSecretTokenButton</span></x-authorized>'
        );
        $this->assertStringContainsString('pointer-events: none', $renderedDisabled);
        $this->assertStringContainsString('Restricted', $renderedDisabled);
    }
}
