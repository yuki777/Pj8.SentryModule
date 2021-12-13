# Pj8.SentryModule

[Sentry](https://docs.sentry.io/platforms/php/) を [BEAR.Sunday](http://bearsunday.github.io/) アプリケーションで利用するためのモジュール

![Continuous Integration](https://github.com/pj8/pj8.sentrymodule/workflows/Continuous%20Integration/badge.svg)

## 機能

* BEAR.Sunday アプリケーションでの Sentry PHP Sdk の設定
* Sentry のエラー監視、パフォーマンスモニタリングへの統合を提供

## インストール

[Composer](https://getcomposer.org/) でプロジェクトにインストールします。

```bash
composer require pj8/sentry-module
```

## アプリケーションへの適用

`SentryModule` のインストール

### 設定例

- 設定値を環境変数に定義

```
APP_ENV="local"
SENTRY_DSN="https://xxx@xxx.ingest.sentry.io/xxx"
SENTRY_ERROR_SAMPLE_RATE=1.0
SENTRY_PERFORMANCE_SAMPLER_RATE=1.0
```

- `var/conf/sentry.php` の利用例  

```php
<?php

use Pj8\SentryModule\ExcludeSampler;

return [
    'dsn' => getenv('SENTRY_DSN'),
    'environment' => getenv('APP_ENV'),
    'sample_rate' => (float) getenv('SENTRY_ERROR_SAMPLE_RATE'),
    'traces_sample_rate' => (float) getenv('SENTRY_PERFORMANCE_SAMPLER_RATE'),
    'traces_sampler' => [
        new ExcludeSampler(
            (float) getenv('SENTRY_PERFORMANCE_SAMPLER_RATE'),
            ['/ignore-path']
        ),
        'sample',
    ],
];
```
設定内容はそのまま `\Sentry\init()` の引数として利用されます。  

参考  
[sample_rate](https://docs.sentry.io/platforms/php/configuration/options/#sample-rate)  
[traces_sample_rate](https://docs.sentry.io/platforms/php/configuration/options/#traces-sample-rate)  
[traces_sampler](https://docs.sentry.io/platforms/php/configuration/options/#traces-sampler)  


- モジュールのインストール

```php
use BEAR\Package\AbstractAppModule;
use Pj8\SentryModule\SentryModule;
use BEAR\Sunday\Extension\Error\ErrorInterface;
use Pj8\SentryModule\SentryErrorHandler;

class ProdModule extends AbstractAppModule
{
    protected function configure()
    {
        $this->install(new SentryModule(include $this->appMeta->appDir . '/var/conf/sentry.php'));
        $this->rename(ErrorInterface::class, 'original');
        $this->bind(ErrorInterface::class)->to(SentryErrorHandler::class);
    }
}
```

### モジュールインストールの注意事項

SentryModule はエラーをキャプチャーするために以下のinterfaceの束縛を上書きします。  

- `\BEAR\Sunday\Extension\Error\ErrorInterface`
- `\BEAR\Sunday\Extension\Error\ThrowableHandlerInterface`

そのため、既にプロジェクト独自のエラーハンドラーが束縛されている場合は SentryModule のエラーキャプチャー機能が動作しない場合があります。  
束縛の順序やコンテキストごとのモジュール設定など確認してください。
