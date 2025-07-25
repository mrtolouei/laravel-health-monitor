<?php

use HealthMonitor\Dto\HealthResult;
use HealthMonitor\HealthMonitorExecutor;
use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\ExecutorResult;

test('run returns array of ExecutorResult from checkers', function () {
    $mockChecker1 = Mockery::mock(HealthCheckerInterface::class);
    $mockChecker1->shouldReceive('monitor')
        ->once()
        ->andReturn(new class extends HealthResult {
            public function __construct()
            {
                parent::__construct('checker1', 'app', true);
            }
        });
    $mockChecker1->shouldReceive('getConnectionDriver')
        ->once()
        ->andReturn('driver1');

    $mockChecker2 = Mockery::mock(HealthCheckerInterface::class);
    $mockChecker2->shouldReceive('monitor')
        ->once()
        ->andReturn(new class extends HealthResult {
            public function __construct()
            {
                parent::__construct('checker2', 'cache', false, 'Error!');
            }
        });
    $mockChecker2->shouldReceive('getConnectionDriver')
        ->once()
        ->andReturn('driver2');

    $executor = new HealthMonitorExecutor([$mockChecker1, $mockChecker2]);

    $results = $executor->run();

    expect($results)->toBeArray()->toHaveCount(2)
        ->and($results[0])->toBeInstanceOf(ExecutorResult::class)
        ->and($results[0]->getName())->toBe('checker1')
        ->and($results[0]->getCategory())->toBe('app')
        ->and($results[0]->getStatus())->toBeTrue()
        ->and($results[0]->getDriver())->toBe('driver1')
        ->and($results[0]->getException())->toBeEmpty()
        ->and($results[0]->getResponseTime())->toBeFloat()
        ->and($results[1])->toBeInstanceOf(ExecutorResult::class)
        ->and($results[1]->getName())->toBe('checker2')
        ->and($results[1]->getCategory())->toBe('cache')
        ->and($results[1]->getStatus())->toBeFalse()
        ->and($results[1]->getDriver())->toBe('driver2')
        ->and($results[1]->getException())->toBe('Error!')
        ->and($results[1]->getResponseTime())->toBeFloat();
});
