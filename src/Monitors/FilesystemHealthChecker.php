<?php

namespace HealthMonitor\Monitors;

use Exception;
use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\HealthResult;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FilesystemHealthChecker implements HealthCheckerInterface
{
    private string $name = 'Filesystem';
    private string $category = 'app';

    public function __construct(protected string $disk)
    {
        //
    }

    public function monitor(): HealthResult
    {
        try {
            $disk = Storage::disk($this->disk);
            $testFile = '__health_fs_test.txt';
            $disk->put($testFile, 'ok');
            $exists = $disk->exists($testFile);
            $disk->delete($testFile);
            throw_if(!$exists, new Exception('Cannot access to storage disk'));
            return new HealthResult($this->name, $this->category, true);
        } catch (Throwable $exception) {
            return new HealthResult($this->name, $this->category, false, $exception->getMessage());
        }
    }

    public function getConnectionDriver(): string|null
    {
        return $this->disk;
    }
}
