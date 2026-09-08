<?php

namespace Tests\Feature\Admin;

use App\Models\SyncLog;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncLogManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $manager;
    protected User $standardUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super-admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        $this->standardUser = User::factory()->create();
        $this->standardUser->assignRole('user');
    }

    public function test_super_admin_can_clear_all_sync_logs(): void
    {
        SyncLog::create([
            'trigger_type'   => 'manual_ui',
            'status'         => 'success',
            'imported_count' => 5,
            'updated_count'  => 2,
            'message'        => 'Test sync 1',
        ]);

        SyncLog::create([
            'trigger_type'   => 'cron',
            'status'         => 'failed',
            'imported_count' => 0,
            'updated_count'  => 0,
            'message'        => 'Test sync 2',
        ]);

        $this->assertEquals(2, SyncLog::count());

        $response = $this->actingAs($this->superAdmin)
            ->post('/admin/sync-logs/clear', [
                'scope' => 'all',
            ]);

        $response->assertRedirect('/admin/sync-logs');
        $response->assertSessionHas('success');
        $this->assertEquals(0, SyncLog::count());
    }

    public function test_super_admin_can_clear_logs_older_than_7_days(): void
    {
        // 2 old logs (10 days ago)
        $oldLog1 = SyncLog::create([
            'trigger_type' => 'cron',
            'status'       => 'success',
            'message'      => 'Old log 1',
        ]);
        $oldLog1->created_at = now()->subDays(10);
        $oldLog1->save();

        $oldLog2 = SyncLog::create([
            'trigger_type' => 'cron',
            'status'       => 'failed',
            'message'      => 'Old log 2',
        ]);
        $oldLog2->created_at = now()->subDays(8);
        $oldLog2->save();

        // 1 recent log (today)
        SyncLog::create([
            'trigger_type' => 'cron',
            'status'       => 'success',
            'message'      => 'Recent log',
        ]);

        $this->assertEquals(3, SyncLog::count());

        $response = $this->actingAs($this->superAdmin)
            ->post('/admin/sync-logs/clear', [
                'scope' => 'older_than_7_days',
            ]);

        $response->assertRedirect('/admin/sync-logs');
        $response->assertSessionHas('success');
        $this->assertEquals(1, SyncLog::count());
        $this->assertEquals('Recent log', SyncLog::first()->message);
    }

    public function test_super_admin_can_clear_failed_logs_only(): void
    {
        SyncLog::create([
            'trigger_type' => 'cron',
            'status'       => 'success',
            'message'      => 'Success log 1',
        ]);

        SyncLog::create([
            'trigger_type' => 'cron',
            'status'       => 'failed',
            'message'      => 'Failed log 1',
        ]);

        SyncLog::create([
            'trigger_type' => 'manual_ui',
            'status'       => 'failed',
            'message'      => 'Failed log 2',
        ]);

        $this->assertEquals(3, SyncLog::count());

        $response = $this->actingAs($this->superAdmin)
            ->post('/admin/sync-logs/clear', [
                'scope' => 'failed_only',
            ]);

        $response->assertRedirect('/admin/sync-logs');
        $response->assertSessionHas('success');
        $this->assertEquals(1, SyncLog::count());
        $this->assertEquals('Success log 1', SyncLog::first()->message);
    }

    public function test_non_super_admins_cannot_clear_sync_logs(): void
    {
        SyncLog::create([
            'trigger_type' => 'cron',
            'status'       => 'success',
            'message'      => 'Protected log',
        ]);

        // Regular admin -> 403
        $this->actingAs($this->admin)
            ->post('/admin/sync-logs/clear', ['scope' => 'all'])
            ->assertStatus(403);

        // Manager -> 403
        $this->actingAs($this->manager)
            ->post('/admin/sync-logs/clear', ['scope' => 'all'])
            ->assertStatus(403);

        // Standard user -> 403
        $this->actingAs($this->standardUser)
            ->post('/admin/sync-logs/clear', ['scope' => 'all'])
            ->assertStatus(403);

        $this->assertEquals(1, SyncLog::count());
    }

    public function test_guest_cannot_clear_sync_logs(): void
    {
        $response = $this->post('/admin/sync-logs/clear', ['scope' => 'all']);
        $response->assertRedirect('/login');
    }

    public function test_clear_logs_button_rendered_only_for_super_admin(): void
    {
        // Super Admin sees Clear Logs button & modal
        $response = $this->actingAs($this->superAdmin)->get('/admin/sync-logs');
        $response->assertStatus(200);
        $response->assertSee('Clear Logs');
        $response->assertSee('clearSyncLogsModal');

        // Regular Admin does not see Clear Logs button
        $responseAdmin = $this->actingAs($this->admin)->get('/admin/sync-logs');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertDontSee('clearSyncLogsModal');
    }
}
