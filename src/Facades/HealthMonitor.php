<?php

namespace HealthMonitor\Facades;

use HealthMonitor\HealthMonitorManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string runCheckers()
 */
class HealthMonitor extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return HealthMonitorManager::class;
    }
}
