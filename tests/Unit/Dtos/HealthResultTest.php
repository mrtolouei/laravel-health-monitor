<?php

use HealthMonitor\Dto\HealthResult;

test('health result constructs correctly and returns expected values', function () {
    $result = new HealthResult('Database', 'app', true);

    expect($result->getName())->toBe('Database')
        ->and($result->getCategory())->toBe('app')
        ->and($result->isHealthy())->toBeTrue()
        ->and($result->getException())->toBeEmpty();

    $resultWithException = new HealthResult('Database', 'app', false, 'Error message');

    expect($resultWithException->isHealthy())->toBeFalse()
        ->and($resultWithException->getException())->toBe('Error message');
});
