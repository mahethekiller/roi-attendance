<?php

namespace Tests\Feature\Admin;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->adminUser = User::where('email', 'admin@example.com')->first();
    }

    public function test_guest_cannot_access_employee_crud(): void
    {
        $response = $this->get('/admin/employees');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_employees_list(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/employees');
        $response->assertStatus(200);
        $response->assertSee('Employee Directory');
    }

    public function test_admin_can_create_employee_and_user_is_created(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/employees', [
            'first_name' => 'Alexander',
            'last_name' => 'Pierce',
            'employee_id' => 'EMP-5001',
            'card_no' => 'CRD-5001',
            'email' => 'alexander.pierce@example.com',
        ]);

        $response->assertRedirect('/admin/employees');
        $this->assertDatabaseHas('users', ['email' => 'alexander.pierce@example.com', 'name' => 'Alexander Pierce']);
        $this->assertDatabaseHas('employees', ['employee_id' => 'EMP-5001', 'email' => 'alexander.pierce@example.com']);
    }

    public function test_admin_can_update_employee(): void
    {
        $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-999',
            'card_no' => 'CRD-999',
            'first_name' => 'Old',
            'last_name' => 'Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($this->adminUser)->put("/admin/employees/{$employee->id}", [
            'first_name' => 'New',
            'last_name' => 'Name',
            'employee_id' => 'EMP-999',
            'card_no' => 'CRD-999-NEW',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect('/admin/employees');
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'email' => 'new@example.com', 'card_no' => 'CRD-999-NEW']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'new@example.com', 'name' => 'New Name']);
    }

    public function test_admin_can_delete_employee_and_user(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-DEL',
            'first_name' => 'Delete',
            'last_name' => 'Me',
            'email' => $user->email,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/employees/{$employee->id}");
        $response->assertRedirect('/admin/employees');
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_download_sample_csv(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/employees/sample-csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="employees_sample.csv"');
    }

    public function test_admin_can_import_employees_via_csv(): void
    {
        $csvContent = "employee_id,card_no,first_name,last_name,email\n" .
                      "EMP-7001,CRD-7001,Test,UserOne,test.userone@example.com\n" .
                      "EMP-7002,CRD-7002,Test,UserTwo,test.usertwo@example.com\n";

        $file = UploadedFile::fake()->createWithContent('employees.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)->post('/admin/employees/import', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect('/admin/employees');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('employees', ['employee_id' => 'EMP-7001', 'email' => 'test.userone@example.com']);
        $this->assertDatabaseHas('employees', ['employee_id' => 'EMP-7002', 'email' => 'test.usertwo@example.com']);
    }
}
