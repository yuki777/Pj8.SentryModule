# Pj8.SentryModule

[Sentry](https://docs.sentry.io/platforms/php/) のBEAR.Sundayアプリケーションスターターキット

![Continuous Integration](https://github.com/pj8/pj8.sentrymodule/workflows/Continuous%20Integration/badge.svg)

## 機能

* BEAR.Sunday アプリケーションに Sentry をセットアップ、設定
* Sentry のパフォーマンスモニタリング機能に対応

## Installation

SDK を使うには [Composer]([https://getcomposer.org/) でプロジェクトにインストールします。

```bash
composer require pj8/sentry-module
```

### Step 2: モジュールを利用可能にする

モジュール `SentryModule` をSDKのオプション設定で初期化してインストールします。

例:
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

DSN に値があるときだけ機能が有効化されることに注意してください。 (次項 "SDKの設定"参照)。

## SDKの設定

プロジェクトの [Sentry DSN](https://docs.sentry.io/quickstart/#configure-the-dsn) の値を
 `var/conf/sentry.php` に設定してください。

`dsn` 値が空または未定義の場合、全環境で Sentry が無効化されます。
ふだんの開発環境では Sentry へのイベント送信が不要であることがほとんどでしょうから無効化しましょう。

例:

開発環境用のプロジェクトの.env
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

BEARブートストラップで組み込みの処理を追加してください。

例:
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
