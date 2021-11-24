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
