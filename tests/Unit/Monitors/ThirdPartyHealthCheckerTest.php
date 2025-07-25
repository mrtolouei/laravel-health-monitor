<?php

use HealthMonitor\Monitors\ThirdPartyHealthChecker;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

beforeEach(function () {
    $this->config = [
        'url' => 'https://jsonplaceholder.typicode.com/todos/1',
        'method' => 'GET',
        'headers' => ['Accept' => 'application/json'],
        'timeout' => 5,
        'expected_status' => 200,
        'auth' => null,
    ];
});

test('third party health checker returns healthy result when response status matches', function () {
    $mockClient = Mockery::mock(Client::class);
    $mockResponse = new Response(200);

    $mockClient->shouldReceive('request')
        ->once()
        ->with('GET', $this->config['url'], Mockery::type('array'))
        ->andReturn($mockResponse);

    $checker = new ThirdPartyHealthChecker('json-test-api', $this->config);
    $checker->setClient($mockClient);

    $result = $checker->monitor();

    expect($result->isHealthy())->toBeTrue()
        ->and($result->getName())->toBe('json-test-api')
        ->and($result->getCategory())->toBe('third-party')
        ->and($result->getException())->toBeEmpty();
});

test('third party health checker returns unhealthy result on unexpected status code', function () {
    $mockClient = Mockery::mock(Client::class);
    $mockResponse = new Response(500);

    $mockClient->shouldReceive('request')
        ->once()
        ->andReturn($mockResponse);

    $checker = new ThirdPartyHealthChecker('json-test-api', $this->config);
    $checker->setClient($mockClient);

    $result = $checker->monitor();

    expect($result->isHealthy())->toBeFalse()
        ->and($result->getException())->toContain('Request http status code is 500');
});

test('third party health checker returns unhealthy result on client exception', function () {
    $mockClient = Mockery::mock(Client::class);

    $mockClient->shouldReceive('request')
        ->once()
        ->andThrow(new Exception('Connection error'));

    $checker = new ThirdPartyHealthChecker('json-test-api', $this->config);
    $checker->setClient($mockClient);

    $result = $checker->monitor();

    expect($result->isHealthy())->toBeFalse()
        ->and($result->getException())->toContain('Connection error');
});
