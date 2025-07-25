<?php

namespace HealthMonitor\Factories;

use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Monitors\ThirdPartyHealthChecker;
use InvalidArgumentException;

class HealthCheckerFactory
{
    /**
     * @return HealthCheckerInterface[]
     */
    public static function create(): array
    {
        $checkers = [];
        $appServices = config('health-monitor.app-services', []);
        $thirdPartyServices = config('health-monitor.third-party-services', []);
        foreach ($appServices as $key => $service) {
            if (empty($service['enabled']) || empty($service['inspector'])) {
                continue;
            }
            $inspector = $service['inspector'];
            if (!class_exists($inspector)) {
                throw new InvalidArgumentException("Health monitor class [$inspector] for [$key] does not exist.");
            }
            $checkers[] = new $inspector(...$service['arguments']);
        }
        foreach ($thirdPartyServices as $key => $service) {
            if (!empty($service['enabled'])) {
                $checkers[] = new ThirdPartyHealthChecker($key, $service);
            }
        }
        return $checkers;
    }
}
