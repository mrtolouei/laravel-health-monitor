<?php

use HealthMonitor\Dto\ExecutorResult;

test('ExecutorResult constructs and returns correct values', function () {
    $result = new ExecutorResult(
        name: 'Database',
        category: 'app',
        status: true,
        responseTime: 1.23456,
        driver: 'pgsql',
        exception: null
    );

    expect($result->getName())->toBe('Database')
        ->and($result->getCategory())->toBe('app')
        ->and($result->getStatus())->toBeTrue()
        ->and($result->getResponseTime())->toBe(1.23)
        ->and($result->getDriver())->toBe('pgsql')
        ->and($result->getException())->toBeNull();

    $array = $result->toArray();

    expect($array)->toBeArray()
        ->toHaveKey('name')
        ->toHaveKey('category')
        ->toHaveKey('status')
        ->toHaveKey('responseTime')
        ->toHaveKey('driver')
        ->toHaveKey('exception')
        ->and($array['name'])->toBe('Database')
        ->and($array['responseTime'])->toBe(1.23);
});

test('ExecutorResult handles exception string properly', function () {
    $exceptionMessage = 'Something went wrong';
    $result = new ExecutorResult(
        name: 'Cache',
        category: 'app',
        status: false,
        responseTime: 0.5678,
        driver: 'redis',
        exception: $exceptionMessage
    );

    expect($result->getStatus())->toBeFalse()
        ->and($result->getException())->toBe($exceptionMessage)
        ->and($result->getResponseTime())->toBe(0.57);
});
