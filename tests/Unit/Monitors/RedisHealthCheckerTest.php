<?php

use HealthMonitor\Monitors\RedisHealthChecker;
use Illuminate\Support\Facades\Redis;

test('redis health checker returns healthy result when redis responds with PONG', function () {
    Redis::shouldReceive('ping')->once()->andReturnTrue();

    $checker = new RedisHealthChecker();
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeTrue()
        ->and($result->getName())->toBe('Redis')
        ->and($result->getCategory())->toBe('app')
        ->and($result->getException())->toBeEmpty();
});

test('redis health checker returns unhealthy result on failure', function () {
    Redis::shouldReceive('ping')->andThrow(new Exception('Redis down'));

    $checker = new RedisHealthChecker();
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeFalse()
        ->and($result->getException())->toContain('Redis down');
});
