# Laravel Health Monitor
A lightweight, extendable Laravel package for monitoring the health of internal services like Database, Redis, Queue, Cache, Filesystem, and any third-party APIs.

Built for developers who care about observability and application resilience.

## Installation
Install the package via Composer:
```
composer require mrtolouei/laravel-health-monitor
```
Publish the configuration and provider stub:
```
php artisan vendor:publish --tag=health-monitor-config
php artisan vendor:publish --tag=health-monitor-provider
```

## Configuration
The config file will be published to:

`config/health-monitor.php`

It contains two main sections:
### ✅ App Services
You can enable/disable health checkers and configure each one:
```php
'app-services' => [
    'database' => [
        'enabled' => true,
        'inspector' => HealthMonitor\Monitors\DatabaseHealthChecker::class,
        'arguments' => [
            'connection' => config('database.default', 'sqlite')
        ],
    ],
    'filesystem' => [
        'enabled' => true,
        'inspector' => HealthMonitor\Monitors\FilesystemHealthChecker::class,
        'arguments' => [
            'disk' => config('filesystems.default', 'local')
        ],
    ],
    ...
],
```

### 🌐 Third-Party APIs
You can define any number of third-party HTTP APIs to monitor:

```php
'third-party-services' => [
    'json-test-api' => [
        'enabled' => true,
        'url' => 'https://jsonplaceholder.typicode.com/todos/1',
        'method' => 'GET',
        'headers' => [
            'Accept' => 'application/json',
        ],
        'auth' => null,
        'timeout' => 5,
        'expected_status' => 200,
    ],
],
```

Supported authentication types:

- `none`
- `basic`  (username & password)
- `bearer` (token)
- `ntlm`

### 🔒 Access Control
A `Gate` is registered automatically to restrict access to non-local environments.

You can customize allowed users in `config/health-monitor.php`:
```php
'allowed_emails' => [
    'admin@example.com',
    'dev@example.com',
],
```

### 📡 Routes
This package registers two routes (behind the `viewHealthMonitor` gate):

| Route              | Method | Description                         |
|--------------------|--------|-------------------------------------|
| `/api-service-health`| `GET`    | Returns JSON of health check status|
| `/service-health`    | `GET`    | Shows a Blade view of statuses     |

### 🧪 Add Custom Checkers
Create a new class that implements the `HealthCheckerInterface`:

```php
use HealthMonitor\Contracts\HealthCheckerInterface;
use HealthMonitor\Dto\HealthResult;

class CustomChecker implements HealthCheckerInterface {
    public function monitor(): HealthResult {
        return new HealthResult('My Checker', 'custom', true);
    }

    public function getConnectionDriver(): string|null {
        return 'custom';
    }
}
```
Then register it in the config file.

### 📂 Directory Structure (short overview)
```text
laravel-health-monitor/
├── config/
├── src/
│   ├── Contracts/
│   ├── Dto/
│   ├── Monitors/
│   ├── Facades/
│   ├── Factories/
│   └── HealthMonitorExecutor.php
└── routes/web.php
```

### 🙌 Credits
Developed and maintained by [Ali Tolouei](https://github.com/mrtolouei)

Feel free to contribute or suggest features via issues and PRs!

### 📄 License
This package is open-sourced software licensed under the [MIT license](LICENSE).
