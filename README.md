# Pj8.SentryModule

BEAR.Sunday integration for [Sentry](https://docs.sentry.io/platforms/php/).

![Continuous Integration](https://github.com/pj8/pj8.sentrymodule/workflows/Continuous%20Integration/badge.svg)

[README_ja](./README_ja.md)

## Feature

- A fast Sentry setup and easy configuration in your BEAR.Sunday app
- Performance monitoring

## Installation

To install the SDK you will need to be using [Composer]([https://getcomposer.org/) in your project.

```bash
composer require pj8/sentry-module
```

### Step 2: Enable the module

Install `SentryModule` with the configuration of the SDK.

Example:
```php
use BEAR\Package\AbstractAppModule;
use Pj8\SentryModule\SentryModule;

class AppModule extends AbstractAppModule
{
    protected function configure()
    {
        // ...
        $this->install(new SentryModule(include $this->appMeta->appDir . '/var/conf/sentry.php'));
    }
}
```

Note that, the package will be enabled only when providing a DSN (see the next step "Configuration of the SDK").

## Configuration of the SDK

Add your [Sentry DSN](https://docs.sentry.io/quickstart/#configure-the-dsn) value of your project,
Add it to `var/conf/sentry.php` .

Keep in mind that leaving the `dsn` value empty (or undeclared) in other environments will effectively disable Sentry reporting.

Example:

.env in your project for local development environment only
```
APP_ENV="local" # local|development|staging|production
SENTRY_DSN="https://xxx@xxx.ingest.sentry.io/xxx"
SENTRY_SAMPLE_RATE=1.0
SENTRY_SUNDAY_TRACES_RATE_DEFAULT=1.0
```

var/conf/sentry.php
```php
<?php

use Pj8\SentryModule\TracesSampler;

// \Sentry\init の引数になるオプション配列
return [
    'dsn' => getenv('SENTRY_DSN') ?: null,
    'environment' => getenv('APP_ENV') ?: 'unknown',
    // @see https://docs.sentry.io/platforms/php/configuration/options/#sample-rate
    'sample_rate' => (float) getenv('SENTRY_SAMPLE_RATE'),
    // @see https://docs.sentry.io/platforms/php/configuration/options/#traces-sampler
    'traces_sampler' => [
        new TracesSampler(
            (float) getenv('SENTRY_SUNDAY_TRACES_RATE_DEFAULT'),
            // トレースしないトランザクション名の配列
            ['healthcheck']
        ),
        'sample',
    ],
];
```

BEAR Bootstrap Example:

```php
<?php

use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Extension\Application\AppInterface;
use MyVendor\MyProject\Injector;
use MyVendor\MyProject\Module\App;
use Pj8\SentryModule\Transaction\TransactionInterface;

use function Sentry\captureException;

return static function (string $context, array $globals, array $server): int {

    $app = Injector::getInstance($context)->getInstance(AppInterface::class);
    $request = $app->router->match($globals, $server);

    try {
        // [integration point 1]
        /** @var TransactionFacade $sentry */
        $sentry = Injector::getInstance($context)->getInstance(TransactionInterface::class, 'sentry-transaction-web');
        $sentry->startTransaction();

        $uri = parse_url($server['REQUEST_URI']);
        $response = $app->resource->{$request->method}->uri($uri)($request->query);

        // [integration point 2]
        $sentry->finishTransaction();

        /** @var ResourceObject $response */
        $response->transfer($app->responder, $server);

        return 0;
    } catch (\Exception $e) {
        // [integration point 3]
        if (getenv('SENTRY_DSN')) {
            captureException($e);
        }

        $app->error->handle($e, $request)->transfer();

        return 1;
    }
};
```
