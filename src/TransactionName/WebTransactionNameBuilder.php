<?php

declare(strict_types=1);

namespace Pj8\SentryModule\TransactionName;

use function parse_url;
use function sprintf;

use const PHP_URL_PATH;

class WebTransactionNameBuilder
{
    /**
     * \BEAR\Package\Provide\Router\WebRouter のパス作成手順に準じたパスのトランザクション名
     *
     * 例："aaa.bbb.jp - /foo/bar"
     *
     * @param array<string, mixed> $server 環境変数
     */
    public function buildBy(array $server): string
    {
        if (! isset($server['HTTP_HOST']) || ! isset($server['REQUEST_URI'])) {
            return 'web - unknown';
        }

        $site = $server['HTTP_HOST'];
        // See \BEAR\Sunday\Provide\Router\WebRouter
        $path = parse_url($server['REQUEST_URI'], PHP_URL_PATH);

        return sprintf('%s - %s', $site, $path);
    }
}
