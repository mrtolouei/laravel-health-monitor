<?php

use HealthMonitor\Monitors\QueueHealthChecker;
use Illuminate\Support\Facades\Queue;

test('queue health checker returns healthy result when queue works', function () {
    Queue::shouldReceive('connection->size')->once()->andReturn(0);

    $checker = new QueueHealthChecker();
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeTrue()
        ->and($result->getName())->toBe('Queue')
        ->and($result->getCategory())->toBe('app')
        ->and($result->getException())->toBeEmpty();
});

test('queue health checker returns unhealthy result on failure', function () {
    Queue::shouldReceive('connection->size')->andThrow(new Exception('Queue down'));

    $checker = new QueueHealthChecker();
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeFalse()
        ->and($result->getException())->toContain('Queue down');
});
