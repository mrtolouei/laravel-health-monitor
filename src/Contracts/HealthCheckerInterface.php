<?php

namespace HealthMonitor\Contracts;

use HealthMonitor\Dto\HealthResult;

interface HealthCheckerInterface
{
    public function monitor(): HealthResult;

    public function getConnectionDriver(): string|null;
}
