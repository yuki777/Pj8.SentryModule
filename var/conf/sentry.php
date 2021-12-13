<?php

use Pj8\SentryModule\ExcludeSampler;

// \Sentry\init の引数になるオプション配列
return [
    'dsn' => getenv('SENTRY_DSN') ?: null,
    'environment' => getenv('APP_ENV') ?: 'unknown',

    // エラーサンプリングレート
    // @see https://docs.sentry.io/platforms/php/configuration/options/#sample-rate
    'sample_rate' => (float) getenv('SENTRY_ERROR_SAMPLE_RATE'),

    // パフォーマンスサンプリングレートを固定値にする場合
    // @see https://docs.sentry.io/platforms/php/configuration/options/#traces-sample-rate
    //'traces_sample_rate' => (float) getenv('SENTRY_PERFORMANCE_SAMPLE_RATE'),

    // パフォーマンスサンプリングをコールバック関数で決める場合
    // @see https://docs.sentry.io/platforms/php/configuration/options/#traces-sampler
    'traces_sampler' => [
        new ExcludeSampler(
            (float) getenv('SENTRY_PERFORMANCE_SAMPLER_RATE'),
            // パフォーマンス計測をしたくないリクエストURIの配列
            ['/ignoreRequestUri.php']
        ),
        '__invoke',
    ],
];
