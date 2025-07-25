<?php

namespace HealthMonitor;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class HealthMonitorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/health-monitor.php', 'health-monitor');
    }

    public function boot(): void
    {
        $this->gate();
        $this->registerPublishing();
        $this->loadViews();
        $this->loadRoutes();
    }

    /**
     * Register the Health Monitor gate.
     *
     * This gate determines who can access Health Monitor in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHealthMonitor', function ($user = null) {
            if (app()->environment('local')) {
                return true;
            }
            return in_array(optional($user)->email, config('health-monitor.allowed_emails', []));
        });
    }

    private function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../config/health-monitor.php' => config_path('health-monitor.php'),
        ], 'health-monitor-config');

        $this->publishes([
            __DIR__ . '/../stubs/HealthMonitorServiceProvider.stub' => app_path('Providers/HealthMonitorServiceProvider.php'),
        ], 'health-monitor-provider');
    }

    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'health-monitor');
    }
}
