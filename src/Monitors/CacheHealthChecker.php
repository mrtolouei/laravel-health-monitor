<?php

namespace HealthMonitor\Monitors;

use Exception;
use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\HealthResult;
use Illuminate\Support\Facades\Cache;
use Throwable;

class CacheHealthChecker implements HealthCheckerInterface
{
    private string $name = 'Cache';
    private string $category = 'app';

    public function monitor(): HealthResult
    {
        try {
            $key = '__health_cache_test__';
            Cache::put($key, true, now()->addSeconds(5));
            throw_if(Cache::get($key) !== true, new Exception('Unexpected error'));
            return new HealthResult($this->name, $this->category, true);
        } catch (Throwable $exception) {
            return new HealthResult($this->name, $this->category, false, $exception->getMessage());
        }
    }

    public function getConnectionDriver(): string|null
    {
        return Cache::getDefaultDriver() ?? null;
    }
}
