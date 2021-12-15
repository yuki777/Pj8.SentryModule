<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Sunday\Extension\Error\ErrorInterface;
use Pj8\SentryModule\Exception\InvalidArgumentException;
use Ray\Di\AbstractModule;

class SentryErrorModule extends AbstractModule
{
    public function __construct(?AbstractModule $module = null)
    {
        if ($module === null) {
            throw new InvalidArgumentException('module is required');
        }

        parent::__construct($module);
        $module->rename(ErrorInterface::class, 'original');
    }

    protected function configure(): void
    {
        $this->bind(ErrorInterface::class)->to(SentryErrorHandler::class);
    }
}
