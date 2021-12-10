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
