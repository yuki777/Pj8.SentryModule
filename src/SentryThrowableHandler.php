<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Sunday\Extension\Error\ThrowableHandlerInterface;
use BEAR\Sunday\Extension\Router\RouterMatch as Request;
use Ray\Di\Di\Named;
use Throwable;

use function Sentry\captureException;

class SentryThrowableHandler implements ThrowableHandlerInterface
{
    private ThrowableHandlerInterface $originalHandler;

    /**
     * @Named("original=original")
     */
    public function __construct(ThrowableHandlerInterface $original)
    {
        $this->originalHandler = $original;
    }

    /**
     * Handle Throwable
     */
    public function handle(Throwable $e, Request $request): ThrowableHandlerInterface
    {
        captureException($e);

        return $this->originalHandler->handle($e, $request);
    }

    public function transfer(): void
    {
        $this->originalHandler->transfer();
    }
}
