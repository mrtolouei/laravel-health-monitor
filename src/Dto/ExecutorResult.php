<?php

namespace HealthMonitor\Dto;

class ExecutorResult
{
    public function __construct(
        public string      $name,
        public string      $category,
        public bool        $status,
        public float       $responseTime,
        public string      $driver,
        public string|null $exception,
    )
    {
        //
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'category' => $this->getCategory(),
            'status' => $this->getStatus(),
            'responseTime' => $this->getResponseTime(),
            'driver' => $this->getDriver(),
            'exception' => $this->getException(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getResponseTime(): float
    {
        return round($this->responseTime, 2);
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getException(): ?string
    {
        return $this->exception;
    }
}
