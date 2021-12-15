# Pj8.SentryModule

[Sentry](https://docs.sentry.io/platforms/php/) を [BEAR.Sunday](http://bearsunday.github.io/) アプリケーションで利用するためのモジュール

![Continuous Integration](https://github.com/pj8/pj8.sentrymodule/workflows/Continuous%20Integration/badge.svg)

## 機能

* BEAR.Sunday アプリケーションでの [Sentry PHP SDK](https://github.com/getsentry/sentry-php) の設定
* Sentry のエラー監視、パフォーマンスモニタリングへの統合を提供

## インストール

[Composer](https://getcomposer.org/) でプロジェクトにインストールします。

```bash
composer require pj8/sentry-module
```

## アプリケーションへの適用

- モジュールインストール

```php
use BEAR\Package\AbstractAppModule;
use BEAR\Package\Context\ProdModule as PackageProdModule;
use BEAR\Sunday\Extension\Error\ErrorInterface;
use Pj8\SentryModule\SentryModule;
use Pj8\SentryModule\SentryErrorHandler;

class ProdModule extends AbstractAppModule
{
    protected function configure()
    {
        $this->install(new PackageProdModule());
        $this->install(new SentryModule(['dsn' => 'https://secret@sentry.example.com/1"']));
        $this->rename(ErrorInterface::class, 'original');
        $this->bind(ErrorInterface::class)->to(SentryErrorHandler::class);
    }
}
```

### モジュールインストールの注意事項

SentryModule はエラーをキャプチャーするために以下のインターフェイスの束縛を上書きします。

- `\BEAR\Sunday\Extension\Error\ErrorInterface`
- `\BEAR\Sunday\Extension\Error\ThrowableHandlerInterface`

そのため、既にプロジェクト独自のエラーハンドラーが束縛されている場合は SentryModule のエラーキャプチャー機能が動作しない場合があります。
束縛の順序やコンテキストごとのモジュール設定など確認してください。

## パフォーマンスモニタリング

- パフォーマンスオプションを設定した場合、BEARリソースの処理時間が計測されます
- `Monitorable` アノテーションを使うと任意の処理を計測することもできます
