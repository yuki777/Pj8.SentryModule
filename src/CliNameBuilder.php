<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use function sprintf;

class CliNameBuilder
{
    /**
     * \BEAR\Package\Provide\Router\CliRouter のパス作成手順に準じたパスのトランザクション名
     *
     * 例："cli - foo.sh", "cli - bin/bar.php - get - /debug/"
     *
     * @param array<string, mixed> $server 環境変数
     *
     * @see \BEAR\Package\Provide\Router\CliRouter
     */
    public function buildBy(array $server): string
    {
        if (! isset($server['argv'])) {
            return 'cli - unknown';
        }

        $argv = $server['argv'];
        if (! isset($argv[0])) {
            return 'cli - unknown';
        }

        $scriptName = $argv[0];
        if (! isset($argv[1])) {
            return sprintf('cli - %s', $scriptName);
        }

        if (! isset($argv[2])) {
            return sprintf('cli - %s - %s', $scriptName, $argv[1]);
        }

        return sprintf('cli - %s - %s - %s', $scriptName, $argv[1], $argv[2]);
    }
}
