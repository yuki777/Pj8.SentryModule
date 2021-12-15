# Pj8.SentryModule

![Continuous Integration](https://github.com/pj8/pj8.sentrymodule/workflows/Continuous%20Integration/badge.svg)

BEAR.Sundayアプリケーションのエラーとパフォーマンスを[Sentry](https://docs.sentry.io/platforms/php/)でモニタリングします。

## インストール

### Composerインストール

```bash
composer require pj8/sentry-module
```

### モジュールインストール

```php
use BEAR\Package\AbstractAppModule;
use BEAR\Package\Context\ProdModule as PackageProdModule;
use BEAR\Sunday\Extension\Error\ErrorInterface;
use Pj8\SentryModule\SentryModule;
use Pj8\SentryModule\SentryErrorHandler;

class ProdModule extends AbstractAppModule
{
    protected function configure(): void
    {
        // ...
        $this->install(new PackageProdModule());
        // PackageProdModuleの後にSentryModuleをインストール
        $this->install(new SentryModule([
            'dsn' => 'https://secret@sentry.example.com/1"'
        ])
        $this->install(new SentryErrorModule($this));
    }
}
```

## パフォーマンスモニタリング

パフォーマンス計測するメソッドに`Monitorable` 属性またはアノテーションを付与します。

```php
use Pj8\SentryModule\Annotation\Monitorable;

#[Monitorable]
public function foo()
{
}
```

```php
use Pj8\SentryModule\Annotation\Monitorable;

/** @Monitorable **/
public function foo()
{
}
```

## BEAR.Resourceサポート

[BEAR.Resource](https://github.com/bearsunday/BEAR.Resource)のリソースリクエストを全てモニタリングします。

```php
    $this->install(new SentryModule(['dsn' => 'https://secret@sentry.example.com/1']));
    $this->install(new ResourceMonitorModule());
```
