<?php

namespace HealthMonitor\Monitors;

use Exception;
use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\HealthResult;
use Illuminate\Support\Facades\Redis;
use Throwable;

class RedisHealthChecker implements HealthCheckerInterface
{
    private string $name = 'Redis';
    private string $category = 'app';

    public function monitor(): HealthResult
    {
        try {
            $ping = Redis::ping();
            throw_if($ping !== true, new Exception('Unexpected error'));
            return new HealthResult($this->name, $this->category, true);
        } catch (Throwable $exception) {
            return new HealthResult($this->name, $this->category, false, $exception->getMessage());
        }
    }

    public function getConnectionDriver(): string|null
    {
        return config('database.redis.client', 'redis');
    }
}
