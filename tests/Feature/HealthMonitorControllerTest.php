<?php

use HealthMonitor\HealthMonitorManager;
use HealthMonitor\Http\Controllers\HealthMonitorController;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/api-service-health', [HealthMonitorController::class, 'status']);
    $this->mockManager = Mockery::mock(HealthMonitorManager::class);
    app()->instance(HealthMonitorManager::class, $this->mockManager);
});

test('GET /api-service-health returns health status JSON structure', function () {
    $this->mockManager->shouldReceive('runCheckers')
        ->once()
        ->andReturn([
            'status' => true,
            'checks' => [
                [
                    'name' => 'db',
                    'category' => 'app',
                    'status' => true,
                    'responseTime' => 12.34,
                    'driver' => 'pgsql',
                    'exception' => null,
                ],
                [
                    'name' => 'cache',
                    'category' => 'app',
                    'status' => true,
                    'responseTime' => 7.89,
                    'driver' => 'redis',
                    'exception' => null,
                ],
            ],
        ]);

    $response = $this->getJson('/api-service-health');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'checks' => [
                '*' => ['name', 'category', 'status', 'responseTime', 'driver', 'exception']
            ],
        ])
        ->assertJson([
            'status' => true,
        ]);
});
