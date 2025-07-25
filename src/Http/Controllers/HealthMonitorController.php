<?php

namespace HealthMonitor\Http\Controllers;

use HealthMonitor\Facades\HealthMonitor;
use Illuminate\Http\JsonResponse;

class HealthMonitorController
{
    public function status(): JsonResponse
    {
        return response()->json(
           HealthMonitor::runCheckers(),
        );
    }
}