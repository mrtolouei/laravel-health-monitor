<?php

namespace HealthMonitor\Monitors;

use Exception;
use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\HealthResult;
use Illuminate\Support\Facades\DB;

class DatabaseHealthChecker implements HealthCheckerInterface
{
    private string $name = 'Database';
    private string $category = 'app';

    public function __construct(protected string $connection)
    {
        //
    }

    public function monitor(): HealthResult
    {
        try {
            DB::connection($this->connection)->getPdo();
            return new HealthResult($this->name, $this->category, true);
        } catch (Exception $exception) {
            return new HealthResult($this->name, $this->category, false, $exception->getMessage());
        }
    }

    public function getConnectionDriver(): string|null
    {
        return DB::connection($this->connection)->getDriverName();
    }
}
