<?php

use HealthMonitor\Monitors\DatabaseHealthChecker;

beforeEach(function () {
    $this->app['config']->set('database.default', 'sqlite');
    $this->app['config']->set('database.connections.sqlite', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ]);
});

test('successful database check returns healthy result', function () {
    $checker = new DatabaseHealthChecker('sqlite');
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeTrue()
        ->and($result->getName())->toBe('Database')
        ->and($result->getCategory())->toBe('app')
        ->and($result->getException())->toBeEmpty();
});

test('invalid connection returns unhealthy result', function () {
    $checker = new DatabaseHealthChecker('invalid_connection');
    $result = $checker->monitor();

    expect($result->isHealthy())->toBeFalse()
        ->and($result->getException())->not->toBeNull();
});
