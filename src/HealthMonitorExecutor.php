<?php

namespace HealthMonitor;

use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\ExecutorResult;

class HealthMonitorExecutor
{
    /**
     * @param HealthCheckerInterface[] $checkers
     */
    public function __construct(protected array $checkers)
    {
        //
    }

    /**
     * @return ExecutorResult[]
     */
    public function run(): array
    {
        $results = [];
        foreach ($this->checkers as $key => $checker) {
            $start = microtime(true);
            $healthDto = $checker->monitor();
            $duration = (microtime(true) - $start) * 1000;
            $results[] = new ExecutorResult(
                name: $healthDto->getName(),
                category: $healthDto->getCategory(),
                status: $healthDto->isHealthy(),
                responseTime: $duration,
                driver: $checker->getConnectionDriver(),
                exception: $healthDto->getException(),
            );
        }

        return $results;
    }
}
