<?php

namespace HealthMonitor\Monitors;

use Exception;
use GuzzleHttp\Client;
use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\HealthResult;
use Throwable;

class ThirdPartyHealthChecker implements HealthCheckerInterface
{
    private string $category = 'third-party';

    protected Client $client;

    public function __construct(protected string $name, protected array $config)
    {
        $this->setClient(new Client());
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function monitor(): HealthResult
    {
        try {
            $options = $this->buildRequestOptions();
            $method = $this->config['method'] ?? 'GET';
            $url = $this->config['url'];
            $response = $this->client->request($method, $url, $options);
            $expectedStatus = $this->config['expected_status'] ?? 200;
            throw_if($response->getStatusCode() !== $expectedStatus, new Exception('Request http status code is ' . $response->getStatusCode()));
            return new HealthResult($this->name, $this->category, true);
        } catch (Throwable $exception) {
            return new HealthResult($this->name, $this->category, false, $exception->getMessage());
        }
    }

    private function buildRequestOptions(): array
    {
        $options = [
            'headers' => $this->config['headers'] ?? [],
            'timeout' => $this->config['timeout'] ?? 5,
        ];
        if (empty($this->config['auth']) || $this->config['auth']['type'] === 'none') {
            return $options;
        }
        switch ($this->config['auth']['type']) {
            case 'basic':
                $options['auth'] = $this->getBasicAuthCredentials();
                break;
            case 'bearer':
                $options['headers']['Authorization'] = 'Bearer ' . $this->getBearerToken();
                break;
            case 'ntlm':
                $options['auth'] = $this->getNtlmAuthCredentials();
                break;
            default:
                break;
        }
        return $options;
    }

    protected function getBasicAuthCredentials(): array
    {
        return [
            $this->config['auth']['data'][0] ?? '',
            $this->config['auth']['data'][1] ?? '',
        ];
    }

    protected function getBearerToken(): string
    {
        return $this->config['auth']['data'][0] ?? '';
    }

    protected function getNtlmAuthCredentials(): array
    {
        return [
            $this->config['auth']['data'][0] ?? '',
            $this->config['auth']['data'][1] ?? '',
            'NTLM',
        ];
    }

    public function getConnectionDriver(): string|null
    {
        return 'Guzzle HTTP Client';
    }
}
