<?php

use Pj8\SentryModule\ExcludeSampler;

// \Sentry\init の引数になるオプション配列
return [
    'dsn' => null,
    'traces_sampler' => [
        new ExcludeSampler(
            0.0,
            // パフォーマンス計測をしたくないリクエストURIの配列
            ['/ignoreRequestUri.php']
        ),
        '__invoke',
    ],
];
