<?php

use HealthMonitor\Factories\HealthCheckerFactory;
use HealthMonitor\Monitors\DatabaseHealthChecker;
use HealthMonitor\Monitors\ThirdPartyHealthChecker;

beforeEach(function () {
    config()->set('health-monitor.app-services', []);
    config()->set('health-monitor.third-party-services', []);
});

test('creates empty array if no services configured', function () {
    config()->set('health-monitor.app-services', []);
    config()->set('health-monitor.third-party-services', []);

    $result = HealthCheckerFactory::create();

    expect($result)->toBeArray()->toHaveCount(0);
});

test('creates app service checkers when enabled and inspector class exists', function () {
    config()->set('health-monitor.app-services', [
        'database' => [
            'enabled' => true,
            'inspector' => DatabaseHealthChecker::class,
            'arguments' => ['sqlite'],
        ],
    ]);
    config()->set('health-monitor.third-party-services', []);

    $result = HealthCheckerFactory::create();

    expect($result)->toBeArray()->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(DatabaseHealthChecker::class);
});

test('skips app service if not enabled or inspector missing', function () {
    config()->set('health-monitor.app-services', [
        'badservice' => [
            'enabled' => false,
            'inspector' => DatabaseHealthChecker::class,
            'arguments' => [],
        ],
        'noinspector' => [
            'enabled' => true,
            'inspector' => '',
            'arguments' => [],
        ],
    ]);
    config()->set('health-monitor.third-party-services', []);

    $result = HealthCheckerFactory::create();

    expect($result)->toBeArray()->toHaveCount(0);
});

test('throws exception if inspector class does not exist', function () {
    config()->set('health-monitor.app-services', [
        'invalid' => [
            'enabled' => true,
            'inspector' => 'NonExistentClass',
            'arguments' => [],
        ],
    ]);
    config()->set('health-monitor.third-party-services', []);

    HealthCheckerFactory::create();
})->throws(InvalidArgumentException::class, 'Health monitor class [NonExistentClass] for [invalid] does not exist.');

test('creates third party checkers when enabled', function () {
    config()->set('health-monitor.app-services', []);
    config()->set('health-monitor.third-party-services', [
        'api1' => [
            'enabled' => true,
            'url' => 'https://example.com',
        ],
        'api2' => [
            'enabled' => false,
            'url' => 'https://skip.com',
        ],
    ]);

    $result = HealthCheckerFactory::create();

    expect($result)->toBeArray()->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(ThirdPartyHealthChecker::class);
});
