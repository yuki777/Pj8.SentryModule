<?php

declare(strict_types=1);

namespace Pj8\SentryModule\TransactionName;

use function sprintf;

class CliTransactionNameBuilder
{
    /**
     * \BEAR\Package\Provide\Router\CliRouter のパス作成手順に準じたパスのトランザクション名
     *
     * 例："cli - foo.sh"
     *
     * @param array<string, mixed> $globals グローバル変数
     */
    public function buildBy(array $globals): string
    {
        if (! isset($globals['argv'])) {
            return 'cli - unknown';
        }

        $argv = $globals['argv'];
        if (! isset($argv[0])) {
            return 'cli - unknown';
        }

        $scriptName = $argv[0];
        if (! isset($argv[2])) {
            return sprintf('cli - %s', $scriptName);
        }

        $uri = $argv[2];

        return sprintf('cli - %s - %s', $scriptName, $uri);
    }
}
