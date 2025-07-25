<?php

namespace HealthMonitor\Dto;

class HealthResult
{
    public function __construct(
        protected string  $name,
        protected string  $category,
        protected bool    $isHealthy,
        protected ?string $exception = null,
    )
    {
        //
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function isHealthy(): bool
    {
        return $this->isHealthy;
    }

    public function getException(): string|null
    {
        return $this->clean($this->exception);
    }

    private function clean(string|null $message): string|null
    {
        $message = str_replace(["\n", "\r", "\t"], ' ', $message);
        $message = preg_replace('/\s+/', ' ', $message);
        $message = trim($message, "\"' ");
        return stripslashes($message);
    }
}
