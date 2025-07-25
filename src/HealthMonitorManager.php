<?php

namespace HealthMonitor;

use HealthMonitor\Dto\ExecutorResult;
use HealthMonitor\Factories\HealthCheckerFactory;

class HealthMonitorManager
{
    protected HealthMonitorExecutor $executor;

    public function __construct()
    {
        $this->executor = new HealthMonitorExecutor(
            HealthCheckerFactory::create(),
        );
    }

    public function runCheckers(): array
    {
        $results = $this->executor->run();
        $checksArray = [];

        foreach ($results as $result) {
            $checksArray[] = $result->toArray();
        }

        $overallStatus = !collect($results)->contains(fn(ExecutorResult $r) => $r->getStatus() === false);

        return [
            'status' => $overallStatus,
            'checks' => $checksArray,
        ];
    }
}
