<?php


return [
    'app-services' => [
        'database' => [
            'enabled' => true,
            'inspector' => HealthMonitor\Monitors\DatabaseHealthChecker::class,
            'arguments' => [
                'connection' => config('database.default', 'sqlite')
            ],
        ],
        'filesystem' => [
            'enabled' => true,
            'inspector' => HealthMonitor\Monitors\FilesystemHealthChecker::class,
            'arguments' => [
                'disk' => config('filesystems.default', 'local')
            ],
        ],
        'cache' => [
            'enabled' => true,
            'inspector' => HealthMonitor\Monitors\CacheHealthChecker::class,
            'arguments' => [],
        ],
        'queue' => [
            'enabled' => true,
            'inspector' => HealthMonitor\Monitors\QueueHealthChecker::class,
            'arguments' => [],
        ],
        'redis' => [
            'enabled' => true,
            'inspector' => HealthMonitor\Monitors\RedisHealthChecker::class,
            'arguments' => [],
        ],
    ],
    'third-party-services' => [
        'json-test-api' => [
            'enabled' => true,
            'url' => 'https://jsonplaceholder.typicode.com/todos/1',
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
            ],
            'auth' => null,
            'timeout' => 5,
            'expected_status' => 200,
        ],
    ],
];
