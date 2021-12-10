# Pj8.SentryModule

[Sentry](https://docs.sentry.io/platforms/php/) を [BEAR.Sunday](http://bearsunday.github.io/) アプリケーションで使うためのスターターキットモジュール

![Continuous Integration](https://github.com/pj8/pj8.sentrymodule/workflows/Continuous%20Integration/badge.svg)

## 機能

* BEAR.Sunday アプリケーションでの Sentry のセットアップ、設定
* Sentry のエラー監視、パフォーマンスモニタリング機能に対応

## インストール

[Composer]([https://getcomposer.org/) でプロジェクトにインストールします。

```bash
composer require pj8/sentry-module
```

## Step 2: モジュールを利用可能にする

モジュール `SentryModule` を [Sentry SDK](https://docs.sentry.io/platforms/php/configuration/) のオプション設定で初期化してインストールします。

### 設定とインストールの例:

- プロジェクトの開発環境用 [Sentry DSN](https://docs.sentry.io/quickstart/#configure-the-dsn) の値をプロジェクトの.env を設定

```
APP_ENV="local" # local|development|staging|production
SENTRY_DSN="https://xxx@xxx.ingest.sentry.io/xxx"
SENTRY_ERROR_SAMPLE_RATE=1.0
SENTRY_PERFORMANCE_SAMPLER_RATE=1.0
```

- `var/conf/sentry.php` で利用して設定

```php
<?php

use Pj8\SentryModule\ExcludeSampler;

// \Sentry\init の引数になるオプション配列
return [
    'dsn' => getenv('SENTRY_DSN') ?: null,
    'environment' => getenv('APP_ENV') ?: 'unknown',
    // @see https://docs.sentry.io/platforms/php/configuration/options/#sample-rate
    'sample_rate' => (float) getenv('SENTRY_ERROR_SAMPLE_RATE'),
    // パフォーマンス計測サンプリングを固定値にする場合
    // @see https://docs.sentry.io/platforms/php/configuration/options/#traces-sample-rate
    //'traces_sample_rate' => (float) getenv('SENTRY_SAMPLE_RATE'),
    // パフォーマンス計測サンプリングをPHPのコールバック関数で決める場合
    // @see https://docs.sentry.io/platforms/php/configuration/options/#traces-sampler
    'traces_sampler' => [
        new ExcludeSampler(
            (float) getenv('SENTRY_PERFORMANCE_SAMPLER_RATE'),
            // パフォーマンス計測をしたくないトランザクション名パターンの配列
            ['/healthcheck']
        ),
        'sample',
    ],
];
```

- モジュールをインストール

```php
use BEAR\Package\AbstractAppModule;
use Pj8\SentryModule\SentryModule;
use BEAR\Sunday\Extension\Error\ErrorInterface;
use Pj8\SentryModule\SentryErrorHandler;

class ProdModule extends AbstractAppModule
{
    protected function configure()
    {
        // ...
        $this->install(new SentryModule(include $this->appMeta->appDir . '/var/conf/sentry.php'));
        // Sentry のエラーキャプチャー機能を利用する設定例  ここから
        $this->rename(ErrorInterface::class, 'original');
        $this->bind(ErrorInterface::class)->to(SentryErrorHandler::class);
        // Sentry のエラーキャプチャー機能を利用する設定例  ここまで
    }
}
```

### 設定の注意事項

`dsn` 値が空または未定義の場合、全環境で Sentry が無効化されます。
ふだんの開発環境やテスト環境、CIビルド環境では Sentry へのイベント送信が不要であることがほとんどでしょうから無効化がのぞましいでしょう。
不用意に有効化してしまうと料金プランのクオータを超過する恐れがあるので気をつけてください。

### モジュールインストールの注意事項

SentryModule は、Sentry のエラーキャプチャー機能のために
`\BEAR\Sunday\Extension\Error\ErrorInterface` と `\BEAR\Sunday\Extension\Error\ThrowableHandlerInterface`に対する前処理をはさみこみます。

プロジェクトで束縛されていたエラーハンドラーをオリジナルインターフェイスとして保持し、
前処理として Sentry のエラーキャプチャー処理を呼び出してからオリジナルインターフェイスに処理を委譲する仕組みになっています。
このため、コンテキストをデコレートする側のモジュール（ProdModule）にプロジェクト固有の束縛設定を上書きで書いてしまうと
SentryModule のエラーキャプチャー機能は使えなくなるので注意してください。

たとえば、アプリケーションコンテキストが `prod-html-app` であれば、最優先である ProdModule 内にエラーハンドリングの束縛を書けば
エラーキャプチャー機能を確実に利用することができます。
