<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Ray\Di\ProviderInterface;

use const PHP_SAPI;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class TransactionNameProvider implements ProviderInterface
{
    private CliNameBuilder $cli;
    private WebNameBuilder $web;
    private string $name;

    public function __construct(CliNameBuilder $cli, WebNameBuilder $web)
    {
        $this->cli = $cli;
        $this->web = $web;
        if (PHP_SAPI === 'cli') {
            /** @psalm-suppress Superglobals */
            $this->name = $this->cli->buildBy($_SERVER);

            return;
        }

        /** @psalm-suppress Superglobals */
        $this->name = $this->web->buildBy($_SERVER);
    }

    public function get(): string
    {
        return $this->name;
    }
}
