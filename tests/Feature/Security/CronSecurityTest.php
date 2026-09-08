<?php

namespace Tests\Feature\Security;

use App\Services\BiometricSyncService;
use Mockery;
use Tests\TestCase;

class CronSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.biometric.cron_token' => 'test_secure_cron_token_12345']);
    }

    public function test_cron_webhook_rejects_request_without_token(): void
    {
        $response = $this->getJson('/cron/sync-attendance');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized cron token.',
        ]);
    }

    public function test_cron_webhook_rejects_request_with_invalid_token(): void
    {
        $response = $this->getJson('/cron/sync-attendance?token=wrong_token');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized cron token.',
        ]);
    }

    public function test_cron_webhook_accepts_valid_token_query_param(): void
    {
        $mockService = Mockery::mock(BiometricSyncService::class);
        $mockService->shouldReceive('sync')
            ->once()
            ->with(null, null, 'webhook')
            ->andReturn(['success' => true, 'message' => 'Sync successful']);

        $this->app->instance(BiometricSyncService::class, $mockService);

        $response = $this->getJson('/cron/sync-attendance?token=test_secure_cron_token_12345');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Sync successful',
        ]);
    }

    public function test_cron_webhook_accepts_valid_token_header(): void
    {
        $mockService = Mockery::mock(BiometricSyncService::class);
        $mockService->shouldReceive('sync')
            ->once()
            ->with(null, null, 'webhook')
            ->andReturn(['success' => true, 'message' => 'Sync successful']);

        $this->app->instance(BiometricSyncService::class, $mockService);

        $response = $this->withHeaders([
            'X-Cron-Token' => 'test_secure_cron_token_12345',
        ])->postJson('/cron/sync-attendance');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_cron_webhook_rate_limiting_protects_against_dos(): void
    {
        $mockService = Mockery::mock(BiometricSyncService::class);
        $mockService->shouldReceive('sync')->andReturn(['success' => true]);
        $this->app->instance(BiometricSyncService::class, $mockService);

        // Send 10 allowed requests
        for ($i = 0; $i < 10; $i++) {
            $this->getJson('/cron/sync-attendance?token=test_secure_cron_token_12345');
        }

        // 11th request within same minute should hit 429 Too Many Requests
        $response = $this->getJson('/cron/sync-attendance?token=test_secure_cron_token_12345');
        $response->assertStatus(429);
    }
}
