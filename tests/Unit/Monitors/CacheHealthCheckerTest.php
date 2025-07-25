<?php

use HealthMonitor\Monitors\CacheHealthChecker;
use Illuminate\Support\Facades\Cache;

test('cache health checker returns healthy result when cache works', function () {
    Cache::shouldReceive('put')->once();
    Cache::shouldReceive('get')->once()->andReturnTrue();

    $checker = new CacheHealthChecker();
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeTrue()
        ->and($result->getName())->toBe('Cache')
        ->and($result->getCategory())->toBe('app')
        ->and($result->getException())->toBeEmpty();
});

test('cache health checker returns unhealthy result on failure', function () {
    Cache::shouldReceive('put')->andThrow(new Exception('Cache down'));

    $checker = new CacheHealthChecker();
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeFalse()
        ->and($result->getException())->toContain('Cache down');
});
