<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->adminUser = User::where('email', 'admin@example.com')->first();
    }

    public function test_guest_cannot_access_company_crud(): void
    {
        $response = $this->get('/admin/companies');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_companies_list_and_kpis(): void
    {
        Company::create([
            'name' => 'Acme Test Corp',
            'code' => 'ATC',
            'status' => 'active',
            'description' => 'Test company description',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/companies');

        $response->assertStatus(200);
        $response->assertSee('Company Management');
        $response->assertSee('Acme Test Corp');
        $response->assertSee('ATC');
    }

    public function test_admin_can_filter_and_search_companies(): void
    {
        Company::create(['name' => 'Alpha Solutions', 'code' => 'AS', 'status' => 'active']);
        Company::create(['name' => 'Beta Systems', 'code' => 'BS', 'status' => 'inactive']);

        // Search by keyword
        $searchResponse = $this->actingAs($this->adminUser)->get('/admin/companies?search=Alpha');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Alpha Solutions');
        $searchResponse->assertDontSee('Beta Systems');

        // Filter by status
        $statusResponse = $this->actingAs($this->adminUser)->get('/admin/companies?status=inactive');
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee('Beta Systems');
        $statusResponse->assertDontSee('Alpha Solutions');
    }

    public function test_admin_can_create_a_company(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/companies', [
            'name' => 'Innovative Labs Pvt Ltd',
            'code' => 'ILPL',
            'status' => 'active',
            'description' => 'Research and Development wing',
        ]);

        $response->assertRedirect('/admin/companies');
        $this->assertDatabaseHas('companies', [
            'name' => 'Innovative Labs Pvt Ltd',
            'code' => 'ILPL',
            'status' => 'active',
        ]);
    }

    public function test_company_name_must_be_unique(): void
    {
        Company::create(['name' => 'Unique Corp', 'status' => 'active']);

        $response = $this->actingAs($this->adminUser)->post('/admin/companies', [
            'name' => 'Unique Corp',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_a_company_and_synced_employees_are_updated(): void
    {
        $company = Company::create([
            'name' => 'Old Enterprise Name',
            'code' => 'OEN',
            'status' => 'active',
        ]);

        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-991',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'company' => 'Old Enterprise Name',
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($this->adminUser)->put("/admin/companies/{$company->id}", [
            'name' => 'New Global Enterprise',
            'code' => 'NGE',
            'status' => 'active',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect('/admin/companies');
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'New Global Enterprise',
            'code' => 'NGE',
        ]);

        // Verify assigned employee's string column also synced
        $employee->refresh();
        $this->assertEquals('New Global Enterprise', $employee->company);
    }

    public function test_cannot_delete_company_if_employees_are_assigned(): void
    {
        $company = Company::create([
            'name' => 'Protected Company',
            'status' => 'active',
        ]);

        $user = User::factory()->create();
        Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-992',
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice.smith@example.com',
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/companies/{$company->id}");

        $response->assertRedirect('/admin/companies');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }

    public function test_can_delete_company_if_no_employees_are_assigned(): void
    {
        $company = Company::create([
            'name' => 'Empty Company',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/companies/{$company->id}");

        $response->assertRedirect('/admin/companies');
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_employee_creation_with_company_dropdown(): void
    {
        $company = Company::create([
            'name' => 'Tech Solutions Ltd',
            'code' => 'TSL',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser)->post('/admin/employees', [
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'employee_id' => 'EMP-7788',
            'card_no' => 'CRD-7788',
            'email' => 'michael.scott@example.com',
            'company_id' => $company->id,
        ]);

        $response->assertRedirect('/admin/employees');

        $this->assertDatabaseHas('employees', [
            'employee_id' => 'EMP-7788',
            'company_id' => $company->id,
            'company' => 'Tech Solutions Ltd',
        ]);
    }

    public function test_employee_update_with_company_dropdown(): void
    {
        $companyA = Company::create(['name' => 'Company A', 'status' => 'active']);
        $companyB = Company::create(['name' => 'Company B', 'status' => 'active']);

        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-5555',
            'first_name' => 'Pam',
            'last_name' => 'Beesly',
            'email' => 'pam.beesly@example.com',
            'company' => 'Company A',
            'company_id' => $companyA->id,
        ]);

        $response = $this->actingAs($this->adminUser)->put("/admin/employees/{$employee->id}", [
            'first_name' => 'Pam',
            'last_name' => 'Halpert',
            'employee_id' => 'EMP-5555',
            'email' => 'pam.halpert@example.com',
            'company_id' => $companyB->id,
        ]);

        $response->assertRedirect('/admin/employees');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'company_id' => $companyB->id,
            'company' => 'Company B',
            'last_name' => 'Halpert',
        ]);
    }
}
