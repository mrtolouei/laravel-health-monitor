<?php

use HealthMonitor\Monitors\FilesystemHealthChecker;
use Illuminate\Support\Facades\Storage;

test('filesystem health checker returns healthy result when storage disk works', function () {
    Storage::shouldReceive('disk')->once()->andReturnSelf();
    Storage::shouldReceive('put')->once()->andReturnTrue();
    Storage::shouldReceive('exists')->once()->andReturnTrue();
    Storage::shouldReceive('delete')->once()->andReturnTrue();

    $checker = new FilesystemHealthChecker('local');
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeTrue()
        ->and($result->getName())->toBe('Filesystem')
        ->and($result->getCategory())->toBe('app')
        ->and($result->getException())->toBeEmpty();
});

test('filesystem health checker returns unhealthy result on failure', function () {
    Storage::shouldReceive('disk')->andThrow(new Exception('Filesystem down'));

    $checker = new FilesystemHealthChecker('local');
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeFalse()
        ->and($result->getException())->toContain('Filesystem down');
});
