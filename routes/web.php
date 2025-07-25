<?php

use HealthMonitor\Http\Controllers\HealthMonitorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['can:viewHealthMonitor'])->group(function () {
    Route::get('/api-service-health', [HealthMonitorController::class, 'status']);
    Route::get('/service-health', function () {
        return view('health-monitor::health-monitor');
    });
});
