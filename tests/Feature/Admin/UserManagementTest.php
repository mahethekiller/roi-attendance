<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->adminUser = User::where('email', 'admin@example.com')->first();
    }

    public function test_guest_cannot_access_user_crud(): void
    {
        $response = $this->get('/admin/users');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('User Management');
        $response->assertSee($this->adminUser->name);
    }

    public function test_admin_can_create_user_with_role(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/users', [
            'name' => 'Jane Developer',
            'email' => 'jane@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'roles' => ['admin'],
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com', 'name' => 'Jane Developer']);

        $createdUser = User::where('email', 'jane@example.com')->first();
        $this->assertTrue($createdUser->hasRole('admin'));
    }

    public function test_admin_can_update_user_and_roles(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $user->assignRole('user');

        $response = $this->actingAs($this->adminUser)->put("/admin/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email,
            'roles' => ['admin'],
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);

        $user->refresh();
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_user_cannot_delete_their_own_account(): void
    {
        $response = $this->actingAs($this->adminUser)->delete("/admin/users/{$this->adminUser->id}");
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('error', 'You cannot delete your own active account.');
        $this->assertDatabaseHas('users', ['id' => $this->adminUser->id]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($this->adminUser)->delete("/admin/users/{$user->id}");
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
