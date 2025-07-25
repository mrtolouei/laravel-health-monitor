<?php

namespace HealthMonitor\Monitors;

use Exception;
use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\HealthResult;
use Illuminate\Support\Facades\Queue;

class QueueHealthChecker implements HealthCheckerInterface
{
    private string $name = 'Queue';
    private string $category = 'app';

    public function monitor(): HealthResult
    {
        try {
            Queue::connection()->size();
            return new HealthResult($this->name, $this->category, true);
        } catch (Exception $exception) {
            return new HealthResult($this->name, $this->category, false, $exception->getMessage());
        }
    }

    public function getConnectionDriver(): string|null
    {
        return Queue::getDefaultDriver();
    }
}
