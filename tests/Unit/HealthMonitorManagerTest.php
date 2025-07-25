<?php

use HealthMonitor\HealthMonitorManager;
use HealthMonitor\Dto\ExecutorResult;

test('runCheckers returns overall status and checks array', function () {

    $result1 = Mockery::mock(ExecutorResult::class);
    $result1->shouldReceive('toArray')->andReturn([
        'name' => 'db',
        'category' => 'app',
        'status' => true,
        'responseTime' => 10,
        'driver' => 'pgsql',
        'exception' => null,
    ]);
    $result1->shouldReceive('getStatus')->andReturn(true);

    $result2 = Mockery::mock(ExecutorResult::class);
    $result2->shouldReceive('toArray')->andReturn([
        'name' => 'cache',
        'category' => 'app',
        'status' => true,
        'responseTime' => 5,
        'driver' => 'redis',
        'exception' => null,
    ]);
    $result2->shouldReceive('getStatus')->andReturn(true);

    $mockExecutor = Mockery::mock('HealthMonitor\HealthMonitorExecutor');
    $mockExecutor->shouldReceive('run')->andReturn([$result1, $result2]);

    $manager = new class($mockExecutor) extends HealthMonitorManager {
        public function __construct($executor)
        {
            parent::__construct();
            $this->executor = $executor;
        }
    };

    $output = $manager->runCheckers();

    expect($output)->toBeArray()
        ->and($output['status'])->toBeTrue()
        ->and($output['checks'])->toHaveCount(2)
        ->and($output['checks'][0]['name'])->toBe('db')
        ->and($output['checks'][1]['name'])->toBe('cache');
});
